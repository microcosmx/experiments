<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2010, StatusNet, Inc.
 *
 * Import del.icio.us bookmarks backups
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
 * @category  Bookmark
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

/**
 * UI for importing del.icio.us bookmark backups
 *
 * @category  Bookmark
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class ImportdeliciousAction extends Action
{
    protected $success = false;
    private $inprogress = false;

    /**
     * Return the title of the page
     *
     * @return string page title
     */

    function title()
    {
        return _("Import del.icio.us bookmarks");
    }

    /**
     * For initializing members of the class.
     *
     * @param array $argarray misc. arguments
     *
     * @return boolean true
     */

    function prepare($argarray)
    {
        parent::prepare($argarray);

        $cur = common_current_user();

        if (empty($cur)) {
            throw new ClientException(_('Only logged-in users can '.
                                        'import del.icio.us backups.'),
                                      403);
        }

        if (!$cur->hasRight(BookmarkPlugin::IMPORTDELICIOUS)) {
            throw new ClientException(_('You may not restore your account.'), 403);
        }

        return true;
    }

    /**
     * Handler method
     *
     * @param array $argarray is ignored since it's now passed in in prepare()
     *
     * @return void
     */

    function handle($argarray=null)
    {
        parent::handle($argarray);

        if ($this->isPost()) {
            $this->importDelicious();
        } else {
            $this->showPage();
        }
        return;
    }

    /**
     * Queue a file for importation
     * 
     * Uses the DeliciousBackupImporter class; may take a long time!
     *
     * @return void
     */

    function importDelicious()
    {
        $this->checkSessionToken();

        if (!isset($_FILES[ImportDeliciousForm::FILEINPUT]['error'])) {
            throw new ClientException(_('No uploaded file.'));
        }

        switch ($_FILES[ImportDeliciousForm::FILEINPUT]['error']) {
        case UPLOAD_ERR_OK: // success, jump out
            break;
        case UPLOAD_ERR_INI_SIZE:
            // TRANS: Client exception thrown when an uploaded file is too large.
            throw new ClientException(_('The uploaded file exceeds the ' .
                'upload_max_filesize directive in php.ini.'));
            return;
        case UPLOAD_ERR_FORM_SIZE:
            throw new ClientException(
                // TRANS: Client exception.
                _('The uploaded file exceeds the MAX_FILE_SIZE directive' .
                ' that was specified in the HTML form.'));
            return;
        case UPLOAD_ERR_PARTIAL:
            @unlink($_FILES[ImportDeliciousForm::FILEINPUT]['tmp_name']);
            // TRANS: Client exception.
            throw new ClientException(_('The uploaded file was only' .
                ' partially uploaded.'));
            return;
        case UPLOAD_ERR_NO_FILE:
            // No file; probably just a non-AJAX submission.
            throw new ClientException(_('No uploaded file.'));
            return;
        case UPLOAD_ERR_NO_TMP_DIR:
            // TRANS: Client exception thrown when a temporary folder is not present
            throw new ClientException(_('Missing a temporary folder.'));
            return;
        case UPLOAD_ERR_CANT_WRITE:
            // TRANS: Client exception thrown when writing to disk is not possible
            throw new ClientException(_('Failed to write file to disk.'));
            return;
        case UPLOAD_ERR_EXTENSION:
            // TRANS: Client exception thrown when a file upload has been stopped
            throw new ClientException(_('File upload stopped by extension.'));
            return;
        default:
            common_log(LOG_ERR, __METHOD__ . ": Unknown upload error " .
                $_FILES[ImportDeliciousForm::FILEINPUT]['error']);
            // TRANS: Client exception thrown when a file upload operation has failed
            throw new ClientException(_('System error uploading file.'));
            return;
        }

        $filename = $_FILES[ImportDeliciousForm::FILEINPUT]['tmp_name'];

        try {
            if (!file_exists($filename)) {
                throw new ServerException("No such file '$filename'.");
            }
        
            if (!is_file($filename)) {
                throw new ServerException("Not a regular file: '$filename'.");
            }
        
            if (!is_readable($filename)) {
                throw new ServerException("File '$filename' not readable.");
            }
        
            common_debug(sprintf(_("Getting backup from file '%s'."), $filename));

            $html = file_get_contents($filename);

            // Enqueue for processing.

            $qm = QueueManager::get();
            $qm->enqueue(array(common_current_user(), $html), 'dlcsback');

            if ($qm instanceof UnQueueManager) {
                // No active queuing means we've actually just completed the job!
                $this->success = true;
            } else {
                // We've fed data into background queues, and it's probably still running.
                $this->inprogress = true;
            }

            $this->showPage();

        } catch (Exception $e) {
            // Delete the file and re-throw
            @unlink($_FILES[ImportDeliciousForm::FILEINPUT]['tmp_name']);
            throw $e;
        }
    }

    /**
     * Show the content of the page
     *
     * @return void
     */

    function showContent()
    {
        if ($this->success) {
            $this->element('p', null,
                           _('Bookmarks have been imported. Your bookmarks should now appear in search and your profile page.'));
        } else if ($this->inprogress) {
            $this->element('p', null,
                           _('Bookmarks are being imported. Please wait a few minutes for results.'));
        } else {
            $form = new ImportDeliciousForm($this);
            $form->show();
        }
    }

    /**
     * Return true if read only.
     *
     * MAY override
     *
     * @param array $args other arguments
     *
     * @return boolean is read only action?
     */

    function isReadOnly($args)
    {
        return !$this->isPost();
    }
}

/**
 * A form for backing up the account.
 *
 * @category  Account
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class ImportDeliciousForm extends Form
{
    const FILEINPUT = 'deliciousbackupfile';

    /**
     * Constructor
     * 
     * Set the encoding type, since this is a file upload.
     *
     * @param HTMLOutputter $out output channel
     *
     * @return ImportDeliciousForm this
     */

    function __construct($out=null)
    {
        parent::__construct($out);
        $this->enctype = 'multipart/form-data';
    }

    /**
     * Class of the form.
     *
     * @return string the form's class
     */

    function formClass()
    {
        return 'form_import_delicious';
    }

    /**
     * URL the form posts to
     *
     * @return string the form's action URL
     */

    function action()
    {
        return common_local_url('importdelicious');
    }

    /**
     * Output form data
     * 
     * Really, just instructions for doing a backup.
     *
     * @return void
     */

    function formData()
    {
        $this->out->elementStart('p', 'instructions');

        $this->out->raw(_('You can upload a backed-up '.
                          'delicious.com bookmarks file.'));
        
        $this->out->elementEnd('p');

        $this->out->elementStart('ul', 'form_data');

        $this->out->elementStart('li', array ('id' => 'settings_attach'));
        $this->out->element('input', array('name' => self::FILEINPUT,
                                           'type' => 'file',
                                           'id' => self::FILEINPUT));
        $this->out->elementEnd('li');

        $this->out->elementEnd('ul');
    }

    /**
     * Buttons for the form
     * 
     * In this case, a single submit button
     *
     * @return void
     */

    function formActions()
    {
        $this->out->submit('submit',
                           _m('BUTTON', 'Upload'),
                           'submit',
                           null,
                           _('Upload the file'));
    }
}
