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
 * @name SlashRouter implements iRouter
 * Used to recover the Controllers and Methods
 * 
 * @package Catapult.Controller.Router
 * @filesource SlashRouter.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 07/04/2008
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
class SlashRouter extends aRouter {
	/**
	 * @name protected function _create
	 * Used to recover the values from the uri
	 * 
	 * @return void
	 */
	protected function _create () {
		$sUri = substr ($_SERVER['PHP_SELF'], strlen ($_SERVER['SCRIPT_NAME']) + 1);

		$aSeparateBaseArgument = explode('&', $sUri, 2);
		
		if (isset ($aSeparateBaseArgument[0])) {
			$aElements = explode ('/', $aSeparateBaseArgument[0]);
			$this->_sController = array_shift ($aElements);
			$this->_sMethod = array_shift ($aElements);

		}
		
		if (isset ($aSeparateBaseArgument[1])) {
			$aArgs = explode ('&', $aSeparateBaseArgument[1]);
			foreach ($aArgs as $mValue) {
				$aKVArgs = explode ('=', $mValue);
				if (count ($aKVArgs) == 2)
					$this->_aArgs[$aKVArgs[0]] = $aKVArgs[1];
				else
					$this->_aArgs[] = $aKVArgs[0];
			}
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
				$sArgAdds .= '/'.$sKey.'='.$sValue;
			}
		}
		
		return $_SERVER['SCRIPT_NAME'].'/'.$sController.'/'.$sMethod.$sArgAdds;
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
		if (is_array ($mElement) && count ($mElement) == 2)
			return $_SERVER['PHP_SELF'].'/'.$mElement[0].'='.$mElement[1];
		elseif (is_string ($mElement) || is_int ($mElement))
			return $_SERVER['PHP_SELF'].'/'.$mElement;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}
	}
}
?>