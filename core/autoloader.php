<?php
/**
 * @name
 * Autoload classes using SPL's
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

class AutoLoader {
    public static function init() {
        spl_autoload_register(array('self', 'loader'));
    }

    private static function loader($classname) {
        $classname = str_replace('\\', DIRECTORY_SEPARATOR, $classname);
        $classname = strtolower($classname);

        if ('catapult' === substr($classname, 0, 8)) {
            $classname = substr($classname, 9);
        }

        require_once($classname.'.php');
    }
}

AutoLoader::init();
