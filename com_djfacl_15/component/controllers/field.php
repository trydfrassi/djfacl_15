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
require_once (JPATH_PLUGINS.DS.'system'.DS.'djflibraries'.DS.'utility.php');

class fieldController extends JController {
	
	function __construct($default = array()) {
		parent::__construct ( $default );
	}
	
	function publish() {
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		foreach ( $cid as $id ) {
			Utility::executeQuery ( 'update #__content set state = 1 where id = ' . $id );
		}
		$msg = JText::_ ( 'LISTA_PUBBLICATA' );
		$uri = & JFactory::getURI ();
		$ret = $uri->toString ();
		
		$this->setRedirect ( $ret, $msg );
	
	}
	function unpublish() {
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		foreach ( $cid as $id ) {
			Utility::executeQuery ( 'update #__content set state = 0 where id = ' . $id );
		}
		$msg = JText::_ ( 'LISTA_SPUBBLICATA' );
		$uri = & JFactory::getURI ();
		$ret = $uri->toString ();
		
		$this->setRedirect ( $ret, $msg );
	
	}
	
	function save() {
		
		global $mainframe;
		
		$post = JRequest::get ( 'post' );
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$post ['id'] = $cid [0];
		$article = & JTable::getInstance ( 'content' );
		
		$article->id = 0;
		$article->title = $post ['title'];
		//$article->alias = $post ['alias'];
		$post ['introtext'] = JRequest::getVar ( 'introtext', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$article->introtext = $post ['introtext'];
		//$article->introtext = $post['introtext'];
		if(!empty($post ['fulltext']))
		$article->fulltext = $post ['fulltext'];
		$article->state = $post ['state'];
		$article->catid = $post ['catid'];
		$article->publish_up = $post['publish_up'];
		$article->modified = $post['created'];
		$article->publish_down = $post['publish_down'];
		
		$article->sectionid = $post['sectionid'];
		$article->catid = $post['catid'];
		
		
	/*	$arraysectin = utility::getArray ( 'select section from #__categories where id = ' . $article->catid );
		foreach ( $arraysectin as $questasection ) {
			$sectionid = $questasection->section;
		}*/
		//$article->sectionid = $sectionid;
		
		$article->created = $post ['created'];
		
		$user = & JFactory::getUser ();
		$article->created_by = $user->get ( 'id' );
		
		if ($article->store ()) {
			$esito = utility::check_if_table_exists ( '#__djfappend_field' );
			if ($esito) {
				
				$id_field_type = JRequest::getVar ('id_field_type');
				
				$field_value = JRequest::getVar ('field_value');
				$event_date = JRequest::getVar ('event_date');
				if (!empty($id_field_type) && !empty($field_value)){
				utility::executeScriptQuery ( 'insert into #__djfappend_field 
				(id,   id_jarticle, 	 id_field_type,  field_value,  event_date) 
		 values (null, ' . $article->id . ', ' . $id_field_type . ', "' . $field_value . '", "' . $event_date . '" );' );
				}
			}
		}
		
		//echo ("<h1>$article->id</h1>");
		//exit();
		

		$msg = JText::_ ( 'ARTICLE_SAVED' );
		$uri = & JFactory::getURI ();
		$ret = $uri->toString ();
		
		$this->setRedirect ( $ret, $msg );
	
	}
	function cancel() {
		
		global $mainframe;
		
		$post = JRequest::get ( 'post' );
		
		$uri = & JFactory::getURI ();
		$ret = $uri->toString ();
		
		$this->setRedirect ( $ret, $msg );
	
	}
	/**
	 * Method display
	 * 
	 * 1) crea il controller
	 * 2) passa il modello alla vista
	 * 3) carica il template e lo renderizza  	  	 	 
	 */
	
	function display() {
		
		
		global $mainframe, $context;
		
		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', '1' );
		$mainframe->setUserState($context.'filter_order', '1');
	
		parent::display ();
	}

}
?>
