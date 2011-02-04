<?php
/**
 * @name FormHtml
 * Html Helpers to create HTML form elements
 * 
 * @package Catapult.Helpers.Html
 * @filesource FormHtml.php
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
class FormHtml {
	/**
	 * @name private function _makeType
	 * Make the <input ...> html value
	 *
	 * @param String $sType
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default null
	 * 
	 * @return String
	 */
	private function _makeType ($sType, $sName, $sValue, $aOptions = array ()) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::ARRAY_NEEDED);
		}
		
		return '<input type="'.$sType.'" name="'.$sName.'" value="'.$sValue.'"'.$this->_makeOptions ($aOptions).' />';
	}

	/**
	 * @name private function _makeOptions
	 * Make the options look like key="value"
	 *
	 * @param Array $aOptions
	 * 
	 * @return String
	 */
	private function _makeOptions ($aOptions) {
		if (isset ($aOptions) && is_array ($aOptions)) {
			$sReturn = ' ';
			foreach ($aOptions as $sKey=>$sValue) {
				$sReturn .= ' '.$sKey.'="'.$sValue.'"';
			}
			
			return $sReturn;
		}
		else return '';
	}
	
	/**
	 * @name public function formOpen
	 * Return the <form ...> html value
	 * 
	 * @param String $sAction
	 * @param String $sMethod
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function formOpen ($sAction, $sMethod = 'post', $aOptions = array ()) {
		if (!is_string ($sAction)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sMethod)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		return '<form method="'.$sMethod.'" action="'.$sAction.'"'.$this->_makeOptions ($aOptions).'>';
	}
	
	/**
	 * @name public function multipartFormOpen
	 * Return the <form ...> html value with the multipart section
	 * 
	 * @param String $sAction
	 * @param String $sMethod
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function multipartFormOpen ($sAction, $sMethod = 'post', $aOptions = array ()) {
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$aOptions['enctype'] = 'multipart/form-data';
		return $this->formOpen ($sAction, $sMethod, $aOptions);
	}
	
	/**
	 * @name public function text
	 * Make the input type="text"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function text ($sName, $sValue, $aOptions = array ()) {
		return $this->_makeType ('text', $sName, $sValue, $aOptions);
	}

	/**
	 * @name public function password
	 * Make the input type="password"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function password ($sName, $sValue, $aOptions = array ()) {
		return $this->_makeType ('password', $sName, $sValue, $aOptions);
	}

	/**
	 * @name public function textarea
	 * Make the <textarea ...> html value
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Int $iCols
	 * @param Int $iRows
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function textarea ($sName, $sValue, $iCols, $iRows, $aOptions = array ()) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_int ($iCols)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iRows)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (5, InvalidTypeException::ARRAY_NEEDED);
		}
				
		return '<textarea name="'.$sName.'" cols="'.$iCols.'" rows="'.$iRows.'"'.$this->_makeOptions ($aOptions).'>'.$sValue.'</textarea>';
	}

	/**
	 * @name public function select
	 * Make the <select ...> html value
	 *
	 * @param String $sName
	 * @param String $aValue
	 * @param Mixed $mKey, default null
	 * @param Array $aOptions, default null
	 * 
	 * @return String
	 */
	public function select ($sName, $aValues, $mKey = null, $aOptions = array ()) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if (isset ($mKey) && !is_scalar ($mKey)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::SCALAR_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$sReturn = '<select name="'.$sName.'"'.$this->_makeOptions ($aOptions).'>'."\n";
		foreach ($aValues as $sOption=>$sCaption) {
			if ($mKey === $sOption)
				$sSelected = ' selected="selected"';
			else
				$sSelected = '';
			
			$sReturn .= "\t".'<option value="'.$sOption.'"'.$sSelected.'>'.$sCaption.'</option>'."\n";
		}
	}
	
	/**
	 * @name public function text
	 * Make the input type="text"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function checkbox ($sName, $sValue, $bChecked = false, $aOptions = array ()) {
		if (!is_bool ($bChecked)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::BOOLEAN_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if ($bChecked)
			$aOptions['selected'] = 'selected';

		return $this->_makeType ('checkbox', $sName, $sValue, $aOptions);
	}
	
	/**
	 * @name public function text
	 * Make the input type="text"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function radio ($sName, $sValue, $bSelected = false, $aOptions = array ()) {
		if (!is_bool ($bSelected)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::BOOLEAN_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if ($bSelected)
			$aOptions['selected'] = 'selected';

		return $this->_makeType ('radio', $sName, $sValue, $aOptions);
	}
	
	/**
	 * @name public function button
	 * Make the <button ..> html value
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function button ($sName, $sValue, $aOptions = array ()) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		return '<button name="'.$sName.'"'.$this->_makeOptions ($aOptions).'>'.$sValue.'</button>';
	}
	
	/**
	 * @name public function submit
	 * Make the input type="submit"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function submit ($sName, $sValue, $aOptions = array ()) {
		return $this->_makeType ('submit', $sName, $sValue, $aOptions);
	}
	
	/**
	 * @name public function reset
	 * Make the input type="reset"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function reset ($sName, $sValue, $aOptions = array ()) {
		return $this->_makeType ('reset', $sName, $sValue, $aOptions);
	}
	
	/**
	 * @name public function hidden
	 * Make the input type="hidden"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * 
	 * @return String
	 */
	public function hidden ($sName, $sValue) {
		return $this->_makeType ('hidden', $sName, $sValue);
	}
	
	/**
	 * @name public function formClose
	 * Return the closing form
	 * 
	 * @return String
	 */
	public function formClose () {
		return '</form>';
	}
	
	/**
	 * @name public function file
	 * Make the input type="file"
	 *
	 * @param String $sName
	 * @param String $sValue
	 * @param Array $aOptions, default array ()
	 * 
	 * @return String
	 */
	public function file ($sName, $sValue, $aOptions = array ()) {
		return $this->_makeType ('file', $sName, $sValue, $aOptions);
	}
}
?>