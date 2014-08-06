<?php
/**
 * @name
 * Load and give access to config file
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

class Config {
    const AS_BOOLEAN = 'boolean';
    const AS_INTEGER = 'integer';
    const AS_STRING = 'string';
    const AS_ARRAY = 'array';

    private static $instance = null;

    private static $environments = array();
    private static $filepath = 'config.php';

    public static function setFile($filepath) {
        if (file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
            self::$filepath = $filepath;
            return true;
        }

        throw new \Catapult\Exceptions\FileNotFoundException('Configuration file not found.');
    }

    public static function register($env, $rules = null) {
        self::$environments[$env] = $rules;
    }

    public static function getBool($key) {
        return self::getInstance()->getAs($key, self::AS_BOOLEAN);
    }

    public static function getInt($key) {
        return self::getInstance()->getAs($key, self::AS_INTEGER);
    }

    public static function getString($key) {
        return self::getInstance()->getAs($key, self::AS_STRING);
    }

    public static function get($key) {
        return self::getInstance()->getAs($key, self::AS_STRING);
    }

    public static function getArray($key) {
        return self::getInstance()->getAs($key, self::AS_ARRAY);
    }

    public static function has($key) {
        return self::getInstance()->hasKey($key);
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    private $environment = null;
    private $config = array();

    private function __construct() {
        foreach (self::$environments as $key=>$rule) {
            if (is_null($rule)) {
                $this->environment = $key;
                break;
            }
            if (in_array($_SERVER['REMOTE_ADDR'], $rule)) {
                $this->environment = $key;
                break;
            }
        }

        // Load the config file
        $this->config = include(self::$filepath);
    }

    public function getAs($key, $type = self::AS_STRING) {
        $result = $this->getValue($key);

        if (is_null($result)) {
            return $result;
        }

        switch($type) {
            case self::AS_BOOLEAN:
                return boolval($result);
                break;
            case self::AS_INTEGER:
                return intval($result);
                break;
            case self::AS_STRING:
                return strval($result);
                break;
            case self::AS_ARRAY:
                if (is_array($result)) {
                    return $result;
                } else {
                    return array($result);
                }
                break;
        }

        return null;
    }

    public function hasKey($key) {
        return !is_null($this->getValue($key));
    }

    public function getEnvironment() {
        return $this->environment;
    }

    private function getValue($key) {
        $key = explode('.', $key);
        $result = null;

        if (isset($this->config[$this->environment])) {
            $result = $this->lookup($this->config[$this->environment], $key);
            if (!is_null($result)) {
                return $result;
            }
        }

        $result = $this->lookup($this->config['global'], $key);
        return $result;
    }

    private function lookup($level, $parts) {
        foreach ($parts as $part) {
            // Special conditions
            if ('database' === $part) {
                $level = $this->lookup($level, array('databases', '0'));
                continue;
            }

            if (is_numeric($part)) {
                $part = intval($part);
            }

            if (!isset($level[$part])) return null;

            $level = $level[$part];
        }

        return $level;
    }
}
