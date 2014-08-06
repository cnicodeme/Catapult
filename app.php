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
use \Catapult\Controller\Request;
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

        self::loadMiddlewares();
        self::dispatch();
    }

    public static function abort($code) {
        $title = null;
        switch ($code) {
            case '400':
                $title = 'Bad Request';
                break;
            case '403':
                $title = 'Forbidden';
                break;
            case '404':
                $title = 'Not Found';
                break;
        }

        $destination = Controller\Route::getDestination($code);
        if (is_null($destination)) {
            EventDispatcher::trigger('process_view');

            Response::addHeader('HTTP/1.0 '.$code.' '.$title);
            Response::setBody($title);

            // Render template
            Response::render();
        } else {
            self::call($destination);
        }

        exit();
    }

    public static function isEnvironment($env) {
        return ($env === Config::getInstance()->getEnvironment());
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
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
        } else {
            $path = $_SERVER['REQUEST_URI'];
        }

        $base_uri = Core\Config::get('base_uri');

        if (!is_null($base_uri)) {
            if (substr($path, 0, strlen($base_uri)) === $base_uri) {
                $path = '/'.substr($path, strlen($base_uri));
                $path = str_replace('//', '/', $path);
            }
        }

        Request::setPath($path);
        Request::setMethod($_SERVER['REQUEST_METHOD']);
        $destination = Controller\Route::getDestination(Request::getPath(), Request::getMethod());

        if (is_null($destination)) {
            self::abort(404);
        }

        self::call($destination);
        exit();
    }

    private static function call($destination) {
        Request::setDestination($destination);
        EventDispatcher::trigger('process_view');

        call_user_func_array($destination['destination'], $destination['params']);

        // Render template
        Response::render();
    }
}
