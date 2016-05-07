<?php
/**
 * DokuWiki Plugin directorylist (Action Component)
 *
 * @author  alexwenzel <alexander.wenzel.berlin@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class Action_Plugin_Directorylist_Directorylist extends DokuWiki_Action_Plugin
{
    /**
     * Register Event to catch download action
     * @param  Doku_Event_Handler $controller
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook(ACTION_ACT_PREPROCESS, BEFORE, $this, 'handle_event');
    }

    /**
     * Checks the action for the keyword to download files
     * @see http://www.php.net/manual/en/function.finfo-open.php
     * @param  Doku_Event $event
     * @return void
     */
    public function handle_event(Doku_Event &$event)
    {
        // check if we need to download a file
        if ($event->data === 'download' && !empty($_GET['file'])) {

            // get filename
            $file = ($_GET['file']);

            //  get mimetype of this file
            $finfo = new finfo(FILEINFO_MIME);
            $mimetype = $finfo->file($file);

            //  force download
            header("Content-disposition: attachment; filename=".basename($file));
            header("Content-type: ".$mimetype);
            readfile($file);
            die();
        }
    }
}
