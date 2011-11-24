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
 * gruppi_detail  Controller
 *
 * @package		Joomla
 * @subpackage	gruppi
 * @since 1.5
 */
class gruppi_detailController extends JController {
	
	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
		
		// Register Extra tasks
		$this->registerTask ( 'add', 'edit');
	
	}
	
	function edit() {
		
		JRequest::setVar ( 'view', 'gruppi_detail' );
		JRequest::setVar ( 'layout', 'form' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		parent::display ();
		// give me  the gruppi
		$model = $this->getModel ( 'gruppi_detail' );
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
		$model = $this->getModel ( 'gruppi_detail' );	
		if ($model->store ( $post )) {
					$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_SALVATO' );
			} else {
				$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_ERRORE_SALVATAGGIO' );
				
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin ();
			$this->setRedirect ( 'index.php?option=com_djfacl&controller=gruppi', $msg );
		
	}
	
	/** 
	 * function remove
	 */
	
	function remove() {
		global $mainframe;
		$toremove = JRequest::getVar('toremove');
		$model = $this->getModel ( 'gruppi_detail' );
		if (! $model->delete ( $toremove)) {
			echo "<script> alert('" . $model->getError ( true ) . "'); window.history.go(-1); </script>\n";
		}
		
		$this->setRedirect ( 'index.php?option=com_djfacl&controller=gruppi' );
	}
	
	/** 
	 * function cancel
	 */

	function cancel() {
		// Checkin the detail
		$model = $this->getModel ( 'gruppi_detail' );
		$model->checkin ();
		$this->setRedirect ( 'index.php?option=com_djfacl&controller=gruppi' );
	}
	
	
	
	
}
