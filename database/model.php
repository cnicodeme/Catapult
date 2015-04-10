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
        $setter = 'set'.ucfirst(implode(array_map('ucfirst', explode('_', $column))));
        if (method_exists($this, $setter)) {
            return $this->{$setter}($value);
        } else {
            $this->_columns[$column] = $value;
        }
    }

    public function __get($column) {
        $getter = 'get'.ucfirst(implode(array_map('ucfirst', explode('_', $column))));
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        } else {
            return (array_key_exists($column, $this->_columns) ? $this->_columns[$column] : null);
        }
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

        $cols = array_keys($this->structure);
        $index = array_search($this->primaryKey, $cols);
        if ($index !== false) {
            unset($cols[$index]);
        }

        $data = array();
        foreach ($cols as $col) {
            if (isset($this->_columns[$col])) {
                $data[$col] = $this->_columns[$col];
            } else {
                $data[$col] = null;
            }
        }

        if ($this->__isset($this->primaryKey)) { // Update
            $sql = 'UPDATE `'.$this->table.'` SET ';
            foreach ($cols as $col) {
                $sql .= '`'.$col.'` = :'.$col.', ';
            }

            $sql = substr($sql, 0, -2).' WHERE `'.$this->primaryKey.'` = :'.$this->primaryKey.' LIMIT 1;';

            $data[$this->primaryKey] = $this->_columns[$this->primaryKey];
        } else { // Insert
            $sql = 'INSERT INTO `'.$this->table.'` (`'.implode($cols, '`, `').'`) VALUES (:'.implode($cols, ', :').');';
        }

        return Database::execute($sql, $data);
    }

    public function getStructure() {
        if (!is_array($this->structure)) return array();

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
