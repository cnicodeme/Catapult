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
            // NULL and empty are different meanings !
            // Empty means the data needs to be set
            if (is_null($this->data[$column])) continue;
            if (!isset($this->structure[$column])) continue;

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

    public function __set($name, $value) {
        if (!is_array($this->data)) $this->data = array();

        $setter = 'set'.ucfirst(implode(array_map('ucfirst', explode('_', $name))));
        if (method_exists($this, $setter)) {
            return $this->{$setter}($value);
        } else {
            $this->data[$name] = $value;
        }

        if (isset($this->structure[$name])) {
            if (isset($this->structure[$name]['constraints'])) {
                foreach ($this->structure[$name]['constraints'] as $constraint) {
                    if (!$constraint instanceof \Catapult\Data\Constraints\IConstraints) {
                        throw new \Catapult\Exceptions\InvalidParameterException("Constraint must extend IConstraints class.");
                    }

                    $result = $constraint->validate($name, $this->data[$name]);
                    if (!is_null($result)) {
                        $this->reject($name, $result);
                    }
                }
            }

            if (isset($this->structure[$name]['type']) && !is_null($this->data[$name])) {
                $this->data[$name] = \Catapult\Core\Utils::castTo($this->data[$name], $this->structure[$name]['type']);
            }
        }
    }

    public function __get($column) {
        $getter = 'get'.ucfirst(implode(array_map('ucfirst', explode('_', $column))));
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        } else {
            return $this->value($name);
        }
    }

    public function value($name, $default = null) {
        if (is_null($this->data)) return $default;
        if (!isset($this->data[$name])) return $default;

        return $this->data[$name];
    }

    // Be able to choose GET/POST/PUT ?
    public static function bindFromRequest($params = null) {
        if (is_array($params)) {
            $data = array();
            foreach ($params as $name) {
                $data[$name] = \Catapult\Controller\Controller::request()->getData($name);
            }
        } else {
            $data = \Catapult\Controller\Controller::request()->getData();
        }

        $class = get_called_class();
        $form = new $class();
        foreach($data as $key=>$value) {
            $form->{$key} = $value;
        }

        $form->validate();

        return $form;
    }
}
