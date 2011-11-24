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
 * gruppi_icone_detail  Controller
 *
 * @package		Joomla
 * @subpackage	gruppi_icone
 * @since 1.5
 */
class gruppi_icone_detailController extends JController {
	
	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
		
		// Register Extra tasks
		$this->registerTask ( 'add', 'edit' );
	
	}
	
	function edit() {
		
		JRequest::setVar ( 'view', 'gruppi_icone_detail' );
		JRequest::setVar ( 'layout', 'form' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		parent::display ();
		// give me  the gruppi_icone
		$model = $this->getModel ( 'gruppi_icone_detail' );
		$model->checkout ();
	
	}
	
	/**
	 * Funzione di salvataggio
	 *
	 */
	function save() {
		
		$post = JRequest::get ( 'post' );
		$matricola = $post ['matricola'];
		JRequest::setVar ( 'matricola', $matricola );
		$searchString = "";
		if ($matricola != null && $matricola != "") {
			$searchString = "&matricola=$matricola";
		}
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$post ['id'] = $cid [0];
		$model = $this->getModel ( 'gruppi_icone_detail' );
		if ($model->store ( $post )) {
					$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_SALVATO' );
			} else {
				$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_ERRORE_SALVATAGGIO' );
				
			}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin ();
		$redirect = 'index.php?option=com_djfacl&controller=gruppi_icone';
		$this->setRedirect ( $redirect, $msg );
	
	}
	
	/** 
	 * function remove
	 */
	
	function remove() {
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		if (! is_array ( $cid ) || count ( $cid ) < 1) {
			JError::raiseError ( 500, JText::_ ( 'Select an item to delete' ) );
		}
		$model = $this->getModel ( 'gruppi_icone_detail' );
		if (! $model->delete ( $cid )) {
			echo "<script> alert('" . $model->getError ( true ) . "'); window.history.go(-1); </script>\n";
		}
		$redirect = 'index.php?option=com_djfacl&controller=gruppi_icone';
		$msg = JText::_('GRUPPO_UTENTE_CANCELLATO');
		$this->setRedirect ( $redirect, $msg );
	}
	
	/** 
	 * function cancel
	 */
	
	function cancel() {
		
		$post = JRequest::get ( 'post' );
		$matricola = $post ['matricola'];
		JRequest::setVar ( 'matricola', $matricola );
		$searchString = "";
		if ($matricola != null && $matricola != "") {
			$searchString = "&matricola=$matricola";
		}
		$model = $this->getModel ( 'gruppi_icone_detail' );
		$model->checkin ();
		$redirect = 'index.php?option=com_djfacl&controller=gruppi_icone' . $searchString;
		$this->setRedirect ( $redirect, $msg );	
	}

}
