<?php
/**
 * @name Request
 * Used to recover user's vars (like POST, GET, FILES, COOKIES) with security options
 * 
 * @package Catapult.Controller.Request
 * @filesource Request.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 05/03/2008
 * 
 * @see http://julien-pauli.developpez.com/tutoriels/php/mvc-controleur/
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
class Request {
	/**
	 * @var Array $_aArgs
	 * Contain the arguments founded by the Router class
	 */
	private $_aArgs;

	/**
	 * @var protected Int $_iMaxArgs
	 * Contain the max number of argument that are allowed to be parsed
	 * 
	 * @default : 20
	 */
	protected $_iMaxArgs = 20;
	
	/**
	 * @var protected Boolean $_bParseArgs
	 * Indicate if parse or not the Args givens
	 * 
	 * @default : true
	 */
	protected $_bParseArgs = true;
	
	/**
	 * @name public function __construct
	 * Constructor
	 * Retrieve the values founded by the Router and recover the configuration from the config file.
	 *
	 * @param Array $aArgs
	 * @param Config $oConfig
	 */
	public function __construct ($aArgs, Config $oConfig) {
		if (!is_array ($aArgs)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if (!is_object ($oConfig) || !($oConfig instanceof Config)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::OBJECT_NEEDED);
		}
		
		$this->_aArgs = $aArgs;
		$this->_iMaxArgs = $oConfig->get ('Request.Security.MaxArgs', 10);
		$this->_bParseArgs = $oConfig->get ('Request.Security.ParseArgs', true);
	}

	/**
	 * @name public function getArg
	 * Return an asked var by its key (or its id)
	 * 
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	public function getArg ($sKey) {
		return ($this->_bParseArgs) ? 
			 $this->_protectVar ($this->_getParam ($sKey)) :
			$this->_getParam ($sKey);
	}
	
	/**
	 * @name public function getValidatedArg
	 * Return an asked var by its key if it is validated
	 * 
	 * @param String $sKey
	 * @param String $sKindValidation, default string
	 * @param Mixed $mOptions, default null
	 * 
	 * @return Mixed
	 */
	public function getValidatedArg ($sKey, $sKindValidation = 'Alpha', $mOptions = null) {
		if (!is_string ($sKey)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sKindValidation)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		Loader::loadBase ('Libraries.Validate.Validate');
		return (Validate::isValide ($sKindValidation, $sKey, $mOptions)) ? $sKey : null;
	}

	/**
	 * @name public function getArgs
	 * Return an array of arguments given in the url
	 *
	 * @return Array
	 */
	public function getArgs () {
		if (is_array ($this->_aArgs) && (count ($this->_aArgs) < $this->_iMaxArgs || $this->_iMaxArgs == 0)) {
			if (!$this->_bParseArgs)
				return $this->_aArgs;
			else {
				$aArgs = array ();
				foreach ($this->_aArgs as $sKey=>$sValue) {
					$aArgs[$sKey] = $this->_protectVar ($sValue);
				}
				return $aArgs;
			}
		}
		else
			return array ();
	}
	
	/**
	 * @name private function _getParam
	 * try to find where the param could be found and return it if it's find
	 *
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	private function _getParam ($sKey) {
		if (isset ($this->_aArgs[$sKey]))
			return $this->_aArgs[$sKey];
		elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$sKey]))
			return $_POST[$sKey];
		elseif (isset($_GET[$sKey]))
			return $_GET[$sKey];
		elseif (isset ($_FILES[$sKey]))
			return $_FILES[$sKey];
		elseif (isset ($_SESSION[$sKey]))
			return $_SESSION[$sKey];
		elseif (isset ($_COOKIE[$sKey]))
			return $_COOKIE[$sKey];
		elseif (isset ($_SERVER[$sKey]))
			return $_SERVER[$sKey];
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name private function _protectVar
	 * Protect a value if $this->_bParseArgs is set to true
	 * 
	 * @param Mixed $mValue
	 * 
	 * @return Mixed
	 */
	private function _protectVar ($mValue) {
		if (preg_match ('/^[0-9\-]+$/i', $mValue))
			settype ($mValue, 'int');
		elseif (preg_match ('/^[0-9\,\.\-]+$/i', $mValue))
			settype ($mValue, 'float');
		else
			$mValue = filter_var ($mValue, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		
		return $mValue;
	}
}
?>