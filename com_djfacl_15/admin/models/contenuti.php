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

class contenutiModelcontenuti extends JModel {
	
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
	 * Method to get a contenuti data
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
	 * Method to get a pagination object for the contenuti
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
		
		$search = JRequest::getVar ( 'search' );
		if (isset ( $post ['search'] ))
			$search = $post ['search']; // se c'è un parametro search settato.
		

		$grupposearch = utility::getDjfVar('idgroup','0' );
		

		if ($search != "")
			$query_search = " 
		and (
		u.name like '%" . $search . "%'
		or c.title like '%" . $search . "%'
		or g.name like '%" . $search . "%'
		or comp.`option` like '%" . $search . "%'
		or css.css_block like '%" . $search . "%'
		or a.title like '%" . $search . "%'
		or s.title like '%" . $search . "%'
		or h.jtask like '%" . $search . "%'
		) 		
		";
		else
			$query_search = "";
		
		if ($grupposearch != "") {
			if ($grupposearch=="0")$query_gruppo = ""; else
			$query_gruppo = " and (g.id = '" . $grupposearch . "')";
		} else
			$query_gruppo = "";
		
		$query = ' SELECT 
		
		h.id as id, 
		h.site_admin,
		h.id_article, h.id_modules,
		h.id_category as id_category,
		case (h.css_block)
		WHEN "999999" THEN "Tutti"
		WHEN "0" THEN "Nessuno"
		else css.css_block END as css_block,
		
		
		
		
		u.name as utente, 
		g.name as gruppo, 
		
		
		
		case (h.id_modules)
		WHEN "999999" THEN "Tutti"
		WHEN "0" THEN "Nessuno"
		else modu.title END as modulo,
		
		
		
		
		
		case (h.id_components)
		WHEN "999999" THEN "Tutte"
		WHEN "0" THEN "Nessuna"
		else comp.`option` END as componente,
		
			
		
		
		a.title as articolo,
		
		case (h.id_category)
		WHEN "999999" THEN "Tutte"
		WHEN "0" THEN "Nessuna"
		else c.title  END as categoria,
		
		case (h.id_modules)
		WHEN "999999" THEN "Tutti"
		WHEN "0" THEN "Nessuno"
		else m.name  END as menu,		

		case (h.id_section)
		WHEN "999999" THEN "Tutte"
		WHEN "0" THEN "Nessuna"
		else s.title  END as sezione,		
		
		
		case (h.jtask)
		WHEN "999999" THEN "Tutte"
		WHEN "0" THEN "Nessuna"
			else (concat(j.name," - ",j.jtask))  END as jtask
	
		
		' . ' FROM
		' . $this->_table_prefix . 'contenuti as h
		left join #__core_acl_aro_groups as g on (g.id = h.id_group)
		left join #__users as u on (u.id = h.id_users)
		left join #__sections as s on (s.id = h.id_section)
		left join #__categories as c on (c.id = h.id_category)
		left join #__modules as modu on (modu.id = h.id_modules)
		left join #__djfacl_components as comp on (comp.id = h.id_components)
		left join #__menu m on (m.id = h.id_item)
		left join #__content as a on (a.id = h.id_article)
		left join #__djfacl_cssblock as css on (css.id = h.css_block)
		left join #__djfacl_jtask as j on (j.id = h.jtask)
			
		where h.id=h.id and h.id > 13 ' . $query_search . $query_gruppo . $orderby;
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
	
	function getComponents($order = '`option`') {
		global $mainframe;
		$query = 'SELECT id AS value, `option` AS text' . ' FROM #__djfacl_components where parent=0 ORDER BY ' . $order;
		$this->_db->setQuery ( $query );
		
		return $this->_db->loadObjectList ();
	}
	
	function getGruppi($order = 'name') {
		global $mainframe;
		$query = 'SELECT id AS value, name AS text FROM #__core_acl_aro_groups ORDER BY ' . $order;
		//echo $query;
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ();
	}

}

?>

