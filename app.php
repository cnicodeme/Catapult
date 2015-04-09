<?php
/**
 * @name App
 * Main class of the Catapult framework
 *
 * @package Catapult
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/01/2014
 *
 * @license Gnu v3
 */

namespace Catapult;

if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    exit('PHP Version 5.3.0 or above is required');
}

// Registering Catapult folder in include path
set_include_path(get_include_path().PATH_SEPARATOR.__DIR__);

// Loading AutoLoader
require_once(__DIR__.'/core/autoloader.php');

use \Catapult\Core\Config;
use \Catapult\Core\EventDispatcher;
use \Catapult\Controller\Controller;

class App {
    private static $applicationPath = null;

    public static function getApplicationPath() {
        return self::$applicationPath;
    }

    public static function init($applicationPath) {
        self::$applicationPath = $applicationPath;

        // Adding application to include path
        set_include_path(get_include_path().PATH_SEPARATOR.self::$applicationPath);

        self::initBase();
        self::loadMiddlewares();
        self::dispatch();
    }

    public static function isEnvironment($env) {
        return ($env === Config::getInstance()->getEnvironment());
    }

    private static function initBase() {
        $tz = Config::get('timezone', 'UTC');
        date_default_timezone_set($tz);
    }

    private static function loadMiddlewares() {
        if (Config::has('middlewares')) {
            $middlewares = Config::getArray('middlewares');
            foreach($middlewares as $mw) {
                new $mw();
            }
        }
    }

    private static function dispatch() {
        EventDispatcher::trigger('process_request');

        $request = Controller::getRequest();
        list($route, $params) = \Catapult\Controller\Router::getRoute($request->getPath(), $request->getMethod());

        if (is_null($route)) {
            Controller::getResponse()->abort(404);
            die();
        }

        self::call($route, $params);
        die();
    }

    private static function call(\Catapult\Controller\Route $route, $params = array()) {
        Controller::getRequest()->setRoute($route);

        if (is_null($params) || !is_array($params)) {
            $params = array();
        }

        try {
            EventDispatcher::trigger('process_view', array($route->getDestination, $params));
            $result = call_user_func_array($route->getDestination(), $params);
            Controller::getResponse()->render($result);
        } catch (\Exception $e) {
            EventDispatcher::trigger('process_exception', array($e));
            if (self::isEnvironment('prod')) {
                Controller::getResponse()->abort(500, 'Internal Server Error');
            }
            header('content-type: text/plain; charset=utf8');
            throw $e;
        }
    }
}
