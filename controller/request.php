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
    private static $route = null;

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

    public static function setRoute(\Catapult\Controller\Route $route) {
        self::$route = $route;
    }

    public static function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    /**
     * Test if the current url is the given name
     * For example, test if "/" (current url) is "home"
     */
    public static function isUrl($name) {
        if (is_null(self::$route)) return false;

        if (self::$route->getName() === $name) {
            return true;
        } else if (self::$route->getUrl() === $name) {
            return true;
        }

        return false;
    }

    public static function getRouteName() {
        if (is_null(self::$route)) return null;

        return self::$route->getName();
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
