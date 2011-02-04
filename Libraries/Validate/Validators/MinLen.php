<?php
Loader::loadBase('Libraries.Validate.iValidator');
Loader::loadBase('Libraries.Validate.iValidatorArgs');

/**
 * @name MinLen
 * Check if the length of the given value is more than the min value
 * 
 * @package Catapult.Libraries.Validate.Validators
 * @filesource MinLen.php
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
class MinLen implements iValidator, iValidatorArgs {
	/**
	 * @var Int $_iMinLen
	 * Indicate the minimum length to check
	 */
	private $_iMinLen;

	/**
	 * @name public function setValue
	 * Set specifics values for class that need external elements to check the rule
	 *
	 * @param Mixed $mArg1
	 * @param Mixed $mArg2
	 * 
	 * @return void
	 */
	public function setValue ($mArg1, $mArg2 = null) {
		if (is_int ($mArg1))
			$this->_iMinLen = $mArg1;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
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
		return (strlen ($mValue) >= $this->_iMinLen) ? true : false;
	}
}
?>