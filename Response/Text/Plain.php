<?php
Loader::loadBase ('Response.aResponse');
/**
 * @name Html extends aResponse
 * Html element for the Output
 * 
 * @package Catapult.Response.Text
 * @filesource Plain.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 16/03/08
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
class Plain extends aResponse {
	/**
	 * @name __construct
	 * Constructor, set the ContentType
	 * 
	 * @return void
	 */
	public function __construct () {
		$this->_sContentType = 'text/plain';
	}
	
	/**
	 * @name protected _render 
	 * Import file and add the values into it
	 * 
	 * @param String $sTemplatePath
	 * @param Array $mValues (optionnal)
	 * 
	 * @return String
	 */
	protected function _run ($aValues, $sTemplatePath = '') {
		$tabCount = (func_num_args()> 1) ? func_get_arg (1) : 0;
		if (!is_int ($tabCount))
			$tabCount = 0;
		
		foreach ($aValues as $mValue) {
			if (is_array ($mValue))
				$this->_run ($mValue, ++$tabCount);
			else {
				echo ($tabCount > 0) ? str_pad ("\t", $tabCount) : '';
				echo $mValue;
				echo $this->_aConfig['NewLine'];
			}
		}
	}
}
?>