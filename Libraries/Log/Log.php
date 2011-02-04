<?php
/**
 * @name Log
 * Use to log informations on a file
 * 
 * @package Catapult.Libraries.Log
 * @filesource Log.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 12/03/08
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
class Log {
	/**
	 * @var private String $_sLogPath
	 * Contain the file path to the log
	 */
	private $_sLogPath = 'errors.log';
	
	/**
	 * @var private static Log $_oInstance
	 * Used for the singleton method (getInstance), contain an instance of Log
	 */
	private static $_oInstance;

	/**
	 * @name public static function getInstance
	 * Method used as a Singleton. Retrieve an instance of Log
	 *
	 * @return Log
	 */
	public static function getInstance () {
		if (!(self::$_oInstance instanceof self))
			self::$_oInstance = new Log ();
		
		return self::$_oInstance;
	}
	
	/**
	 * @name private function __construct
	 * Constructor. Set the log path
	 */
	private function __construct () {
		$oConfig = Config::getInstance();
		$this->_sLogPath = $oConfig->get ('Libraries.Log.Path', 'errors.log');
	}
	
	/**
	 * @name public function __clone
	 * Clone is not allowed ! :)
	 */
	public function __clone () {
		Loader::loadBase ('Exceptions.CloneNotSupportedException');
		throw new CloneNotSupportedException ();
	}
	
	/**
	 * @name public function __set
	 * Setter, used to set the path of the log
	 *
	 * @param String $sKey
	 * @param String $sValue
	 * 
	 * @return Void
	 */
	public function __set ($sKey, $sValue) {
		if (!is_string ($sKey)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (strtolower ($sKey) == 'logpath' || strtolower ($sKey) == 'path')
			$this->_sLogPath = $sValue;
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name public function writeContent
	 * Write the specified content into the log file and add a break line at the end of the file
	 *
	 * @param String $sMessage
	 * 
	 * @return Void
	 */
	public function writeContent ($sMessage) {
		if (@error_log ($sMessage, 3, $this->_sLogPath) === false) {
			Loader::loadBase ('Exceptions.IOException');
			throw new IOException (IOException::WRITE, $this->_sLogPath);
		}
	}
	
	/**
	 * @name public static function write
	 * Method called in static to simplify the log writing for the user
	 *
	 * @param String $sMessage
	 * 
	 * @return Void
	 */
	public static function write ($sMessage) {
		Log::getInstance ()->writeContent ($sMessage);
	}
}
?>