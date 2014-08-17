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
    private static $headers = null;
    private static $destination = null;

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
        self::$destination = $destination;
    }

    public static function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    public static function isUrl($mixed) {
        if (is_null(self::$destination)) return false;

        if (is_string($mixed) && self::$destination['name'] === $mixed) {
            return true;
        } else {
            if (is_callable($mixed, true, $absoluteName) && self::$destination['destination'] === $absoluteName) {
                return true;
            }
        }

        return false;
    }

    public static function getHeader($name) {
        $name = strtolower($name);
        if (is_null(self::$headers)) {
            $tmpHeaders = getallheaders();
            self::$headers = array();
            foreach ($tmpHeaders as $key=>$value) {
                self::$headers[strtolower($key)] = $value;
            }
        }

        if (!isset(self::$headers[$name])) return null;
        return self::$headers[$name];
    }
}
