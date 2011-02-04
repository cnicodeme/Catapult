<?php
Loader::loadBase('Libraries.Validate.iValidator');
Loader::loadBase('Libraries.Validate.iValidatorArgs');

/**
 * @name RegExp
 * Check the given value with a specific regular expression
 * 
 * @package Catapult.Libraries.Validate.Validators
 * @filesource RegExp.php
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
class RegExp implements iValidator, iValidatorArgs {
	/**
	 * @var String $_sPattern
	 * Contain the regular expression
	 */
	private $_sPattern;

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
		if (is_string ($mArg1))
			$this->_sPattern = $mArg1;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
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
		return (filter_var($mValue, FILTER_VALIDATE_REGEXP, array ("options"=>array ("regexp"=>$this->_sPattern))) === false) ? false : true;
	}
}
?>