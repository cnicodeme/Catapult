<?php
/**
 * @name Route
 * A route matching a potential request
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
    const SLUG_PATTERN     = '[a-zA-Z0-9\-]+';
    const ALPHANUM_PATTERN = '[a-zA-Z0-9]+';
    const ALPHA_PATTERN    = '[a-zA-Z]+';
    const NUMERIC_PATTERN  = '[0-9]+';

    private static $defaultMatch = '[a-zA-Z0-9\-]+';

    public static function setDefaultMatch($defaultMatch) {
        self::$defaultMatch = $defaultMatch;
    }

    public static function getDefaultMatch() {
        return self::$defaultMatch;
    }

    private $url = null;
    private $urlPattern = null;
    private $name = null;
    private $destination = null;
    private $methods = array('GET');
    private $forceHttps = false;

    public function __construct($url, $destination, $methods) {
        $this->setUrl($url);
        $this->setDestination($destination);
        $this->setMethods($methods);
    }

    public function setUrl($url) {
        $this->url = $url;

        $this->urlPattern = '{^'.$url.'$}uS';
        if (preg_match_all('{(\:[a-z]+)}', $url, $patterns, PREG_PATTERN_ORDER) > 0) {
            foreach ($patterns[1] as $pattern) {
                $this->urlPattern = str_replace($pattern, '('.(isset($params[substr($pattern, 1)]) ? $params[substr($pattern, 1)] : self::$defaultMatch).')', $this->urlPattern);
            }

            preg_replace('{(\:[a-z]+)}', '('.self::$defaultMatch.')', $this->urlPattern);
        }

        if ($this->urlPattern === '{^'.$url.'$}uS') {
            $this->urlPattern = null;
        }
    }

    public function getUrl() {
        return $this->url;
    }

    public function setDestination($destination) {
        if (!is_callable($destination, true, $callableName)) {
            throw new \Catapult\Exceptions\NotFoundException('Invalid destination name.');
        }

        $this->destination = $destination;
    }

    public function getDestination() {
        return $this->destination;
    }

    public function setMethods($methods) {
        if (is_string($methods)) {
            $methods = array($methods);
        } else if (is_array($methods)) {
            $methods = $methods;
        } else {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "methods" expected to be either string or array.');
        }

        $this->methods = array();
        foreach ($methods as $m) {
            if (!\Catapult\Controller\Router::isMethodAllowed($m)) {
                throw new \Catapult\Exceptions\NotSupportedException('Method '.$m.' is not supported.');
            }
            $this->methods[$m] = true;
        }
    }

    public function getMethods() {
        return $this->methods;
    }

    public function setForceHttps($forceHttps) {
        if (!is_bool($forceHttps)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "forceHttps" expected to be boolean.');
        }

        $this->forceHttps = $forceHttps;
    }

    public function getForceHttps() {
        return $this->forceHttps;
    }

    public function isMatch($url, $method) {
        if (!isset($this->getMethods()[$method])) return false;

        if (is_null($this->urlPattern)) {
            if ($this->getUrl() === $url) {
                return true;
            }

            return false;
        }

        $results = array();
        if (preg_match_all($this->urlPattern, $url, $results, PREG_PATTERN_ORDER) > 0) {
            $params = array();
            array_shift($results);
            foreach ($results as $key=>$result) {
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

            return $params;
        }

        return false;
    }

    public function reverse($params, $absolute = false, $forceHttps = false) {
        $url = $this->url;
        if (preg_match_all('{(\:[a-z]+)}', $url, $patterns, PREG_PATTERN_ORDER) > 0) {
            foreach ($patterns[1] as $pattern) {
                if (!isset($params[substr($pattern, 1)])) {
                    throw new \Catapult\Exceptions\InvalidParameterException('Parameter '.substr($pattern, 1).' was not found for route name "'.$this->name.'".');
                }
                $url = str_replace($pattern, $params[substr($pattern, 1)], $url);
            }
        }

        if (strpos($url, ':') !== false) {
            if (preg_match_all('{(\:[a-z]+)}', $url, $patterns, PREG_PATTERN_ORDER) > 0) {
                throw new \Catapult\Exceptions\InvalidParameterException('Route "'.$this->name.'" is missing some parameters.');
            }
        }

        if ($absolute) {
            if ($this->forceHttps || \Catapult\Controller\Request::isSecure() || $forceHttps) {
                $scheme = 'https';
            } else {
                $scheme = 'http';
            }

            $url = str_replace('//', '/', $_SERVER['SERVER_NAME'].'/'.\Catapult\Core\Config::get('base_uri').$url);

            return $scheme.'://'.$url;
        } else {
            return $url;
        }
    }
}
