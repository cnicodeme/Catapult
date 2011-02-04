<?php
require_once ('CatapultException.php');
/**
 * @name InvalidTypeException
 * Throwed when an Invalid type is used
 * 
 * @package Catapult.Exception
 * @filesource InvalidTypeException.php
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
class InvalidTypeException extends CatapultException {
	const BOOLEAN_NEEDED = 0;
	const INTEGER_NEEDED = 1;
	const FLOAT_NEEDED = 2;
	const DOUBLE_NEEDED = 3;
	const LONG_NEEDED = 4;
	const STRING_NEEDED = 5;
	const ARRAY_NEEDED = 6;
	const SCALAR_NEEDED = 7;
	const RESOURCE_NEEDED = 8;
	const OBJECT_NEEDED = 9;
	
	
	public function __construct ($iPosition) {
		$sErrorMsg = 'Argument N°'.$iPosition.' must be ';
		
		$aArgs = func_get_args ();
		array_shift ($aArgs);
		$iCount = count ($aArgs);
		foreach ($aArgs as $iKey=>$iConstant) {
			switch ($iConstant) {
				case InvalidTypeException::BOOLEAN_NEEDED:
					$sErrorMsg .= 'Boolean, ';
					break;
				case InvalidTypeException::INTEGER_NEEDED:
					$sErrorMsg .= 'Integer, ';
					break;
				case InvalidTypeException::FLOAT_NEEDED:
					$sErrorMsg .= 'Float, ';
					break;
				case InvalidTypeException::DOUBLE_NEEDED:
					$sErrorMsg .= 'Double, ';
					break;
				case InvalidTypeException::LONG_NEEDED:
					$sErrorMsg .= 'Long, ';
					break;
				case InvalidTypeException::STRING_NEEDED:
					$sErrorMsg .= 'String, ';
					break;
				case InvalidTypeException::ARRAY_NEEDED:
					$sErrorMsg .= 'Array, ';
					break;
				case InvalidTypeException::SCALAR_NEEDED:
					$sErrorMsg .= 'Scalar, ';
					break;
				case InvalidTypeException::RESOURCE_NEEDED:
					$sErrorMsg .= 'Resource, ';
					break;
				case InvalidTypeException::OBJECT_NEEDED:
					$sErrorMsg .= 'Object, ';
					break;
			}
			
			if ($iCount > 1 && $iKey + 1 >= $iCount)
				$sErrorMsg .= substr ($sErrorMsg, 0, -2).' or ';
		}
		
		parent::__construct (substr ($sErrorMsg, 0, -2));
	}
}
?>