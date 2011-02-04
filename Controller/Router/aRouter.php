<?php
Loader::loadBase ('Controller.Request.Request');
/**
 * @name aRouter
 * @abstract
 * Abstract class for the Router's objects
 * 
 * @package Catapult.Controller.Router
 * @filesource aRouter.php
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
abstract class aRouter {
	/**
	 * @var Request $_oRequest
	 * Contain an instance of Request object
	 */
	private $_oRequest;
	
	/**
	 * @var private String $_sController
	 * Contain the default controller name
	 * 
	 * @default : 'Index'
	 */
	protected $_sController;
	
	/**
	 * @var private String $_sMethod
	 * Contain the default method name
	 * 
	 * @default : 'index'
	 */
	protected $_sMethod;
	
	/**
	 * @var protected $_aArgs
	 * Contain the Arguments given (all excepts controller et method)
	 */
	protected $_aArgs = array ();
	
	/**
	 * @name public function __construct
	 * Constructor, Set the defaults values
	 */
	public function __construct () {
		$this->_create ();

		$oConfig = Config::getInstance ();
		$this->_sController = (!empty ($this->_sController)) ? $this->_sController : $oConfig->get ('Router.Controller', 'Index');
		$this->_sMethod = (!empty ($this->_sMethod)) ? $this->_sMethod : $oConfig->get ('Router.Method', 'index');
		
		$this->_oRequest = new Request ($this->_aArgs, $oConfig);
	}
	
	/**
	 * @name public getController
	 * Method used to indicate the controller's name
	 *
	 * @return String
	 */
	public function getController () {
		return $this->_sController.'Controller';
	}
	
	/**
	 * @name public function setController
	 * Set the Controller value
	 * 
	 * @param String $sName
	 * 
	 * @return void;
	 */
	public function setController ($sName) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_sController = $sName;
	}
	
	/**
	 * @name public getMethod
	 * Method used to indicate the method's name
	 *
	 * @return String
	 */
	public function getMethod () {
		return $this->_sMethod;
	}

	/**
	 * @name public function setMethod
	 * Set the Method value
	 * 
	 * @param String $sName
	 * 
	 * @return void
	 */
	public function setMethod ($sName) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_sMethod = $sName;
	}
	
	/**
	 * @name public function getArg
	 * Return the value of an asked arg, eventually parsed
	 * 
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	public function getArg ($sKey) {
		return $this->_oRequest->getArg ($sKey);
	}

	/**
	 * @name public function getArgs
	 * Return an array of arguments given in the url (from Req
	 *
	 * @return Array
	 */
	public function getArgs () {
		return $this->_oRequest->getArgs ();
	}

	/**
	 * @name public function getAbsolutePath
	 * Return the absolute path used for css or image path for example
	 *
	 * @return String
	 */
	public function getAbsolutePath () {
		return substr ($_SERVER['SCRIPT_NAME'], 0, strrpos ($_SERVER['SCRIPT_NAME'], '/') + 1);
	}
	
	/**
	 * @abstract methods
	 */
	abstract protected function _create ();
	abstract public function makeUri ($sController, $sMethod, $aArgs = null);
	abstract public function appendUri ($mElement);
}
?>