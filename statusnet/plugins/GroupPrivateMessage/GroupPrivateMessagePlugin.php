<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * Private groups for StatusNet 0.9.x
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
 * @category  Privacy
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

/**
 * Private groups
 *
 * This plugin allows users to send private messages to a group.
 *
 * @category  Privacy
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class GroupPrivateMessagePlugin extends Plugin
{
    /**
     * Database schema setup
     *
     * @see Schema
     * @see ColumnDef
     *
     * @return boolean hook value
     */

    function onCheckSchema()
    {
        $schema = Schema::get();

        // For storing user-submitted flags on profiles

        $schema->ensureTable('group_privacy_settings',
                             array(new ColumnDef('group_id',
                                                 'integer',
                                                 null,
                                                 false,
                                                 'PRI'),
                                   new ColumnDef('allow_privacy',
                                                 'integer'),
                                   new ColumnDef('allow_sender',
                                                 'integer'),
                                   new ColumnDef('created',
                                                 'datetime'),
                                   new ColumnDef('modified',
                                                 'timestamp')));
                             
        $schema->ensureTable('group_message',
                             array(new ColumnDef('id',
                                                 'char',
                                                 36,
                                                 false,
                                                 'PRI'),
                                   new ColumnDef('uri',
                                                 'varchar',
                                                 255,
                                                 false,
                                                 'UNI'),
                                   new ColumnDef('from_profile',
                                                 'integer',
                                                 null,
                                                 false,
                                                 'MUL'),
                                   new ColumnDef('to_group',
                                                 'integer',
                                                 null,
                                                 false,
                                                 'MUL'),
                                   new ColumnDef('content',
                                                 'text'),
                                   new ColumnDef('rendered',
                                                 'text'),
                                   new ColumnDef('url',
                                                 'varchar',
                                                 255,
                                                 false,
                                                 'UNI'),
                                   new ColumnDef('created',
                                                 'datetime')));

        $schema->ensureTable('group_message_profile',
                             array(new ColumnDef('to_profile',
                                                 'integer',
                                                 null,
                                                 false,
                                                 'PRI'),
                                   new ColumnDef('group_message_id',
                                                 'char',
                                                 36,
                                                 false,
                                                 'PRI'),
                                   new ColumnDef('created',
                                                 'datetime')));

        return true;
    }

    /**
     * Load related modules when needed
     *
     * @param string $cls Name of the class to be loaded
     *
     * @return boolean hook value
     */

    function onAutoload($cls)
    {
        $dir = dirname(__FILE__);

        switch ($cls)
        {
        case 'GroupinboxAction':
        case 'ShowgroupmessageAction':
        case 'NewgroupmessageAction':
            include_once $dir . '/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
            return false;
        case 'Group_privacy_settings':
        case 'Group_message':
        case 'Group_message_profile':
            include_once $dir . '/'.$cls.'.php';
            return false;
        case 'GroupMessageCommand':
        case 'GroupMessageList':
        case 'GroupMessageListItem':
        case 'GroupMessageForm':
            include_once $dir . '/'.strtolower($cls).'.php';
            return false;
        default:
            return true;
        }
    }

    /**
     * Map URLs to actions
     *
     * @param Net_URL_Mapper $m path-to-action mapper
     *
     * @return boolean hook value
     */

    function onRouterInitialized($m)
    {
        $m->connect('group/:nickname/inbox',
                    array('action' => 'groupinbox'),
                    array('nickname' => Nickname::DISPLAY_FMT));

        $m->connect('group/message/:id',
                    array('action' => 'showgroupmessage'),
                    array('id' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'));

        $m->connect('group/:nickname/message/new',
                    array('action' => 'newgroupmessage'),
                    array('nickname' => Nickname::DISPLAY_FMT));

        return true;
    }

    /**
     * Add group inbox to the menu
     *
     * @param Action $action The current action handler. Use this to
     *                       do any output.
     *
     * @return boolean hook value; true means continue processing, false means stop.
     *
     * @see Action
     */

    function onEndGroupGroupNav($groupnav)
    {
        $action = $groupnav->action;
        $group  = $groupnav->group;

        $action->menuItem(common_local_url('groupinbox',
                                           array('nickname' => $group->nickname)),
                          _m('Inbox'),
                          _m('Private messages for this group'),
                          $action->trimmed('action') == 'groupinbox',
                          'nav_group_inbox');
        return true;
    }

    /**
     * Create default group privacy settings at group create time
     *
     * @param User_group $group Group that was just created
     *
     * @result boolean hook value
     */

    function onEndGroupSave($group)
    {
        $gps = new Group_privacy_settings();

        $gps->group_id      = $group->id;
        $gps->allow_privacy = Group_privacy_settings::SOMETIMES;
        $gps->allow_sender  = Group_privacy_settings::MEMBER;
        $gps->created       = common_sql_now();
        $gps->modified      = $gps->created;

        // This will throw an exception on error

        $gps->insert();

        return true;
    }

    /**
     * Show group privacy controls on group edit form
     *
     * @param GroupEditForm $form form being shown
     */

    function onEndGroupEditFormData($form)
    {
        $gps = null;

        if (!empty($form->group)) {
            $gps = Group_privacy_settings::staticGet('group_id', $form->group->id);
        }

        $form->out->elementStart('li');
        $form->out->dropdown('allow_privacy',
                             _('Private messages'),
                             array(Group_privacy_settings::SOMETIMES => _('Sometimes'),
                                   Group_privacy_settings::ALWAYS => _('Always'),
                                   Group_privacy_settings::NEVER => _('Never')),
                             _('Whether to allow private messages to this group'),
                             false,
                             (empty($gps)) ? Group_privacy_settings::SOMETIMES : $gps->allow_privacy);
        $form->out->elementEnd('li');
        $form->out->elementStart('li');
        $form->out->dropdown('allow_sender',
                             _('Private sender'),
                             array(Group_privacy_settings::EVERYONE => _('Everyone'),
                                   Group_privacy_settings::MEMBER => _('Member'),
                                   Group_privacy_settings::ADMIN => _('Admin')),
                             _('Who can send private messages to the group'),
                             false,
                             (empty($gps)) ? Group_privacy_settings::MEMBER : $gps->allow_sender);
        $form->out->elementEnd('li');
        return true;
    }

    function onEndGroupSaveForm($action)
    {
        $gps = null;

        if (!empty($action->group)) {
            $gps = Group_privacy_settings::staticGet('group_id', $action->group->id);
        }

        $orig = null;

        if (empty($gps)) {
            $gps = new Group_privacy_settings();
            $gps->group_id = $action->group->id;
        } else {
            $orig = clone($gps);
        }
        
        $gps->allow_privacy = $action->trimmed('allow_privacy');
        $gps->allow_sender  = $action->trimmed('allow_sender');

        if (empty($orig)) {
            $gps->created = common_sql_now();
            $gps->insert();
        } else {
            $gps->update($orig);
        }
        
        return true;
    }

    /**
     * Overload 'd' command to send private messages to groups.
     * 
     * 'd !group word word word' will send the private message
     * 'word word word' to the group 'group'.
     * 
     * @param string  $cmd     Command being run
     * @param string  $arg     Rest of the message (including address)
     * @param User    $user    User sending the message
     * @param Command &$result The resulting command object to be run.
     * 
     * @return boolean hook value
     */
    function onStartIntepretCommand($cmd, $arg, $user, &$result)
    {
        if ($cmd == 'd' || $cmd == 'dm') {

            $this->debug('Got a d command');

            // Break off the first word as the address

            $pieces = explode(' ', $arg, 2);

            if (count($pieces) == 1) {
                $pieces[] = null;
            }

            list($addr, $msg) = $pieces;

            if (!empty($addr) && $addr[0] == '!') {
                $result = new GroupMessageCommand($user, substr($addr, 1), $msg);
                Event::handle('EndInterpretCommand', array($cmd, $arg, $user, $result));
                return false;
            }
        }

        return true;
    }

    /**
     * To add a "Message" button to the group profile page
     *
     * @param Action     $action The showgroup action being shown
     * @param User_group $group  The current group
     * 
     * @return boolean hook value
     */
    function onEndGroupActionsList($action, $group)
    {
        $cur = common_current_user();

        if (empty($cur)) {
            return true;
        }

        try {
            Group_privacy_settings::ensurePost($cur, $group);
        } catch (Exception $e) {
            return true;
        }

        $action->elementStart('li', 'entity_send-a-message');
        $action->element('a', array('href' => common_local_url('newgroupmessage', array('nickname' => $group->nickname)),
                                    'title' => _('Send a direct message to this group')),
                         _('Message'));
        // $form = new GroupMessageForm($action, $group);
        // $form->hidden = true;
        // $form->show();
        $action->elementEnd('li');
        return true;
    }

    /**
     * When saving a notice, check its groups. If any of them has
     * privacy == always, force a group private message to all mentioned groups.
     * If any of the groups disallows private messages, skip it.
     *
     * @param 
     *
     */

    function onStartNoticeSave(&$notice) {

        // Look for group tags
        // FIXME: won't work for remote groups
        // @fixme if Notice::saveNew is refactored so we can just pull its list
        // of groups between processing and saving, make use of it

        $count = preg_match_all('/(?:^|\s)!(' . Nickname::DISPLAY_FMT . ')/',
                                strtolower($notice->content),
                                $match);

        $groups = array();
        $ignored = array();

        $forcePrivate = false;

        if ($count > 0) {

            /* Add them to the database */

            foreach (array_unique($match[1]) as $nickname) {

                $group = User_group::getForNickname($nickname, $profile);

                if (empty($group)) {
                    continue;
                }

                $gps = Group_privacy_settings::forGroup($group);

                switch ($gps->allow_privacy) {
                case Group_privacy_settings::ALWAYS:
                    $forcePrivate = true;
                    // fall through
                case Group_privacy_settings::SOMETIMES:
                    $groups[] = $group;
                    break;
                case Group_privacy_settings::NEVER:
                    $ignored[] = $group;
                    break;
                }
            }

            if ($forcePrivate) {

                foreach ($ignored as $group) {
                    common_log(LOG_NOTICE,
                               "Notice forced to group direct message ".
                               "but group ".$group->nickname." does not allow them.");
                }

                $user = User::staticGet('id', $notice->profile_id);

                if (empty($user)) {
                    common_log(LOG_WARNING,
                               "Notice forced to group direct message ".
                               "but profile ".$notice->profile_id." is not a local user.");
                } else {
                    foreach ($groups as $group) {
                        Group_message::send($user, $group, $notice->content);
                    }
                }

                // Don't save the notice!
                // FIXME: this is probably cheating.
                throw new ClientException(sprintf(_('Forced notice to private group message.')),
                                          200);
            }
        }
        
        return true;
    }

    /**
     * Show an indicator that the group is (essentially) private on the group page
     *
     * @param Action     $action The action being shown
     * @param User_group $group  The group being shown
     *
     * @return boolean hook value
     */

    function onEndGroupProfileElements($action, $group)
    {
        $gps = Group_privacy_settings::forGroup($group);
        
        if ($gps->allow_privacy == Group_privacy_settings::ALWAYS) {
            $action->element('p', 'privategroupindicator', _('Private'));
        }

        return true;
    }

    function onStartShowExportData($action)
    {
        if ($action instanceof ShowgroupAction) {
            $gps = Group_privacy_settings::forGroup($action->group);
        
            if ($gps->allow_privacy == Group_privacy_settings::ALWAYS) {
                return false;
            }
        }
        return true;
    }

    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'GroupPrivateMessage',
                            'version' => STATUSNET_VERSION,
                            'author' => 'Evan Prodromou',
                            'homepage' => 'http://status.net/wiki/Plugin:GroupPrivateMessage',
                            'rawdescription' =>
                            _m('Allow posting DMs to a group.'));
        return true;
    }
}
