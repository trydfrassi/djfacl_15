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

class fieldModelfield extends JModel {
	
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_table_prefix = null;
	//var $_query = null;
	

	function __construct() {
		parent::__construct ();
		global $mainframe, $context;
		$this->_table_prefix = '#__djfappend_';
		
		$config = JFactory::getConfig ();
		$this->setState ( 'limit', $mainframe->getUserStateFromRequest ( 'com_weblinks.limit', 'limit', $config->getValue ( 'config.list_limit' ), 'int' ) );
		
		$limitstart = JRequest::getVar ( 'limitstart' );
		$limit = JRequest::getVar ( 'limit' );
		
		if ($limit != "")
			$this->setState ( 'limitstart', 0 );
		else
			$this->setState ( 'limitstart', JRequest::getVar ( 'limitstart', 0, '', 'int' ) );
	
		//$this->setState ( 'limitstart', ($this->getState ( 'limit' ) != 0 ? (floor ( $this->getState ( 'limitstart' ) / $this->getState ( 'limit' ) ) * $this->getState ( 'limit' )) : 0) );
	}
	
	/**
	 * Method to get a field data
	 *
	 * questo metodo è chiamato da ogni proprietario della vista
	 */
	
	function getData() {
		if (empty ( $this->_data )) {
			$query = $this->_buildQuery ();
			//$this->_query = $query;
			$this->_data = $this->_getList ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		}
		return $this->_data;
	}
	
	/**
	 * Il metodo restituisce il numero totale di righe del modulo
	 */
	
	function getTotal() {
		if (empty ( $this->_total )) {
			$query = $this->_buildQuery ();
			//$query = $this->_query;
			$this->_total = $this->_getListCount ( $query );
		}
		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the field
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
		
		global $mainframe;
		
		$query_search="";
		$query_tipologie_search="";
		$query_valore_search="";
		$query_category_search="";
		$query_section_search="";
		$query_anno_search="";
		$query_stato="";
		
		
		
		
		$orderby = $this->_buildContentOrderBy (); // costruisce l'order by (vedi sotto)
		
		
		$daform = JRequest::getVar('pag');
		//echo("daform = ".$daform);
		
		$limitstart = JRequest::getVar ( 'limitstart' );
		$limit = JRequest::getVar ( 'limit' );
		
		if (empty($daform)){
				//$orderby ="";
				utility::setDjfVar('search','');
				utility::setDjfVar('tipologia','');
				utility::setDjfVar('year','');
				utility::setDjfVar('catid','');
				utility::setDjfVar('sectionid','');
				utility::setDjfVar('anno','');
		} 
		
		$search = utility::getDjfVar('search');
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$id = $cid [0];

		if (!empty ( $search )) $query_search = " and (h.title like '%" . $search . "%' or h.introtext like '%" . $search . "%') ";

		$params = &$mainframe->getParams ();

		$tipologia = $params->get ( 'tipologia' );
		if (empty($tipologia)) $tipologia = utility::getDjfVar('tipologia');

		$valore = $params->get ( 'valore' );
		$anno = $params->get ( 'year' );
		
		if (empty($anno)) $anno = utility::getDjfVar('year');
		
		$catid_fromparam = $params->get ( 'catid' );
		$sectionid_fromparam = $params->get ( 'sectionid' );
		
		//echo("sectionid = ".$sectionid_fromparam);
		
		$esito = utility::check_if_table_exists ( '#__djfappend_field' );
		
		if (!empty($tipologia) && $esito) $query_tipologie_search = " and (select count(*) from #__djfappend_field_type as tipo where tipo.id = df.id_field_type 	and df.id_jarticle = h.id and tipo.name = '$tipologia')>0 ";

		if ($valore != "" && $valore != '0' && $esito)
			$query_valore_search = " and  
				(select count(*) from #__djfappend_field_type as tipo, #__djfappend_field_value as dfvv
				where tipo.id = df.id_field_type
				and df.id_jarticle = h.id
				and tipo.name = '$tipologia'
				and dfvv.id_field_type = tipo.id
				and dfvv.valore = '$valore'
				and df.field_value = dfvv.id)>0
			 	";
		
		$catid = utility::getDjfVar('catid');
		
		if (!empty ( $catid_fromparam )) $catid = $catid_fromparam;
		if (!empty ( $catid )) $query_category_search = " and h.catid = " . $catid;
		if (!empty ( $sectionid_fromparam )) $sectionid = $sectionid_fromparam;
		if (!empty ( $sectionid )) $query_section_search = " and h.sectionid = " . $sectionid;
		
		$user = & JFactory::getUser ();
		$gid = $user->get ( 'gid' );
		$canUpload = utility::canJAccess();
		
		if (!$canUpload) $query_stato = " and h.state = 1";
		if (! empty ( $anno )) $query_anno_search = " and (year(h.created) = " . $anno . ") ";

		$user = & JFactory::getUser ();
			
			if (! empty ( $anno ))	$query_anno_search = " and (year(df.event_date) = " . $anno . ") ";
 			if (!empty ( $search )) $query_search = " and (h.title like '%" . $search . "%'	or h.introtext like '%" . $search . "%' or df.field_value like '%" . $search . "%' ) ";
	
 			
 			if (!empty($tipologia)) $mostraCampi = "si";
 			else $mostraCampi = "";
 			
 			if ($mostraCampi=="si")
 			
 			$joinfield = ",
				df.event_date as event_date,
				dft.name as tiponome,
				df.filename,
				if(dfv.valore is not null ,dfv.valore, df.field_value) as valore,
				dfv.valore as valore1";
 			
 			else $joinfield = "";
 			
 			
 			
 			
 			
			$query = '
			
			SELECT
				distinct
				h.id,
				h.title,
				h.introtext,
				h.state,
				h.sectionid,
				h.created,
				h.created_by,
				h.catid,
				h.sectionid,
				h.hits,
				h.modified,
				h.modified_by,
				h.version'.$joinfield.'
				from #__sections as sect, #__categories as cate, #__content as h
				left join #__djfappend_field as df on (h.id = df.id_jarticle)
				left join #__djfappend_field_type as dft on (df.id_field_type = dft.id and dft.name = "' . $tipologia . '")
				left join #__djfappend_field_value as dfv on (df.field_value = dfv.id and df.id_field_type = dfv.id_field_type)
				
				where 

				sect.id = h.sectionid and cate.id = h.catid 
				and h.state>-2  
				and h.publish_up <= current_timestamp() 
				AND (h.publish_down >= current_timestamp() || h.publish_down = "0000-00-00 00:00:00")  
				' . 
				$query_search . 
				$query_tipologie_search . 
				$query_valore_search . 
				$query_category_search . 
				$query_section_search .
				$query_anno_search . 
				$query_stato . 

			$orderby;
		
		
		$siono = utility::check_if_table_exists("#__djfappend_field");
		if (!$siono) {
			$query = '
			
			SELECT
				distinct
				h.id,
				h.title,
				h.introtext,
				h.state,
				h.sectionid,
				h.created,
				h.created_by,
				h.catid,
				h.sectionid,
				h.hits,
				h.modified,
				h.modified_by,
				h.version'.$joinfield.'
				from #__sections as sect, #__categories as cate, #__content as h
				
				where 

				sect.id = h.sectionid and cate.id = h.catid 
				and h.state>-2  
				and h.publish_up <= current_timestamp() 
				AND (h.publish_down >= current_timestamp() || h.publish_down = "0000-00-00 00:00:00")  
				' . 
				$query_search . 
				$query_tipologie_search . 
				$query_valore_search . 
				$query_category_search . 
				$query_section_search .
				$query_anno_search . 
				$query_stato . 

			$orderby;
		
		}
		
		//echo($query."<br><br>");
		return $query;
	}
	
	/**
	 * Costruisce l'order by automatico su colonna
	 */
	
	function _buildContentOrderBy() {
		
		global $mainframe, $context;
		
		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', '1' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', '' );
		
		if ($filter_order == 'ordering' ) {
			$orderby = ' ORDER BY 1 ';
		} else {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , h.ordering ';
		}
		return $orderby;
	}
	
	function getField_type($order = 'name') {
		global $mainframe;
		$query = 'SELECT id AS value, name AS text FROM #__djfappend_field_type 
		ORDER BY ' . $order;
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ();
	}
	
	function getEvent_date($order = 'name') {
		global $mainframe;
		$query = 'SELECT distinct year(event_date) AS value, year(event_date) as text FROM #__djfappend_field ORDER BY 1';
		//echo($query);
		$this->_db->setQuery ( $query );
		return $this->_db->loadObjectList ();
	}
}

?>

