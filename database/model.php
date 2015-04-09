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
 * @since 04/2015
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Database;

abstract class Model {
    protected $primaryKey = 'id';
    protected $table = null;
    protected $structure = null;

    // Add structure to convert dates, numerics to proper value

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

    public function save($database = 'default') {
        if (empty($this->primaryKey)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table ID.');
        }

        if (empty($this->table)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table name.');
        }

    /*    if ($this->__isset($this->primaryKey)) { // Update
            $sql = 'UPDATE `'.$this->table.'` SET '..' WHERE `'.$this->primaryKey.'` = :id LIMIT 1;';
        } else { // Insert
            $sql = 'INSERT INTO `'.$this->table.'` (`'.implode($this->_columns, '`, `').'`) VALUES (:'.implode($this->_columns, ', :').');';
        }*/
    }

    public function getStructure() {
        return array_keys($this->structure);
    }

    public function delete($database = 'default') {
        if (empty($this->primaryKey)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table ID.');
        }

        if (empty($this->table)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table name.');
        }

        return Database::execute('DELETE FROM `'.$this->table.'` WHERE `'.$this->primaryKey.'` = :id LIMIT 1;', array('id' => $this->_columns[$this->_primaryKey]));
    }

    public function __toString() {
        if (isset($this->_columns[$this->primaryKey])) {
            return 'Model ID#'.self::$_columns[$this->primaryKey];
        }

        return 'Model';
    }

    protected static function load($sql, $params = null) {
        return Database::getInstance('default')->load($sql, $params, get_called_class());
    }

    protected static function loadFirst($sql, $params) {
        $model = self::load($sql, $params)->fetch();
        return ($model === false ? null : $model);
    }
}
