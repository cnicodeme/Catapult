<?php
Loader::loadBase ('Database.iTransaction');

/**
 * @name MysqlTransaction implements iTransaction
 * Load a specified config file and allow the application to use it
 * 
 * @package Catapult.Database.Drivers.Mysql.MysqlTransaction
 * @filesource MysqlTransaction.php
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
class MysqlTransaction extends iTransaction {
	/**
	 * @name public begin
	 * Begin a Transaction with an optional name
	 *
	 * @param String $sTransactionName
	 * 
	 * @return void
	 */
	public function begin ($sTransactionName = null) {
		if (isset ($sTransactionName) && is_string ($sTransactionName))
			$this->_oInstance->executeNonQuery ('START TRANSACTION '.$sTransactionName);
		elseif (!isset ($sTransactionName))
			$this->_oInstance->executeNonQuery ('START TRANSACTION');
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
	}
	
	/**
	 * @name public commit
	 * Commit a Transaction with an optional name
	 *
	 * @param String $sTransactionName
	 * 
	 * @return void
	 */
	public function commit ($sTransactionName = null) {
		if (isset ($sTransactionName) && is_string ($sTransactionName))
			$this->_oInstance->executeNonQuery ('COMMIT '.$sTransactionName);
		elseif (!isset ($sTransactionName))
			$this->_oInstance->executeNonQuery ('COMMIT');
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
	}
	
	/**
	 * @name public rollBack
	 * RollBack a Transaction with an optional name
	 *
	 * @param String $sTransactionName
	 * 
	 * @return void
	 */
	public function rollBack ($sTransactionName = null) {
		if (isset ($sTransactionName) && is_string ($sTransactionName))
			$this->_oInstance->executeNonQuery ('ROLLBACK '.$sTransactionName);
		elseif (!isset ($sTransactionName))
			$this->_oInstance->executeNonQuery ('ROLLBACK');
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
	}
	
	/**
	 * @name public count
	 * Count how many actual transaction there is
	 * 
	 * @return void
	 */
	public function count () {
		Loader::loadBase ('Exceptions.NotSupportedException');
		throw new NotSupportedException ('MysqlTransaction::count');
	}
	
	/**
	 * @name public savePoint
	 * Create a save point
	 *
	 * @param String $sSavePointName
	 * 
	 * @return void
	 */
	public function savePoint ($sSavePointName) {
		if (!is_string ($sSavePointName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_oInstance->executeNonQuery ('SAVEPOINT '.$sSavePointName); 
	}
	
	/**
	 * @name public rollBackToSavePoint
	 * Roll back to a saved point
	 *
	 * @param String $sSavePointName
	 * 
	 * @return void
	 */
	public function rollBackToSavePoint ($sSavePointName) {
		if (!is_string ($sSavePointName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_oInstance->executeNonQuery ('ROLLBACK TO SAVEPOINT '.$sSavePointName); 
	}
	
	/**
	 * @name public releaseSavePoint
	 * Release a saved point
	 *
	 * @param String $sSavePointName
	 * 
	 * @return void
	 */
	public function releaseSavePoint ($sSavePointName) {
		if (!is_string ($sSavePointName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_oInstance->executeNonQuery ('RELEASE SAVEPOINT '.$sSavePointName); 
	}
	
}
?>