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

//DEVNOTE: import CONTROLLER object class
jimport ( 'joomla.application.component.controller' );

/**
 * contenuti_detail  Controller
 *
 * @package		Joomla
 * @subpackage	contenuti
 * @since 1.5
 */
class contenuti_detailController extends JController {
	
	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
		
		// Register Extra tasks
		$this->registerTask ( 'add', 'edit' );
	
	}
	
	function edit() {
		
		JRequest::setVar ( 'view', 'contenuti_detail' );
		JRequest::setVar ( 'layout', 'form' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		parent::display ();
		// give me  the contenuti
		$model = $this->getModel ( 'contenuti_detail' );
		$model->checkout ();
	
	}
	
	/**
	 * Funzione di salvataggio
	 *
	 */
	function save() {
		
		$post = JRequest::get ( 'post' );
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$post ['id'] = $cid [0];
		$seccatid = Jrequest::getVar('id_category');
		$arsecatid = explode("-",$seccatid);
		$sectionid = $arsecatid[0];
		$catid = $arsecatid[1];
		
		
		if (!empty($catid)){
			$post['id_section'] = '0';
			$post['id_category'] = $catid;
		}else{
			$post['id_section'] = $sectionid;
			$post['id_category'] = '0';
		}
		if ($catid=="999999" && $sectionid="999999"){
			$post['id_section'] = $sectionid;
			$post['id_category'] = $catid;
		}

		if ($catid=="0" && $sectionid="0"){
			$post['id_section'] = $sectionid;
			$post['id_category'] = $catid;
		}
		
		
		
		$model = $this->getModel ( 'contenuti_detail' );
		if ($model->store ( $post )) {
			$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ) . ' ' . JText::_ ( 'MESSAGGIO_SALVATO' );
		} else {
			$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ) . ' ' . JText::_ ( 'MESSAGGIO_ERRORE_SALVATAGGIO' );
		
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin ();
		$post = JRequest::get ( 'post' );
		
		$gruppo = $post ['grupposearch'];
		JRequest::setVar ( 'grupposearch', $gruppo );
		$searchGroupString = "";
		if ($gruppo != null && $gruppo != "") {
			$searchGroupString = "&grupposearch=$gruppo";
		}
		
		$search = $post ['search'];
		JRequest::setVar ( 'search', $search );
		$searchString = "";
		if ($search != null && $search != "") {
			$searchString = "&search=$search";
		}
		
		$redirect = 'index.php?option=com_djfacl&controller=contenuti' . $searchGroupString . $searchString;
		$this->setRedirect ( $redirect, $msg );
	
	}
	
	/**
	 * function remove
	 */
	
	function remove() {
		global $mainframe;
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		if (! is_array ( $cid ) || count ( $cid ) < 1) {
			JError::raiseError ( 500, JText::_ ( 'Select an item to delete' ) );
		}
		
		$model = $this->getModel ( 'contenuti_detail' );
		if (! $model->delete ( $cid )) {
			echo "<script> alert('" . $model->getError ( true ) . "'); window.history.go(-1); </script>\n";
		}
		
		$post = JRequest::get ( 'post' );
		
		$gruppo = $post ['grupposearch'];
		JRequest::setVar ( 'grupposearch', $gruppo );
		$searchGroupString = "";
		if ($gruppo != null && $gruppo != "") {
			$searchGroupString = "&grupposearch=$gruppo";
		}
		
		$search = $post ['search'];
		JRequest::setVar ( 'search', $search );
		$searchString = "";
		if ($search != null && $search != "") {
			$searchString = "&search=$search";
		}
		
		$this->setRedirect ( 'index.php?option=com_djfacl&controller=contenuti' . $searchGroupString . $searchString );
	}
	
	/**
	 * function cancel
	 */
	
	function cancel() {
		// Checkin the detail
		$model = $this->getModel ( 'contenuti_detail' );
		$model->checkin ();
		$post = JRequest::get ( 'post' );
		
		$gruppo = $post ['grupposearch'];
		JRequest::setVar ( 'grupposearch', $gruppo );
		$searchGroupString = "";
		if ($gruppo != null && $gruppo != "") {
			$searchGroupString = "&grupposearch=$gruppo";
		}
		
		$search = $post ['search'];
		JRequest::setVar ( 'search', $search );
		$searchString = "";
		if ($search != null && $search != "") {
			$searchString = "&search=$search";
		}
		
		$redirect = 'index.php?option=com_djfacl&controller=contenuti' . $searchGroupString . $searchString;
		$this->setRedirect ( $redirect, $msg );
	
	}
	
	function rebuildselect() {
		$field = JRequest::getVar ( 'field' );
		$value = JRequest::getVar ( 'value' );
		
		$select_custom = utility::addArrayItemToSelect ( array (" - TUTTI - " => "999999", " - NESSUNO - " => "0" ) );
		$query = 'select id as value, title as text from #__categories where section = ' . $value . ' order by trim(title)';
		
		if ($value == '0' || $value == '999999')
			$query = 'select id as value, title as text from #__categories order by trim(title)';
		
		$uscita = utility::getAjaxRebuildField ( 'select', $field, $query, $select_custom );
		
		echo ($uscita);
	}
	
	function image() {
		
		$field = JRequest::getVar ( 'field' );
		$value = JRequest::getVar ( 'value' );
		
		
		//echo($field);
		//echo($value);
		
		
		$lista = utility::getQueryArray ( "select icon from #__djfacl_quickicon where id = " . $value );
		if (sizeof ( $lista ) > 0) {
			foreach ( $lista as $questo ) {
				echo ('<img src="' . utility::getBaseUrl () . $questo->icon . '"/>');
				return true;
			}
		}
	
	}

}
