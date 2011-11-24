<?php
/**
* @package HelloWorld
* @version 1.5
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: iInclude library dependencies
jimport('joomla.application.component.model');


/**
* helloworld Table class
*
* @package		Joomla
* @subpackage	helloworlds
* @since 1.0
*/
class Tablegruppi_icone_detail extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = 0;
	
	var $idgroup = 0;
	var $idicon = 0;
	var $ordering = 0;
	var $checked_out = 0;
	var $checked_out_time = 0;
	

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function Tablegruppi_icone_detail(& $db) 
	{
		//initialize class property
	  $this->_table_prefix = '#__djfacl_';
			
		parent::__construct($this->_table_prefix.'gruppi_icone', 'id', $db);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/

	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] )) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	
	function check()
	{

		/** check for valid name */
		if (trim($this->id) == '') {
			$this->_error = JText::_('YOUR djfacl MUST CONTAIN A TITLE.');
			return false;
		}

		$query = 'SELECT id FROM '.$this->_table_prefix.'gruppi_icone 
		WHERE id = '.$this->id.' or (idgroup='.$this->idgroup.' and idicon='.$this->idicon.')';
		$this->_db->setQuery($query);
		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->_error = JText::sprintf('WARNNAMETRYAGAIN', JText::_('Hadf'));
			
			return false;
		}
		return true;
	}
}
?>
