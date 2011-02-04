<?php
/**
 * @name Config
 * @abstract
 * Parent of the Controller's
 * 
 * @package Catapult.Controller
 * @filesource aController.php
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
abstract class Controller {
	/**
	 * @var Array $_aObjects
	 * Contain all the objets instantiated by the User Controller
	 */
	private $_aObjects = array ();
	
	/**
	 * @var String $_sDefaultContentType
	 * Contain the default value for the Content Type
	 */
	private $_sDefaultContentType;
	
	/**
	 * @name final public __construct
	 * Constructor
	 * Recover instances of Router, Config, Loader and call the Init method
	 *
	 * @param iRouter $oRouter
	 */
	final public function __construct (aRouter $oRouter) {
		$this->_aObjects['Router'] = $oRouter;
		$this->_aObjects['Config'] = Config::getInstance ();
		$this->_aObjects['Loader'] = Loader::getInstance ();
		$this->_sDefaultContentType = $this->_aObjects['Config']->get ('Controller.defaultContentType', 'text/html');
		$this->init ();
	}
	
	/**
	 * @name final protected function __get
	 * Use the magic method to make the Controller more easy to use
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	final protected function __get ($sKey) {
		if ($sKey == 'Response' && !isset ($this->_aObjects['Response']))
			$this->setContentType ($this->_sDefaultContentType);
		
		if (!isset ($this->_aObjects[$sKey])) {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
		
		return $this->_aObjects[$sKey];
	}
	
	/**
	 * @name init
	 * Method created to be overwritted
	 * 
	 * @return void
	 */
	protected function init () {}

	/**
	 * @name final protected setOutput
	 * Create an instance of the specific Output
	 *
	 * @param String $sOutputType
	 * 
	 * @return void
	 */
	final protected function setContentType ($sOutputType) {
		$aOutputs = explode ('/', strtolower ($sOutputType));
		$sOutputClassPath = 'Response.'.ucfirst ($aOutputs[0]).'.';
		
		$sClassName = preg_replace_callback ('/-([a-z])/', create_function ('$aMatches', 'return strtoupper ($aMatches[1]);'), ucfirst ($aOutputs[1]));
		
		Loader::loadBase ($sOutputClassPath.$sClassName);
		
		if (!class_exists ($sClassName)) {
			Loader::loadBase ('Exceptions.InvalidClassException');
			throw new InvalidClassException ($sClassName);
		}
		
		$this->_aObjects['Response'] = new $sClassName;
	}
	
	/**
	 * @name final private _load
	 * Load a specific class and instanciate it
	 *
	 * @param String $sClassName
	 * @param Mixed $mArgs
	 * @param String $sVarName
	 * 
	 * @return Object
	 */
	final private function _load ($sClassName, $mArgs, $sVarName) {
		$oReflectionClass = new ReflectionClass($sClassName);
		
		if (isset ($mArgs) && is_array ($mArgs))
			$this->_aObjects[$sVarName] = $oReflectionClass->newInstanceArgs ($mArgs);
		else if (isset ($mArgs))
			$this->_aObjects[$sVarName] = $oReflectionClass->newInstance ($mArgs);
		else
			$this->_aObjects[$sVarName] = $oReflectionClass->newInstance ();
		
		return $this->_aObjects[$sVarName];
	}
	
	/**
	 * @name final protected load
	 * Import a file specified by the path and call _load to instanciate it
	 *
	 * @param String $sPath
	 * @param Mixed $mArgs
	 * @param String $sVarName
	 * 
	 * @eturn Object
	 */
	final protected function load ($sPath, $mArgs = null, $sVarName = null) {
		$this->_aObjects['Loader']->base ($sPath);
		$sVarName = (isset ($sVarName)) ? $sVarName : $this->_aObjects['Loader']->getClassName ($sPath);
		return $this->_load ($this->_aObjects['Loader']->getClassName ($sPath), $mArgs, $sVarName);
	}

	/**
	 * @name final protected loadModel
	 * Import a model specified by the path and call _load to instanciate it
	 *
	 * @param String $sPath
	 * @param Mixed $mArgs
	 * @param String $sVarName
	 * 
	 * @return Object
	 */
	final protected function loadModel ($sPath, $mArgs = null, $sVarName = null) {
		$this->_aObjects['Loader']->model ($sPath);
		$sVarName = (isset ($sVarName)) ? $sVarName : $this->_aObjects['Loader']->getClassName ($sPath);
		return $this->_load ($this->_aObjects['Loader']->getClassName ($sPath), $mArgs, $sVarName);
	}

	/**
	 * @name final protected loadDb
	 * Import a Database driver specified by the Dsn and call _load to instanciate it
	 *
	 * @param String $sDsn
	 * @param String $sVarName : default 'db'
	 * 
	 * @return aDbManager
	 */
	final protected function loadDb ($sDsn, $sVarName = 'Db') {
		$sDriver = ucfirst (strtolower (parse_url ($sDsn, PHP_URL_SCHEME)));
		$this->_aObjects['Loader']->base ('Database.Drivers.'.$sDriver.'.'.$sDriver);
		$this->_aObjects[$sVarName] = new $sDriver ($sDsn);
		return $this->_aObjects[$sVarName];
	}
	
	/**
	 * @name final protected delegate
	 * Delegate a function to another one
	 * 
	 * @param String $sMethod
	 * @param Boolean $bForce, default false
	 * 
	 * @return void
	 */
	final protected function delegate ($sMethod, $bForce = false) {
		if ($bForce) {
			$aSavedObjects['Router'] = $this->_aObjects['Router'];
			$aSavedObjects['Config'] = $this->_aObjects['Config'];
			$aSavedObjects['Loader'] = $this->_aObjects['Loader'];
			if (isset ($this->_aObjects['Response']))
				unset ($this->_aObjects['Response']);
			
			$this->_aObjects = $aSavedObjects;
			
			$this->_aObjects['Router']->setMethod ($sMethod);
			$this->init ();
		}
		
		$this->$sMethod ();
	}
}
?>