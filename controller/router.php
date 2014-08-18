<?php
/**
 * @name Router
 * Manage routes and reverse routing
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

class Router {
    private static $methods = array('GET' => true, 'POST' => true, 'PUT' => true, 'DELETE' => true);

    private static $errorNames = array (
        400 => 'badRequest',
        403 => 'forbidden',
        404 => 'notFound'
    );

    private static $routes = array();

    public static function add(\Catapult\Controller\Route $route) {
        if (in_array($route->getUrl(), self::$errorNames)) {
            return self::addError(array_search($route->getUrl(), self::$errorNames), $route);
        }

        if (isset(self::$routes[$route->getName()])) {
            throw new \Catapult\Exceptions\AlreadyExistsException('Route name "'.$route->getName().'" already exists.');
        }

        self::$routes[$route->getName()] = $route;
    }

    public static function addError($code, $route) {
        if (isset(self::$routes[$code])) {
            throw new \Catapult\Exceptions\AlreadyExistsException('Error route "'.$code.'" already exists.');
        }

        self::$routes[$code] = $route;
    }

    public static function getRoute($url, $method = 'GET') {
        if ((is_int($url) && isset(self::$errorNames[$url])) || (in_array($url, self::$errorNames))) {
            if (isset(self::$errorNames[$url])) {
                return array(self::$routes[$url], null);
            } else {
                $code = array_search($url, self::$errorNames);
                if ($code !== false && isset(self::$routes[$code])) {
                    return array(self::$routes[$code], null);
                }
            }

            return null;
        }

        foreach (self::$routes as $route) {
            if (($params = $route->isMatch($url, $method)) !== false) {
                return array($route, $params);
            }
        }

        return null;
    }

    public static function isMethodAllowed($method) {
        return isset(self::$methods[$method]);
    }

    public static function reverse($name, $params = array(), $absolute=false, $forceHttps = false) {
        if (!isset(self::$routes[$name])) {
            throw new \Catapult\Exceptions\NotFoundException('Route "'.$name.'" was not found.');
        }

        return self::$routes[$name]->reverse($params, $absolute, $forceHttps);
    }

    /*
     * Returns true if the router has the route with $name or $name === $url
     */
    public static function has($name) {
        if (!is_string($name)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "name" expected to be string.');
        }

        if (isset(self::$routes[$name])) {
            return true;
        }

        foreach(self::$routes as $route) {
            if ($route->getUrl() === $name) return true;
        }

        return false;
    }
}

/*

filter
    before, after
    classe/method
    specific url

has (url/statuscode)

reverse(url_name, array parameters) : Return the route based on url_name

Special status codes :
    badRequest 400
    forbidden 403
    notFound 404

    generic handler: addError($status code, class/method/anonymous)
*/
