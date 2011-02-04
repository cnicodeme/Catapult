<?php
/**
 * @name aLimit implements Iterator, SeekableIterator, Countable
 * @abstract
 * The Exceptions Manager to specify if the Exceptions will be thrown or not, and with specifics informations
 * 
 * @package Catapult.Limit
 * @filesource Limit.php
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
abstract class aLimit implements Iterator, SeekableIterator, Countable {
	/**
	 * @var Object $_mElement
	 * Contain the Element to Iterate
	 */
	protected $_mElement;
	
	/**
	 * @var int $_iStart
	 * The start of the values
	 */
	protected $_iStart;
	
	/**
	 * @var int $_iLimit
	 * The max values to return
	 */
	protected $_iLimit;
	
	/**
	 * @var int $_iMax
	 * The max value from the query
	 */
	protected $_iMax = 0;
	
	/**
	 * @var int $_iPos
	 * The current position of the index
	 */
	protected $_iPos = -1;
	
	/**
	 * @var Array $_mCurrentValue
	 * The current value
	 */
	protected $_mCurrentValue = null;

	/**
	 * @name setLimit
	 * Modify the limits values
	 * 
	 * @param int $iStart
	 * @param int $iLimit
	 * 
	 * @return void
	 */
	public function setLimit ($iStart, $iLimit = -1) {
		if (!is_int ($iStart)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iLimit)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if ($iStart > $this->_iMax) {
			Loader::loadBase ('Exceptions.OutOfBoundException');
			throw new OutOfBoundException ($iStart);
		}
		
		if (($iStart + $iLimit) > $this->_iMax) {
			Loader::loadBase ('Exceptions.OutOfBoundException');
			throw new OutOfBoundException ($iStart, $iLimit);
		}
		
		$this->_iStart = $iStart;
		$this->_iLimit = $iLimit;
	}
	
	/**
	 * @name public __get
	 * Magic method to recover particular values (if it's an array, $sKey will return the key, if it's an object, the ->$sKey)
	 *
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	public function __get ($sKey) {
		if (is_array ($this->_mCurrentValue) && isset ($this->_mCurrentValue [$sKey]))
			return $this->_mCurrentValue [$sKey];
		elseif (is_object ($this->_mCurrentValue) && isset ($this->_mCurrentValue->$sKey))
			return $this->_mCurrentValue->$sKey;
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name current
	 * Return the current value from the fetch method
	 * 
	 * @return Array
	 */
	public function current () {
		return $this->_mCurrentValue;
	}
	
	/**
	 * @name valid
	 * Indicate if the value is valid
	 * 
	 * @return boolean
	 */
	public function valid () {
		if ($this->_iPos >= $this->_iMax)
			return false;
		elseif ($this->_iLimit > 0 && ($this->_iPos + $this->_iStart >= $this->_iStart + $this->_iLimit)) {
			return false;
		}
		elseif ($this->_mCurrentValue === false)
			return false;
		else
			return true;
	}
	
	/**
	 * @name rewind
	 * Move the internal pointer to the beginning
	 * 
	 * @return void
	 */
	public function rewind () {
		$this->seek ($this->_iStart);
		$this->iPos = -1;
		$this->next ();
	}
	
	/**
	 * @name key
	 * Return the current internal position
	 * 
	 * @return int
	 */
	public function key () {
		return $this->_iPos;
	}
	
	/**
	 * @name count
	 * Return the number of rows affected by the request
	 * 
	 * @return int
	 */
	public function count () {
		return $this->_iMax;
	}
	
	/**
	 * @name getInternalPosition
	 * Return the current internal position
	 * 
	 * @return int
	 */
	public function getInternalPosition () {
		return $this->_iPos;
	}
	
	/**
	 * @name getExternalPosition
	 * Return the current external position
	 * 
	 * @return int
	 */
	public function getExternalPosition () {
		return ($this->_iStart + $this->_iPos);
	}
	
	/**
	 * Abstract method to be implemented !
	 * Note : Don't Forget next and seek to !
	 */
	abstract public function __construct ($aElement, $iStart = 0, $iLimit = -1);
}
?>