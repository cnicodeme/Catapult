<?php
/**
 * @name Database
 * Database manager
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
use \PDO;

class Database {
    private static $instances = array();

    public static function getInstance($instance = 'default') {
        if (!isset(self::$instances[$instance])) {
            self::$instances[$instance] = new Database(\Catapult\Core\Config::getArray('databases.'.$instance));
        }

        return self::$instances[$instance];
    }

    public static function __callStatic($name, $arguments) {
        return call_user_func_array(array(self::getInstance('default'), $name), $arguments);
    }

    private $pdo;

    public function __construct($params) {
        if (!is_array($params)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Array expected.');
        }

        if (!isset($params['database'])) {
            throw new \Catapult\Exceptions\NotFoundException('Parameter "database" was not found.');
        }

        if (!isset($params['username'])) {
            throw new \Catapult\Exceptions\NotFoundException('Parameter "username" was not found.');
        }

        if (!isset($params['password'])) {
            throw new \Catapult\Exceptions\NotFoundException('Parameter "password" was not found.');
        }

        if (!isset($params['charset'])) $params['charset'] = 'utf8';
        if (!isset($params['collation'])) $params['collation'] = 'utf8';
        if (!isset($params['host'])) $params['host'] = '127.0.0.1';
        if (!isset($params['port'])) $params['port'] = '3306';

        $this->pdo = new PDO(
            'mysql:dbname='.$params['database'].';host='.$params['host'].';port='.$params['port'],
            $params['username'],
            $params['password'],
            array (
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".$params['charset']."'"
            )
        );

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function exec($sql, $params = null) {
        $stmt = $this->pdo->prepare($sql);
        $result = false;

        if (is_null($params)) {
            $result = $stmt->execute();
        } else {
            $result = $stmt->execute($params);
        }

        if (!$result) {
            throw new \Catapult\Exceptions\DatabaseException($this->pdo->errorInfo(), $this->pdo->errorCode());
        }

        return $stmt->rowCount();
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    public function load($sql, $params, $model) {
        $stmt = $this->pdo->prepare($sql);
        $result = false;

        if (is_null($params)) {
            $result = $stmt->execute();
        } else {
            $result = $stmt->execute($params);
        }

        if (!$result) {
            throw new \Catapult\Exceptions\DatabaseException($this->pdo->errorInfo(), $this->pdo->errorCode());
        }

        $stmt->setFetchMode(PDO::FETCH_CLASS, $model);
        return $stmt;
    }

    /* Direct accessor to PDO */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this-pdo, $name), $arguments);
    }
}
