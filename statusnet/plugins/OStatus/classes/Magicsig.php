<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2010, StatusNet, Inc.
 *
 * A sample module to show best practices for StatusNet plugins
 *
 * PHP version 5
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   StatusNet
 * @author    James Walker <james@status.net>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    exit(1);
}

require_once 'Crypt/RSA.php';

class Magicsig extends Memcached_DataObject
{
    const PUBLICKEYREL = 'magic-public-key';

    public $__table = 'magicsig';

    /**
     * Key to user.id/profile.id for the local user whose key we're storing.
     *
     * @var int
     */
    public $user_id;

    /**
     * Flattened string representation of the key pair; callers should
     * usually use $this->publicKey and $this->privateKey directly,
     * which hold live Crypt_RSA key objects.
     *
     * @var string
     */
    public $keypair;

    /**
     * Crypto algorithm used for this key; currently only RSA-SHA256 is supported.
     *
     * @var string
     */
    public $alg;

    /**
     * Public RSA key; gets serialized in/out via $this->keypair string.
     *
     * @var Crypt_RSA
     */
    public $publicKey;

    /**
     * PrivateRSA key; gets serialized in/out via $this->keypair string.
     *
     * @var Crypt_RSA
     */
    public $privateKey;

    public function __construct($alg = 'RSA-SHA256')
    {
        $this->alg = $alg;
    }

    /**
     * Fetch a Magicsig object from the cache or database on a field match.
     *
     * @param string $k
     * @param mixed $v
     * @return Magicsig
     */
    public /*static*/ function staticGet($k, $v=null)
    {
        $obj =  parent::staticGet(__CLASS__, $k, $v);
        if (!empty($obj)) {
            $obj = Magicsig::fromString($obj->keypair);

            // Double check keys: Crypt_RSA did not
            // consistently generate good keypairs.
            // We've also moved to 1024 bit keys.
            if (strlen($obj->publicKey->modulus->toBits()) != 1024) {
                $obj->delete();
                return false;
            }
        }

        return $obj;
    }


    function table()
    {
        return array(
            'user_id' => DB_DATAOBJECT_INT,
            'keypair' => DB_DATAOBJECT_STR + DB_DATAOBJECT_NOTNULL,
            'alg'     => DB_DATAOBJECT_STR
        );
    }

    static function schemaDef()
    {
        return array(new ColumnDef('user_id', 'integer',
                                   null, false, 'PRI'),
                     new ColumnDef('keypair', 'text',
                                   false, false),
                     new ColumnDef('alg', 'varchar',
                                   64, false));
    }

    function keys()
    {
        return array_keys($this->keyTypes());
    }

    function keyTypes()
    {
        return array('user_id' => 'K');
    }

    function sequenceKey() {
        return array(false, false, false);
    }

    /**
     * Save this keypair into the database.
     *
     * Overloads default insert behavior to encode the live key objects
     * as a flat string for storage.
     *
     * @return mixed
     */
    function insert()
    {
        $this->keypair = $this->toString();

        return parent::insert();
    }

    /**
     * Generate a new keypair for a local user and store in the database.
     *
     * Warning: this can be very slow on systems without the GMP module.
     * Runtimes of 20-30 seconds are not unheard-of.
     *
     * @param int $user_id id of local user we're creating a key for
     */
    public function generate($user_id)
    {
        $rsa = new Crypt_RSA();

        $keypair = $rsa->createKey();

        $rsa->loadKey($keypair['privatekey']);

        $this->privateKey = new Crypt_RSA();
        $this->privateKey->loadKey($keypair['privatekey']);

        $this->publicKey = new Crypt_RSA();
        $this->publicKey->loadKey($keypair['publickey']);

        $this->user_id = $user_id;
        $this->insert();
    }

    /**
     * Encode the keypair or public key as a string.
     *
     * @param boolean $full_pair set to false to leave out the private key.
     * @return string
     */
    public function toString($full_pair = true)
    {
        $mod = Magicsig::base64_url_encode($this->publicKey->modulus->toBytes());
        $exp = Magicsig::base64_url_encode($this->publicKey->exponent->toBytes());
        $private_exp = '';
        if ($full_pair && $this->privateKey->exponent->toBytes()) {
            $private_exp = '.' . Magicsig::base64_url_encode($this->privateKey->exponent->toBytes());
        }

        return 'RSA.' . $mod . '.' . $exp . $private_exp;
    }

    /**
     * Decode a string representation of an RSA public key or keypair
     * as a Magicsig object which can be used to sign or verify.
     *
     * @param string $text
     * @return Magicsig
     */
    public static function fromString($text)
    {
        $magic_sig = new Magicsig();

        // remove whitespace
        $text = preg_replace('/\s+/', '', $text);

        // parse components
        if (!preg_match('/RSA\.([^\.]+)\.([^\.]+)(.([^\.]+))?/', $text, $matches)) {
            return false;
        }

        $mod = $matches[1];
        $exp = $matches[2];
        if (!empty($matches[4])) {
            $private_exp = $matches[4];
        } else {
            $private_exp = false;
        }

        $magic_sig->loadKey($mod, $exp, 'public');
        if ($private_exp) {
            $magic_sig->loadKey($mod, $private_exp, 'private');
        }

        return $magic_sig;
    }

    /**
     * Fill out $this->privateKey or $this->publicKey with a Crypt_RSA object
     * representing the give key (as mod/exponent pair).
     *
     * @param string $mod base64-encoded
     * @param string $exp base64-encoded exponent
     * @param string $type one of 'public' or 'private'
     */
    public function loadKey($mod, $exp, $type = 'public')
    {
        common_log(LOG_DEBUG, "Adding ".$type." key: (".$mod .', '. $exp .")");

        $rsa = new Crypt_RSA();
        $rsa->signatureMode = CRYPT_RSA_SIGNATURE_PKCS1;
        $rsa->setHash('sha256');
        $rsa->modulus = new Math_BigInteger(Magicsig::base64_url_decode($mod), 256);
        $rsa->k = strlen($rsa->modulus->toBytes());
        $rsa->exponent = new Math_BigInteger(Magicsig::base64_url_decode($exp), 256);

        if ($type == 'private') {
            $this->privateKey = $rsa;
        } else {
            $this->publicKey = $rsa;
        }
    }

    /**
     * Returns the name of the crypto algorithm used for this key.
     *
     * @return string
     */
    public function getName()
    {
        return $this->alg;
    }

    /**
     * Returns the name of a hash function to use for signing with this key.
     *
     * @return string
     * @fixme is this used? doesn't seem to be called by name.
     */
    public function getHash()
    {
        switch ($this->alg) {

        case 'RSA-SHA256':
            return 'sha256';
        }
    }

    /**
     * Generate base64-encoded signature for the given byte string
     * using our private key.
     *
     * @param string $bytes as raw byte string
     * @return string base64-encoded signature
     */
    public function sign($bytes)
    {
        $sig = $this->privateKey->sign($bytes);
        return Magicsig::base64_url_encode($sig);
    }

    /**
     *
     * @param string $signed_bytes as raw byte string
     * @param string $signature as base64
     * @return boolean
     */
    public function verify($signed_bytes, $signature)
    {
        $signature = Magicsig::base64_url_decode($signature);
        return $this->publicKey->verify($signed_bytes, $signature);
    }

    /**
     * URL-encoding-friendly base64 variant encoding.
     *
     * @param string $input
     * @return string
     */
    public static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * URL-encoding-friendly base64 variant decoding.
     *
     * @param string $input
     * @return string
     */
    public static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
