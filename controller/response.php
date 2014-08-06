<?php
/**
 * @name Response
 * Returns a well formed response to the client
 *
 * @package Catapult.Controller
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Controller;

use \Catapult\Core\EventDispatcher;

class Response {
    public static function addHeader() {

    }

    public static function setBody() {

    }

    public static function render() {
        EventDispatcher::trigger('process_template_response');

        // Render here

        EventDispatcher::trigger('process_response');
    }
}
