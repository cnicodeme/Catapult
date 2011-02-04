<?php
/**
 * @name Config
 * Load a specified config file and allow the application to use it
 * 
 * @package Catapult.Config
 * @filesource Config.php
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
class Config {
	/**
	 * @var private Static Config $_oInstance
	 * Static var used for the singleton to recover an instance of Config
	 */
	private static $_oInstance;
	
	/**
	 * @var private Array $_aConfig
	 * Contain all the configurations specified into the loaded file
	 */
	private $_aConfig;
	
	/**
	 * @name public static getInstance
	 * Used as Singleton, retrieve an instance of Config class
	 *
	 * @return Config
	 */
	public static function getInstance () {
		if (!(self::$_oInstance instanceof self))
			self::$_oInstance = new Config ();
		
		return self::$_oInstance;
	}
	
	/**
	 * @name private function __construct
	 * Constructor, not authorized to be called from outside
	 */
	private function __construct () {
		/* Cannot instanciate from outside */
	}
	
	/**
	 * @name public function __clone
	 * Clone is not allowed !
	 */
	public function __clone () {
		Loader::loadBase ('Exceptions.CloneNotSupportedException');
		throw new CloneNotSupportedException ();
	}

	/**
	 * @name public function __get
	 * Magic method used to return specifics values available into the Config::$_aConfig var
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
	 * @name public function get
	 * Retrieve a config as a namespace (parent.firstchild.childchild.value)
	 * or the $mReplace value if does not exists
	 * or null if $mReplace does not exists
	 * 
	 * @param String $sElement
	 * @param Mixed $mReplace (Optionnal)
	 * 
	 * @return Mixed
	 */
	public function get ($sElement, $mReplace = null) {
		if (!is_string ($sElement)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (isset ($mReplace) && !is_scalar ($mReplace)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::SCALAR_NEEDED);
		}
		
		$aElt = explode ('.', $sElement);
		$aCurrentArray = $this->_aConfig;

		foreach ($aElt as $sElement) {
			if (isset ($aCurrentArray[$sElement]))
				$aCurrentArray = $aCurrentArray[$sElement];
			elseif (isset ($mReplace))
				return $mReplace;
			else
				return null;
		}
		return $aCurrentArray;
	}
	
	/**
	 * @name public loadFromPhp
	 * Load a specified Php file and save it into Config::$_aConfig var
	 *
	 * @param String $sFilePath
	 * 
	 * @return void
	 */
	public function loadFromPhp ($sFilePath) {
		if (!is_string ($sFilePath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!file_exists ($sFilePath)) {
			Loader::loadBase ('Exceptions.NotFoundException');
			throw new NotFoundException ($sFilePath);
		}
		
		if (!is_readable ($sFilePath)) {
			Loader::loadBase ('Exceptions.IOException');
			throw new IOException (IOException::READ, $sFilePath);
		}

		include $sFilePath;
		
		if (isset ($config) && is_array ($config))
			$this->_aConfig = $config;
    	else {
    		$sArrayName = substr (basename ($sFilePath), 0, strrpos (basename ($sFilePath), '.'));
    		if ($sArrayName != 'config' && isset ($sArrayName) && is_array ($sArrayName))
    			$this->_aConfig = $sArrayName;
    		else {
    			Loader::loadBase ('Exceptions.InvalidFormatException');
				throw new InvalidFormatException (InvalidFormatException::VARIABLE_FORMAT);
    		}
    	}
	}
	
	/**
	 * @name public loadFromIni
	 * Load a specified Ini file and save it into Config::$_aConfig var
	 *
	 * @param String $sFilePath
	 * 
	 * @return void
	 */
	public function loadFromIni ($sFilePath) {
		if (!is_string ($sFilePath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!file_exists ($sFilePath)) {
			Loader::loadBase ('Exceptions.NotFoundException');
			throw new NotFoundException ($sFilePath);
		}
		
		if (!is_readable ($sFilePath)) {
			Loader::loadBase ('Exceptions.IOException');
			throw new IOException (IOException::READ, $sFilePath);
		}
		
		$this->_aConfig = parse_ini_file ($sFilePath);
	}
	
	/**
	 * @name public loadFromXml
	 * Load a specified Xml file and save it into Config::$_aConfig var
	 *
	 * @param String $sFilePath
	 * 
	 * @return void
	 */
	public function loadFromXml ($sFilePath) {
		if (!is_string ($sFilePath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!file_exists ($sFilePath)) {
			Loader::loadBase ('Exceptions.NotFoundException');
			throw new NotFoundException ($sFilePath);
		}
		
		if (!is_readable ($sFilePath)) {
			Loader::loadBase ('Exceptions.IOException');
			throw new IOException (IOException::READ, $sFilePath);
		}
		
		if (($oXml = @simplexml_load_file ($sFilePath)) === false) {
			Loader::loadBase ('Exceptions.ParseException');
			throw new ParseException ($sFilePath);
		}
		
		$this->_aConfig = $this->_parseXml ($oXml);
	}
	
	/**
	 * @name private function _parseXml
	 * Use to add values from the xml into the array
	 *
	 * @param Objet $oXml
	 * @param Array $aContent
	 * 
	 * @return Array
	 */
	private function _parseXml ($oXml, $aContent = array ()) {
		foreach ($oXml as $sKey=>$sValue) {
			if (count ($sValue) > 0) {
				$aContent[$sKey] = array ();
				$aContent[$sKey] = $this->_parseXml ($sValue, $aContent[$sKey]);
			}
			else
				$aContent [$sKey] = (string) $sValue;
		}

		return $aContent;
	}
}
?>