<?php
/**
 * @name Event
 * Event system
 *
 * @package Catapult.Core
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Core;

class EventDispatcher {
    private static $events = array();

    public static function trigger($name, $params = null) {
        if (!isset(self::$events[$name])) {
            return null;
        }

        foreach (self::$events[$name] as $event) {
            // We stop if one of the methods returns false
            $result = call_user_func_array($event['method'], array_merge((array) $event['params'], (array) $params));
            if ($result === false) break;
            if ($result instanceof \Catapult\Controller\Response) {
                $result->render();
                die();
            }
        }
    }

    public static function on($name, $method, $params = null) {
        $name = self::getNameWithNamespace($name);
        if (!isset(self::$events[$name['name']])) {
            self::$events[$name['name']] = array();
        }

        self::$events[$name['name']][] = array (
            'namespace' => $name['namespace'],
            'method'    => $method,
            'params'    => $params
        );
    }

    public static function off($name, $method = null) {
        // off by name, namespace or method

        $name = self::getNameWithNamespace($name);
        if (isset($name['name'])) {
            foreach (self::$events[$name['name']] as $eventIndex=>$event) {
                // if no namespace or namespace equal
                if (!is_null($name['namespace']) && $name['namespace'] !== $event['namespace']) continue;

                // if no method or method equals
                if (!is_null($method) && !self::isSameMethods($method, $event['method'])) continue;

                unset(self::$events[$name['name']][$eventIndex]);
            }

            if (count(self::$events[$name['name']]) === 0) {
                unset(self::$events[$name['name']]);
            }

        } else if (!is_null($name['namespace'])) {
            foreach (self::$events as $eventName=>$eventList) {
                foreach ($eventList as $eventIndex=>$event) {
                    if ($event['namespace'] === $name['namespace']) {
                        unset(self::$events[$eventName][$eventIndex]);
                    }
                }

                if (count($eventList) === 0) {
                    unset(self::$events[$eventName]);
                }
            }
        }
    }

    private static function getNameWithNamespace($name) {
        @list($name, $namespace) = explode('.', $name);
        return array (
            'name' => $name,
            'namespace' => $namespace
        );
    }

    private static function isSameMethods($m1, $m2) {
        if (is_string($m1) && is_string($m2) && $m1 === $m2) return true;
        if (is_array($m1) && is_array($m2)) {
            foreach ($m1 as $key=>$value) {
                if (!isset($m2[$key])) return false;
                if ($value !== $m2[$key]) return false;
            }

            return true;
        }

        return false;
    }
}
