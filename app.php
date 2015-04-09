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
use \Catapult\Controller\Response;

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

        $request = Controller::request();
        list($route, $params) = \Catapult\Controller\Router::getRoute($request->getPath(), $request->getMethod());

        if (is_null($route)) {
            Response::abort(404)->render();
        }

        self::call($route, $params);
        die();
    }

    private static function call(\Catapult\Controller\Route $route, $params = array()) {
        Controller::request()->setRoute($route);
        EventDispatcher::on('process_view', substr($route->getDestination(), 0, strrpos($route->getDestination(), '::')).'::before');
        EventDispatcher::on('process_response', substr($route->getDestination(), 0, strrpos($route->getDestination(), '::')).'::after');

        if (is_null($params) || !is_array($params)) {
            $params = array();
        }

        try {
            EventDispatcher::trigger('process_view', array($route, $params));
            $response = call_user_func_array($route->getDestination(), $params);
            $response->render();
        } catch (\Exception $e) {
            EventDispatcher::trigger('process_exception', array($e));
            if (self::isEnvironment('prod')) {
                Response::abort(500, null, $e)->render();
            }
            
            header('content-type: text/plain; charset=utf8');
            throw $e;
        }
    }
}
