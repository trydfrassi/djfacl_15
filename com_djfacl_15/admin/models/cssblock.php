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

class cssblockModelcssblock extends JModel {
	
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
	 * Method to get a cssblock data
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
	 * Method to get a pagination object for the cssblock
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
		
		$search="";
		if (isset($post['search']))
		$search = $post ['search']; // se c'è un parametro search settato.
		

		if ($search != "")
			$query_search = " 
		and (
		h.css_block like '%" . $search . "%'
		) 		
		";
		else
			$query_search = "";
		
		$query = ' SELECT 
		
		h.id, 
		h.css_block
		 FROM
		' . $this->_table_prefix . 'cssblock as h
		where h.id=h.id  ' . $query_search . $orderby;
		//echo ($query);
		
		//exit();
		

		return $query;
	}
	/**
	 * Costruisce l'order by automatico su colonna
	 */
	
	function _buildContentOrderBy() {
		global $mainframe, $context;
		
		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'h.css_block' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', '' );
		
		if ($filter_order == 'ordering') {
			$orderby = ' ORDER BY h.css_block ';
		} else {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir ;
		}
		return $orderby;
	}
	

	
	
	

}

?>

