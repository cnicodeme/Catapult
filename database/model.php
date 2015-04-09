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

abstract class Model {
    protected static $_primaryKey = 'id';
    protected static $_table = null;

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
        if (empty(self::$_primaryKey)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table ID.');
        }

        if (empty(self::$_table)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table name.');
        }

    /*    if ($this->__isset(self::$_primaryKey)) { // Update
            $sql = 'UPDATE `'.self::$_table.'` SET '..' WHERE `'.self::$_primaryKey.'` = :id LIMIT 1;';
        } else { // Insert
            $sql = 'INSERT INTO `'.self::$_table.'` (`'.implode($this->_columns, '`, `').'`) VALUES (:'.implode($this->_columns, ', :').');';
        }*/
    }

    public function delete($database = 'default') {
        if (empty(self::$_primaryKey)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table ID.');
        }

        if (empty(self::$_table)) {
            throw new \Catapult\Exceptions\NotSupportedException('Missing table name.');
        }

        return Database::execute('DELETE FROM `'.self::$_table.'` WHERE `'.self::$_primaryKey.'` = :id LIMIT 1;', array('id' => $this->_columns[self::$_primaryKey]));
    }

    protected static function load($sql, $params = null) {
        return Database::getInstance('default')->load($sql, $params, get_called_class());
    }

    protected static function loadFirst($sql, $params) {
        $model = self::load($sql, $params)->fetch();
        return ($model === false ? null : $model);
    }

    public function __toString() {
        if (isset($this->_columns[self::$_primaryKey])) {
            return 'Model ID#'.self::$_columns[$this->_primaryKey];
        }

        return 'Model';
    }
}
