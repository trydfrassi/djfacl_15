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



class icon_detailModelicon_detail extends JModel {
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
			$icon_detail = & $this->getTable ();
			if (! $icon_detail->checkout ( $uid, $this->_id )) {
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
			$icon_detail = & $this->getTable ();
			if (! $icon_detail->checkin ( $this->_id )) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Tests if icon_detail is checked out
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
	 * Method to load content icon_detail data
	 */
	
	function _loadData() {
		
		if (empty ( $this->_data )) {
			$query = 'SELECT h.* FROM ' . $this->_table_prefix . 'quickicon AS h' . ' WHERE h.id = ' . $this->_id;
			//echo($query);
			$this->_db->setQuery ( $query );
			$this->_data = $this->_db->loadObject ();
			return ( boolean ) $this->_data;
		}
		return true;
	}
	
	/**
	 * Method to initialise the icon_detail data
	 */
	
	function _initData() {
		if (empty ( $this->_data )) {
			
			$detail = new stdClass ( );
			
				
			$detail->id = 0;
			$detail->text = 0;
			$detail->target = 0;
			$detail->icon = 0;
			$detail->ordering = 0;
			$detail->new_window = 0;
			$detail->prefix = 0;
			$detail->postfix = 0;
			$detail->published = 0;
			$detail->title = 0;
			$detail->cm_check = 0;
			$detail->cm_path = 0;
			$detail->akey = 0;
			
			$detail->display = 0;
			$detail->access = 0;
			$detail->gid = 0;
			$detail->checked_out = 0;
			$detail->checked_out_time = 0;
			
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
	 * Method to remove a icon_detail
	 */
	
	function delete($cid = array()) {
		$result = false;
		if (count ( $cid )) {
			$cids = implode ( ',', $cid );
			$query = 'DELETE FROM ' . $this->_table_prefix . 'quickicon WHERE id IN ( ' . $cids . ' ) and id not in (select idicon from #__djfacl_gruppi_icone)';
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			$queryCheck = 'select * FROM ' . $this->_table_prefix . 'quickicon 
			WHERE id IN ( ' . $cids . ' ) and id in (select idicon from #__djfacl_gruppi_icone)';
			
			$arrayris = utility::getArray($queryCheck);
		
			if(sizeof($arrayris)>0){
				echo($this->setError ( 'Icona usata in politica valide'));
				return false;
			}
		}
		return true;
	}
	
	
}

?>
