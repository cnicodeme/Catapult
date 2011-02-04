<?php
/**
 * @name aTransactions
 * @abstract
 * Abstract class to use the transactions
 * 
 * @package Catapult.Database
 * @filesource aTransactions.php
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
abstract class aTransaction {
	protected $_oInstance;
	
	/**
	 * @name public function __construct
	 * Constructor
	 * Retrieve the current instance of aDbManager
	 *
	 * @param aDbManager $oInstance
	 */
	public function __construct (aDbManager $oInstance) {
		$this->_oInstance = $oInstance;
	}
	
	/**
	 * Abstracted Methods
	 */
	abstract public function begin ($sTransactionName = null);
	abstract public function commit ($sTransactionName = null);
	abstract public function rollBack ($sTransactionName = null);
	abstract public function count ();
	abstract public function savePoint ($sSavePointName);
	abstract public function rollBackToSavePoint ($sSavePointName);
	abstract public function releaseSavePoint ($sSavePointName);
}
?>