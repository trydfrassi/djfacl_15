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


class gruppi_detailModelgruppi_detail extends JModel {
	var $_id = null;
	var $_data = null;
	var $_table_prefix = null;
	
	function __construct() {
		parent::__construct ();
		//$this->_table_prefix = '#__djfacl_';
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
			$gruppi_detail = & $this->getTable ();
			if (! $gruppi_detail->checkout ( $uid, $this->_id )) {
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
			$gruppi_detail = & $this->getTable ();
			if (! $gruppi_detail->checkin ( $this->_id )) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Tests if gruppi_detail is checked out
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
	 * Method to load content gruppi_detail data
	 */
	
	function _loadData() {
		
		if (empty ( $this->_data )) {
			$query = 'SELECT h.* FROM #__core_acl_aro_groups AS h' . ' WHERE h.id = ' . $this->_id;
			$this->_db->setQuery ( $query );
			$this->_data = $this->_db->loadObject ();
			return ( boolean ) $this->_data;
		}
		return true;
	}
	
	/**
	 * Method to initialise the gruppi_detail data
	 */
	
	function _initData() {
		if (empty ( $this->_data )) {
			$detail = new stdClass ( );
			$detail->id = 0;
			$detail->parent_id = 0;
			$detail->name = 0;
			$detail->lft = 0;
			$detail->rgt = 0;
			$detail->value = 0;
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
		echo("id = ".$row->id);
		//exit(); 
		$row->value = "djfacl";
		
		$rowFromQuery = $this->getRowFromQuery('#__core_acl_aro_groups',$row->parent_id);
		
		echo($rowFromQuery->lft);
		echo($rowFromQuery->rgt);
		
	
		$row->lft = $rowFromQuery->rgt;
		$vecchioRgt = $rowFromQuery->rgt;;
		$row->rgt = $rowFromQuery->rgt+1;
		
		
		/*
		$toCreateGroupQuery = 
		
		"
		SET @parent_id = '$row->parent_id';
		SET @new_name = '$row->name';
 
		
		SELECT @ins_id := id, @ins_lft := lft, @ins_rgt := rgt
		FROM #__core_acl_aro_groups
		WHERE id = @parent_id;
 
		SELECT @new_id := MAX(id) + 1 FROM #__core_acl_aro_groups;
	 
		
		UPDATE #__core_acl_aro_groups SET rgt=rgt+2 WHERE rgt>=@ins_rgt;
		UPDATE #__core_acl_aro_groups SET lft=lft+2 WHERE lft>@ins_rgt;
	 
		
		INSERT INTO #__core_acl_aro_groups (id,parent_id,name,lft,rgt)
		VALUES (@new_id,@ins_id,@new_name,@ins_rgt,@ins_rgt+1);
		";
		
		echo($toCreateGroupQuery);
		
		
		
			
		if (!utility::executeScriptQuery($toCreateGroupQuery)){
			$this->setError ( $this->_db->getErrorMsg () );
			echo ($this->_db->getErrorMsg ());
			return false;
		}
		*/
		//exit();
		
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
			utility::executeScriptQuery("
			
		UPDATE #__core_acl_aro_groups SET rgt=rgt+2 WHERE rgt>=$vecchioRgt;
		UPDATE #__core_acl_aro_groups SET lft=lft+2 WHERE lft>=$vecchioRgt;
	 
		");
		
		utility::_rebuild_tree('#__core_acl_aro_groups',17,1);
	
		
		return true;
	}
	
	
	
	
	
	
	
	
	/**
	 * Method to remove a gruppi_detail
	 */
	
	function delete($todelete) {
		
		
		
			$rowParent = $this->getRowFromQuery('#__core_acl_aro_groups', $todelete);
			$parent_id = $rowParent->parent_id;
			$rowParent = $this->getRowFromQuery('#__core_acl_aro_groups', $parent_id);
			$name = $rowParent->name;
			
			
			$query = 'update #__users set gid = '.$parent_id.' , usertype = "'.$name.'" where gid = '.$todelete ;
			$this->_db->setQuery ( $query );
			echo("<br>".$query);
			
			if (! $this->_db->query ()) {
				
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			$query = 'update #__djfacl_gruppi_utenti set idgroup = '.$parent_id.' where idgroup = '.$todelete ;
			$this->_db->setQuery ( $query );
			echo("<br>".$query);
			
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
			$query = 'update #__core_acl_groups_aro_map a, #__core_acl_aro b, #__users c set a.group_id = c.gid where a.aro_id = b.id and c.id = b.value ' ;
			$this->_db->setQuery ( $query );
			echo("<br>".$query);
			
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
			$query = 'DELETE FROM #__core_acl_aro_groups WHERE id ='. $todelete ;
			
			$this->_db->setQuery ( $query );
			echo("<br>".$query);
			
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
			
			$query = 'DELETE FROM #__djfacl_gruppi_utenti WHERE idgroup ='. $todelete ;
			$this->_db->setQuery ( $query );
			echo("<br>".$query);
			
			

			//exit();
			
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
			$query = 'DELETE FROM #__djfacl_contenuti WHERE id_group ='. $todelete ;
			$this->_db->setQuery ( $query ); 	
			echo("<br>".$query);
			
			

			//exit();
			
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
			
		return true;
		}
	
	function getRowFromQuery($tabletoquery, $id) {
		global $mainframe;
		$db = & JFactory::getDBO ();
		$query = 'SELECT * FROM '.$tabletoquery. ' where id = '.$id;
			
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if ($rows) {
			foreach ( $rows as $row ) {
				return $row;
			}
		}
		return null;
	}
	
}

?>
