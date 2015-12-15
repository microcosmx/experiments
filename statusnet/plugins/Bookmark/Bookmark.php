<?php
/**
 * Data class to mark notices as bookmarks
 *
 * PHP version 5
 *
 * @category Data
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2009, StatusNet, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.     See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

/**
 * For storing the fact that a notice is a bookmark
 *
 * @category Bookmark
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * @see      DB_DataObject
 */

class Bookmark extends Memcached_DataObject
{
    public $__table = 'bookmark'; // table name
    public $id;          // char(36) primary_key not_null
    public $profile_id;  // int(4) not_null
    public $url;         // varchar(255) not_null
    public $title;       // varchar(255)
    public $description; // text
    public $uri;         // varchar(255)
    public $created;     // datetime

    /**
     * Get an instance by key
     *
     * This is a utility method to get a single instance with a given key value.
     *
     * @param string $k Key to use to lookup (usually 'user_id' for this class)
     * @param mixed  $v Value to lookup
     *
     * @return User_greeting_count object found, or null for no hits
     *
     */

    function staticGet($k, $v=null)
    {
        return Memcached_DataObject::staticGet('Bookmark', $k, $v);
    }

    /**
     * Get an instance by compound key
     *
     * This is a utility method to get a single instance with a given set of
     * key-value pairs. Usually used for the primary key for a compound key; thus
     * the name.
     *
     * @param array $kv array of key-value mappings
     *
     * @return Bookmark object found, or null for no hits
     *
     */

    function pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Bookmark', $kv);
    }

    /**
     * return table definition for DB_DataObject
     *
     * DB_DataObject needs to know something about the table to manipulate
     * instances. This method provides all the DB_DataObject needs to know.
     *
     * @return array array of column definitions
     */

    function table()
    {
        return array('id' => DB_DATAOBJECT_STR + DB_DATAOBJECT_NOTNULL,
                     'profile_id' => DB_DATAOBJECT_INT + DB_DATAOBJECT_NOTNULL,
                     'url' => DB_DATAOBJECT_STR,
                     'title' => DB_DATAOBJECT_STR,
                     'description' => DB_DATAOBJECT_STR,
                     'uri' => DB_DATAOBJECT_STR,
                     'created' => DB_DATAOBJECT_STR + DB_DATAOBJECT_DATE + 
                     DB_DATAOBJECT_TIME + DB_DATAOBJECT_NOTNULL);
    }

    /**
     * return key definitions for DB_DataObject
     *
     * @return array list of key field names
     */

    function keys()
    {
        return array_keys($this->keyTypes());
    }

    /**
     * return key definitions for Memcached_DataObject
     *
     * @return array associative array of key definitions
     */

    function keyTypes()
    {
        return array('id' => 'K',
                     'uri' => 'U');
    }

    /**
     * Magic formula for non-autoincrementing integer primary keys
     *
     * @return array magic three-false array that stops auto-incrementing.
     */

    function sequenceKey()
    {
        return array(false, false, false);
    }

    /**
     * Get a bookmark based on a notice
     * 
     * @param Notice $notice Notice to check for
     *
     * @return Bookmark found bookmark or null
     */
    
    function getByNotice($notice)
    {
        return self::staticGet('uri', $notice->uri);
    }

    /**
     * Get the bookmark that a user made for an URL
     *
     * @param Profile $profile Profile to check for
     * @param string  $url     URL to check for
     *
     * @return Bookmark bookmark found or null
     */
     
    static function getByURL($profile, $url)
    {
        $nb = new Bookmark();
        
        $nb->profile_id = $profile->id;
        $nb->url        = $url;

        if ($nb->find(true)) {
            return $nb;
        } else {
            return null;
        }
    }

    /**
     * Save a new notice bookmark
     *
     * @param Profile $profile     To save the bookmark for
     * @param string  $title       Title of the bookmark
     * @param string  $url         URL of the bookmark
     * @param mixed   $rawtags     array of tags or string
     * @param string  $description Description of the bookmark
     * @param array   $options     Options for the Notice::saveNew()
     *
     * @return Notice saved notice
     */

    static function saveNew($profile, $title, $url, $rawtags, $description,
                            $options=null)
    {
        $nb = self::getByURL($profile, $url);

        if (!empty($nb)) {
            throw new ClientException(_('Bookmark already exists.'));
        }

        if (empty($options)) {
            $options = array();
        }

        if (array_key_exists('uri', $options)) {
            $other = Bookmark::staticGet('uri', $options['uri']);
            if (!empty($other)) {
                throw new ClientException(_('Bookmark already exists.'));
            }
        }

        if (is_string($rawtags)) {
            if (empty($rawtags)) {
                $rawtags = array();
            } else {
                $rawtags = preg_split('/[\s,]+/', $rawtags);
            }
        }

        $nb = new Bookmark();

        $nb->id          = UUID::gen();
        $nb->profile_id  = $profile->id;
        $nb->url         = $url;
        $nb->title       = $title;
        $nb->description = $description;

        if (array_key_exists('created', $options)) {
            $nb->created = $options['created'];
        } else {
            $nb->created = common_sql_now();
        }

        if (array_key_exists('uri', $options)) {
            $nb->uri = $options['uri'];
        } else {
            // FIXME: hacks to work around router bugs in
            // queue daemons

            $r = Router::get();

            $path = $r->build('showbookmark',
                              array('id' => $nb->id));

            if (empty($path)) {
                $nb->uri = common_path('bookmark/'.$nb->id, false, false);
            } else {
                $nb->uri = common_local_url('showbookmark',
                                            array('id' => $nb->id),
                                            null,
                                            null,
                                            false);
            }
        }

        $nb->insert();

        $tags    = array();
        $replies = array();

        // filter "for:nickname" tags

        foreach ($rawtags as $tag) {
            if (strtolower(mb_substr($tag, 0, 4)) == 'for:') {
                // skip if done by caller
                if (!array_key_exists('replies', $options)) {
                    $nickname = mb_substr($tag, 4);
                    $other    = common_relative_profile($profile,
                                                        $nickname);
                    if (!empty($other)) {
                        $replies[] = $other->getUri();
                    }
                }
            } else {
                $tags[] = common_canonical_tag($tag);
            }
        }

        $hashtags = array();
        $taglinks = array();

        foreach ($tags as $tag) {
            $hashtags[] = '#'.$tag;
            $attrs      = array('href' => Notice_tag::url($tag),
                                'rel'  => $tag,
                                'class' => 'tag');
            $taglinks[] = XMLStringer::estring('a', $attrs, $tag);
        }

        // Use user's preferences for short URLs, if possible

        try {
            $user = User::staticGet('id', $profile->id);

            $shortUrl = File_redirection::makeShort($url, 
                                                    empty($user) ? null : $user);
        } catch (Exception $e) {
            // Don't let this stop us.
            $shortUrl = $url;
        }

        $content = sprintf(_('"%s" %s %s %s'),
                           $title,
                           $shortUrl,
                           $description,
                           implode(' ', $hashtags));

        $rendered = sprintf(_('<span class="xfolkentry">'.
                              '<a class="taggedlink" href="%s">%s</a> '.
                              '<span class="description">%s</span> '.
                              '<span class="meta">%s</span>'.
                              '</span>'),
                            htmlspecialchars($url),
                            htmlspecialchars($title),
                            htmlspecialchars($description),
                            implode(' ', $taglinks));

        $options = array_merge(array('urls' => array($url),
                                     'rendered' => $rendered,
                                     'tags' => $tags,
                                     'replies' => $replies),
                               $options);

        if (!array_key_exists('uri', $options)) {
            $options['uri'] = $nb->uri;
        }

        try {
            $saved = Notice::saveNew($profile->id,
                                     $content,
                                     array_key_exists('source', $options) ?
                                     $options['source'] : 'web',
                                     $options);
        } catch (Exception $e) {
            $nb->delete();
            throw $e;
        }

        if (empty($saved)) {
            $nb->delete();
        }

        return $saved;
    }
}
