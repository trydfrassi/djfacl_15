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
jimport ( 'joomla.application.component.model' );

class gruppiModelgruppi extends JModel {
	
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_table_prefix = null;
	var $_query = null;
	
	function __construct() {
		parent::__construct ();
		global $mainframe, $context;
		//$this->_table_prefix = '#__djfacl_';
		
		//DEVNOTE: Parametri di paginazione
		$limit = $mainframe->getUserStateFromRequest ( $context . 'limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 0 );
		$limitstart = $mainframe->getUserStateFromRequest ( $context . 'limitstart', 'limitstart', 0 );
		$this->setState ( 'limit', $limit );
		$this->setState ( 'limitstart', $limitstart );
	}
	
	/**
	 * Method to get a gruppi data
	 *
	 * questo metodo è chiamato da ogni proprietario della vista
	 */
	
	function getData() {
		if (empty ( $this->_data )) {
			$query = $this->_buildQuery ();
			$this->_query = $query;
			$this->_data = $this->_getList ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		}
		return $this->_data;
	}
	
	/**
	 * Il metodo restituisce il numero totale di righe del modulo
	 */
	
	function getTotal() {
		if (empty ( $this->_total )) {
			//$query = $this->_buildQuery ();
			$query = $this->_query;
			$this->_total = $this->_getListCount ( $query );
		}
		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the gruppi
	 */
	
	function getPagination() {
		if (empty ( $this->_pagination )) {
			jimport ( 'joomla.html.pagination' );
			$this->_pagination = new JPagination ( $this->getTotal (), $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		}
		return $this->_pagination;
	}
	
	/**
	 * Metodo che effettua la query vera e propria sul db
	 */
	
	function _buildQuery() {
		$orderby = $this->_buildContentOrderBy (); // costruisce l'order by (vedi sotto)

		$post = JRequest::get ( 'post' );
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$post ['id'] = $cid [0];
			
		$query = ' SELECT h.* from #__core_acl_aro_groups as h  ' . $orderby;
		//echo ($query);
		

		return $query;
	}
	
	
	
	/**
	 * Costruisce l'order by automatico su colonna
	 */
	
	function _buildContentOrderBy() {
		global $mainframe, $context;
		
		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'h.id, h.parent_id' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', '' );
		
		if ($filter_order == 'h.ordering') {
			$orderby = ' ORDER BY h.id, h.parent_id ';
		} else {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , h.id, h.parent_id ';
		}
		return $orderby;
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
		//echo ($query);
		//exit();
		

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
		//echo($query);
		//exit();
		

		return true;
	
	}
	
	function purge(){	
			
			$query = ' delete from #__djfacl_componenti where 
			(
			id_users not in (select id from #__users) or 
			id_group not in (select id from #__core_acl_aro_groups) or
			id_components not in (select id from #__djfacl_components)  
			)
			';
			echo($query);
			$this->_db->setQuery ( $query );
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
		$query2 = ' delete from #__djfacl_moduli where 
			(
			id_users not in (select id from #__users) or 
			id_group not in (select id from #__core_acl_aro_groups) or
			id_modules not in (select id from #__modules)  
			)
			';
			echo($query2);
			$this->_db->setQuery ( $query2 );
			if (! $this->_db->query ()) {
				$this->setError ( $this->_db->getErrorMsg () );
				return false;
			}
			
			
			return true;
	}
	

}

?>

