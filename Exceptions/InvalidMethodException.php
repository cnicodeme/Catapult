<?php
require_once ('CatapultException.php');
/**
 * @name InvalidMethodException
 * Throwed when an Invalid method is called
 * 
 * @package Catapult.Exception
 * @filesource InvalidMethodException.php
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
class InvalidMethodException extends CatapultException {
	const CONSTRUCTOR_UNAUTHORISED = 0;
	const MUST_BE_PUBLIC = 1;
	
	public function __construct ($iError) {
		$sErrorMsg = '';
		switch ($iError) {
			case InvalidMethodException::CONSTRUCTOR_UNAUTHORISED:
				$sErrorMsg = 'The constructor is not authorized';
				break;
			case InvalidMethodException::MUST_BE_PUBLIC:
				$sErrorMsg = 'The method must be public';
				break;
		}
		
		parent::__construct ($sErrorMsg);
	}
}
?>