<?php
/**
 * Loading Catapult.Database.aDbManager
 */
Loader::loadBase ('Database.aDbManager');

/**
 * @name Mysql extends aDbManager
 * Mysql driver for the aDbManager object
 * 
 * @package Catapult.Database.Drivers.Mysql
 * @filesource Mysql.php
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
class Mysql extends aDbManager {
	/**
	 * @name protected _open
	 * Open a connection in persistant link or not
	 *
	 * @param String $sHost
	 * @param Int $iPort
	 * @param String $sLogin
	 * @param String $sPasswd
	 * @param Boolean $bIsPersistant
	 * 
	 * @return boolean
	 */
	protected function _open ($sHost, $iPort, $sLogin, $sPasswd, $bIsPersistant) {
		if ($bIsPersistant)
			return @mysql_pconnect ($sHost.':'.$iPort, $sLogin, $sPasswd);
		else
			return @mysql_connect ($sHost.':'.$iPort, $sLogin, $sPasswd);
	}
	
	/**
	 * @name protected _close
	 * Close the connection
	 *
	 * @return Boolean
	 */
	protected function _close () {
		return @mysql_close ($this->_rCnx);
	}
	
	/**
	 * @name protected _selectDb
	 * Select a specified Database
	 *
	 * @return Boolean
	 */
	protected function _selectDb ($sDbName) {
		return @mysql_select_db ($sDbName, $this->_rCnx);
	}
	
	/**
	 * @name protected _escape
	 * Escape a given scalar
	 *
	 * @return Mixed
	 */
	protected function _escape ($mUnescaped) {
		return @mysql_real_escape_string ($mUnescaped, $this->_rCnx);
	}
	
	/**
	 * @name public error
	 * Return the last error occured
	 *
	 * @return String
	 */
	public function error () {
		return @mysql_error ($this->_rCnx);
	}
	
	/**
	 * @name public version
	 * Return the version of the Server
	 *
	 * @return String
	 */
	public function version () {
		return @mysql_get_server_info ($this->_rCnx);
	}
	
	/**
	 * @name public lastInsertId
	 * Return the last insert id
	 *
	 * @return Int
	 */
	public function lastInsertId () {
		return @mysql_insert_id ($this->_rCnx);
	}
	
	/**
	 * @name public affectedRows
	 * Return the number of affected Rows
	 *
	 * @return Int
	 */
	public function affectedRows () {
		return @mysql_affected_rows ($this->_rCnx);
	}
	
	/**
	 * @name protected _query
	 * Execute the given query
	 *
	 * @return Mixed
	 */
	protected function _query ($sQuery) {
		return @mysql_query ($sQuery, $this->_rCnx);
	}
	
	/**
	 * @name protected _fetch
	 * Fetch the given resource
	 *
	 * @return Mixed
	 */
	protected function _fetch ($rResult) {
		return @mysql_fetch_assoc ($rResult);
	}
	
	/**
	 * @name protected _count
	 * Count the number of lines
	 *
	 * @return Int
	 */
	protected function _count ($rResult) {
		return @mysql_num_rows ($rResult);
	}
	
	/**
	 * @name protected _seek
	 * Seek the resource to a specified position
	 *
	 * @return Boolean
	 */
	protected function _seek ($rResult, $iPosition) {
		return @mysql_data_seek ($rResult, $iPosition);
	}
	
	/**
	 * @name protected _freeResult
	 * Set the resource free
	 *
	 * @return Boolean
	 */
	protected function _freeResult ($rResult) {
		return @mysql_free_result ($rResult);
	}
	
	/**
	 * @name protected function beginTransaction
	 * Begin a transaction with a given name (optionnal)
	 * 
	 * @param String $sTransactionName
	 * 
	 * @return 
	 */
	protected function beginTransaction ($sTransactionName = null) {
		if (!$this->_oTransactionInstance instanceof aTransaction) {
			Loader::loadBase('Database.Drivers.Mysql.MysqlTransaction');
			$this->_oTransactionInstance = new MysqlTransaction ($this);
		}
		
		return $this->_oTransactionInstance->begin ($sTransactionName);
	}
	
}
?>