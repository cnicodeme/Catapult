<?php
/**
 * @name Validate
 * Validate a specified value
 * 
 * @package Catapult.Libraries.Validate
 * @filesource Validate.php
 * 
 * @author Cyril Nicodème
 * @version 0.2
 * 
 * @since 31/03/2008
 * 
 * @see http://fr2.php.net/manual/fr/ref.filter.php
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
class Validate {
	/**
	 * @var Array $_aValidators
	 * Contain an instance of each Validators instanciated. To avoid multiple instance
	 */
	private static $_aValidators = array ();
	
	/**
	 * @name public static function isValid
	 * Check if a value is valid with the specified Validators and eventually arguments
	 *
	 * @param String $sClassName
	 * @param Mixed $mValue
	 * @param Mixed $mArgs
	 * 
	 * @return Boolean
	 */
	public static function isValid ($sClassName, $mValue, $mArgs = null) {
		if (!is_string ($sClassName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_scalar ($mValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::SCALAR_NEEDED);
		}
		
		if (isset ($mArgs) && !is_scalar ($mArgs)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::SCALAR_NEEDED);
		}
		
		if (!array_key_exists ($sClassName, self::$_aValidators)) {
			Loader::loadBase ('Libraries.Validate.Validators.'.$sClassName);
			self::$_aValidators [$sClassName] = new $sClassName ();
		}
		
		if (isset ($mArgs)) {
			if (is_array ($mArgs)) {
				switch (count ($mArgs)) {
					case 1:
						self::$_aValidators [$sClassName]->setValue ($mArgs[0]);
						break;
					case 2:
						self::$_aValidators [$sClassName]->setValue ($mArgs[0], $mArgs[1]);
				}
			}
			elseif (is_string ($mArgs))
				self::$_aValidators [$sClassName]->setValue ($mArgs);
		}
		
		return self::$_aValidators [$sClassName]->validate ($mValue);
	}
}
?>