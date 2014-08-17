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

    private static $defaultMatch = '[a-zA-Z0-9]+';
    private static $errorNames = array (
        400 => 'badRequest',
        403 => 'forbidden',
        404 => 'notFound'
    );

    private static $routes = array();

    public static function add($url, $destination, $name = null, $extra = 'GET') {
        $baseExtra = array(
            'methods' => array('GET'),
            'forceHttps' => false,
            'params' => null
        );

        if (is_array($extra) && (isset($extra['methods']) || isset($extra['params']) || isset($extra['forceHttps']))) {
            $extra = array_merge($baseExtra, $extra);
        } else {
            if (is_string($extra)) {
                $extra = array_merge($baseExtra, array('methods' => array($extra)));
            } else {
                $extra = array_merge($baseExtra, array('methods' => $extra));
            }
        }

        if (in_array($url, self::$errorNames)) {
            return self::addError(array_search($url, self::$errorNames), $destination, $url);
        }

        if (!is_callable($destination, true, $callableName)) {
            throw new \Catapult\Exceptions\NotFoundException('Destination cannot be accessed.');
        }

        if (is_null($name)) {
            $name = str_replace('::', '.', substr($callableName, strrpos($callableName, '\\') + 1));
        }

        if (isset(self::$routes[$name])) {
            throw new \Catapult\Exceptions\AlreadyExistsException('Route name "'.$name.'" already exists.');
        }

        $destination = $callableName;

        self::register($url, $destination, $name, $extra['methods'], $extra['params'], $extra['forceHttps']);
    }

    private static function register($url, $destination, $name, $method, $params, $forceHttps) {
        $methods = array();
        foreach ($method as $m) {
            if (!self::isMethodAllowed($m)) {
                throw new \Catapult\Exceptions\NotSupportedException('Method '.$m.' is not supported.');
            }
            $methods[$m] = true;
        }

        $urlPattern = '{^'.$url.'$}uS';
        if (preg_match_all('{(\:[a-z]+)}', $url, $patterns, PREG_PATTERN_ORDER) > 0) {
            foreach ($patterns[1] as $pattern) {
                $urlPattern = str_replace($pattern, '('.(isset($params[substr($pattern, 1)]) ? $params[substr($pattern, 1)] : self::$defaultMatch).')', $urlPattern);
            }

            preg_replace('{(\:[a-z]+)}', '('.self::$defaultMatch.')', $urlPattern);
        }

        if ($urlPattern === '{^'.$url.'$}uS') {
            $urlPattern = null;
        }

        self::$routes[$name] = array(
            'url'         => $url,
            'urlPattern'  => $urlPattern,
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
        if (in_array($url, self::$errorNames)) {
            $url = array_search($url, self::$errorNames);
        }

        $route = null;
        foreach (self::$routes as $route) {
            if (!isset($route['method'][$method])) continue;

            if (is_null($route['urlPattern'])) {
                if ($route['url'] === $url) {
                    if (!isset($route['params'])) {
                        $route['params'] = array();
                    }

                    return $route;
                }

                continue;
            }

            $results = array();
            if (preg_match_all($route['urlPattern'], $url, $results, PREG_PATTERN_ORDER) > 0) {
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

        return null;
    }

    public static function isMethodAllowed($method) {
        return isset(self::$methods[$method]);
    }

    public static function reverse($name, $params = array(), $absolute=false) {
        $url = self::$routes[$name]['url'];
        if (preg_match_all('{(\:[a-z]+)}', $url, $patterns, PREG_PATTERN_ORDER) > 0) {
            foreach ($patterns[1] as $pattern) {
                if (!isset($params[substr($pattern, 1)])) {
                    throw new \Catapult\Exceptions\InvalidParameterException('Parameter '.substr($pattern, 1).' was not found for route name "'.$name.'".');
                }
                $url = str_replace($pattern, $params[substr($pattern, 1)], $url);
            }
        }

        if (strpos($url, ':') !== false) {
            if (preg_match_all('{(\:[a-z]+)}', $url, $patterns, PREG_PATTERN_ORDER) > 0) {
                throw new \Catapult\Exceptions\InvalidParameterException('Route "'.$name.'" is missing some parameters.');
            }
        }

        if ($absolute) {
            $scheme = (self::$routes[$name]['forceHttps'] ? 'https' : (Request::isSecure() ? 'https' : 'http')).'://';
            $url = str_replace('//', '/', $_SERVER['SERVER_NAME'].'/'.\Catapult\Core\Config::get('base_uri').$url);

            return $scheme.$url;
        } else {
            return $url;
        }
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
