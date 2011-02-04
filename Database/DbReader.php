<?php
Loader::loadBase ('Limit.aLimit');

/**
 * @name DbReader extends aLimit
 * Used to read a multiple result by using the Limit advantages
 * 
 * @package Catapult.Database
 * @filesource DbReader.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 05/03/2008
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
class DbReader extends aLimit {
	/**
	 * @var private aDbManager $_oDbManager
	 * Contain an instance of aDbManager
	 */
	private $_oDbManager;
	
	/**
	 * @name __construct
	 * constructor
	 * Use the Limit system to a Db element
	 * 
	 * @param aDbManager $oDbManager
	 * @param Resource $rResult
	 * @param int $iStart : default 0
	 * @param int $iLimit : default 25
	 */
	public function __construct (aDbManager $oDbManager, $rResult, $iStart = 0, $iLimit = -1) {
		if (!$oDbManager instanceof aDbManager) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::OBJECT_NEEDED);
		}
		
		if (!is_resource ($rResult)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::RESOURCE_NEEDED);
		}
		
		if (!is_int ($iStart)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iLimit)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::INTEGER_NEEDED);
		}

		$this->_mElement = $rResult;
		$this->_iStart = $iStart;
		$this->_iLimit = $iLimit;
		$this->_iPos = -1;
		$this->_iMax = $oDbManager->count ($rResult);
		$this->_oDbManager = $oDbManager;
	}

	/**
	 * @name __destruct
	 * Set the result from the query free :) and happy to leave
	 * 
	 * @return void
	 */
	public function __destruct () {
		if (is_resource ($this->_mElement)) {
			$this->_oDbManager->freeResult ($this->_mElement);
			$this->_mElement = null;
		}
	}

	/**
	 * @name next
	 * Move the pointer to the next value
	 * 
	 * @return void
	 */
	public function next () {
		$this->_iPos++;
		$this->_mCurrentValue = $this->_oDbManager->fetch ($this->_mElement);
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
		$this->_oDbManager->seek ($this->_mElement, $iIndex);
	}
}
?>