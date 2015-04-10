<?php
/**
 * @name Required
 * Ensure that the given value is valid agains't a regex pattern .
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

class Pattern implements IConstraints {
    private $message;
    private $pattern;

    public function __construct($pattern, $message = 'Pattern is required.') {
        $this->pattern = $pattern;
        $this->message = $message;
    }

    public function validate($name, $value) {
        if (empty($value)) return null;

        if (@preg_match($this->pattern, $value) === false) {
            return sprintf($this->message, $name, $this->pattern);
        }
    }
}
