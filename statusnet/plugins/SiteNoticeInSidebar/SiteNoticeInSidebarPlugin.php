<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * Plugin to put the site notice in the sidebar
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
 * @category  Plugin
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
 * Put the site notice in the sidebar
 *
 * @category  General
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class SiteNoticeInSidebarPlugin extends Plugin
{
    /**
     * Function comment
     *
     * @param
     *
     * @return
     */

    function onStartShowSiteNotice($action)
    {
        return false;
    }

    function onStartShowSections($action)
    {
        $text = common_config('site', 'notice');
        if (!empty($text)) {
            $sns = new SiteNoticeSection($action, $text);
            $sns->show();
        }
        return true;
    }

    function onEndShowStyles($action)
    {
        $action->element('style', null, '#site_notice { width: 100% }');
        return true;
    }

    function onAutoload($cls)
    {
        $dir = dirname(__FILE__);

        switch ($cls)
        {
        case 'SiteNoticeSection':
            include_once $dir . '/'.strtolower($cls).'.php';
            return false;
        default:
            return true;
        }
    }
}
