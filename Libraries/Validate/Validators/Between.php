<?php
Loader::loadBase('Libraries.Validate.iValidator');
Loader::loadBase('Libraries.Validate.iValidatorArgs');

/**
 * @name Between
 * Check if the given value is between to other values
 * 
 * @package Catapult.Libraries.Validate.Validators
 * @filesource Between.php
 * 
 * @author Cyril Nicodème
 * @version 0.2
 * 
 * @since 31/03/2008
 * 
 * @license Gnu/Agpl
 * Copyright (C) 2008  Cyril Nicodème
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Between implements iValidator, iValidatorArgs {
	/**
	 * @var Int $_iMinValue
	 * Contain the Minimum value to check
	 */
	private $_iMinValue;
	
	/**
	 * @var Int $_iMaxValue
	 * Contain the Maximum value to check
	 */
	private $_iMaxValue;

	/**
	 * @name public function setValue
	 * Set specifics values for class that need external elements to check the rule
	 *
	 * @param Mixed $mArg1
	 * @param Mixed $mArg2
	 * 
	 * @return void
	 */
	public function setValue ($mArg1, $mArg2) {
		if (!is_int ($mArg1)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($mArg2)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}

		$this->_iMinValue = $mArg1;
		$this->_iMaxValue = $mArg2;
	}

	/**
	 * @name public function validate
	 * Validate a given value with specific rule
	 * 
	 * @param Mixed $mValue
	 * 
	 * @return Boolean
	 */
	public function validate ($mValue) {
		return (filter_var ($mValue, FILTER_VALIDATE_INT, array("min_range"=>$this->_iMinValue, "max_range"=>$this->_iMaxValue)) === false) ? false : true;
	}
}
?>