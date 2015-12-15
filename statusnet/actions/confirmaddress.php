<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Confirm an address
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
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
 * @category  Confirm
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2008-2009 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

/**
 * Confirm an address
 *
 * When users change their SMS, email, Jabber, or other addresses, we send out
 * a confirmation code to make sure the owner of that address approves. This class
 * accepts those codes.
 *
 * @category Confirm
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 */
class ConfirmaddressAction extends Action
{
    /** type of confirmation. */

    var $type = null;

    /**
     * Accept a confirmation code
     *
     * Checks the code and confirms the address in the
     * user record
     *
     * @param args $args $_REQUEST array
     *
     * @return void
     */
    function handle($args)
    {
        parent::handle($args);
        if (!common_logged_in()) {
            common_set_returnto($this->selfUrl());
            common_redirect(common_local_url('login'));
            return;
        }
        $code = $this->trimmed('code');
        if (!$code) {
            // TRANS: Client error displayed when not providing a confirmation code in the contact address confirmation action.
            $this->clientError(_('No confirmation code.'));
            return;
        }
        $confirm = Confirm_address::staticGet('code', $code);
        if (!$confirm) {
            // TRANS: Client error displayed when providing a non-existing confirmation code in the contact address confirmation action.
            $this->clientError(_('Confirmation code not found.'));
            return;
        }
        $cur = common_current_user();
        if ($cur->id != $confirm->user_id) {
            // TRANS: Client error displayed when not providing a confirmation code for another user in the contact address confirmation action.
            $this->clientError(_('That confirmation code is not for you!'));
            return;
        }
        $type = $confirm->address_type;
        if (!in_array($type, array('email', 'jabber', 'sms'))) {
            // TRANS: Server error for a unknow address type %s, which can be 'email', 'jabber', or 'sms'.
            $this->serverError(sprintf(_('Unrecognized address type %s.'), $type));
            return;
        }
        if ($cur->$type == $confirm->address) {
            // TRANS: Client error for an already confirmed email/jabber/sms address.
            $this->clientError(_('That address has already been confirmed.'));
            return;
        }

        $cur->query('BEGIN');

        $orig_user = clone($cur);

        $cur->$type = $confirm->address;

        if ($type == 'sms') {
            $cur->carrier  = ($confirm->address_extra)+0;
            $carrier       = Sms_carrier::staticGet($cur->carrier);
            $cur->smsemail = $carrier->toEmailAddress($cur->sms);
        }

        $result = $cur->updateKeys($orig_user);

        if (!$result) {
            common_log_db_error($cur, 'UPDATE', __FILE__);
            // TRANS: Server error displayed when a user update to the database fails in the contact address confirmation action.
            $this->serverError(_('Could not update user.'));
            return;
        }

        if ($type == 'email') {
            $cur->emailChanged();
        }

        $result = $confirm->delete();

        if (!$result) {
            common_log_db_error($confirm, 'DELETE', __FILE__);
            // TRANS: Server error displayed when an address confirmation code deletion from the
            // TRANS: database fails in the contact address confirmation action.
            $this->serverError(_('Could not delete address confirmation.'));
            return;
        }

        $cur->query('COMMIT');

        $this->type = $type;
        $this->showPage();
    }

    /**
     * Title of the page
     *
     * @return string title
     */
    function title()
    {
        // TRANS: Title for the contact address confirmation action.
        return _('Confirm address');
    }

    /**
     * Show a confirmation message.
     *
     * @return void
     */
    function showContent()
    {
        $cur  = common_current_user();
        $type = $this->type;

        $this->element('p', null,
                       // TRANS: Success message for the contact address confirmation action.
                       // TRANS: %s can be 'email', 'jabber', or 'sms'.
                       sprintf(_('The address "%s" has been '.
                                 'confirmed for your account.'),
                               $cur->$type));
    }
}
