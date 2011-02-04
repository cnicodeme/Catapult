<?php
/**
 * Loading Catapult.Config.Config
 */
require_once (substr (dirname (__FILE__), 0, -10).'Config'.DIRECTORY_SEPARATOR.'Config.php');

/**
 * Loading Catapult.Exceptions.EventHandler
 */
require_once (substr (dirname (__FILE__), 0, -10).'EventHandler'.DIRECTORY_SEPARATOR.'EventHandler.php');

/**
 * Loading Catapult.Loader.Loader
 */
require_once (substr (dirname (__FILE__), 0, -10).'Loader'.DIRECTORY_SEPARATOR.'Loader.php');

/**
 * Loading Catapult.Controller.aController
 */
require_once (substr (dirname (__FILE__), 0, -10).'Controller'.DIRECTORY_SEPARATOR.'aController.php');

/**
 * @name FrontController
 * FrontController to manage an entry connection to a specified Controller
 * 
 * @package Catapult.Controller
 * @filesource FrontController.php
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
class FrontController {
	/**
	 * @var private Static Config $_oInstance
	 * Static var used for the singleton to recover an instance of FrontController
	 */
	private static $_oInstance;
	
	/**
	 * @var private iRouter $_oRouter
	 * Contain an instance of Router
	 */
	private $_oRouter;

	/**
	 * @name public static getInstance
	 * Used as Singleton, retrieve an instance of FrontController class
	 *
	 * @return FrontController
	 */
	public static function getInstance () {
		if (!(self::$_oInstance instanceof self))
			self::$_oInstance = new FrontController ();
		
		return self::$_oInstance;
	}

	/**
	 * @name private function __construct
	 * Constructor, not authorized to be called from outside
	 */
	private function __construct () {
		EventHandler::getInstance ();
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
	 * @name public __get
	 * Magic Method used to have access easyly at the Router instance
	 *
	 * @param String $sInstance
	 * 
	 * @return Object
	 */
	public function __get ($sInstance) {
		switch (strtolower ($sInstance)) {
			case 'router':
				return $this->_oRouter;
				break;
			default:
				Loader::loadBase ('Exceptions.IllegalKeyException');
				throw new IllegalKeyException ($sInstance);
		}
	}
	
	/**
	 * @name public setRouter
	 * Set a specific Router
	 *
	 * @param iRouter $oRouter
	 * 
	 * @return void
	 */
	public function setRouter (aRouter $oRouter = null) {
		if (isset ($oRouter) && $oRouter instanceof aRouter)
			$this->_oRouter = $oRouter;
		else {
			Loader::loadBase ('Controller.Router.GetRouter');
			$this->_oRouter = new GetRouter ();
		}
	}
	
	/**
	 * @name public init
	 * Try to instanciate the Controller and call the method asked by the requested uri
	 * 
	 * @return void
	 */
	public function init () {
		if (!isset ($this->_oRouter) || !($this->_oRouter instanceof aRouter))
			$this->setRouter ();
		
		$oLoader = Loader::getInstance ();
		$sControllerPath = $this->_oRouter->getController ();

		$sControllerName = $oLoader->getClassName ($sControllerPath);

		$oLoader->controller ($sControllerPath);

		if (!class_exists ($sControllerName)) {
			Loader::loadBase ('Exceptions.InvalidClassException');
			throw new InvalidClassException ($sControllerName);
		}

		$oReflectionMethod = new ReflectionMethod ($sControllerName, $this->_oRouter->getMethod ());
		
		if (!$oReflectionMethod->isPublic()) {
			Loader::loadBase ('Exceptions.InvalidMethodException');
			throw new InvalidMethodException (InvalidMethodException::MUST_BE_PUBLIC);
		}
		elseif ($oReflectionMethod->isConstructor()) {
			Loader::loadBase ('Exceptions.InvalidMethodException');
			throw new InvalidMethodException (InvalidMethodException::CONSTRUCTOR_UNAUTHORISED);
		}
		else
			$oReflectionMethod->invokeArgs (new $sControllerName ($this->_oRouter), $this->_oRouter->getArgs ());
	}
}
?>