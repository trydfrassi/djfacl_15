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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

//DEVNOTE: import MODEL object class
jimport ( 'joomla.application.component.model' );
//require_once (JPATH_COMPONENT . DS . 'objects' . DS . 'esito_test.php');
//require_once (JPATH_COMPONENT . DS . 'objects' . DS . 'oggetto_dipendente.php');
//require_once (JPATH_COMPONENT . DS . 'objects' . DS . 'corsi_domande.php');

class field_detailModelfield_detail extends JModel {
	var $_id = null;
	var $_data = null;
	var $_table_prefix = null;
	
	function __construct() {
		parent::__construct ();
		$this->_table_prefix = '#__djfappend_';
		$array = JRequest::getVar ( 'cid', 0, '', 'array' );
		$this->setId ( ( int ) $array [0] );
	}
	
	function setId($id) {
		$this->_id = $id;
		$this->_data = null;
	}
	
	function getId() {
		return $this->_id;
	}
	
	function &getData() {
		if ($this->_loadData ()) {
		} else
			$this->_initData ();
		return $this->_data;
	}
	
	/**
	 * Il metodo check serve per vedere se il record è già occupato da un altro utente
	 */
	
	function checkout($uid = null) {
		if ($this->_id) {
			if (is_null ( $uid )) {
				$user = & JFactory::getUser ();
				$uid = $user->get ( 'id' );
			}
			$field_detail = & $this->getTable ();
			if (! $field_detail->checkout ( $uid, $this->_id )) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Il metodo check serve per vedere se il record è già occupato da un altro utente
	 */
	
	function checkin() {
		if ($this->_id) {
			$field_detail = & $this->getTable ();
			if (! $field_detail->checkin ( $this->_id )) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Tests if field_detail is checked out
	 */
	
	
	
	/**
	 * Method to load content field_detail data
	 */
	
	function _loadData() {
		
		if (empty ( $this->_data )) {
			$query = 'SELECT h.*, a.title title, ft.name field_type 
			FROM ' . $this->_table_prefix . 'field AS h, 
			#__content as a, 
			#__djfappend_field_type as ft ' . ' 
			WHERE ft.id = h.id_field_type and a.id = h.id_jarticle and h.id = ' . $this->_id;
			
			//echo($query);
			$this->_db->setQuery ( $query );
			$this->_data = $this->_db->loadObject ();
			return ( boolean ) $this->_data;
		}
		return true;
	}
	
	/**
	 * Method to initialise the field_detail data
	 */
	
	function _initData() {
		if (empty ( $this->_data )) {
			$detail = new stdClass ( );
			$detail->id = 0;
			$detail->params = null;
			$this->_data = $detail;
			return ( boolean ) $this->_data;
		}
		return true;
	}
	
	/**
	 * Method to store the modules text
	 */
	
	function store($data) {
		
		$row = & $this->getTable ();
		//$row->data_aggiornamento = gmdate ( 'Y-m-d H:i:s' );
		if (! $row->bind ( $data )) {
			$this->setError ( $this->_db->getErrorMsg () );
			return false;
		}
		if (! $row->store ()) {
			$this->setError ( $this->_db->getErrorMsg () );
			echo ($this->_db->getErrorMsg ());
			return false;
		}
		return true;
	}
	/**
	 * Method to remove a field_detail
	 */
	
	function delete($cid = array()) {
		$result = false;
		if (count ( $cid )) {
			//$cids = implode ( ',', $cid );
			$query = 'DELETE FROM ' . $this->_table_prefix . 'field WHERE id IN ( ' . $cid . ' )';
			//echo($query);
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return true;
	}
	
	function getField_type($order = 'name') {
		global $mainframe;
		$query = 'SELECT id AS value, name AS text FROM #__djfappend_field_type 
		ORDER BY ' . $order;
		//echo $query;
		$this->_db->setQuery ( $query );
		//exit();
		return $this->_db->loadObjectList ();
	}
	
	
	

}

?>
