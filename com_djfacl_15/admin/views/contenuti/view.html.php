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
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'toolbar.php' );

/**
 [controller]View[controller]
 */

class contenutiViewcontenuti extends JView
{

	function __construct( $config = array()){
	 
		global $context;
	 	$context = 'contenuti.list.';
	 	parent::__construct( $config );
		
	}




	function display($tpl = null)
	{
		global $mainframe, $context;

	
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('contenuti') );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
	

		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_CONTENUTI' ), 'content' );
		//djfaclHelperToolbar::import('contenuti');
		//djfaclHelperToolbar::export('contenuti');
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList();
		//JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		//djfaclHelperToolbar::customX( 'purge', 'export.png', 'export.png', 'purge', false );
		
		$uri	=& JFactory::getURI();
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order',      'filter_order', 	  'ordering' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',  'filter_order_Dir', '' );
		
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;		
		
		$grupposearch = utility::getDjfVar('idgroup','0' );
		
		$grupposearchcopia="";
		$select_gruppi = utility::addItemToSelect(NULL,'TUTTI','0');
		
		
		$query_gruppi_associati = "select id as value, 
		name as text 
		from #__core_acl_aro_groups 
		where id >30 order by trim(name)";
		
		$risult = utility::getQueryArray($query_gruppi_associati);
		
		$lists ['gruppi_associati'] = utility::getSelectExt2($query_gruppi_associati
		,'idgroup',$grupposearch,  
		'onchange="document.adminForm.submit();"',$select_gruppi,'CREARE_UN_GRUPPO','index.php?option=com_djfacl&controller=gruppi');
		
		$query_gruppi_associati_copia = "
		select id as value, 
		name as text 
		from #__core_acl_aro_groups 
		where id >30 
		order by trim(name)";
		
		$risult = utility::getQueryArray($query_gruppi_associati_copia);
		
		if (sizeof($risult)<1){
			$list['gruppi_associati']="";
		}
		else
		$lists ['gruppi_associati_copia'] = utility::getSelectExt2($query_gruppi_associati_copia,'grupposearchcopia',$grupposearchcopia,  '',null,'CREARE_UN_GRUPPO','index.php?option=com_djfacl&controller=gruppi');
	
		//JHTML::_('select.genericlist',  $groups, 'filter_id_group', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id_group', 'name', $filter_id_group);
		
		$items	= & $this->get( 'Data');
		$total	= & $this->get( 'Total');
		
		$pagination = & $this->get( 'Pagination' );
		
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('request_url',	$uri->toString());		
		//$this->assignRef('grupposearch',$grupposearch);
		$this->assignRef('search', JRequest::getVar('search'));
		$this->assignRef('grupposearch', JRequest::getVar('grupposearch'));
		$this->assignRef('pulsanti', djfaclHelperToolbar::getToolbar());
		
		parent::display($tpl);
	}
	
	
}
?>
