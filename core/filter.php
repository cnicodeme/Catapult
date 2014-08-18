<?php
/**
 * @name Filter
 * Add filters options (before|after) to request for a specific route name
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

class Filter {
    private static $beforeCalls = null;
    private static $afterCalls = null;

    public static function addBefore($name, $destination) {
        self::register('before', $name, $destination);
    }

    public static function addAfter($name, $destination) {
        self::register('after', $name, $destination);
    }

    private static function register($action, $name, $destination) {
        if (!is_callable($destination, true)) {
            throw new \Catapult\Exceptions\NotFoundException('Destination cannot be accessed.');
        }

        $varName = $action.'Calls';

        if (is_null(self::${$varName})) {
            \Catapult\Core\EventDispatcher::on('process_'.($action === 'before' ? 'view' : 'response'), '\Catapult\Core\Filter::trigger'.ucfirst($action));
            self::${$varName} = array();
        }

        if (!isset(self::${$varName}[$name])) {
            self::${$varName}[$name] = array();
        }

        self::${$varName}[$name][] = $destination;
    }

    public static function triggerBefore() {
        self::trigger('before', \Catapult\Controller\Request::getRouteName());
    }

    public function triggerAfter() {
        self::trigger('after', \Catapult\Controller\Request::getRouteName());
    }

    private static function trigger($action, $name) {
        if (is_null($name)) return;
        
        $varName = $action.'Calls';
        if (isset(self::${$varName}[$name])) {
            foreach (self::${$varName}[$name] as $action) {
                call_user_func($action);
            }
        }
    }
}
