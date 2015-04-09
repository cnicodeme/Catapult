<?php
/**
 * @name Session
 * Wrapper around the submitted data
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

class Form {
    protected $structure = array();
    private $errors = null;
    private $data = null;

    public function applyTo(\Catapult\Database\Model $model) {
        if (is_null($model)) {
            throw new \Catapult\Exceptions\NullPointerException();
        }

        foreach ($model->getStructure() as $column) {
            $model->{$column} = $this->data[$column];
        }
    }

    public function fill(\Catapult\Database\Model $model) {
        if (is_null($model)) {
            throw new \Catapult\Exceptions\NullPointerException();
        }

        foreach ($model->getStructure() as $column) {
            $this->data[$column] = $model->{$column};
        }
    }

    public function validate() {}

    public function reject($name, $value) {
        if (is_null($this->errors)) {
            $this->errors = array();
        }

        if (!isset($this->errors[$name])) {
            $this->errors[$name] = array();
        }

        $this->errors[$name][] = $value;
    }

    public function errors($name = null) {
        if (is_null($name)) {
            return $this->errors;
        }

        if (!is_array($this->errors)) return null;
        if (!isset($this->errors[$name])) return null;

        return $this->errors[$name];
    }

    public function hasErrors() {
        return is_array($this->errors);
    }

    public function __get($name) {
        return $this->value($name);
    }

    public function value($name, $default = null) {
        if (is_null($this->data)) return $default;
        if (!isset($this->data[$name])) return $default;

        return $this->data[$name];
    }

    public function bindFromRequest($params = null) {
        if (is_array($params)) {
            $this->data = array();
            foreach ($params as $name) {
                $this->data[$name] = \Catapult\Controller\Controller::request()->getData($name);
            }
        } else {
            $this->data = \Catapult\Controller\Controller::request()->getData();
        }

        if (is_array($this->structure)) {
            foreach($this->structure as $name=>$rules) {
                if (isset($rules['constraints'])) {
                    foreach ($rules['constraints'] as $constraint) {
                        if (!$constraints instanceof \Catapult\Data\Constraints\IConstraint) {
                            throw new \Catapult\Exceptions\InvalidParameterException("Constraint must extend IConstraint class.");
                        }
                        $result = $constraint->validate($name, $this->data[$name]);
                        if (!is_null($result)) {
                            $this->reject($name, $result);
                        }
                    }
                }

                if (isset($rules['type']) && !is_null($this->data[$name])) {
                    $this->data[$name] = $this->setType($this->data[$name], $rule['type']);
                }
            }
        }

        $this->validate();
    }

    private function setType($value, $type) {
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
                return call_user_func_array($type, $value)
                break;
        }
    }
}
