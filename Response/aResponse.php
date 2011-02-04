<?php
/**
 * @abstract aResponse
 * Abstract method used to send the response
 * 
 * @package Catapult.Response
 * @filesource aResponse.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 16/03/2008
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
abstract class aResponse {
	protected $_aConfig = array (
								'Encoding' => 'Utf-8',
								'NewLine' => "\r\n",
								'AutoRender' => true);
	
	protected $_sContentType = '';
	protected $_aHeaders = array ();
	protected $_aPreRender = array ();
	protected $_aPostRender = array ();

	/**
	 * @name __clone
	 * Clone is Forbidden
	 * 
	 * @return void
	 */
	final public function __clone () {
		Loader::loadBase ('Exceptions.CloneNotSupportedException');
		throw new CloneNotSupportedException ();
	}
	
	/**
	 * @name final public function __get
	 * Magic method used to retrieve a private/protected value
	 *
	 * @param String $sKey
	 * 
	 * @return Mixed
	 */
	final public function __get ($sKey) {
		if (!isset ($this->_aConfig[$sKey])) {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
		
		return $this->_aConfig[$sKey];
	}
	
	/**
	 * @name final public function __set
	 * Magic method used to set a private/protected value
	 *
	 * @param String $sKey
	 * @param Mixed $sValue
	 * 
	 * @return void
	 */
	final public function __set ($sKey, $mValue) {
		if (!isset ($this->_aConfig[$sKey])) {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
		
		if (!is_string ($mValue) || !is_bool ($mValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED, InvalidTypeException::BOOLEAN_NEEDED);
		}
		
		$this->_aConfig[$sKey] = $mValue;
	}

	/**
	 * @name setHeader
	 * Set a specific Header
	 * 
	 * @param String $sKey
	 * @param String $sValue
	 * 
	 * @return void
	 */
	final public function setHeader ($sKey, $sValue) {
		if (!is_string ($sKey)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sValue) || !is_int ($sValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (strtolower ($sKey) == 'content-type') {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ('Content-Type');
		}
		
		$this->_aHeaders[$sKey] = $sValue;
	}

	/**
	 * @name redirect
	 * Redirect to another url
	 * 
	 * @param String $sUrl
	 * @param String $sType
	 * 
	 * @return void
	 * 
	 * @see http://www.rankspirit.com/redirections.php
	 */
	final public function redirect ($sUrl, $sType) {
		if (!is_string ($sUrl)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		switch ($sType) {
			case 'Temporary':
				header('HTTP/1.1 307 Temporary Redirect', false, 307);
				header('Location: '.$sUrl);
				break;
			case 'Found':
				header('HTTP/1.1 302 Found', false, 302);
				header('Location: '.$sUrl);
				break;
			case 'Other':
				header('HTTP/1.1 303 See Other', false, 303);
				header('Location: '.$sUrl);
				break;
			default: //case 'Permanently':
				header('HTTP/1.1 301 Moved Permanently', false, 301);
				header('Location: '.$sUrl);
				break;
		}
		exit ();
	}

	/**
	 * @name preRender
	 * The values to put before the render
	 * 
	 * @param Mixed $mArg (could be the path of the Template or an array of values)
	 * @param Array $aValues (only when first argument is the path of the template)
	 * 
	 * @return String
	 */
	final public function preRender ($mArg, $aValues = array ()) {
		$this->_aPreRender[] = $this->_render ($mArg, $aValues);
	}
	
	/**
	 * @name postRender
	 * The values to put after the render
	 * 
	 * @param Mixed $mArg (could be the path of the Template or an array of values)
	 * @param Array $aValues (only when first argument is the path of the template)
	 * 
	 * @return String
	 */
	final public function postRender ($mArg, $aValues = array ()) {
		$this->_aPostRender[] = $this->_render ($mArg, $aValues);
	}

	/**
	 * @name public function render
	 * The values to put for the render
	 * 
	 * @param Mixed $mArg (could be the path of the Template or an array of values)
	 * @param Array $aValues (only when first argument is the path of the template)
	 * 
	 * @return String
	 */
	final public function render ($mArg, $aValues = array ()) {
		$sMainRender = $this->_render ($mArg, $aValues);
		
		header('Content-type: '.$this->_sContentType.'; charset='.$this->_aConfig['Encoding']);
		
		foreach ($this->_aHeaders as $sKey=>$sValues) {
			header ($sKey.':'.$sValue);
		}
		
		$sRender = '';
		
		if (count ($this->_aPreRender) > 0)
			$sRender = implode ($this->_aConfig['NewLine'], $this->_aPreRender);
		
		$sRender .= $sMainRender;
		
		if (count ($this->_aPostRender) > 0)
			$sRender .= implode ($this->_aConfig['NewLine'], $this->_aPostRender);
		
		if ($this->_aConfig['AutoRender'])
			echo $sRender;
		else return $sRender;
	}
	
	/**
	 * @name final public function clearHeaders
	 * Set the Headers array to be empty
	 * 
	 * @return void
	 */
	final public function clearHeaders () {
		$this->_aHeaders = array ();
	}
	
	/**
	 * @name final public function clearPreRender
	 * Set the Headers array to be empty
	 * 
	 * @return void
	 */
	final public function clearPreRender () {
		$this->_aPreRender = array ();
	}
	
	/**
	 * @name final public function clearPostRender
	 * Set the Headers array to be empty
	 * 
	 * @return void
	 */
	final public function clearPostRender () {
		$this->_aPostRender = array ();
	}
	
	/**
	 * @name final public function clearAll
	 * Set the Headers array to be empty
	 * 
	 * @return void
	 */
	final public function clearAll () {
		$this->clearHeaders ();
		$this->clearPreRender ();
		$this->clearPostRender ();
	}
	
	/**
	 * @name public function renderLayout
	 * Used to use a rendered html as content for future render without using post/pre render functions
	 *
	 * @param Array $aValues
	 * @param String $sTemplatePath
	 * 
	 * @return String
	 */
	public function renderLayout ($aValues = null, $sTemplatePath = null) {
		return $this->_render ($aValues, $sTemplatePath);
	}
	
	/**
	 * @name private function _render
	 * Do the concrete rendering
	 * 
	 * @param Mixed $mArg (could be the path of the Template or an array of values)
	 * @param Array $aValues (only when first argument is the path of the template)
	 * 
	 * @return String
	 */
	private function _render ($mArg, $aValues = array ()) {
		if (is_array ($mArg)) {
			$aValues = $mArg;
			$sRealTplPath = '';
		}
		elseif (is_string ($mArg)) {
			if (!is_array ($aValues)) {
				Loader::loadBase ('Exceptions.InvalidTypeException');
				throw new InvalidTypeException (2, InvalidTypeException::ARRAY_NEEDED);
			}
			
			$oLoader = Loader::getInstance ();
			$sRealTplPath = $oLoader->getRealPath ($mArg, 'View');
		}
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}

		ob_start ();
		$this->_run ($aValues, $sRealTplPath);
		return ob_get_clean ();
	}
	
	/**
	 * Abstracts Methods
	 */
	abstract public function __construct ();
	abstract protected function _run ($aValues, $sTemplatePath = '');
}
?>