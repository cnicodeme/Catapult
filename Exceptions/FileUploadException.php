<?php
require_once ('CatapultException.php');
/**
 * @name FileUploadException
 * Throwed when an error occured during an upload
 * 
 * @package Catapult.Exception
 * @filesource FileUploadException.php
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
class FileUploadException extends CatapultException {
	const UPLOAD_FAILED = 0;
	const INVALID_UPLOADED_FILE = 1;
	const MISSING_UPLOADED_FILE = 2;
	const INCOMPLETE_UPLOADED_FILE = 3;
	const MAX_AUTHORIZED_SIZE = 4;
	const MAX_AUTHORIZED_FORM_SIZE = 5;

	public function __construct ($iError) {
		$sErrorMsg = '';
		switch ($iError) {
			case FileUploadException::UPLOAD_FAILED:
				$sErrorMsg = 'Unable to upload the file. Maybe you haven\'t the rights to write into the folder ?';
				break;
			case FileUploadException::INVALID_UPLOADED_FILE:
				$sErrorMsg = 'The file is not a valid uploaded file.';
				break;
			case FileUploadException::MISSING_UPLOADED_FILE:
				$sErrorMsg = 'No file was uploaded';
				break;
			case FileUploadException::INCOMPLETE_UPLOADED_FILE:
				$sErrorMsg = 'The file was not completely uploaded';
				break;
			case FileUploadException::MAX_AUTHORIZED_SIZE:
				$sErrorMsg = 'The file size is over the authorized size';
				break;
			case FileUploadException::MAX_AUTHORIZED_FORM_SIZE:
				$sErrorMsg = 'The file size is over the form authorized size';
				break;
		}
		parent::__construct ();
	}
}
?>