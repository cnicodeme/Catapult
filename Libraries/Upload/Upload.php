<?php
/**
 * @name Upload
 * Used to make the upload filesystem easier
 * 
 * @package Catapult.Libraries.Upload
 * @filesource Upload.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 12/01/2008
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
class Upload {
	/**
	 * @var Array $_aParameters
	 * Contain all the parameters
	 */
	private $_aParameters = array (
		// @var String : Contain the Path to the destination folder
		'DestinationFolder' => '',
	
		// @var String : Contain the name of the file
		'FileName' => '',
	
		// @var String : Contain the destination folder + the file name
		'FilePath' => '',
	
		// @var Array : Contain an array of Allowed extensions. If it's an empty array, all the extensions will be allowed
		'AllowedExt' => array (),
	
		// @var boolean : Indicate if we rename the file (if already exists) or not
		'Rename' => false,
	
		// @var boolean : Indicate if we cleaning the file from strange characters (allow only alphanumeric, ., - and _)
		'CleanFileName' => false,
	
		// @var boolean : Indicate if we create the subfolder for the destinationFolder value
		'CreateSubFolders' => true,
	
		// @var int : Indicate the max file size for the uploaded file
		'MaxFileSize' => 0,
	
		// @var boolean : Indicate if we need to check if the file is an image
		'IsImage' => false);

	/**
	 * @name public function __construct
	 * Constructor
	 * Set the defaults values
	 */
	public function __construct () {
		$oConfig = Config::getInstance();
		$this->_aParameters['DestinationFolder'] = $oConfig->get ('Libraries.Upload.DestinationFolder', '');
		$this->_aParameters['FileName'] = $oConfig->get ('Libraries.Upload.FileName', '');
		$this->_aParameters['FilePath'] = $oConfig->get ('Libraries.Upload.FilePath', '');
		$this->_aParameters['AllowedExt'] = $oConfig->get ('Libraries.Upload.AllowedExt', array ());
		$this->_aParameters['Rename'] = $oConfig->get ('Libraries.Upload.Rename', false);
		$this->_aParameters['CleanFileName'] = $oConfig->get ('Libraries.Upload.CleanFileName', false);
		$this->_aParameters['CreateSubFolders'] = $oConfig->get ('Libraries.Upload.CreateSubFolders', true);
		$this->_aParameters['MaxFileSize'] = $oConfig->get ('Libraries.Upload.MaxFileSize', 0);
		$this->_aParameters['IsImage'] = $oConfig->get ('Libraries.Upload.IsImage', false);
	}
	
	/**
	 * @name __set
	 * Modify the values : destinationFolder, fileName, filePath, allowedExt, rename, cleanFileName, createSubFolders, maxFileSize, isImage
	 * 
	 * @param String $sKey
	 * @param Mixed (String, Array, Boolean) $mValue
	 * 
	 * @return void
	 */
	public function __set ($sKey, $mValue) {
		if (!is_string ($sKey)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		switch (strtolower ($sKey)) {
			case 'destinationfolder':
				if (!is_string ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
				}
				
				$mValue = str_replace(array ("\\", "/"), DIR_S, $mValue);
				
				if (substr ($mValue, -1) != DIR_S)
					$mValue .= DIR_S;
				$this->_aParameters['DestinationFolder'] = $mValue;
				break;
			case 'filename':
				if (!is_string ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
				}
				
				$this->_aParameters['FileName'] = $mValue;
				break;
			case 'filepath':
				if (!is_string ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
				}
					
				$mValue = str_replace(array ("\\", "/"), DIR_S, $mValue);
				$this->_aParameters['DestinationFolder'] = substr ($mValue, 0, strrpos ($mValue, DIR_S)+1);
				$this->_aParameters['FileName'] = substr ($mValue, strrpos ($mValue, DIR_S)+1);
				$this->_aParameters['FilePath'] = $mValue;
				break;
			case 'allowedext':
				if (!is_array ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::ARRAY_NEEDED);
				}
				
				$this->_aParameters['AllowedExt'] = $mValue;
				break;
			case 'rename':
				if (!is_bool ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::BOOLEAN_NEEDED);
				}
				
				$this->_aParameters['Rename'] = $mValue;
				break;
			case 'cleanfilename':
				if (!is_bool ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::BOOLEAN_NEEDED);
				}
				
				$this->_aParameters['CleanFileName'] = $mValue;
				break;
			case 'createsubfolders':
				if (!is_bool ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::BOOLEAN_NEEDED);
				}
				
				$this->_aParameters['CreateSubFolders'] = $mValue;
				break;
			case 'maxfilesize':
				if (!is_int ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
				}
				
				$this->_aParameters['MaxFileSize'] = $mValue;
				break;
			case 'isimage':
				if (!is_bool ($mValue)) {
					Loader::loadBase ('Exceptions.InvalidTypeException');
					throw new InvalidTypeException (2, InvalidTypeException::BOOLEAN_NEEDED);
				}
				
				$this->_aParameters['IsImage'] = $mValue;
				break;
			default:
				Loader::loadBase ('Exceptions.IllegalKeyException');
				throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name __get
	 * Get the values : destinationFolder, fileName, filePath, allowedExt, rename, cleanFileName, createSubFolders, maxFileSize, isImage
	 * 
	 * @param String $sKey
	 * 
	 * @return Mixed (String, Array, Boolean)
	 */
	public function __get ($sKey) {
		if (!is_string ($sKey)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (isset ($this->_aParameters [$sKey]))
			return $this->_aParameters [$sKey];
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name addAllowedExtension
	 * Add a specific extension or an array of extensions
	 * 
	 * @param Mixed (Array, String) $mValue
	 * 
	 * @return void
	 */
	public function addAllowedExtension ($mValue) {
		if (is_array ($mValue))
			$this->_aParameters['AllowedExt'] = array_merge ($this->_aParameters['AllowedExt'], $mValue);
		elseif (is_string ($mValue))
			$this->_aParameters['AllowedExt'] [] = $mValue;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED, InvalidTypeException::ARRAY_NEEDED);
		}
	}
	
	/**
	 * @name cleanAllowedExtension
	 * Set the $_aAllowedExt array empty
	 * 
	 * @return void
	 */
	public function cleanAllowedExtension () {
		$this->_aParameters['AllowedExt'] = array ();
	}
	
	/**
	 * @name createSubFolders
	 * Create sub folders from a specific path or from the $_sDestinationFolder
	 * 
	 * @param String $sFolderToCreate (optional)
	 * 
	 * @return void
	 */
	public function createSubFolders  ($sFolderToCreate=null) {
		if (isset ($sFolderToCreate) && !is_string ($sFolderToCreate)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!isset ($sFolderToCreate))
			$sFolderToCreate = $this->_aParameters['DestinationFolder'];
		
		$aFolders = explode (DIR_S, $sFolderToCreate);
		$sFinalFolder = '';
		foreach ($aFolders as $sFolder) {
			$sFinalFolder .= $sFolder.DIRECTORY_SEPARATOR;
			if (!is_dir ($sFinalFolder)) {
				if (!mkdir ($sFinalFolder)) {
					Loader::loadBase ('Exceptions.IOException');
					throw new IOException (IOException::CREATE, $sFolderToCreate);
				}
			}
		}
	}
	
	/**
	 * @name cleanFileName
	 * Modify the value to be only alphanumeric, _, - and .
	 * 
	 * @param String $sFileName
	 * 
	 * @return String
	 */
	public function cleanFileName ($sFileName) {
		if (!is_string ($sFileName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$aSearch = array ('#à|â|ä#i', '#é|è|ê|ë#i', '#î|ï#i', '#ô|ö#i', '#ù|û|ü#i', '#ç#i', '#&#i', '#@#i', "# |'#", '#"#', '#[^a-zA-Z0-9_\.-]*#i');
		$aReplace = array('a', 'e', 'i', 'o', 'u', 'c', '_and_', 'at', '_');
		return preg_replace($aSearch, $aReplace, strtolower($sFileName));
	}
	
	/**
	 * @name renameFile
	 * Rename a specific value while the file from the given file path exists and return the new file path
	 * 
	 * @param String $sFilePath
	 * 
	 * @return String
	 */
	public function renameFile ($sFilePath) {
		if (!is_string ($sFilePath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$sFileName = substr ($sFilePath, strrpos ($sFilePath, DIRECTORY_SEPARATOR)+1);
		$sDestinationFolder = substr ($sFilePath, 0, strrpos ($sFilePath, DIRECTORY_SEPARATOR)+1);
		
		$sBaseName = substr ($sFileName, 0, strrpos ($sFileName, '.'));
		$sExtension = '.'.preg_replace ('`.*\.([^\.]*)$`', '$1', $sFileName);
		
		$sAdd = '';
		$iWhile = 0;
		
		while (file_exists ($sDestinationFolder.$sBaseName.$sAdd.$sExtension)) {
			$sAdd = '('.$iWhile.')';
			$iWhile++;
		}
		
		return $sDestinationFolder.$sBaseName.$sAdd.$sExtension;
	}
	
	/**
	 * @name isImage
	 * Get if the file is an image or not
	 * 
	 * @param String $sFilePath
	 * 
	 * @return Boolean
	 */
	public function isImage ($sFilePath) {
		if (!is_string ($sFilePath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!file_exists ($sFilePath)) {
			Loader::loadBase ('Exceptions.NotFoundException');
			throw new NotFoundException ($sFilePath);
		}
		
		$aParams = @getimagesize($sFilePath);
		
		/*
		 * 1  = IMAGETYPE_GIF
		 * 2  = IMAGETYPE_JPEG
		 * 3  = IMAGETYPE_PNG
		 * 4  = IMAGETYPE_SWF
		 * 5  = IMAGETYPE_PSD
		 * 6  = IMAGETYPE_BMP
		 * 7  = IMAGETYPE_TIFF_II (ordre d'octets d'Intel)
		 * 8  = IMAGETYPE_TIFF_MM (ordre d'octets Motorola)
		 * 9  = IMAGETYPE_JPC
		 * 10 = IMAGETYPE_JP2
		 * 11 = IMAGETYPE_JPX
		 * 12 = IMAGETYPE_JB2
		 * 13 = IMAGETYPE_SWC
		 * 14 = IMAGETYPE_IFF
		 * 15 = IMAGETYPE_WBMP
		 * 16 = IMAGETYPE_XBM
		 */
		if (!isset ($aParams[2]))
			return false;
		elseif ($aParams[2] > 0 && $aParams[2] < 16)
			return true;
		else
			return false;
	}
	
	/**
	 * @name upload
	 * Upload the file given into the $_sDestinationFolder given and return the file path
	 * 
	 * @param Array $aSubmittedFile
	 * 
	 * @return String
	 */
	public function upload ($aSubmittedFile) {
		if (!is_array ($aSubmittedFile)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if ($aSubmittedFile['error'] == UPLOAD_ERR_INI_SIZE) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::MAX_AUTHORIZED_SIZE);
		}
		
		if ($aSubmittedFile['error'] == UPLOAD_ERR_FORM_SIZE) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::MAX_AUTHORIZED_FORM_SIZE);
		}
		
		if ($aSubmittedFile['error'] == UPLOAD_ERR_PARTIAL) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::INCOMPLETE_UPLOADED_FILE);
		}
		
		if ($aSubmittedFile['error'] == UPLOAD_ERR_NO_FILE) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::MISSING_UPLOADED_FILE);
		}
		
		if (!is_uploaded_file ($aSubmittedFile['tmp_name'])) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::INVALID_UPLOADED_FILE);
		}
		
		if (isset ($this->_aParameters['MaxFileSize']) && filesize ($aSubmittedFile['tmp_name']) > $this->_aParameters['MaxFileSize']) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::MAX_AUTHORIZED_SIZE);
		}
		
		if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $aSubmittedFile['name'])) {
			Loader::loadBase ('Exceptions.InvalidFormatException');
			throw new InvalidFormatException (InvalidFormatException::FILE_FORMAT);
		}
		
		if ((count ($this->_aParameters['AllowedExt']) > 0) && 
			!(in_array (preg_replace ('`.*\.([^\.]*)$`', '$1', $aSubmittedFile['name']), $this->_aParameters['AllowedExt']))) {
			Loader::loadBase ('Exceptions.InvalidFormatException');
			throw new InvalidFormatException (InvalidFormatException::EXTENSION_FORMAT);
		}
		
		if ($this->_aParameters['IsImage'] && !$this->isImage($aSubmittedFile['tmp_name'])) {
			Loader::loadBase ('Exceptions.InvalidFormatException');
			throw new InvalidFormatException (InvalidFormatException::IMAGE_FORMAT);
		}
		
		if (!is_dir ($this->_aParameters['DestinationFolder']) && $this->_aParameters['CreateSubFolders'])
			$this->createSubFolders ();
		
		if (!is_dir ($this->_aParameters['DestinationFolder']) && !$this->_aParameters['CreateSubFolders']) {
			Loader::loadBase ('Exceptions.NotFoundException');
			throw new NotFoundException ($this->_aParameters['DestinationFolder']);
		}
		
		$this->_aParameters['FileName'] = (isset ($this->_aParameters['FileName'])) ? $this->_aParameters['FileName'] : $aSubmittedFile['name'];
		
		if ($this->_aParameters['CleanFileName'])
			$this->_aParameters['FileName'] = $this->cleanFileName ($this->_aParameters['FileName']);
		
		$this->_aParameters['FilePath'] = $this->_aParameters['DestinationFolder'].$this->_aParameters['FileName'];

		if (file_exists ($this->_aParameters['FilePath']) && !$this->_aParameters['Rename']) {
			Loader::loadBase ('Exceptions.AlreadyExistException');
			throw new AlreadyExistException ($this->_aParameters['FilePath']);
		}
			
		if (file_exists ($this->_aParameters['FilePath']) && $this->_aParameters['Rename'])
			$this->_aParameters['FilePath'] = $this->renameFile ($this->_aParameters['FilePath']);
		
		if (!move_uploaded_file ($aSubmittedFile['tmp_name'], $this->_aParameters['FilePath'])) {
			Loader::loadBase ('Exceptions.FileUploadException');
			throw new FileUploadException (FileUploadException::UPLOAD_FAILED);
		}
		
		$this->_aParameters['FileSize'] = filesize ($aSubmittedFile['tmp_name']);
		$this->_aParameters['Ext'] = preg_replace ('`.*\.([^\.]*)$`', '$1', $aSubmittedFile['name']);
		
		return $sFilePath;
	}
}
?>