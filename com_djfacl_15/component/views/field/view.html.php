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
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );
jimport('joomla.application.component.helper');
require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'toolbar.php');
require_once (JPATH_PLUGINS.DS.'system'.DS.'djflibraries'.DS.'utility.php');


/**
 [controller]View[controller]
 */

class fieldViewfield extends JView
{

	function __construct( $config = array()){
	 
		global $context;
	 	$context = 'field.list.';
	 	parent::__construct( $config );
	}

	function display($tpl = null)
	{
		global $mainframe, $context;
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('field') );
		$document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
		$uri	=& JFactory::getURI();		

		$filter_order = $mainframe->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'ordering' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', '' );
		
		$lists ['order'] = $filter_order;
		$lists ['order_Dir'] = $filter_order_Dir;
		$items			= & $this->get( 'Data');
		//$total			= & $this->get( 'Total');		
		
		$pagination = & $this->get( 'Pagination' );	
		$pathway    =& $mainframe->getPathway();
		$pathway->addItem(JText::_('List '), '');
		
		$params = &$mainframe->getParams();
		$catid_fromparam = $params->get( 'catid');
		if (empty($catid_fromparam)){
			$catid = JRequest::getVar('catid');
		}else $catid = $catid_fromparam;
		
		$sectionid = utility::getField('select section as value from #__categories where id = '.$catid);
		
	
		//echo("<h1>$catid - $sectionid</h1>");
		
		$date_format = $params->get('date_format');
		if (empty($date_format)) $date_format="%Y-%m-%d";
			
		$user = JFactory::getUser();
		$usertype = $user->usertype;
		
		if (
		$usertype == "Author" ||
		$usertype == "Editor" ||
		$usertype == "Publisher" ||
		$usertype == "Manager" ||
		$usertype == "Administrator" ||
		$usertype == "Super Administrator")
		
		$this->assignRef ( 'pulsanti', fieldHelperToolbar::getToolbar () );
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);			
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('itemid',	JRequest::getVar('Itemid'));	
		$this->assignRef('request_url',	$uri->toString());		
		$this->assignRef('search', JRequest::getVar('search'));
		$this->assignRef('date_format', $date_format);
		$this->assignRef('id_field_type', JRequest::getVar('id_field_type'));
		$this->assignRef('catid', $catid);
		$this->assignRef('sectionid', $sectionid);
		$this->assignRef('anno', JRequest::getVar('anno'));
		$this->assignRef('maxchars_introtext', JRequest::getVar('maxchars_introtext'));
		
		parent::display($tpl);
	}
	
}
?>
