<?php
/**
 * @name Required
 * Ensure that the length of the given value is lower than .
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

class MaxLength implements IConstraints {
    private $message;
    private $number = 0;

    public function __construct($number, $message = '%d chars maximum.') {
        $this->number = $number;
        $this->message = $message;
    }

    public function validate($name, $value) {
        if (empty($value)) return null;

        if (strlen($value) >= $this->number) {
            return sprintf($this->message, $name, $this->number);
        }
    }
}
