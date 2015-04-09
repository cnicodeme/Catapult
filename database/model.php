<?php
/**
 * @name Model
 * Abstracted model class for creating (ORM like) model from database.
 *
 * @package Catapult.Database
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Database;

abstract class Database {
    protected $_primaryKey = 'id';
    protected $_table = null;
    protected $_structure = null;

    private $_columns = array();

    public function __set($column, $value) {
        $this->_columns[$column] = $value;
    }

    public function __get($column) {
        return (array_key_exists($column, $this->_columns) ? $this->_columns[$column] : null);
    }

    public function __isset($column) {
        return isset($this->_columns[$column]);
    }

    public function __unset($column) {
        unset($this->_columns[$column]);
    }

    public function save() {
        if (empty($this->_primaryKey)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table ID.');
        }

        if (empty($this->_table)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table name.');
        }

        if ($this->__isset($this->_primaryKey)) { // Update
            $sql = 'UPDATE `'.$this->_table.'` SET '..' WHERE `'.$this->_primaryKey.'` = :id LIMIT 1;';
        } else { // Insert
            $sql = 'INSERT INTO `'.$this->_table.'` (`'.implode($this->_columns, '`, `').'`) VALUES (:'.implode($this->_columns, ', :').');';
        }
    }

    public function delete() {
        if (empty($this->_primaryKey)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table ID.');
        }

        if (empty($this->_table)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table name.');
        }

        $sql = 'DELETE FROM `'.$this->_table.'` WHERE `'.$this->_primaryKey.'` = :id LIMIT 1;';
    }

    public function __toString() {
        if (isset($this->_columns[$this->_primaryKey])) {
            return 'Model ID#'.$this->_columns[$this->_primaryKey];
        }

        return 'Model';
    }
}
