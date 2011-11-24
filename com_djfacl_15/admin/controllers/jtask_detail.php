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
 * task_detail  Controller
 *
 * @package		Joomla
 * @subpackage	task
 * @since 1.5
 */
class jtask_detailController extends JController {
	
	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
		
		// Register Extra tasks
		$this->registerTask ( 'add', 'edit');
	
	}
	
	function edit() {
		
		JRequest::setVar ( 'view', 'jtask_detail' );
		JRequest::setVar ( 'layout', 'form' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		parent::display ();
		// give me  the task
		$model = $this->getModel ( 'jtask_detail' );
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
		$model = $this->getModel ( 'jtask_detail' );	
		if ($model->store ( $post )) {
					$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_SALVATO' );
			} else {
				$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_ERRORE_SALVATAGGIO' );
				
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin ();
			$this->setRedirect ( 'index.php?option=com_djfacl&controller=jtask', $msg );
		
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
		
		$model = $this->getModel ( 'jtask_detail' );
		if (! $model->delete ( $cid )) {
			
			echo "<script> alert('Impossibile cancellare, oggetto usato in politica valida'); window.history.go(-1); </script>\n";
		}
		else{
			
			$this->setRedirect ( 'index.php?option=com_djfacl&controller=jtask' );
		}
	}
	
	/** 
	 * function cancel
	 */

	function cancel() {
		// Checkin the detail
		$model = $this->getModel ( 'jtask_detail' );
		$model->checkin ();
		$this->setRedirect ( 'index.php?option=com_djfacl&controller=jtask' );
	}
	
	
	
	
}
