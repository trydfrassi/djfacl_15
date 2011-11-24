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


class gruppi_utentiModelgruppi_utenti extends JModel {
	
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_table_prefix = null;
	var $_query = null;
	
	function __construct() {
		parent::__construct ();
		global $mainframe, $context;
		$this->_table_prefix = '#__djfacl_';
		
		//DEVNOTE: Parametri di paginazione
		$limit = $mainframe->getUserStateFromRequest ( $context . 'limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 0 );
		$limitstart = $mainframe->getUserStateFromRequest ( $context . 'limitstart', 'limitstart', 0 );
		$this->setState ( 'limit', $limit );
		$this->setState ( 'limitstart', $limitstart );
	}
	
	/**
	 * Method to get a gruppi_utenti data
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
	 * Il metodo restituisce il numero totale di righe del componente
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
	 * Method to get a pagination object for the gruppi_utenti
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
		
		$query_matricola = "";
		$post = JRequest::get ( 'post' );
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$post ['id'] = $cid [0];
		
		$search = utility::getDjfVar('search','');
		$matricola = utility::getDjfVar('matricola','');
		$idgroup = utility::getDjfVar('idgroup','0');
		$iduser = utility::getDjfVar('iduser','');
		$tipologia = utility::getDjfVar('tipologia','djfacl');
		
		if ($idgroup!="0" && $idgroup!="") $queryGroup = ' and idgroup = "'.$idgroup.'"'; else $queryGroup="";
		if ($iduser!="" && $iduser!="0") $queryUser = " and (u.id = '" . $iduser . "') "; else $queryUser ="";
		if ($search!="") $query_search = " and (u.name like '%" . $search . "%' or u.username like '%" . $search . "%'
			or u.id like '%" . $search . "%' or gj.name like '%" . $search . "%' or g.name like '%" . $search . "%'
		) 		
		"; else $query_search=""; 
		if ($matricola!="" && $matricola!="0") $query_matricola = "  and (u.id = '" . $matricola . "')"; else $query_matricola = "";

		if ($tipologia != "0")
		$query_tipologia = ' and h.typology = "'.$tipologia.'"';
		else $query_tipologia="";
	
		$query = ' SELECT 
			h.id as id, 
			u.id as matricola,
			h.typology as typology,
			u.username as username,
			u.name as utente,
			u.gid as gid, 
			gj.name as gruppo_joomla, 
			g.name as gruppo FROM
			#__djfacl_gruppi_utenti as h, 
			#__core_acl_aro_groups as g,
			#__core_acl_aro_groups as gj,
			#__users as u
			where u.id = h.iduser and u.block=0 and g.id = h.idgroup and u.gid = gj.id and u.gid<>25 
			  ' . $query_search . $queryUser .$query_tipologia.$queryGroup.$query_matricola. $orderby;
			//echo ($query);
			//exit();
		
		return $query;
	}
	/**
	 * Costruisce l'order by automatico su colonna
	 */
	
	function _buildContentOrderBy() {
		global $mainframe, $context;
		
		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'u.name' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', '' );
		
		if ($filter_order == 'ordering') {
			$orderby = ' ORDER BY u.name ';
		} else {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
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

