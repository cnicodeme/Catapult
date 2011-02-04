<?php
/**
 * Loading Router abstract class (aRouter)
 */
Loader::loadBase ('Controller.Router.aRouter');

/**
 * Loading Request Object
 *
Loader::loadBase ('Controller.Request.Request');*/

/**
 * @name GetRouter implements iRouter
 * Used to recover the Controllers and Methods
 * 
 * @package Catapult.Controller.Router
 * @filesource GetRouter.php
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
class GetRouter extends aRouter {
	/**
	 * @name protected function _create
	 * Used to recover the values from the uri
	 * 
	 * @return void
	 */
	public function _create () {
		$oConfig = Config::getInstance ();
		foreach ($_GET as $sKey=>$sValue) {
			if ($sKey == 'controller')
				$this->_sController = $_GET['controller'];
			elseif ($sKey == 'method')
				$this->_sMethod = $_GET['method'];
			else
				$this->_aArgs[$sKey] = $sValue;
		}
	}

	/**
	 * @name public function makeUri
	 * Method used to create the uri
	 * 
	 * @param String $sController
	 * @param String $sMethod
	 * @param Array $aArgs Optionnal
	 * 
	 * @return String
	 */
	public function makeUri ($sController, $sMethod, $aArgs = null) {
		if (!is_string ($sController)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sMethod)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (isset ($aArgs) && !is_array ($aArgs)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$sArgAdds = '';
		if (isset ($aArgs)) {
			foreach ($aArgs as $sKey=>$sValue) {
				$sArgAdds .= '&amp;'.$sKey.'='.$sValue;
			}
		}
		
		return 'index.php?controller='.$sController.'&amp;method='.$sMethod.$sArgAdds;
	}
	
	/**
	 * @name public function appendUri
	 * Return a well formed Uri with an appended param
	 * 
	 * @param Mixed $mElement
	 * 
	 * @return String
	 */
	public function appendUri ($mElement) {
		$sOriginalUri = $_SERVER['PHP_SELF'];
		$aQuery = array ();
		
		if (!empty ($_SERVER['QUERY_STRING'])) {
			$aParts = explode ('&', $_SERVER['QUERY_STRING']);
			foreach ($aParts as $sPart) {
				$aKV = explode ('=', $sPart);
				$sKey = (isset ($aKV[0])) ? $aKV[0] : '';
				$sValue = (isset ($aKV[1])) ? $aKV[1] : '';
				$aQuery[$sKey] = $sValue;
			}
		}
		
		if (is_array ($mElement)) {
			foreach ($mElement as $sKey=>$sValue) {
				$aQuery[$sKey] = $sValue;
			}
		}
		elseif (is_string ($mElement)) {
			$aKV = explode ('=', $mElement);
			$sKey = (isset ($aKV[0])) ? $aKV[0] : '';
			$sValue = (isset ($aKV[1])) ? $aKV[1] : '';
			$aQuery[$sKey] = $sValue;
		}
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if (count ($aQuery) > 0) {
			$sOriginalUri .= '?';
			foreach ($aQuery as $sKey=>$sValue) {
				$sOriginalUri .= $sKey.'='.$sValue.'&amp;';
			}
			return substr ($sOriginalUri, 0, -5);
		}
		else
			return $sOriginalUri;
	}
}
?>