<?php
Loader::loadBase('Libraries.Validate.iValidator');

/**
 * @name Ipv4
 * Check if the given value is a correct Ipv4
 * 
 * @package Catapult.Libraries.Validate.Validators
 * @filesource Ipv4.php
 * 
 * @author Cyril Nicodème
 * @version 0.2
 * 
 * @since 31/03/2008
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
class Ipv4 implements iValidator {
	/**
	 * @name public function validate
	 * Validate a given value with specific rule
	 * 
	 * @param Mixed $mValue
	 * 
	 * @return Boolean
	 */
	public function validate ($mValue) {
		// '#^((([0-1]{1}[0-9]{0,2})|([2]{1}(([0-4]{1}[0-9]{1})|([5]{1}[0-5]{1}))))\.(([0-1]{1}[0-9]{0,2})|([2]{1}(([0-4]{1}[0-9]{1})|([5]{1}[0-5]{1}))))\.(([0-1]{1}[0-9]{0,2})|([2]{1}(([0-4]{1}[0-9]{1})|([5]{1}[0-5]{1}))))\.(([0-1]{1}[0-9]{0,2})|([2]{1}(([0-4]{1}[0-9]{1})|([5]{1}[0-5]{1})))))$#i'
		return (filter_var ($mValue,  FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) ? false : true;
	}
}
?>