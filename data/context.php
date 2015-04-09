<?php
/**
 * @name Session
 * Manage the data for the current context
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

class Context {
    private $_ctx = array();

    public function __get($name) {
        if (!array_key_exists($name, $this->_ctx)) return null;

        return $this->_ctx[$name];
    }

    public function __set($name, $value) {
        $this->_ctx[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->_ctx[$name]);
    }

    public function __unset($name) {
        unset($this->_ctx[$name]);
    }

    public function clear() {
        $this->_ctx = array();
    }
}
