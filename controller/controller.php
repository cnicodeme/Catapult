<?php
/**
 * @name Controller
 * Top level controller
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

abstract class Controller {
    private static $response = null;
    private static $request = null;

    public static function getRequest() {
        if (is_null(self::$request)) {
            self::$request = new \Catapult\Controller\Request();
        }

        return self::$request;
    }

    public static function getResponse() {
        if (is_null(self::$response)) {
            self::$response = new \Catapult\Controller\Response();
        }

        return self::$response;
    }

    public static function session() {

    }
}
