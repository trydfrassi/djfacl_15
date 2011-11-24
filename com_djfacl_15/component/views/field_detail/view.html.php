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

//DEVNOTE: import VIEW object class
jimport ( 'joomla.application.component.view' );
jimport ( 'joomla.application.component.helper' );

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'toolbar.php');
require_once (JPATH_PLUGINS.DS.'system'.DS.'djflibraries'.DS.'utility.php');

class field_detailVIEWfield_detail extends JView {
	/**
	 * Display the view
	 */
	function display($tpl = null) {
		global $mainframe, $option;
		
		$uri = & JFactory::getURI ();
		$user = & JFactory::getUser ();
		$model = & $this->getModel ();
		$this->setLayout ( 'form' );
		$lists = array ();
		$detail = & $this->get ( 'data' );
		$isNew = ($detail->id < 1);
		
		$document = & JFactory::getDocument ();
		$document->setTitle ( JText::_ ( 'field' ) );
		
		$text = $isNew ? JText::_ ( 'NEW' ) : JText::_ ( 'EDIT' );
		fieldHelperToolbar::title ( JText::_ ( 'djfappend - Gestione Campi' ) . ': <small><small>[ ' . $text . ' ]</small></small>', 'addedit' );
		fieldHelperToolbar::save ();
		
		if ($isNew) {
			fieldHelperToolbar::cancel ();
		} else {
			fieldHelperToolbar::cancel ( 'cancel', 'Close' );
		}
		
		if (! $isNew) {
			$model->checkout ( $user->get ( 'id' ) );
		} else {
			$detail->id_jarticle = 0;
			$detail->field_name = "";
			$detail->field_value = "";
			$detail->introtext = "";
			$detail->title="";
			$detail->created=gmdate ( 'Y-m-d H:i:s' );
			$detail->publish_up=gmdate ( 'Y-m-d H:i:s' );
			$detail->publish_down='0000-00-00 00:00:00';
			$data_odierna = gmdate ( 'Y-m-d H:i:s' );
			$detail->state=1;
			$detail->event_date = $data_odierna;
		}

		jimport ( 'joomla.filter.filteroutput' );
		JFilterOutput::objectHTMLSafe ( $detail, ENT_QUOTES, 'description' );
		
		jimport ( 'joomla.filter.filteroutput' );
		JFilterOutput::objectHTMLSafe ( $detail, ENT_QUOTES, 'introtext' );
		
		$editor = & JFactory::getEditor ();
		$this->assignRef ( 'editor', $editor );
		
		$this->assignRef ( 'isNew', $isNew );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'pulsanti', fieldHelperToolbar::getToolbar () );
		$this->assignRef ( 'detail', $detail );
		$this->assignRef ( "ret", JRequest::getVar ( 'ret' ) );
		$this->assignRef ( 'request_url', $uri->toString () );
		
		parent::display ( $tpl );
	}
	
	function Field_type_associati($name, $active = NULL, $javascript = NULL, $order = 'name', $size = 1, $sel_desc = 1) {
		global $mainframe;
		$model = & $this->getModel ();
		$field_type_associati [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( 'Seleziona una tipolpgia' ) . ' -' );
		$field_type_associati = array_merge ( $field_type_associati, $model->getField_type ( $order ) );
		if (count ( $field_type_associati ) < 1) {
			$mainframe->redirect ( 'index.php?option=com_djfacl', JText::_ ( 'Devi prima creare una tipologia.' ) );
		}
		$field_type = JHTML::_ ( 'select.genericList', $field_type_associati, $name, 'class="inputbox" size="' . $size . '" ' . $javascript, 'value', 'text', $sel_desc );
		return $field_type;
	}

}

?>
