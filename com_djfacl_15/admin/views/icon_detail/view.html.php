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


class icon_detailVIEWicon_detail extends JView {
	/**
	 * Display the view
	 */
	function display($tpl = null) {
		global $mainframe, $context;

	
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('icon') );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
		
		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_ICONE_DETTAGLIO' ), 'icon' );
		$uri = & JFactory::getURI ();
		$user = & JFactory::getUser ();
		$model = & $this->getModel ();
		$this->setLayout ( 'form' );
		$lists = array ();
		$detail = & $this->get ( 'data' );
		$isNew = ($detail->id < 1);
		
		$text = $isNew ? JText::_ ( 'NEW' ) : JText::_ ( 'EDIT' );
		
		JToolBarHelper::save ();
		
		if ($isNew) {
			JToolBarHelper::cancel ();
			$detail->text = '';
			$detail->target = '';
			$detail->title = '';
			$detail->ordering = '';
			
		} else {
			JToolBarHelper::cancel ( 'cancel', 'Close' );
		}
		
		if (! $isNew) {
			$model->checkout ( $user->get ( 'id' ) );
		}
		
		$parent_id = JRequest::getVar('id_parent');
		
		$acl =& JFactory::getACL();
		$gtree = $acl->get_group_children_tree( null, 'USERS', false );
		
		
		jimport ( 'joomla.filter.filteroutput' );
		JFilterOutput::objectHTMLSafe ( $detail, ENT_QUOTES, 'description' );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'detail', $detail );
		$this->assignRef ( 'request_url', $uri->toString () );
		
		parent::display ( $tpl );
	}
	
	
	

}

?>
