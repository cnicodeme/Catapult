<?php
/**
 * @name Route
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

class Route {
    private static $methods = array('GET' => true, 'POST' => true, 'PUT' => true, 'DELETE' => true);
    private static $errorNames = array (
        400 => 'badRequest',
        403 => 'forbidden',
        404 => 'notFound'
    );

    private static $routes = array();

    public static function add($url, $destination, $method = 'GET', $name = null, $forceHttps = false) {
        if (in_array($url, self::$errorNames)) {
            return self::addError(array_search($url, self::$errorNames), $destination, $url);
        }

        self::register($url, $destination, $method, (is_null($name) ? $url : $name), $forceHttps);
    }

    private static function register($url, $destination, $method, $name, $forceHttps) {
        if (!is_array($method)) {
            $method = array($method);
        }

        $methods = array();
        foreach ($method as $m) {
            if (!self::isMethodAllowed($m)) {
                throw new \Catapult\Exceptions\NotSupportedException('Method '.$m.' is not supported.');
            }
            $methods[$m] = true;
        }

        /*
         * Accepted converter :
         *    * int
         *    * float
         *    * str - default
         *    * path

        if (strpos($url, '<') !== false) {
            // Fail if not correct, ex: /<int:id> => works with /15s ! should not!
            //
            $url = '@^'.$url.'$@';
            $url = preg_replace('$<([a-z]+)>$', "(?P<$1>[a-z0-9\-\_]+)", $url);
            $url = preg_replace('$<str:([a-z]+)>$', "(?P<$1>[a-z0-9\-\_]+)", $url);
            $url = preg_replace('$<int:([a-z]+)>$', "(?P<$1>\d+)", $url);
            $url = preg_replace('$<float:([a-z]+)>$', "(?P<$1>-?(?:\d+|\d*\.\d+))", $url);
            $url = preg_replace('$<path:([a-z]+)>$', "(?P<$1>(\/([a-z0-9+\$_-]\.?)+)*\)", $url);
        }*/

        self::$routes[$name] = array(
            'url'         => $url,
            'destination' => $destination,
            'method'      => $methods,
            'name'        => $name,
            'https'       => $forceHttps
        );
    }

    public static function addError($code, $destination, $name = null) {
        self::$routes[$code] = array(
            'url'         => null,
            'destination' => $destination,
            'method'      => null,
            'name'        => (is_null($name) ? $code : $name),
            'https'       => false
        );
    }

    public static function getDestination($url, $method = 'GET') {
        \Catapult\Core\EventDispatcher::trigger('process_request'); // TODO / PAS BON

        if (in_array($url, self::$errorNames)) {
            $url = array_search($url, self::$errorNames);
        }

        $route = null;
        if (is_int($url) && isset(self::$routes[$url])) {
            $route = self::$routes[$url];
        } else if (isset(self::$routes[$url]) && isset(self::$routes[$url]['method'][$method])) {
            $route = self::$routes[$url];
        } else {
            foreach (self::$routes as $route) {
                if (is_null($route['method'])) continue;
                if (!isset($route['method'][$method])) continue;
                if ('^' !== substr($route['url'], 0, 1)) continue;

                $results = array();
                if (preg_match_all('{'.$route['url'].'}uS', $url, $results, PREG_PATTERN_ORDER) > 0) {
                    $params = array();
                    foreach ($results as $key=>$result) {
                        if (!(is_int($key) && $key !== 0)) continue;
                        if (is_numeric($result[0])) {
                            if (strpos($result[0], '.') !== false) {
                                $params[$key] = floatval($result[0]);
                            } else {
                                $params[$key] = intval($result[0]);
                            }
                        } else {
                            $params[$key] = $result[0];
                        }
                    }

                    $route['params'] = $params;
                    return $route;
                }
            }
        }

        if (!is_null($route)) {
            $route['params'] = array();
            return $route;
        }

        return null;
    }

    public static function isMethodAllowed($method) {
        return isset(self::$methods[$method]);
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
