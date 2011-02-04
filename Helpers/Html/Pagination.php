<?php
Loader::loadBase ('Helpers.Html.iHtmlHelpers');

/**
 * @name Pagination
 * Html Helpers to create a Pagination
 * 
 * @package Catapult.Helpers.Html
 * @filesource Pagination.php
 * 
 * @author Cyril Nicodème
 * @version 0.1
 * 
 * @since 05/03/2008
 * 
 * @todo Refaire le code de facon plus OO :p
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
class Pagination implements iHtmlHelpers {
	/**
	 * @var Int $_iEntries
	 * Contain the number of entries the element have
	 */
	private $_iEntries;
	
	/**
	 * @var Int $_iCurrentPage
	 * Indicate which page is currently viewed
	 */
	private $_iCurrentPage;
	
	/**
	 * @var Int $_iLimit
	 * Indicate how many element to display per page
	 */
	private $_iLimit;
	
	/**
	 * @var Int $_iDisplayType
	 * Indicate which kind of pagination choose
	 */
	private $_iDisplayType;
	
	/**
	 * @constants
	 */
	const ALL = 0; // All the values
	const SPLITTED = 1; // 0 | 1 | .. | x | y | z | ... | max
	const BETWEEN = 2; // Google style
	const TEXTUAL = 3; // << < page x of yyy > >>
	
	/**
	 * @var Array $_aParams
	 * Contain the main structure to display
	 */
	private $_aParams = array (
								'Class' => 'pagination',
								'VarName' => 'page',
								'TextPrepend' => 'Page ',
								'TextAppend' => '',
								'Separator' => ' | ', 
								'Jumper' => '...'
								);
	
	/**
	 * @var $_oRouter
	 * Contain an instance of Router
	 */
	private $_oRouter;
	
	/**
	 * @name public function __construct
	 * Constructor
	 * Set the variables for the displaying
	 *
	 * @param aRouter $oRouter
	 * @param Int $iEntries
	 * @param Int $iCurrentPage
	 * @param Int $iLimit
	 */
	public function __construct (aRouter $oRouter, $iEntries, $iCurrentPage = 0, $iLimit = 25, $iDisplayType = Pagination::ALL) {
		if (!is_object ($oRouter) || !($oRouter instanceof aRouter)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::OBJECT_NEEDED);
		}
		
		if (!is_int ($iEntries)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iCurrentPage)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (3, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iLimit)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (4, InvalidTypeException::INTEGER_NEEDED);
		}
		
		if (!is_int ($iDisplayType)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (5, InvalidTypeException::INTEGER_NEEDED);
		}
		
		$this->_oRouter = $oRouter;
		$this->_iEntries = $iEntries;
		$this->_iCurrentPage = $iCurrentPage;
		$this->_iLimit = $iLimit;
		$this->_iDisplayType = $iDisplayType;
	}
	
	/**
	 * @public function __get
	 * Magic method get to retrieve values from the Params array
	 * 
	 * @param String $sKey
	 * 
	 * @return String
	 */
	public function __get ($sKey) {
		if (isset ($this->_aParams[$sKey]))
			return $this->_aParams[$sKey];
		else {
			Loader::loadBase ('Exceptions.IllegalKeyException');
			throw new IllegalKeyException ($sKey);
		}
	}
	
	/**
	 * @name public function __set
	 * Magic method set use to modify values from the Params array
	 *
	 * @param String $sKey
	 * @param String $sValue
	 * 
	 * @return void
	 */
	public function __set ($sKey, $sValue) {
		if (!is_string ($sValue)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (2, InvalidTypeException::STRING_NEEDED);
		}
		
		if (isset ($this->_aParams[$sKey]))
			$this->_aParams[$sKey] = $sValue;
	}
	
	/**
	 * @name public function setDisplayType
	 * Define the kind of display for the pagination
	 *
	 * @param Constant $iConstant
	 * 
	 * @return void
	 */
	public function setDisplayType ($iConstant) {
		if ($iConstant >= 0 && $iConstant <= 3)
			$this->_iDisplayType = $iConstant;
		else {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
	}
	
	/**
	 * @name public function generate
	 * Generate the pagination as string used for the rendering
	 *
	 * @return String
	 */
	public function generate ($iLimit = null) {
		if (!isset ($iLimit))
			$iLimit = 3;
		elseif (isset ($iLimit) && !is_int ($iLimit)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::INTEGER_NEEDED);
		}
		
		switch ($this->_iDisplayType) {
			case Pagination::SPLITTED:
				return $this->_splitted ($iLimit);
				break;
			case Pagination::BETWEEN:
				return $this->_between ($iLimit);
				break;
			case Pagination::TEXTUAL:
				return $this->_textual ();
				break;
			default:
				return $this->_all ();
				break;
		}
	}
	
	/**
	 * @name private function _all
	 * Function used to display a list of all pages
	 * 
	 * @return String
	 */
	private function _all () {
		$iNumPage = ceil ($this->_iEntries / $this->_iLimit);
		if ($iNumPage > 1) {
			$sReturnValue = '<div class="'.$this->_aParams['Class'].'">'."\n";

			for ($i = 0; $i < $iNumPage; $i++) {
				if ($i == $this->_iCurrentPage)
					$sReturnValue .= '<span> '.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].' </span>'."\n";
				else
					$sReturnValue .= '<a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>$i)).'" title="'.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].'</a>'."\n";
				
				if (($i+1) < $iNumPage)
					$sReturnValue .= $this->_aParams['Separator'];
			}
			$sReturnValue .= '</div>'."\n";
			
			return $sReturnValue;
		}
		else return '';
	}
	
	/**
	 * @name private function _between
	 * Used to display pages like 1 | 2 | 3 | ... | 12 | 13 | 14 | 15 | 16 | 17 | ... | 25 | 26 | 27
	 * 
	 * @param int $iLimit
	 * 
	 * @return String
	 */
	private function _between ($iLimit) {
		$iNumPage = ceil ($this->_iEntries / $this->_iLimit);
		$sReturnValue = '';
		if ($iNumPage > 1) {
			$iBegin = $this->_iCurrentPage - $iLimit;
			$iEnd = $this->_iCurrentPage + $iLimit;
			
			$iBegin = ($iBegin < $iLimit) ? 0 : $iBegin;
			$iEnd = ($iEnd > ($iNumPage - $iLimit)) ? $iNumPage : $iEnd;
			
			$sReturnValue = '<div class="'.$this->_aParams['Class'].'">'."\n";
			
			if ($iBegin > 0) {
				$sReturnValue .= '<a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>$i)).'" title="'.$this->_aParams['TextPrepend'].'0'.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].'0'.$this->_aParams['TextAppend'].'</a>'."\n";
				$sReturnValue .= $this->_aParams['Separator'];
				$sReturnValue .= $this->_aParams['Jumper'];
				$sReturnValue .= $this->_aParams['Separator'];
			}
			
			for ($i = $iBegin; $i < $iEnd; $i++) {
				if ($i == $this->_iCurrentPage)
					$sReturnValue .= '<span> '.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].' </span>'."\n";
				else
					$sReturnValue .= '<a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>$i)).'" title="'.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].'</a>'."\n";
				
				if (($i+1) < $iEnd)
					$sReturnValue .= $this->_aParams['Separator'];
			}
			
			if ($iEnd < $iNumPage) {
				$sReturnValue .= $this->_aParams['Separator'];
				$sReturnValue .= $this->_aParams['Jumper'];
				$sReturnValue .= $this->_aParams['Separator'];
				$sReturnValue .= '<a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>$i)).'" title="'.$this->_aParams['TextPrepend'].$iNumPage.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].$iNumPage.$this->_aParams['TextAppend'].'</a>'."\n";
			}
			
			$sReturnValue .= '</div>'."\n";
		}
		
		return $sReturnValue;
	}
	
	/**
	 * @name private function _splitted
	 * Used to display pages like 5 | 6 | 7 | 8 | 9 | 10 (google style)
	 * 
	 * @param int $iLimit
	 * 
	 * @return String
	 */
	private function _splitted ($iLimit) {
		$iNumPage = ceil ($this->_iEntries / $this->_iLimit);
		$sReturnValue = '';
		if ($iNumPage > 1) {
			$iBegin = $this->_iCurrentPage - $iLimit;
			$iEnd = $this->_iCurrentPage + $iLimit;
			
			$iBegin = ($iBegin < 0) ? 0 : $iBegin;
			$iEnd = ($iEnd > $iNumPage) ? $iNumPage : $iEnd;
			
			$sReturnValue = '<div class="'.$this->_aParams['Class'].'">'."\n";
			
			for ($i = $iBegin; $i < $iEnd; $i++) {
				if ($i == $this->_iCurrentPage)
					$sReturnValue .= '<span> '.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].' </span>'."\n";
				else
					$sReturnValue .= '<a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>$i)).'" title="'.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].$i.$this->_aParams['TextAppend'].'</a>'."\n";
				
				if (($i+1) < $iEnd)
					$sReturnValue .= $this->_aParams['Separator'];
			}
			
			$sReturnValue .= '</div>'."\n";
		}
		
		return $sReturnValue;
	}
	
	/**
	 * @name private function _textual
	 * Used to display pages like < x/y >
	 * 
	 * @return String 
	 */
	private function _textual () {
		$iNumPage = ceil ($this->_iEntries / $this->_iLimit);
		$sReturnValue = '';
		if ($iNumPage > 1) {
			$sReturnValue = '<div class="'.$this->_aParams['Class'].'">'."\n";
			
			if ($this->_iCurrentPage > 0)
				$sReturnValue = '<a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>0)).'" title="'.$this->_aParams['TextPrepend'].'<'.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].'<'.$this->_aParams['TextAppend'].'</a> ';
			
			$sReturnValue = $this->_iCurrentPage.'/'.$iNumPage;
			
			if ($this->_iCurrentPage < $iNumPage)
				$sReturnValue = ' <a href="'.$this->_oRouter->appendUri (array ($this->_aParams['VarName']=>$iNumPage)).'" title="'.$this->_aParams['TextPrepend'].'>'.$this->_aParams['TextAppend'].'">'.$this->_aParams['TextPrepend'].'<'.$this->_aParams['TextAppend'].'</a>';
			
			$sReturnValue .= '</div>'."\n";
		}
		
		return $sReturnValue;
	}
}
?>