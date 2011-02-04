<?php
Loader::loadBase ('Response.aResponse');
/**
 * @name Html extends aResponse
 * Html element for the Output
 * 
 * @package Catapult.Response.Text
 * @filesource Html.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 12/01/08
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
class Html extends aResponse {
	/**
	 * @name __construct
	 * Constructor, set the ContentType
	 * 
	 * @return void
	 */
	public function __construct () {
		$this->_sContentType = 'text/html';
	}
	
	/**
	 * @name protected _render 
	 * Import file and add the values into it
	 * 
	 * @param Array $aValues 
	 * @param String $sTemplatePath (optionnal)
	 * 
	 * @return String
	 */
	protected function _run ($aValues, $sTemplatePath = null) {
		if (isset ($aValues))
			extract ($aValues);

		if (!isset ($sTemplatePath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, 'String');
		}
		elseif (is_readable ($sTemplatePath))
			include $sTemplatePath;
		else {
			Loader::loadBase ('Exceptions.IOException');
			throw new IOException (IOException::READ, $sTemplatePath);
		}
	}
}
?>