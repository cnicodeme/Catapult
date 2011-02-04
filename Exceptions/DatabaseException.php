<?php
require_once ('CatapultException.php');
/**
 * @name DatabaseException
 * Throwed when an error occured when using Database's objects
 * 
 * @package Catapult.Exception
 * @filesource DatabaseException.php
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
 * 
 * @deprecated (when CDO (CatapultDatabaseObject) will be finished (v0.2 of Catapult))
 */
class DatabaseException extends CatapultException {
	const CONNECTION_FAILED = 0;
	const DBSELECT_FAILED = 1;
	const QUERY_FAILED = 2;
	const FETCH_FAILED = 3;
	const SEEK_FAILED = 4;
	const FREE_FAILED = 5;
	
	public function __construct ($iError, $sLastError = null) {
		$sErrorMsg = '';
		switch ($iError) {
			case DatabaseException::CONNECTION_FAILED:
				$sErrorMsg = 'Unable to connect at the database (Do you have a correct Dsn ?)';
				break;
			case DatabaseException::DBSELECT_FAILED:
				$sErrorMsg = 'Unable to select the Database. Does it exist ?';
				break;
			case DatabaseException::QUERY_FAILED:
				$sErrorMsg = 'Unable to perform the query.';
				break;
			case DatabaseException::FETCH_FAILED:
				$sErrorMsg = 'Unable to fetch the result. Do you have results ?';
				break;
			case DatabaseException::SEEK_FAILED:
				$sErrorMsg = 'Unable to seek to the specified index.';
				break;
			case DatabaseException::FREE_FAILED:
				$sErrorMsg = 'Unable to set the result free.';
				break;
		}
		
		if (isset ($sLastError))
			$sErrorMsg .= ' ('.$sErrorMsg.')';
		
		parent::__construct ($sErrorMsg);
	}
}
?>