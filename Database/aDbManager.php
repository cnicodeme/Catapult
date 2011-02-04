<?php
/**
 * Loading Catapult.Database.DbState
 */
Loader::loadBase ('Database.DbState');

/**
 * @name aDbManager
 * @abstract
 * Main element to use an access at a specific Db
 * 
 * @package Catapult.Database
 * @filesource aDbManager.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 04/03/2008
 * 
 * @todo aDbManger : Adapter les classes Db à PDO
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
abstract class aDbManager {
	/**
	 * @var private Boolean $_bAutoPrepareQueries
	 * Indicate if the queries are automatically parsed (escaped and prepared)
	 * 
	 * @default true
	 */
	private $_bAutoPrepareQueries = true;
	
	/**
	 * @var private Int $_iConnectionState
	 * Indicate the state of the Db Link
	 * 
	 * @default DbState::Closed
	 */
	private $_iConnectionState = DbState::Closed;
	
	/**
	 * @var protected resource $_rCnx
	 * Contain the resource of the Db Link
	 */
	protected $_rCnx;
	
	/**
	 * @var private Array $_aErrors
	 * Contain all the errors occured
	 */
	private $_aErrors = array ();
	
	/**
	 * @var protected aTransaction $_oTransactionInstance
	 * Contai an instance of aTransaction
	 */
	protected $_oTransactionInstance;
	
	/**
	 * @name public __construct
	 * Constructor
	 * If $sDsn is specified, automatically connect to the db
	 *
	 * @param String $sDsn : Optional
	 * @param Boolean $bIsPersistant
	 */
	public function __construct ($sDsn = null, $bIsPersistant = false) {
		$bIsPersistant = (is_bool ($bIsPersistant)) ? $bIsPersistant : false;

		if (isset ($sDsn))
			$this->open ($sDsn, $bIsPersistant);
	}
	
	/**
	 * @name public __destruct
	 * Close the Db link on destruct if it's not already close
	 */
	public function __destruct () {
		$this->close ();
	}
	
	/**
	 * @name public __get
	 * Return the state, the errors or the boolean value of autoPrepare vars
	 *
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	public function __get ($sKey) {
		switch (strtolower ($sKey)) {
			case 'state':
				return $this->_iConnectionState;
				break;
			case 'connectionstate':
				return $this->_iConnectionState;
				break;
			case 'errors':
				return $this->_aErrors;
				break;
			case 'autoprepare':
				return $this->_bAutoPrepareQueries;
				break;
		}
	}
	
	/**
	 * @name public open
	 * Open a connection to the db
	 *
	 * @param String $sDsn
	 * @param Boolean $bIsPersistant
	 * 
	 * @return void
	 */
	public function open ($sDsn, $bIsPersistant = false) {
		$bIsPersistant = (is_bool ($bIsPersistant)) ? $bIsPersistant : false;
		
		if (is_string ($sDsn)) {
			$this->_iConnectionState = DbState::Connecting;
			$aDsnComponents = (strpos ($sDsn, '://')) ? parse_url ($sDsn) : parse_url ('db://'.$sDsn);
			$aDsnComponents['host'] = (isset ($aDsnComponents['host'])) ? $aDsnComponents['host'] : '';
			$aDsnComponents['port'] = (isset ($aDsnComponents['port'])) ? $aDsnComponents['port'] : '';
			$aDsnComponents['user'] = (isset ($aDsnComponents['user'])) ? $aDsnComponents['user'] : '';
			$aDsnComponents['pass'] = (isset ($aDsnComponents['pass'])) ? $aDsnComponents['pass'] : '';
			$aDsnComponents['path'] = (isset ($aDsnComponents['path'])) ? $aDsnComponents['path'] : '';
			
			$this->_rCnx = $this->_open ($aDsnComponents['host'], $aDsnComponents['port'], $aDsnComponents['user'], $aDsnComponents['pass'], $bIsPersistant);

			if ($this->_rCnx === false) {
				$this->_iConnectionState = DbState::Closed;
				$this->_aErrors[] = 'Unable to connect at the Database';
				Loader::loadBase ('Exceptions.DatabaseException');
				throw new DatabaseException (DatabaseException::CONNECTION_FAILED);
			}
			
			$this->_iConnectionState = DbState::Open;
			
			$sDbName = substr ($aDsnComponents['path'], 1);
			if (!empty ($sDbName)) {
				$this->_selectDb ($sDbName);
				$this->_iConnectionState = DbState::Ready;
			}
		}
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
	}
	
	/**
	 * @name public close
	 * Close the link to the Db if it's not already the case
	 * 
	 * @return void
	 */
	public function close () {
		if ($this->_iConnectionState != DbState::Closed) {
			$this->_close ();
			$this->_iConnectionState = DbState::Closed;
		}
	}
	
	/**
	 * @name public changeDb
	 * Select an other Database
	 *
	 * @param String $sDbName
	 * 
	 * @return void
	 */
	public function changeDb ($sDbName) {
		if (!is_string ($sDbName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_iConnectionState = aDrivers::Open;
		
		if ($this->_selectDb ($sDbName) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::DBSELECT_FAILED, $sLastError);
		}
		
		$this->_iConnectionState = aDrivers::Ready;
	}
	
	/**
	 * @name public prepare
	 * Prepare a specified query by modifying the Args using the escape method
	 *
	 * @param String $sQuery
	 * @param Array $aArgs
	 * 
	 * @return String
	 */
	public function prepare ($sQuery, $aArgs) {
		if (!is_string ($sQuery)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$iArgCount = count ($aArgs);
		for ($i = $iArgCount-1; $i >= 0; $i--) {
			$sQuery = str_replace (('@'.($i+1)), $this->escape ($aArgs[$i]), $sQuery);
		}
		
		return $sQuery;
	}
	
	/**
	 * @name public escape
	 * Escape a value to avoid sql injection or xss attacks
	 *
	 * @param Mixed $mUnescaped
	 * 
	 * @return Mixed
	 */
	public function escape ($mUnescaped) {
		if (!is_scalar ($mUnescaped)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::INTEGER_NEEDED);
		}
	
		if (get_magic_quotes_gpc ()) {
			$sRequest = stripslashes ($mUnescaped);
		}
		
		return (is_numeric ($mUnescaped)) ? $mUnescaped : "'".$this->_escape ($mUnescaped)."'";
	}
	
	/**
	 * @name public executeReader
	 * Execute the Request and return an instance of DbReader to parse the results
	 *
	 * @param Mixed $mRequest
	 * @param Int $iStart
	 * @param Int $iLimit
	 * 
	 * @return DbReader
	 */
	public function executeReader ($mRequest, $iStart = 0, $iLimit = -1) {
		if (!is_int ($iStart)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iLimit)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::INTEGER_NEEDED);
		}
		
		$this->_iConnectionState = DbState::Executing;
		
		if ($this->_bAutoPrepareQueries && is_array ($mRequest))
			$sRequest = $this->prepare (array_shift ($mRequest), $mRequest);
		else if (is_string ($mRequest))
			$sRequest = $mRequest;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if (($rResult = $this->_query ($sRequest)) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::QUERY_FAILED, $sLastError);
		}
		
		$this->_iConnectionState = DbState::Ready;
		
		Loader::loadBase ('Database.DbReader');
		
		return new DbReader ($this, $rResult, $iStart, $iLimit);
	}
	
	/**
	 * @name public executeLine
	 * Execute the Request and return the first line of the result
	 *
	 * @param Mixed $mRequest
	 * 
	 * @return Array
	 */
	public function executeLine ($mRequest) {
		if ($this->_bAutoPrepareQueries && is_array ($mRequest))
			$mRequest = $this->prepare (array_shift ($mRequest), $mRequest);
		else if (is_string ($mRequest))
			$mRequest = $mRequest;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$this->_iConnectionState = DbState::Executing;
		
		if (($rResult = $this->_query ($mRequest)) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::QUERY_FAILED, $sLastError);
		}
		
		$this->_iConnectionState = DbState::Fetching;
		
		if (($aFetch = $this->_fetch ($rResult)) === false) {
			$this->_aErrors[] = 'Unable to fetch the result, do you have rows from the request ?';
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::FETCH_FAILED);
		}
		
		$this->_iConnectionState = DbState::Ready;
		
		return $aFetch;
	}
	
	/**
	 * @name public executeScalar
	 * Execute the specified request and return the first colonne of the first line of the result
	 *
	 * @param Mixed $mRequest
	 * 
	 * @return Mixed
	 */
	public function executeScalar ($mRequest) {
		if ($this->_bAutoPrepareQueries && is_array ($mRequest))
			$mRequest = $this->prepare (array_shift ($mRequest), $mRequest);
		else if (is_string ($mRequest))
			$mRequest = $mRequest;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$this->_iConnectionState = DbState::Executing;
		
		if (($rResult = $this->_query ($mRequest)) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::QUERY_FAILED, $sLastError);
		}
		
		$this->_iConnectionState = DbState::Fetching;
		
		if (($aFetch = $this->_fetch ($rResult)) === false) {
			$this->_aErrors[] = 'Unable to fetch the result, do you have rows from the request ?';
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::FETCH_FAILED);
		}
		
		$this->_iConnectionState = DbState::Ready;
		
		return array_shift ($aFetch);
	}
	
	/**
	 * @name public executeNonQuery
	 * Execute the specified Request and return nothing
	 *
	 * @param Mixed $mRequest
	 * 
	 * @return void
	 */
	public function executeNonQuery ($mRequest) {
		if ($this->_bAutoPrepareQueries && is_array ($mRequest))
			$mRequest = $this->prepare (array_shift ($mRequest), $mRequest);
		else if (is_string ($mRequest))
			$mRequest = $mRequest;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}

		if ($this->_query ($sRequest) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::QUERY_FAILED, $sLastError);
		}
	}
	
	/**
	 * @name public count
	 * Count the number of rows the resource uses
	 * 
	 * @param Resource $rResult
	 * 
	 * @return Int
	 */
	public function count ($rResult) {
		if (!is_resource ($rResult)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::RESOURCE_NEEDED);
		}
		
		return @$this->_count ($rResult);
	}
	
	/**
	 * @name public fetch
	 * Fetch the Resource
	 *
	 * @param Resource $rResult
	 * 
	 * @return Mixed
	 */
	public function fetch ($rResult) {
		if (!is_resource ($rResult)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::RESOURCE_NEEDED);
		}
		
		return @$this->_fetch ($rResult);
	}
	
	/**
	 * @name public seek
	 * 
	 * @param Resource $rResult
	 * @param Int $iPosition
	 * 
	 * @return Void
	 */
	public function seek ($rResult, $iPosition) {
		if (!is_resource ($rResult)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::RESOURCE_NEEDED);
		}
		
		if (!is_int ($iPosition)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (@$this->_seek ($rResult, $iPosition) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::SEEK_FAILED, $sLastError);
		}
	}
	
	/**
	 * @name public freeResult
	 * Free a specified result
	 *
	 * @param Resource $rResult
	 * 
	 * @return void
	 */
	public function freeResult ($rResult) {
		if (!is_resource ($rResult)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::RESOURCE_NEEDED);
		}
		
		if (@$this->_freeResult ($rResult) === false) {
			$sLastError = $this->error ();
			$this->_aErrors[] = $sLastError;
			Loader::loadBase ('Exceptions.DatabaseException');
			throw new DatabaseException (DatabaseException::FREE_FAILED, $sLastError);
		}
	}
	
	/**
	 * @name public setAutoPrepare
	 * Indicate if the queries need to be auto prepared
	 *
	 * @param Boolean $bAutoPrepareQueries
	 * 
	 * @return void
	 */
	public function setAutoPrepare ($bAutoPrepareQueries) {
		$this->_bAutoPrepareQueries = (is_bool ($bAutoPrepareQueries)) ? $bAutoPrepareQueries : true;
	}

	/**
	 * List of all the Abstract method
	 */
	abstract protected function _open ($sHost, $iPort, $sLogin, $sPasswd, $bIsPersistant);
	abstract protected function _close ();
	abstract protected function _selectDb ($sDbName);
	abstract protected function _escape ($mUnescaped);
	abstract public function error ();
	abstract public function version ();
	abstract public function lastInsertId ();
	abstract public function affectedRows ();
	abstract protected function _query ($sQuery);
	abstract protected function _fetch ($rResult);
	abstract protected function _count ($rResult);
	abstract protected function _seek ($rResult, $iPosition);
	abstract protected function _freeResult ($rResult);
	abstract protected function beginTransaction ($sTransactionName = null);
}
?>