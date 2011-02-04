<?php
require_once ('CatapultException.php');
/**
 * @name IOException
 * Throwed when an error occured on a file or on a folder (Input/Output exception)
 * 
 * @package Catapult.Exception
 * @filesource Exception.php
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
class IOException extends CatapultException {
	const ALREADY_EXIST = 0; // Already exist! ! (File or Folder !)
	const READ = 1;
	const WRITE = 2;
	const CREATE = 3; // Folder or File
	
	public function __construct ($iError, $sElementPath) {
		$sErrorMsg = '';
		switch ($iError) {
			case IOException::ALREADY_EXIST:
				$sErrorMsg = 'The element "'.$sElementPath.'" already exists';
				break;
			case IOException::READ:
				$sErrorMsg = 'The element "'.$sElementPath.'" cannot be read';
				break;
			case IOException::WRITE:
				$sErrorMsg = 'Cannot write content into the element  "'.$sElementPath;
				break;
			case IOException::CREATE:
				$sErrorMsg = 'Cannot create element "'.$sElementPath;
				break;
		}
		parent::__construct ($sErrorMsg);
	}
}
?>