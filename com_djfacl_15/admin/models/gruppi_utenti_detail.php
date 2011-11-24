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

class gruppi_utenti_detailModelgruppi_utenti_detail extends JModel {
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
			$gruppi_utenti_detail = & $this->getTable ();
			if (! $gruppi_utenti_detail->checkout ( $uid, $this->_id )) {
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
			$gruppi_utenti_detail = & $this->getTable ();
			if (! $gruppi_utenti_detail->checkin ( $this->_id )) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Tests if gruppi_utenti_detail is checked out
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
	 * Method to load content gruppi_utenti_detail data
	 */
	
	function _loadData() {
		
		if (empty ( $this->_data )) {
			$query = 'SELECT g.name as gruppo_joomla, h.typology as typology, h.* 
			FROM ' . $this->_table_prefix . 'gruppi_utenti AS h, 
			#__core_acl_aro_groups as g, 
			#__users as u ' . ' 
			WHERE u.gid = g.id and u.id = h.iduser and h.id = ' . $this->_id;
			$this->_db->setQuery ( $query );
			$this->_data = $this->_db->loadObject ();
			return ( boolean ) $this->_data;
		}
		return true;
	}
	
	/**
	 * Method to initialise the gruppi_utenti_detail data
	 */
	
	function _initData() {
		if (empty ( $this->_data )) {
			$detail = new stdClass ( );
			$detail->id=0;
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
		if(!$row->check()){
			$this->setError ( JText::_('CHECK_ESISTENTE'));
			echo (JText::_('CHECK_ESISTENTE'));
			//EXIT();
			return false;
		}
		if (! $row->store ()) {
			$this->setError ( $this->_db->getErrorMsg () );
			echo ($this->_db->getErrorMsg ());
			return false;
		}
		
		
		if (($row->typology)=="joomla"){
		$user = new JUser ( $row->iduser );
		$user->set ( 'gid', $row->idgroup );
		$user->save();
		//echo("salvato utente");
		}
		
		
		return true;
	}
	/**
	 * Method to remove a gruppi_utenti_detail
	 */
	
	function delete($cid = array()) {
		$result = false;
		if (count ( $cid )) {
			$cids = implode ( ',', $cid );
			$query = 'DELETE FROM ' . $this->_table_prefix . 'gruppi_utenti WHERE id IN ( ' . $cids . ' )  ';
			//echo($query);
			//exit();
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return true;
	}
	
	
function importJUser() {
		
		$query = 'INSERT INTO 
				#__djfacl_gruppi_utenti (id, idgroup, iduser, typology)
				select 0, gid, id, "joomla" from #__users c 
				where !exists 
				(select 1 FROM 
				#__users a, 
				#__djfacl_gruppi_utenti b 
				
				where a.id = b.iduser  and c.id = a.id and b.typology="joomla")';
		
		$this->_db->setQuery ( $query );
	
		if (! $this->_db->query ()) {
			$this->setError ( $this->_db->getErrorMsg () );
			return false;
		}
		
		$query = 'update 
			
			#__djfacl_gruppi_utenti a, 
			#__users b 
			set a.idgroup = b.gid
			where a.iduser = b.id and a.typology="joomla"';
		
		$this->_db->setQuery ( $query );
		
		if (! $this->_db->query ()) {
			$this->setError ( $this->_db->getErrorMsg () );
			return false;
		}

		return true;
	
	}
	
	
}

?>
