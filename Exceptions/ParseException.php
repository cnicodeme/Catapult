<?php
require_once ('CatapultException.php');
/**
 * @name ParseException
 * Throwed when an error occured while parsing a file
 * 
 * @package Catapult.Exception
 * @filesource ParseException.php
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
class ParseException extends CatapultException {
	public function __construct ($sFilePath) {
		parent::__construct ('Error while parsing the file "'.$sFilePath.'".');
	}
}
?>