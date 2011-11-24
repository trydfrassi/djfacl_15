<?php

// prova di trydfrassi

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

/**
 * field_detail  Controller
 *
 * @package		Joomla
 * @subpackage	field
 * @since 1.5
 */
class field_detailController extends JController {
	
	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
		
		// Register Extra tasks
		$this->registerTask ( 'add', 'edit' );
	
	}
	
	function rebuildselect_section() {

		$field = JRequest::getVar ( 'field' );
		$value = JRequest::getVar ( 'value' );
		$select_custom = utility::addArrayItemToSelect(array (" - TUTTI - " => "0"));
		$query = 'select id as value, title as text from #__categories where parent_id = '.$field.' order by trim(2) asc';
		$uscita =utility::getAjaxRebuildField('select',$field,$query,$select_custom);
		echo($uscita);
	}
	
	function rebuildselect() {
		//$field_type="1";
		$field_type = JRequest::getVar ( 'id_field_type' );
		$valore = JRequest::getVar ( 'valore' );
		echo ("<script>alert('$field_type - $valore');</script>");
		//exit();
		if (! isset ( $field_type ))
			$field_type = "1";
		
		$risultati = utility::getArray ( "SELECT id AS value, valore as text FROM #__djfappend_field_value
		where id_field_type = $field_type" );
		if (sizeof ( $risultati ) > 0) {
			$lists ['valori'] = utility::getSelectExt ( "SELECT id AS value, valore as text FROM #__djfappend_field_value where id_field_type = $field_type ORDER BY trim(valore)", 'field_value', 'field_value', $risultati->value, 'onChange="checkDisabled();"', false );
			$uscita = $lists ['valori'];
		} else {
			$uscita = "<input type='text' name='field_value' id='field_value' value='" . $valore . "'/>";
		}
		
		echo ($uscita);
	}
	
	function edit() {
		
		JRequest::setVar ( 'view', 'field_detail' );
		JRequest::setVar ( 'layout', 'form' );
		JRequest::setVar ( 'hidemainmenu', 1 );
		
		
		parent::display ();
		// give me  the field
		$model = $this->getModel ( 'field_detail' );
		$model->checkout ();
	
	}
	
	/**
	 * Funzione di salvataggio
	 *
	 */
	function save() {
		
		
	}
	
	/** 
	 * function remove
	 */
	
	function remove() {
		$post = JRequest::get ( 'post' );
		//$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		//$post ['id'] = $cid [0];
		

		$model = $this->getModel ( 'field_detail' );
		$msg = "";
		$id = JRequest::getVar ( 'id' );
		$confirm = JRequest::getVar ( 'confirm' );
		if ($confirm == "yes") {
			if ($model->delete ( $id )) {
				$msg = JText::_ ( 'Field removed' );
			} else {
				$msg = JText::_ ( 'Error removing Field' );
			}
		}
		
		//$delete_url = "index.php?option=com_djfappend&task=delete&id=$id&confirm=yes";
		
		$delete_url = "index.php?option=com_djfappend&controller=field_detail&tmpl=component&id=$id&task=remove&confirm=yes";
		
		$echofori = '<div class="deleteWarning">
        <h1>' . JText::_ ( 'WARNING' ) . '</h1>
        <h2 id="warning_msg">' . JText::_ ( 'REALLY_DELETE' ).'</h2>
        <form action="' . $delete_url . '" name="delete_warning_form" method="post">
        <div align="center">
            <input type="submit" name="submit" value="' . JText::_ ( 'DELETE' ) . '" />
        </div>
        </form>
        </div>';
		echo ($echofori);
		//echo($msg);
		if ($confirm=="yes")
	echo "<script language=\"javascript\" type=\"text/javascript\">
            window.parent.document.getElementById('sbox-window').close();
            window.parent.location.reload();
            </script>";
	// exit();
	//   }
	

	// $this->setRedirect( $redirect_to, $msg );
	

	}
	
	/** 
	 * function cancel
	 */
	
	function cancel() {
		
		
		global $mainframe;
		
		$post = JRequest::get ( 'post' );
		
 
		$msg = JText::_('ARTICLE_SAVED');
	 	$ret = JRequest::getVar("ret");
		$ret = base64_decode($ret);
		$this->setRedirect( $ret, $msg );
	
	}
}
