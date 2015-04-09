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

    public static function addBefore($destination) {
        self::register('before', $destination);
    }

    public static function addAfter($destination) {
        self::register('after', $destination);
    }

    private static function register($action, $destination) {
        if (!is_callable($destination, true)) {
            throw new \Catapult\Exceptions\NotFoundException('Destination cannot be accessed.');
        }

        $varName = $action.'Calls';

        if (is_null(self::${$varName})) {
            \Catapult\Core\EventDispatcher::on('process_'.($action === 'before' ? 'view' : 'response'), '\Catapult\Core\Filter::trigger'.ucfirst($action));
            self::${$varName} = array();
        }

        self::${$varName}[] = $destination;
    }

    public static function triggerBefore() {
        self::trigger('before');
    }
    public function triggerAfter() {
        self::trigger('after');
    }

    private static function trigger($action) {
        $varName = $action.'Calls';
        if (isset(self::${$varName}) && count(self::${$varName}) > 0) {
            foreach (self::${$varName} as $action) {
                call_user_func($action);
            }
        }
    }
}
