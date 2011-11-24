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
 * icon_detail  Controller
 *
 * @package		Joomla
 * @subpackage	icon
 * @since 1.5
 */
class icon_detailController extends JController {
	
	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
		
		// Register Extra tasks
		$this->registerTask ( 'add', 'edit');
	
	}
	
	function edit() {
		
		JRequest::setVar ( 'view', 'icon_detail' );
		JRequest::setVar ( 'layout', 'form' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		parent::display ();
		// give me  the icon
		$model = $this->getModel ( 'icon_detail' );
		$model->checkout ();
	
	}

	
	function save() {
		
		global $mainframe;
		
		jimport ( 'joomla.filesystem.file' );
		$post = JRequest::get ( 'post' );
		$file = JRequest::getVar ( 'upload', null, 'files', 'array' );
		$filename = $file ['name'];
		jimport ( 'joomla.user.helper' );
		
		$urlSelf = 'index.php?option=com_djfacl&controller=icon&task=edit&cid[]='.$post['id'];
		
		$maxsize=5000000;
		
		$dest= JPATH_ROOT . DS . 'images' . DS . 'stories' . DS . 'com_djfacl' . DS . 'uploads';
		$dest_thumb= JPATH_ROOT . DS . 'images' . DS . 'stories' . DS . 'com_djfacl' . DS . 'uploads' . DS .'thumb';
		$dest_url_icon = 'images/stories/com_djfacl/uploads/thumb/'.$filename;
		
		
		if ($filename != ""){
			$msg = utility::saveFile ($maxsize,$dest_thumb, $dest, 48, 'si');
			if ($msg=="") {
				//Clean up filename to get rid of strange characters like spaces etc
				$filename = JFile::makeSafe ( $file ['name'] );
				$post ['images'] = $filename;
				echo ("<h1>images = $filename</h1>");
			} else {
				$params = JComponentHelper::getParams ( 'com_joomuser' );
				$maxsize = $params->get ( 'image_maxsize_upload' );
				$this->setRedirect ( $urlSelf, JText::_ ( $msg ) );
				return false;
				//exit ();
			}
			$post['icon'] = $dest_url_icon;
		}
	
		
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$post ['id'] = $cid [0];
		$model = $this->getModel ( 'icon_detail' );	
		if ($model->store ( $post )) {
					$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_SALVATO' );
			} else {
				$msg = JText::_ ( 'MESSAGGIO_COMPONENTE' ).' '.JText::_ ( 'MESSAGGIO_ERRORE_SALVATAGGIO' );
				
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin ();
			$this->setRedirect ( 'index.php?option=com_djfacl&controller=icon', $msg );
		
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
		
		$model = $this->getModel ( 'icon_detail' );
		if (! $model->delete ( $cid )) {
			echo "<script> alert('Impossibile cancellare, oggetto usato politica valida'); window.history.go(-1); </script>\n";
		}
		else
		
		$this->setRedirect ( 'index.php?option=com_djfacl&controller=icon' );
	}
	
	/** 
	 * function cancel
	 */

	function cancel() {
		// Checkin the detail
		$model = $this->getModel ( 'icon_detail' );
		$model->checkin ();
		$this->setRedirect ( 'index.php?option=com_djfacl&controller=icon' );
	}
	
	
	
	
}
