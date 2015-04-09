<?php
/**
 * @name Controller
 * Top level controller
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

use \Catapult\Controller\Response;

abstract class Controller {
    private static $request  = null;

    private static $session  = null;
    private static $cookie   = null;
    private static $context  = null;
    private static $flash    = null;

    public static function request() {
        if (is_null(self::$request)) {
            self::$request = new \Catapult\Controller\Request();
        }

        return self::$request;
    }

    public static function session() {
        if (is_null(self::$session)) {
            self::$session = new \Catapult\Data\Session();
        }

        return self::$session;
    }

    public static function cookie($name, $value = null) {
        if (is_null(self::$cookie)) {
            self::$cookie = new \Catapult\Data\Cookie();
        }

        if (func_num_args() === 1) {
            return self::$cookie->{$name};
        } else {
            self::$cookie->{$name} = $value;
        }
    }

    public static function flash($name, $value = null) {
        if (is_null(self::$flash)) {
            self::$flash = new \Catapult\Data\Flash();
        }

        if (func_num_args() === 1) {
            return self::$flash->{$name};
        } else {
            self::$flash->{$name} = $value;
        }
    }

    /**
     * Data store for the current context
     * Will be destroyed once the request is finished
     */
    public static function context($name, $value = null) {
        if (is_null(self::$context)) {
            self::$context = new \Catapult\Data\Context();
        }

        if (func_num_args() === 1) {
            return self::$context->{$name};
        } else {
            self::$context->{$name} = $value;
        }
    }

    public static function ok($content) {
        return new Response(200, null, $content);
    }

    public static function noContent() {
        return new Response(204);
    }

    public static function badRequest() {
        if (func_num_args() === 1) {
            return new Response(400, 'Bad Request', func_get_arg(0));
        } else {
            return new Response(400, func_get_arg(0), func_get_arg(1));
        }
    }

    public static function unauthorized($content = null) {
        return new Response(401, 'Unauthorized', $content);
    }

    public static function forbidden($content = null) {
        return new Response(403, 'Forbidden', $content);
    }

    public static function notFound($content = null) {
        return new Response(404, 'Not Found', $content);
    }

    public static function internalServerError($title = null, $content = null) {
        if (func_num_args() === 1) {
            return new Response(500, 'Bad Request', func_get_arg(0));
        } else {
            return new Response(500, func_get_arg(0), func_get_arg(1));
        }
    }

    public static function before() {}
    public static function after($response=null) {}
}
