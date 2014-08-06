<?php
/**
 * @name Request
 * Dispatch the request on the correct controller:method
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

class Request {
    private static $path = null;
    private static $method = null;

    public static function setPath($path) {
        self::$path = $path;
    }

    public static function getPath() {
        return self::$path;
    }

    public static function setMethod($method) {
        self::$method = strtoupper($method);
    }

    public static function getMethod() {
        return self::$method;
    }

    public static function isMethod($method) {
        $method = strtoupper($method);
        return self::$method === $method;
    }

    public static function setDestination($destination) {

    }
}
