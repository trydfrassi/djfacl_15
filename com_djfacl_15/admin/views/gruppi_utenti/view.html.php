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

class gruppi_utentiViewgruppi_utenti extends JView
{
	function __construct( $config = array()){
		global $context;
	 	$context = 'gruppi_utenti.list.';
	 	parent::__construct( $config );
	}
	
	function display($tpl = null)
	{
		global $mainframe, $context;
	
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('gruppi_utenti') );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
		
		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_GRUPPI_UTENTI' ), 'groups' );
		
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList();
		djfaclHelperToolbar::importJUser();
		//JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
	
		$uri	=& JFactory::getURI();
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order',      'filter_order', 	  'ordering' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',  'filter_order_Dir', '' );
		
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;		
		
		$iduser = utility::getDjfVar('iduser', '');
		$matricola = utility::getDjfVar('matricola', '');
		$idgroup = utility::getDjfVar('idgroup', '');
		
		$idgroup_copia = utility::getDjfVar('idgroup_copia', '');
		$idgroup_sposta = utility::getDjfVar('idgroup_sposta', '');
		$tipologia = utility::getDjfVar('tipologia','djfacl');
		
		$array = utility::getArray("select id as value, name as text from #__users where block=0 ", "trim(name)");
		$utenti = utility::getSelect($array, '' ,'iduser', $iduser, ' onChange="document.adminForm.submit();" style="width:198px;padding:0;margin: 0 8px 0 0;"  ' );
		$lists ['utenti_associati'] = $utenti;
		$gruppoext1= "SELECT distinct 
		jcaag.id as value,
		jcaag.name as text,
		jcaag.rgt-jcaag.lft as pad
		 
		FROM 
		#__core_acl_aro_groups as jcaag  where id not in (17,25,28,29,30)
		order by jcaag.name"; 
		$gruppoext2= "SELECT distinct 
		jcaag.id as value,
		jcaag.name as text,
		jcaag.rgt-jcaag.lft as pad
		 
		FROM 
		#__core_acl_aro_groups as jcaag  where id > 30
		order by jcaag.name"; 

		$gruppoext3= "SELECT distinct 
		jcaag.id as value,
		jcaag.name as text, 
		jcaag.rgt-jcaag.lft as pad
		FROM 
		#__core_acl_aro_groups as jcaag where id not in (17,25,28,29,30)
		order by jcaag.name"; 
		
		$users= "SELECT id as value, name as text from #__users where gid not in (17,25,28,29,30) and block=0 order by trim(name)"; 
		$select_gruppi = utility::addItemToSelect(NULL, 'TUTTI', '0');
		$select_users = utility::addItemToSelect(NULL, 'TUTTI', '0');


		//echo($users);
		//exit();
		$lists ['users']= utility::getSelectExt2($users, 'matricola', $matricola, ' onChange="document.adminForm.submit();" style="width:100px;padding:0;margin: 0 8px 0 0;"  ',$select_users, 'NOUSER', 'index.php?option=com_users&task=view' );

		$lists ['gruppi']= utility::getSelectExt2($gruppoext1, 'idgroup', $idgroup, ' onChange="document.adminForm.submit();" style="width:100px;padding:0;margin: 0 8px 0 0;"  ',$select_gruppi,'CREARE_UN_GRUPPO' );
		$lists ['gruppi_copia']= utility::getSelectExt2($gruppoext2, 'idgroup_copia', $idgroup_copia, '',NULL,'CREARE_UN_GRUPPO','index.php?option=com_djfacl&controller=gruppi');
		$lists ['gruppi_sposta']= utility::getSelectExt2($gruppoext3, 'idgroup_sposta', $idgroup_sposta, '',NULL,'CREARE_UN_GRUPPO','index.php?option=com_djfacl&controller=gruppi');
		

		
		$select_tipologie = utility::addItemToSelect(NULL, 'Joomla', 'joomla');
		$select_tipologie = utility::addItemToSelect($select_tipologie, 'DjfAcl', 'djfacl');
		$select_tipologie = utility::addItemToSelect($select_tipologie, 'TUTTI', '0');
		$lists ['tipologie'] = utility::getSelectNoQuery($select_tipologie,'tipologia',$tipologia,' onChange="document.adminForm.submit();" style="width:100px;padding:0;margin: 0 8px 0 0;"  ' );
		
		$items	= & $this->get( 'Data');
		$total	= & $this->get( 'Total');
		
		$pagination = & $this->get( 'Pagination' );
		
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('request_url',	$uri->toString());		
		$this->assignRef('search', utility::getDjfVar('search',''));
		$this->assignRef('matricola', utility::getDjfVar('matricola',''));
		$this->assignRef('pulsanti', djfaclHelperToolbar::getToolbar());
		
		parent::display($tpl);
	}
	
}
?>
