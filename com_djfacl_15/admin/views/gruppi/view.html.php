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
JHTML::_ ( 'behavior.tooltip' );
jimport ( 'joomla.application.component.helper' );
require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'toolbar.php');
/**
 [controller]View[controller]
 */

class gruppiViewgruppi extends JView {
	
	function __construct($config = array()) {
		
		global $context;
		$context = 'gruppi.list.';
		parent::__construct ( $config );
	
	}
	
	/**
	 * Display the view
	 * take data from MODEL and put them into
	 * reference variables
	 *
	 * Go to MODEL, execute Method getData and
	 * result save into reference variable $items
	 * $items		= & $this->get( 'Data');
	 * - getData gets the country list from DB
	 *
	 * variable filter_order specifies what is the order by column
	 * variable filter_order_Dir sepcifies if the ordering is [ascending,descending]
	 */
	
	function display($tpl = null) {
		global $mainframe, $context;
		
		$document = & JFactory::getDocument ();
		$document->setTitle ( JText::_ ( 'GROUPS' ) );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
		$document->addScriptDeclaration ( "var imageFolder =  'components/com_djfacl/assets/images/'" ); // Path to images
		//djfaclHelperToolbar::import('gruppi');
		//djfaclHelperToolbar::export ( 'gruppi' );
		//JToolBarHelper::addNewX();
		//djfaclHelperToolbar::purge ();
		djfaclHelperToolbar::importJUser();
		$document->addScript ( 'components/com_djfacl/assets/js/ajax.js' );
		$document->addScript ( 'components/com_djfacl/assets/js/context-menu.js' );
		$document->addScript ( 'components/com_djfacl/assets/js/drag-drop-folder-tree.js' );
		$document->addScript ( 'components/com_djfacl/assets/js/script_head_tree.js' );
		
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/drag-drop-folder-tree.css' );
		//$document->addStyleSheet ( 'components/com_djfacl/assets/css/context-menu.css' );
		

		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_GRUPPI' ), 'tree' );
		
		
		//JToolBarHelper::editListX();
		//JToolBarHelper::deleteList();
		$uri = & JFactory::getURI ();
		
		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'ordering' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', '' );
		
		$lists ['order'] = $filter_order;
		$lists ['order_Dir'] = $filter_order_Dir;
		
		$items = & $this->get ( 'Data' );
		$total = & $this->get ( 'Total' );
		
		$pagination = & $this->get ( 'Pagination' );
		
		$acl = &JFactory::getACL ();
		$gtree = $acl->get_group_children_tree ( null, 'USERS', false );
		$user = &JFactory::getUser ();
		$total = count ( $gtree );
		$gtreeCopy = $gtree;
		for($i = 0; $i < $total; $i ++) {
			if ($gtree [$i]->value == 30 or $gtree [$i]->value == 29) {
				unset ( $gtree [$i] );
			}
		}
		
		$this->assignRef ( 'gtree', $gtree );
		
		$this->assignRef ( 'user', JFactory::getUser () );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'items', $items );
		$this->assignRef ( 'pagination', $pagination );
		$this->assignRef ( 'request_url', $uri->toString () );
		$this->assignRef ( 'search', JRequest::getVar ( 'search' ) );
		
		parent::display ( $tpl );
	}
	
	
	function getTree(){
		
		$htmlOut="";
		
		
		
		return $htmlOut;
		
		
	}
	
	
	

}
?>