<?php
/**
 * @name Session
 * Manage the session and the session storage
 *
 * @package Catapult.Data
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 04/2015
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Data;

class Session {
    public function __construct() {
        if (session_status() !== PHP_SESSION_DISABLED) {
            session_name(\Catapult\Core\Config::getString('session.name', 'catapult'));
            session_start();
        }
    }

    public function __get($name) {
        if (!array_key_exists($name, $_SESSION)) return null;

        return $_SESSION[$name];
    }

    public function __set($name, $value) {
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new \Catapult\Exceptions\CatapultException('Session is disabled.');
        }

        $_SESSION[$name] = $value;
    }

    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

    public function __unset($name) {
        unset($_SESSION[$name]);
    }

    public function clear() {
        session_unset();
    }
}
