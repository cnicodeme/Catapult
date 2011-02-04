<?php
/**
 * @name BasicHtml
 * Html Helpers to create HTML elements
 * 
 * @package Catapult.Helpers.Html
 * @filesource BasicHtml.php
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
class BasicHtml {
	/**
	 * @name private function _makeOptions
	 * Make the options look like key="value"
	 *
	 * @param Array $aOptions
	 * 
	 * @return String
	 */
	private function _makeOptions ($aOptions) {
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$sReturn = ' ';
		foreach ($aOptions as $sKey=>$sValue) {
			$sReturn .= $sKey.'="'.$sValue.'"';
		}
		
		return $sReturn;
	}

	/**
	 * @name private function _makeList
	 * Create the specified list
	 *
	 * @param String $sKind, default 'ul'
	 * @param String $aValues (could be an array of values or an array of ['options'] = array AND ['value'] = value)
	 * @param Array $aOptions
	 * 
	 * @return String
	 */
	private function _makeList ($sKind = 'ul', $aValues, $aOptions = array ()) {
		if (!is_string ($sKind)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aValues)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::ARRAY_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$sReturn = '<'.$sKind.$this->_makeOptions ($aOptions).'>'."\n";
		foreach ($aValues as $mValue) {
			if (is_array ($mValue)) {
				$sReturn .= "\t".'<li'.((isset ($mValue['options'])) ? $this->_makeOptions ($mValue['options']) : '').'>'.$mValue['value'].'</li>';
			}
			elseif (is_string ($mValue) || is_int ($mValue)) {
				$sReturn .= "\t".'<li>'.$mValue.'</li>'."\n";
			}
		}
		
		return $sReturn.'</'.$sKind.'>';
	}
	
	/**
	 * @name public function meta
	 * Create the <meta ..> html element
	 * 
	 * @param String $sName
	 * @param String $sContent
	 * @param Boolean $bIsHttpEquiv
	 * 
	 * @return unknown
	 */
	public function meta ($sName, $sContent, $bIsHttpEquiv = false) {
		if (!is_string ($sName)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sContent)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_bool ($bIsHttpEquiv)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::BOOLEAN_NEEDED);
		}
		
		if ($bIsHttpEquiv)
			return '<meta http-equiv="'.$sName.'" content="'.$sContent.'" />';
		else
			return '<meta name="'.$sName.'" content="'.$sContent.'" />';
	}
	
	/**
	 * @name public function css
	 * Make the <link rel="stylesheet" ..> html element
	 * 
	 * @param String $sPath
	 * @param Array $aOptions
	 * 
	 * @return String
	 */
	public function css ($sPath, $aOptions = array ()) {
		if (!is_string ($sPath)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::ARRAY_NEEDED);
		}
		
		return '<link rel="stylesheet" href="'.$sPath.'"'.$this->_makeOptions ($aOptions).' />';
	}
	
	/**
	 * @name public function link
	 * Make the <link ...> html element
	 *
	 * @param String $sRel
	 * @param String $sHref
	 * @param Array $aOptions
	 * 
	 * @return String
	 */
	public function link ($sRel, $sHref, $aOptions = array ()) {
		if (!is_string ($sRel)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sHref)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_array ($aOptions)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		return '<link rel="'.$sRel.'" href="'.$sHref.'"'.$this->_makeOptions ($aOptions).' />';
	}

	/**
	 * @name public function hX
	 * Return an Heading
	 *
	 * @param String $sContent
	 * @param Int $iLevel
	 * 
	 * @return String
	 */
	public function hX ($sContent, $iLevel = 1) {
		if (!is_string ($sContent)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_int ($iLevel)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
		}
		
		return '<h'.$iLevel.'>'.$sContent.'</h'.$iLevel.'>';
	}
	
	/**
	 * @name public function anchor
	 * Make the <a ...> html element
	 *
	 * @param String $sHref
	 * @param String $sCaption
	 * @param Array $aOptions
	 * 
	 * @return String
	 */
	public function anchor ($sHref, $sCaption, $aOptions = null) {
		if (!is_string ($sHref)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		if (!is_string ($sCaption)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if ($aOptions) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::ARRAY_NEEDED);
		}
		
		return '<a href="'.$sHref.'" title="'.$sCaption.'"'.$this->_makeOptions ($aOptions).'>'.$sCaption.'</a>';
	}
	
	/**
	 * @name public function numberedList
	 * Make a numbered list <ol>
	 *
	 * @return String
	 */
	public function numberedList () {
		return $this->_makeList ('ol');
	}
	
	/**
	 * @name public function bulletedList
	 * Make a bulleted list <ul>
	 *
	 * @return String
	 */
	public function bulletedList () {
		return $this->_makeList ('ul');
	}
	
	/**
	 * @name public function ws
	 * Return one (or more) White Space
	 *
	 * @param Int $iTimes
	 * 
	 * @return String
	 */
	public function ws ($iTimes = 1) {
		if (!is_int ($iTimes)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
		
		return str_repeat ('&nbsp;', $iTimes);
	}
	
	/**
	 * @name public function br
	 * Return one (or more) break lines
	 *
	 * @param Int $iTimes
	 * 
	 * @return String
	 */
	public function br ($iTimes = 1) {
		if (!is_int ($iTimes)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
		
		return str_repeat ('<br />', $iTimes);
	}
}
?>