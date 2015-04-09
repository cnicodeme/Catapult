<?php
/**
 * @name Flash
 * Flash data through one request
 *
 * @package Catapult.Data
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Data;

use \Catapult\Controller\Controller;

class Flash {
    private $name;
    private $items = array();

    public function __construct() {
        $this->name = \Catapult\Core\Config::getString('cookies.flash', '_flash');
        if (isset(Controller::session()->{$this->name})) {
            $this->items = json_decode(Controller::session()->{$this->name});
            Controller::session()->{$this->name} = array();
        }
    }

    public function __get($name) {
        if (!array_key_exists($name, $this->items)) return null;

        return $this->items[$name];
    }

    public function __set($name, $value) {
        $this->items[$name] = $value;

        $tmp = json_decode(Controller::session()->{$this->name});
        $tmp[$name] = $value;
        Controller::session()->{$this->name} = json_encode($tmp);
    }

    public function __isset($name) {
        return isset($this->items[$name]);
    }

    public function __unset($name) {
        unset($this->items[$name]);

        $tmp = json_decode(Controller::session()->{$this->name});
        unset($tmp[$name]);
        Controller::session()->{$this->name} = json_encode($tmp);
    }
}
