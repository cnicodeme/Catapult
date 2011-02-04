<?php
/**
 * @name TableMaker
 * Html helpers to create a Table
 * 
 * @package Catapult.Helpers.Html
 * @filesource TableMaker.php
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
class TableMaker {
	/**
	 * @var String $_sCaption
	 * Contain the caption of the table
	 */
	protected $_sCaption = Null;
	
	/**
	 * @var String $_sBreakLine
	 * What kind of breakline use after an end of line
	 */
	protected $_sBreakLine = "\n";
	
	/**
	 * @var Array $_aHeader
	 * Contain the head columns values
	 */
	protected $_aHeader = array ();
	
	/**
	 * @var Array $_aFooter
	 * Contain the footer columns values
	 */
	protected $_aFooter = array ();
	
	/**
	 * @var Array $_aRows
	 * Contain the body of the array
	 */
	protected $_aRows = array ();
	
	/**
	 * @var Array $_aTemplate
	 * Contain the template type (which html tag to use)
	 */
	protected $_aTemplate = array ();
	
	/**
	 * @name public function setCaption
	 * Used to set the Caption of the array
	 *
	 * @param String $sCaption
	 * 
	 * @return void
	 */
	public function setCaption ($sCaption) {
		if (!is_string ($sCaption)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::STRING_NEEDED);
		}
		
		$this->_sCaption = $sCaption;
	}
	
	/**
	 * @name public function setHeader
	 * Used to set the header of the table
	 *
	 * @param Array $aHeader
	 * 
	 * @return void
	 */
	public function setHeader ($aHeader) {
		if (!is_array ($aHeader)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$this->_aHeader = $aHeader;
	}
	
	/**
	 * @name public function setFooter
	 * Used to set the footer of the table
	 *
	 * @param Array $aFooter
	 * 
	 * @return void
	 */
	public function setFooter ($aFooter) {
		if (!is_array ($aFooter)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$this->_aFooter = $aFooter;
	}
	
	/**
	 * @name public function addRow
	 * Used to add a new row at the table
	 * 
	 * @param Mixed $mRow (Array or String/Decimal)
	 * @param Mixed $mRow2 (String/Decimal)
	 * @param Mixed $mRow3 (String/Decimal)
	 * @param Mixed $mRow... (String/Decimal)
	 * 
	 * @return void
	 */
	public function addRow () {
		$aRows = func_get_args ();
		$this->_aRows[] = (is_array ($aRows[0])) ? $aRows[0] : $aRows;
	}

	/**
	 * @name public function clear
	 * Force all the values (Headers, Footers, Rows and Template) to be empty
	 * 
	 * @return void
	 */
	public function clear () {
		$this->_aHeader = array ();
		$this->_aFooter = array ();
		$this->_aRows = array ();
		$this->_aTemplate = array ();
	}
	
	/**
	 * @name public function setTemplate
	 * Set an orignal template
	 *
	 * @param Array $aTemplate
	 * 
	 * @return void
	 */
	public function setTemplate ($aTemplate) {
		if (!is_array ($aTemplate)) {
			Loader::loadBase ('Exceptions.InvalidTypeException');
			throw new InvalidTypeException (1, InvalidTypeException::ARRAY_NEEDED);
		}
		
		$this->_aTemplate = array_merge ($this->_aTemplate, $aTemplate);
	}
	
	/**
	 * @name public function generate
	 * Create the rendered Table
	 *
	 * @return string
	 */
	public function generate () {
		if (count ($this->_aTemplate) == 0)
			$this->_aTemplate = $this->_setDefaultTemplate ();
		
		$aOutput = array ();
		$aOutput[] = $this->_aTemplate['table_open'];
		
		if (isset ($this->_sCaption))
			$aOutput[] = $this->_aTemplate['caption_open'].$this->_sCaption.$this->_aTemplate['caption_close'];
		
		if (isset ($this->_aHeader[0])) {
			$aOutput[] = $this->_aTemplate['header_open'];
			$aOutput[] = $this->_aTemplate['header_row_open'];
			foreach ($this->_aHeader as $sHeaders) {
				$aOutput[] = $this->_aTemplate['header_cell_open'].$sHeaders.$this->_aTemplate['header_cell_close'];
			}
			$aOutput[] = $this->_aTemplate['header_row_close'];
			$aOutput[] = $this->_aTemplate['header_close'];
		}

		if (isset ($this->_Footer[0])) {
			$aOutput[] = $this->_aTemplate['footer_open'];
			$aOutput[] = $this->_aTemplate['footer_row_open'];
			foreach ($this->_aHeader as $sFooters) {
				$aOutput[] = $this->_aTemplate['footer_cell_open'].$sFooters.$this->_aTemplate['footer_cell_close'];
			}
			$aOutput[] = $this->_aTemplate['footer_row_close'];
			$aOutput[] = $this->_aTemplate['footer_close'];
		}
		
		if (isset ($this->_aRows[0])) {
			$aOutput[] =  $this->_aTemplate['body_open'];
			$iCountRows = count ($this->_aRows);
			for ($iRows = 0; $iRows < $iCountRows; $iRows++) {
				$aOutput[] =  (($iRows%2) == 0) ? $this->_aTemplate['body_row_open'] : $this->_aTemplate['body_alt_row_open'];
				
				$iCountCells = count ($this->_aRows[$iRows]);
				for ($iCells = 0; $iCells < $iCountCells; $iCells++) {
					$aOutput[] =  (($iCells%2) == 0) ? $this->_aTemplate['body_cell_open'] : $this->_aTemplate['body_alt_cell_open'];
					
					$aOutput[] = $this->_aRows[$iRows][$iCells];
					
					$aOutput[] =  (($iCells%2) == 0) ? $this->_aTemplate['body_cell_close'] : $this->_aTemplate['body_alt_cell_close'];
				}
				
				$aOutput[] =  (($iRows%2) == 0) ? $this->_aTemplate['body_row_close'] : $this->_aTemplate['body_alt_row_close'];
			}
			$aOutput[] =  $this->_aTemplate['body_open'];
		}
		
		$aOutput[] = $this->_aTemplate['table_close'];
		
		if (count ($aOutput) > 2)
			return implode($this->_sBreakLine, $aOutput);
		else
			return '';
	}
	
	/**
	 * @name protected function _setDefaultTemplate
	 * Set the default template to use for the rendering
	 * 
	 * @return Array 
	 */
	protected function _setDefaultTemplate () {
		return array (
				'table_open' => '<table border="0">',

				'caption_open' => "\t<caption>" ,
				'caption_close' => "\t</caption>",
				
				'header_open' => "\t<thead>",
				'header_row_open' => "\t\t<tr>",
				'header_cell_open' => "\t\t\t<th>",
				'header_cell_close' => "\t\t\t</th>",
				'header_row_close' => "\t\t</tr>",
				'header_close' => "\t</thead>",
				
				'footer_open' => "\t<tfoot>",
				'footer_row_open' => "\t\t<tr>",
				'footer_cell_open' => "\t\t\t<td>",
				'footer_cell_close' => "\t\t\t</td>",
				'footer_row_close' => "\t\t</tr>",
				'footer_close' => "\t<tfoot>",
				
				'body_open' => "\t<tbody>",
				
				'body_row_open' => "\t\t<tr>",
				'body_cell_open' => "\t\t\t<td>",
				'body_cell_close' => "\t\t\t</td>",
				'body_row_close' => "\t\t</tr>",
				
				'body_alt_row_open' => "\t\t<tr>",
				'body_alt_cell_open' => "\t\t\t<td>",
				'body_alt_cell_close' => "\t\t\t</td>",
				'body_alt_row_close' => "\t\t</tr>",
				
				'body_close' => "\t</tbody>",
				
				'table_close' => "</table>");
	}
}
?>