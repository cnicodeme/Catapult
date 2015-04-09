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

use \Catapult\Controller\Route;

class Router {
    private static $methods = array('GET' => true, 'POST' => true, 'PUT' => true, 'DELETE' => true);

    private static $routes = array();
    private static $errors = array();

    private static $base = '';

    public static function get($url, $destination) {
        return Router::add('GET', $url, $destination);
    }

    public static function post($url, $destination) {
        return Router::add('POST', $url, $destination);
    }

    public static function put($url, $destination) {
        return Router::add('PUT', $url, $destination);
    }

    public static function delete($url, $destination) {
        return Router::add('DELETE', $url, $destination);
    }

    public static function rest($url, $baseDestination) {
        if (!is_a($baseDestination, '\Catapult\Controller\Controllers\RestController', true)) {
            throw new \Catapult\Exceptions\CatapultException($destination.' does not extends RestController.');
        }

        Route::setDefaultMatch(Route::NUMERIC_PATTERN);
        self::add('GET', $url, $baseDestination.'::lists');
        self::add('POST', $url, $baseDestination.'::create');
        self::add('GET', $url.':id', $baseDestination.'::details');
        self::add('PUT', $url.':id', $baseDestination.'::update');
        self::add('DELETE', $url.':id', $baseDestination.'::delete');
        Route::setDefaultMatch(Route::SLUG_PATTERN);
    }

    public static function with($base, $caller) {
        self::$base .= $base;
        $caller();
        self::$base = '';
    }

    public static function add($methods, $url, $destination) {
        if (is_string($destination)) {
            if (!is_a(substr($destination, 0, strrpos($destination, '::')), '\Catapult\Controller\Controller', true)) {
                throw new \Catapult\Exceptions\CatapultException($destination.' does not extends Controller.');
            }
        }

        $url = self::$base.$url;

        $route = new Route($url, $destination, $methods);
        self::$routes[] = $route;
    }

    public static function getRoute($url, $method = 'GET') {
        foreach (self::$routes as $route) {
            if (($params = $route->isMatch($url, $method)) !== false) {
                return array($route, $params);
            }
        }

        return null;
    }

    public static function addError($code, $destination) {
        if (!is_int($code)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "code" expected to be an integer.');
        }

        self::$errors[$code] = $destination;
    }

    public static function getError($code) {
        if (!isset(self::$errors[$code])) return null;

        return self::$errors[$code];
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
