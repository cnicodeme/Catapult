<?php
/**
 * @name Required
 * Ensure that the given value is not empty.
 *
 * @package Catapult.Data.Constraints
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 04/2015
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Data\Constraints;

class Email implements IConstraints {
    private $message;

    public function __construct($message = 'Please provide a valid email.') {
        $this->message = $message;
    }

    public function validate($name, $value) {
        if (empty($value)) return null;

        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return sprintf($this->message, $name);
        }
    }
}
