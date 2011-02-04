<?php
/**
 * @name DbState
 * Indicate the state of the DbManager
 * 
 * @package Catapult.Database
 * @filesource DbState.php
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
class DbState {
	/**
	 * @const Closed
	 * Specify there is no opened connection
	 */
	const Closed 		= 0;
	
	/**
	 * @const Open
	 * Indicate that the connection is actually open but not ready
	 */
	const Open 			= 1;
	
	/**
	 * @const Connecting
	 * Indicate that the connection is actually connecting to the db 
	 */
	const Connecting 	= 2;
	
	/**
	 * @const Ready
	 * Indicate that the db is ready to be used
	 */
	const Ready 		= 3;
	
	/**
	 * @const Executing
	 * Indicate that the db is actually executing an action
	 */
	const Executing 	= 4;
	
	/**
	 * @const Fetching
	 * Indicate that the db is actually fetching a request
	 */
	const Fetching 		= 5; // Not Used
	
	/**
	 * @const Broken
	 * Indicate that the link is broken
	 */
	const Broken 		= 6; // Not Used
}
?>