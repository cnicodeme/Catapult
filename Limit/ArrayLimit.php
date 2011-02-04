<?php
/**
 * Loading Catapult.Limit.aLimit
 */
Loader::loadBase ('Limit.aLimit');

/**
 * @name ArrayLimit extends aLimit
 * Use to limit the fetch of an array
 * 
 * @package Catapult.Limit
 * @filesource ArrayLimit.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 04/03/2008
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
class ArrayLimit extends aLimit {
	/**
	 * @name __construct
	 * constructor
	 * Use the Limit system to an Array element
	 * 
	 * @param Array $aElement
	 * @param int $iStart : default 0
	 * @param int $iLimit : default 25
	 */
	public function __construct ($aElement, $iStart = 0, $iLimit = -1) {
		if (!is_array ($aElement)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if (!is_int ($iStart)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iLimit)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::INTEGER_NEEDED);
		}
		
		$this->_oElement = $aElement;
		$this->_iStart = $iStart;
		$this->_iLimit = $iLimit;
		$this->_iPos = -1;
		$this->_iMax = count ($aElement);
	}

	/**
	 * @name next
	 * Move the pointer to the next value
	 * 
	 * @return void
	 */
	public function next () {
		$this->_iPos++;
		$this->_mCurrentValue = current ($this->_oElement);
		next ($this->_oElement);
	}

	/**
	 * @name seek
	 * Move the internal pointer to the specified index
	 * 
	 * @param int $iIndex
	 * 
	 * @return void
	 */
	public function seek ($iIndex) {
		reset ($this->_oElement);
		
		for ($i = 0; $i < $iIndex; $i++)
			next ($this->_oElement);
	}
}
?>