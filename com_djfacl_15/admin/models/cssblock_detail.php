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



class cssblock_detailModelcssblock_detail extends JModel {
	var $_id = null;
	var $_data = null;
	var $_table_prefix = null;
	
	function __construct() {
		parent::__construct ();
		$this->_table_prefix = '#__djfacl_';
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
			$cssblock_detail = & $this->getTable ();
			if (! $cssblock_detail->checkout ( $uid, $this->_id )) {
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
			$cssblock_detail = & $this->getTable ();
			if (! $cssblock_detail->checkin ( $this->_id )) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Tests if cssblock_detail is checked out
	 */
	
	function isCheckedOut($uid = 0) {
		if ($this->_loadData ()) {
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}
	
	/**
	 * Method to load content cssblock_detail data
	 */
	
	function _loadData() {
		
		if (empty ( $this->_data )) {
			$query = 'SELECT h.* FROM ' . $this->_table_prefix . 'cssblock AS h' . ' WHERE h.id = ' . $this->_id;
			$this->_db->setQuery ( $query );
			$this->_data = $this->_db->loadObject ();
			return ( boolean ) $this->_data;
		}
		return true;
	}
	
	/**
	 * Method to initialise the cssblock_detail data
	 */
	
	function _initData() {
		if (empty ( $this->_data )) {
			$detail = new stdClass ( );
			$detail->id = 0;
			$detail->id_users = 0;
			$detail->id_group = 0;
			$detail->id_components = 0;
			$detail->id_modules = 0;
			$detail->id_category = 0;
			$detail->site_admin = 1;
			$detail->jtask = "all";
			$detail->ordering = 0;
			$detail->checked_out = 0;
			$detail->checked_out_time = 0;
			$detail->published = 0;
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
	 * Method to remove a cssblock_detail
	 */
	
	function delete($cid = array()) {
		$result = false;
		if (count ( $cid )) {
			$cids = implode ( ',', $cid );
			$query = 'DELETE FROM ' . $this->_table_prefix . 'cssblock WHERE id IN ( ' . $cids . ' ) and id not in (select css_block from #__djfacl_contenuti)';
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			$queryCheck = 'select * FROM ' . $this->_table_prefix . 'cssblock 
			WHERE id IN ( ' . $cids . ' ) and id in (select css_block from #__djfacl_contenuti)';
			
			$arrayris = utility::getArray($queryCheck);
		
			if(sizeof($arrayris)>0){
				echo($this->setError ( 'Blocco Css usato in politiche valide'));
				return false;
			}
		}
		return true;
	}
	
	
}

?>
