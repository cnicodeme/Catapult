<?php
/**
 * @name Loader
 * Used to load specifics files
 * 
 * @package Catapult.Loader
 * @filesource Loader.php
 * 
 * @author Cyril Nicodème
 * @version 0.2
 * 
 * @since 03/03/2008
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
class Loader {
	/**
	 * @var private static Loader $_oInstance
	 * Used for the singleton
	 */
	private static $_oInstance;
	
	/**
	 * @var private Array $_aExts
	 * Contain the specifics Extensions for each kind of elements
	 */
	private $_aExts = array ('Controller' => '.php',
							 'Model' => '.php',
							 'View' => '.phtml');
	
	/**
	 * @var private Array $_aPaths
	 * Contain the specifics Paths for each kind of elements
	 */
	private $_aPaths = array ('Base' => '',
							  'App' => '',
							  'Controller' => '',
							  'Model' => '',
							  'View' => '');
	
	/**
	 * @name public static getInstance
	 * Singleton
	 *
	 * @return Loader
	 */
	public static function getInstance () {
		if (!isset (self::$_oInstance) || !(self::$_oInstance instanceof self))
			self::$_oInstance = new Loader ();
		
		return self::$_oInstance;
	}
	
	/**
	 * @name public __construct
	 * Constructor
	 * Set the different values indicates into the Config files or defaults values
	 */
	private function __construct () {
		$oConfig = Config::getInstance ();

		// Setting the Paths
		$this->_aPaths ['Base'] 		= $oConfig->get ('Loader.Paths.Base', substr (dirname (__FILE__), 0, -6));
		$this->_aPaths ['App'] 			= $oConfig->get ('Loader.Paths.App', dirname ($_SERVER['SCRIPT_FILENAME']).DIRECTORY_SEPARATOR);
		$this->_aPaths ['Controller'] 	= $oConfig->get ('Loader.Paths.Controller', 'Controllers');
		$this->_aPaths ['Model'] 		= $oConfig->get ('Loader.Paths.Model', 'Models');
		$this->_aPaths ['View'] 		= $oConfig->get ('Loader.Paths.View', 'Views');
		
		// Setting the Extensions
		$this->_aExts ['Controller'] 	= $oConfig->get ('Loader.Exts.Controller', '.php');
		$this->_aExts ['Model'] 		= $oConfig->get ('Loader.Exts.Model', '.php');
		$this->_aExts ['View'] 			= $oConfig->get ('Loader.Exts.View', '.phtml');
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
	 * @name public __set
	 * Magic methods to set specifics values, using [type][kind], with type = App (except for kind: Exts), Base, Controller, Model, View ; and King : Ext or Paths
	 *
	 * @param String $sKey
	 * @param String $sValue
	 * 
	 * @return Void
	 */
	public function __set ($sKey, $sValue) {
		if (substr (strtolower ($sKey), -4) == 'path') {
			$sRealKey = ucfirst (substr (strtolower ($sKey), 0, -4));
			if (isset ($this->_aPaths [$sRealKey]))
				$this->_aPaths [$sRealKey] = $sValue;
			else {
				Loader::loadBase ('Exceptions.IllegalKeyException');
				throw new IllegalKeyException ($sKey);
			}
		}
		else if (substr (strtolower ($sKey), -3) == 'ext') {
			$sRealKey = ucfirst (substr (strtolower ($sKey), 0, -3));
			if (isset ($this->_aExts [$sRealKey]))
				$this->_aExts [$sRealKey] = $sValue;
			else {
				Loader::loadBase ('Exceptions.IllegalKeyException');
				throw new IllegalKeyException ($sKey);
			}
		}
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name public __get
	 * Magic methods to get specifics values, using [type][kind], with type = App (except for kind: Exts), Base, Controller, Model, View ; and King : Ext or Paths
	 *
	 * @param String $sKey
	 * 
	 * @return String
	 */
	public function __get ($sKey) {
		if (substr (strtolower ($sKey), -4) == 'path') {
			$sRealKey = ucfirst (substr (strtolower ($sKey), 0, -4));
			if (isset ($this->_aPaths [$sRealKey]))
				return $this->_aPaths [$sRealKey];
			else {
				Loader::loadBase ('Exceptions.IllegalKeyException');
				throw new IllegalKeyException ($sKey);
			}
		}
		else if (substr (strtolower ($sKey), -3) == 'ext') {
			$sRealKey = ucfirst (substr (strtolower ($sKey), 0, -3));
			if (isset ($this->_aExts [$sRealKey]))
				return $this->_aExts [$sRealKey];
			else {
				Loader::loadBase ('Exceptions.IllegalKeyException');
				throw new IllegalKeyException ($sKey);
			}
		}
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name public base
	 * Load a specific base
	 *
	 * @param String $sPath
	 * 
	 * @return Void
	 */
	public function base ($sPath) {
		$this->_load ($this->_aPaths ['Base'].$this->_parsePath ($sPath).'.php');
	}
	
	/**
	 * @name public static loadBase
	 * Recover an instance of Loader and call the load method
	 *
	 * @param String $sPath
	 * 
	 * @return Void
	 */
	public static function loadBase ($sPath) {
		$oLoader = self::getInstance ();
		$oLoader->base ($sPath);
	}
	
	/**
	 * @name public controller
	 * Load a specific Controller
	 *
	 * @param String $sPath
	 * 
	 * @return void
	 */
	public function controller ($sPath) {
		$this->_load ($this->_aPaths ['App'].$this->_aPaths ['Controller'].DIRECTORY_SEPARATOR.$this->_parsePath ($sPath).$this->_aExts['Controller']);
	}
	
	/**
	 * @name public model
	 * Load a specific Model
	 *
	 * @param String $sPath
	 * 
	 * @return void
	 */
	public function model ($sPath) {
		$this->_load ($this->_aPaths ['App'].$this->_aPaths ['Model'].DIRECTORY_SEPARATOR.$this->_parsePath ($sPath).$this->_aExts['Model']);
	}
	
	/**
	 * @name public view
	 * Load a specific View
	 *
	 * @param String $sPath
	 * 
	 * @return void
	 */
	public function view ($sPath) {
		$this->_load ($this->_aPaths ['App'].$this->_aPaths ['View'].DIRECTORY_SEPARATOR.$this->_parsePath ($sPath).$this->_aExts['View']);
	}
	
	/**
	 * @name public getClassName
	 * Return the Class Name of a given Path
	 *
	 * @param Strign $sPath
	 * 
	 * @return String
	 */
	public function getClassName ($sPath) {
		return (strrpos ($sPath, '.') === false) ? $sPath : substr ($sPath, strrpos ($sPath, '.') + 1);
	}

	/**
	 * @name public function getRealPath
	 * Return the real path of a given well formed path with dots.
	 * Throw an exception if the asked path does not exists
	 *
	 * @param String $sPath
	 * @param String $sType, Optionnal, Default 'Base'
	 * @param Boolean $bCheckAvailability, Optionnal, Default true
	 * 
	 * @return String
	 */
	public function getRealPath ($sPath, $sType = 'Base', $bCheckAvailability = true) {
		$aAllowedPaths = array ('Base', 'Controller', 'Model', 'View'); // Base is implicitly allowed
		
		if (!in_array ($sType, $aAllowedPaths)) {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sType);
		}
		
		switch ($sType) {
			case 'Base':
				$sRealPath = $this->_aPaths ['Base'].$this->_parsePath ($sPath).$this->_aExts['Base'];
				break;
			default:
				$sRealPath = $this->_aPaths ['App'].$this->_aPaths [$sType].DIRECTORY_SEPARATOR.$this->_parsePath ($sPath).$this->_aExts[$sType];
				break;
		}
		
		if ($bCheckAvailability)
			$this->_checkPath ($sRealPath);
			
		return $sRealPath;
	}

	/**
	 * @name private function _parsePath
	 * Replace the dots by the correct (anti)slash specified by the DIRECTORY_SEPARATOR value.
	 * 
	 * @param String $sPath
	 * 
	 * @return String
	 */
	private function _parsePath ($sPath) {
		return str_replace ('.', DIRECTORY_SEPARATOR, $sPath);
	}

	/**
	 * @name private function _makePath
	 * Transform the dots into the appropriated slash for the paths and test if the asked file exists
	 * If not, throw an exception
	 *
	 * @param String $sPath
	 * @param String $sFileExt
	 * 
	 * @return String
	 */
	private function _checkPath ($sPath) {
		if (!file_exists ($sPath)) {
			require_once (substr (dirname (__FILE__), 0, -6).DIRECTORY_SEPARATOR.'Exceptions'.DIRECTORY_SEPARATOR.'NotFoundException.php');
			throw new NotFoundException ($sPath);
		}
		
		if (!is_readable ($sPath)) {
			require_once (substr (dirname (__FILE__), 0, -6).DIRECTORY_SEPARATOR.'Exceptions'.DIRECTORY_SEPARATOR.'NotFoundException.php');
			throw new NotFoundException ($sPath);
		}
		
		return true;
	}
	
	/**
	 * @name private _load
	 * Method used to load the elements
	 *
	 * @param String $sPath
	 * @param String $sFileExt
	 * 
	 * @return Void
	 */
	private function _load ($sPath) {
		$this->_checkPath ($sPath);
		include_once $sPath;
	}
}
?>