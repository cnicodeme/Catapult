<?php
/**
 * @name Auth
 * Authentication plugin to manage user session
 *
 * @package Catapult.Helpers
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Helpers;

class Auth extends \Catapult\Core\Middleware {
    public function __construct() {
        \Catapult\Core\EventDispatcher::on('process_request', array($this, 'onProcessRequest'));
    }

    public function onProcessRequest() {
        if(isset($_SESSION['email'])) {
            // Search agains't
        } else {
            self::$lang = $this->getLanguageFromBrowser();
        }
    }
    // getUsername
        // What to find and where ($_SESSION['email'] ? ['user'] ? )
        // IF returns null, not auth !
}
