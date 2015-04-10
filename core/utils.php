<?php
/**
 * @name Utils
 * Various useful snippets
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

class Utils {
    /**
     * Cast a given variable $value to a specific $type.
     * $type can be a callable method
    */
    public static function castTo($value, $type) {
        if (is_null($value)) return null;

        switch($type) {
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'int':
                return intval($value);
                break;
            case 'str':
                return strval($value);
                break;
            default:
                return call_user_func_array($type, array($value));
                break;
        }
    }
}
