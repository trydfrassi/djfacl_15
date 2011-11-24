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
jimport ( 'joomla.application.component.helper' );




/**
 * helloworld Component helloworld Model
 *
 * @author wojta <vojtechovsky@gmail.com>
 * @package		Joomla
 * @subpackage	helloworld
 * @since 1.5
 */
class loaderModelloader extends JModel {
	
	/**
	 * loader data
	 *
	 * @var array
	 */
	var $_data = null;
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;
	
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;
	
	/**
	 * table_prefix - table prefix for all component table
	 * 
	 * @var string
	 */
	var $_table_prefix = null;
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct() {
		parent::__construct ();
		
		global $mainframe, $context;
		
		//initialize class property
		$this->_table_prefix = '#__sapef_';
		
		//DEVNOTE: Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest ( $context . 'limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 0 );
		$limitstart = $mainframe->getUserStateFromRequest ( $context . 'limitstart', 'limitstart', 0 );
		//$params = $mainframe->getConfig();
		

		$this->setState ( 'limit', $limit );
		$this->setState ( 'limitstart', $limitstart );
	
	}
	
	/**
	 * Method to get a loader data
	 *
	 * this method is called from the owner VIEW by VIEW->get('Data');
	 * - get list of all loader for the current data page.
	 * - pagination is spec. by variables limitstart,limit.
	 * - ordering of list is build in _buildContentOrderBy  	 	 	  	 
	 * @since 1.5
	 */
	function getData() 

	{
		
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty ( $this->_data )) {
			$query = $this->_buildQuery ();
			$this->_data = $this->_getList ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		}
		
		return $this->_data;
	}
	
	/**
	 * Method to get the total number of loader items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal() {
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty ( $this->_total )) {
			$query = $this->_buildQuery ();
			$this->_total = $this->_getListCount ( $query );
		}
		
		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the loader
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty ( $this->_pagination )) {
			jimport ( 'joomla.html.pagination' );
			$this->_pagination = new JPagination ( $this->getTotal (), $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		}
		
		return $this->_pagination;
	}
	
	function _buildQuery() {
		$orderby = $this->_buildContentOrderBy ();
		
		$query = ' SELECT h.*, juser.username, u.nome, u.cognome, p.descrizione pacchetto_richiesto, pp.descrizione pacchetto_assegnato ' . ' FROM ' . $this->_table_prefix . 'loader AS h, ' . $this->_table_prefix . 'utenti as u, ' . $this->_table_prefix . 'pacchetti_formativi as p, ' . '#__users as juser, ' . $this->_table_prefix . 'pacchetti_formativi as pp ' . 'where h.id_utente=juser.id and u.id = h.id_utente  and pp.id = h.id_pacchetto_assegnato and p.id = h.id_pacchetto_richiesto ' . $orderby;
		
		//echo ($query);
		

		return $query;
	}
	
	function getMaxUpdateDate() {
		$query = "SELECT max(data_aggiornamento) AS text FROM " . $this->_table_prefix . 'utenti ';
		//echo ($query);
		//exit();
		$this->_db->setQuery ( $query );
		if (! $this->_db->query ()) {
			$this->setError ( $this->_db->getErrorMsg () );
			return '';
		}
		
		$rows = $this->_db->loadObjectList ();
		if ($rows) {
			foreach ( $rows as $row ) {
				$auto = $row->text;
				return $auto;
			}
		}
		return '';
	
	}
	
	/**
	 * 
	 * Metodo utile al collegamento alla banca dati Oracle degli utenti-dipendenti ed al recupero
	 * delle informazioni addizionali e delle nuove assunzioni
	 * 
	 *
	 * @return unknown
	 */
	function loader() {
		
		$esito = "";
		global $mainframe;
		
	}
}
