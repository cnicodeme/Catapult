<?php
require_once ('CatapultException.php');
/**
 * @name InvalidFormatException
 * Throwed when an incorrect format is used
 * 
 * @package Catapult.Exception
 * @filesource InvalidFormatException.php
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
class InvalidFormatException extends CatapultException {
	const FILE_FORMAT = 0;
	const VARIABLE_FORMAT = 1;
	const EXTENSION_FORMAT = 2;
	const IMAGE_FORMAT = 3;
	
	public function __construct ($iFormat) {
		$sErrorMsg = '';
		switch ($iFormat) {
			case InvalidFormatException::FILE_FORMAT:
				$sErrorMsg = 'The format of the file is invalid.';
				break;
			case InvalidFormatException::VARIABLE_FORMAT:
				$sErrorMsg = 'The format of the variable is invalid.';
				break;
			case InvalidFormatException::EXTENSION_FORMAT:
				$sErrorMsg = 'The extension is not allowed.';
				break;
			case InvalidFormatException::IMAGE_FORMAT:
				$sErrorMsg = 'The file is not a valid Image File.';
				break;
		}
		
		parent::__construct ($sErrorMsg);
	}
}
?>