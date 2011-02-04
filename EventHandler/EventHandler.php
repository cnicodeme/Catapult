<?php
/**
 * @name EventHandler
 * Used to handle uncatched Exceptions or errors.
 * All the catched events (Exceptions & Errors) could be logged and displayed or redirected
 * 
 * @package Catapult.EventHandler
 * @filesource EventHandler.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 03/03/2008
 * 
 * @see : http://fr.php.net/set_exception_handler
 * @see : http://fr3.php.net/manual/en/function.set-error-handler.php
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
class EventHandler {
	/**
	 * @var EventHandler $_oInstance
	 * Static variable used for the singleton method (getInstance)
	 */
	private static $_oInstance;
	
	/**
	 * @var Array $_aConfig
	 * Contain a list of parameters
	 */
	private $_aConfig = array ( 'Display' 			=> true, 			// displayErrors
								'DisplayErrors' 	=> E_ALL,
								'Log' 				=> true, 			// logErrors
								'LogErrors' 		=> E_ALL,
								'RedirectPath' 		=> 'error.html',
								'RedirectErrors' 	=> 0,
								'IgnoreRepeated' 	=> true); 			// ignoreRepeatedErrors
	
	/**
	 * @var Array $_aErrorsType
	 * Contain all the errors catachable
	 */
	private $_aErrorsType = array ( E_ERROR 			=> 'ERROR',					// 1
									E_WARNING 			=> 'WARNING',				// 2
									E_STRICT 			=> 'STRICT',				// 2048
									E_PARSE 			=> 'PARSE',					// 4
									E_NOTICE 			=> 'NOTICE',				// 8
									E_CORE_ERROR 		=> 'CORE ERROR',			// 16
									E_CORE_WARNING 		=> 'CORE WARNING',			// 32
									E_COMPILE_ERROR 	=> 'COMPILE ERROR',			// 64
									E_COMPILE_WARNING 	=> 'COMPILE WARNING',		// 128
									E_USER_ERROR 		=> 'USER ERROR',			// 256
									E_USER_WARNING 		=> 'USER WARNING',			// 512
									E_USER_NOTICE 		=> 'USER NOTICE',			// 1024
									E_RECOVERABLE_ERROR	=> 'RECOVERABLE ERROR',		// 4096
									E_ALL				=> 'ALL');					// 6143
	/**
	 * @var Array $_aOccuredErrors
	 * Contain a trace of all events occured
	 */
	private $_aOccuredErrors = array ();
	
	/**
	 * @name public static function getInstance
	 * Singleton. Retrieve an unique instance of EventHandler Class
	 *
	 * @return EventHandler
	 */
	public static function getInstance () {
		if (!(self::$_oInstance instanceof self))
			self::$_oInstance = new EventHandler ();
		
		return self::$_oInstance;
	}
	
	/**
	 * @name private function __construct
	 * Constructor. Set the variables, from the config file or by default
	 */
	private function __construct () {
		$oConfig = Config::getInstance();
	
		$this->_aConfig['Display'] 			= $oConfig->get ('EventHandler.Display', true);
		$this->_aConfig['DisplayErrors'] 	= $oConfig->get ('EventHandler.DisplayErrors', E_ALL);
	
		$this->_aConfig['Log'] 				= $oConfig->get ('EventHandler.Log', true);
		$this->_aConfig['LogErrors'] 		= $oConfig->get ('EventHandler.LogErrors', E_ALL);
	
		$this->_aConfig['RedirectPath'] 	= $oConfig->get ('EventHandler.RedirectPath', 'error.html');
		$this->_aConfig['RedirectErrors'] 	= $oConfig->get ('EventHandler.RedirectErrors', (E_ALL ^ $this->_aConfig['DisplayErrors']));
	
		$this->_aConfig['IgnoreRepeated'] 	= $oConfig->get ('EventHandler.IgnoreRepeated', true);
	
		@set_error_handler (array($this, 'errorHandler'));
		@set_exception_handler (array($this, 'exceptionHandler'));
	}
	
	/**
	 * @name public function __clone
	 * Clone is forbidden :)
	 */
	public function __clone () {
		Loader::loadBase ('Exceptions.CloneNotSupportedException');
		throw new CloneNotSupportedException ();
	}
	
	/**
	 * @name public function __get
	 * Magic method __get ; Return an asked var
	 *
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	public function __get ($sKey) {
		if (isset ($this->_aConfig[$sKey]))
			return $this->_aConfig[$sKey];
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name public function __set
	 * Magic method __set ; Set a var with it specified value
	 *
	 * @param String $sKey
	 * @param String $sValue
	 */
	public function __set ($sKey, $mValue) {
		if (!isset ($this->_aConfig[$sKey])) {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
		if (!is_scalar ($mValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::SCALAR_NEEDED);
		}
		
		$this->_aConfig[$sKey] = $mValue;
	}
	
	/**
	 * @name private function _findBin
	 * Return true if a binary values is in the Container
	 *
	 * @param Int $iSearch
	 * @param Int $iContainer
	 * 
	 * @return Boolean
	 */
	private function _findBin ($iSearch, $iContainer) {
		$iBin = decbin ($iContainer);
		$iTotal = strlen (decbin ($iContainer));

		for ($i = 0; $i < $iTotal; $i++) {
			if ($iBin{$i} != 0 && bindec(str_pad($iBin{$i}, $iTotal - $i, 0)) == $iSearch)
				return true;
    	}
    	
    	return false;
	}
	
	/**
	 * @name private function _eventManger
	 * Manager an Error or an Exception : Log, Display, Redirect relativly of the directives
	 *
	 * @param Strin $sMessage
	 * @param Int $iCode
	 * @param String $sFile
	 * @param Int $iLine
	 * @param Int $iError (Optionnal)
	 * 
	 *  @return void
	 */
	private function _eventManager ($sMessage, $iCode, $sFile, $iLine, $iError = null, $sExceptionClassname = null) {
		if (!isset ($iError)) $iError = $iCode;
		
		$sErrorMsg = '['.date ('d/m/Y H:i:s'). '] - ';
		$sErrorMsg .= (isset ($this->_aErrorsType[$iCode])) ? $this->_aErrorsType[$iCode] : $sExceptionClassname;
		$sErrorMsg .= ' -> '.$sMessage.' ('.$sFile.', line '.$iLine.')'."\n";

		$sCheckSum = md5 ($sFile.$iLine.$sMessage);
		$this->_aOccuredErrors[$sCheckSum] = (isset ($this->_aOccuredErrors[$sCheckSum])) ? $this->_aOccuredErrors[$sCheckSum]+1 : 1;
		
		if (!$this->_aConfig['IgnoreRepeated'] || ($this->_aConfig['IgnoreRepeated'] && $this->_aOccuredErrors[$sCheckSum] == 1)) {
			if ($this->_aConfig['Log'] && $this->_findBin ($iError, $this->_aConfig['LogErrors'])) {
				try {
					Loader::loadBase('Libraries.Log.Log');
					Log::write($sErrorMsg);
				}
				catch (Exception $oE) {
					// Avoid inifinte loop ^^
					die ("A Fatal Error occured : ".$oE->getMessage());
				}
			}
		
			if ($this->_aConfig['Display'] && $this->_findBin ($iError, $this->_aConfig['DisplayErrors']))
				echo $sErrorMsg;
			elseif ($this->_findBin ($iError, $this->_aConfig['RedirectErrors'])) {
				$sScriptUri = isset ($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
				if (strpos ($sScriptUri, $this->_aConfig['RedirectPath']) !== false)
					die ('FATAL ERROR : Cannot redirect error page on itself !');
				else
					header ('location:'.$this->_aConfig['RedirectPath']);
			}
		}
		
		if ($this->_aOccuredErrors[$sCheckSum] > 100) {
			Loader::loadBase ('Exceptions.BufferOverflowException');
			throw new BufferOverflowException (BufferOverflowException::INFINITE_LOOP);
		}

	}
	
	/**
	 * @name errorHandler
	 * Handle an error
	 *
	 * @param Int $iNumber
	 * @param String $sMessage
	 * @param String $sFile
	 * @param Int $iLine
	 * 
	 * @return Boolean (True)
	 * 
	 * @See http://fr3.php.net/manual/fr/ref.errorfunc.php#ini.error-reporting
	 */
	public function errorHandler ($iNumber, $sMessage, $sFile, $iLine) {
		if (error_reporting () != 0)
			$this->_eventManager ($sMessage, $iNumber, $sFile, $iLine);
		
		return true;
	}

	/**
	 * @name public function exceptionHandler
	 * Handle a uncaught exception
	 *
	 * @param Exception $oException
	 * 
	 * @return Void
	 */
	public function exceptionHandler (Exception $oException) {
		$aTrace = $oException->getTrace ();
		$this->_eventManager ($oException->getMessage(), $oException->getCode(), $oException->getFile(), $oException->getLine(), E_WARNING, $aTrace[0]['class']);
	}
}
?>