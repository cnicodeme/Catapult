<?php
/**
 * @name IConstraints
 * Defines rules primary used for Form & Model
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

interface IConstraints {
    public function validate($name, $value);
}
