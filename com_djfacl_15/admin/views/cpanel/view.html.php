<?php
/**
* @version		$Id: view.html.php 47 2009-05-26 18:06:30Z happynoodleboy $
* @package		Joomla
* @subpackage	Config
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
jimport('joomla.html.pane');
jimport('joomla.application.module.helper');
require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'toolbar.php');

/**
 * HTML View class for the Plugins component
 *
 * @static
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.0
 */
class CpanelViewCpanel extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $context;

	
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('contenuti') );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
	djfaclHelperToolbar::importJUser();
		
		JToolBarHelper::preferences ( 'com_djfacl', '350' );
	
		$pane			=& JPane::getInstance('sliders');
		$com_xml 		= JApplicationHelper::parseXMLInstallFile( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_djfacl' .DS. 'com_djfacl.xml' );
		
		$plg_djfcontent 		= array();
		$plg_djfcontent_file 	= JPATH_PLUGINS .DS. 'content' .DS. 'djfcontent.xml';
		if( file_exists( $plg_djfcontent_file ) ){
			$puppa = JApplicationHelper::parseXMLInstallFile( $plg_djfcontent_file );
			$plg_djfcontent['version'] = "<span style='color:green;'>".$puppa['version']."</span>";
		}else{
			$plg_djfcontent['version'] = "<span style='color:red;'>".JText::_('NOT_INSTALLED')."</span>";
		}
		
		
		$plg_djfacl 		= array();
		$plg_djfacl_file 	= JPATH_PLUGINS .DS. 'system' .DS. 'djfacl.xml';
		if( file_exists( $plg_djfacl_file ) ){
			$puppa = JApplicationHelper::parseXMLInstallFile( $plg_djfacl_file );
			$plg_djfacl['version'] = "<span style='color:green;'>".$puppa['version']."</span>";
		}else{
			$plg_djfacl['version'] = "<span style='color:red;'>".JText::_('NOT_INSTALLED')."</span>";
		}		

		$mod_djfacl 		= array();
		$mod_djfacl_file 	= JPATH_ROOT.DS.'modules'.DS.'mod_djfacl'.DS.'mod_djfacl.xml';
		
		if( file_exists( $mod_djfacl_file ) ){
			$puppa = JApplicationHelper::parseXMLInstallFile( $mod_djfacl_file );
			$mod_djfacl['version'] = "<span style='color:green;'>".$puppa['version']."</span>";
		}else{
			$mod_djfacl['version'] = "<span style='color:red;'>".JText::_('NOT_INSTALLED')."</span>";
		}
		
		$mod_djfacl_quickicon 		= array();
		$mod_djfacl_quickicon_file 	= JPATH_ROOT.DS.'administrator'.DS.'modules'.DS.'mod_djfacl_quickicon'.DS.'mod_djfacl_quickicon.xml';
		
		if( file_exists( $mod_djfacl_quickicon_file ) ){
			$puppa = JApplicationHelper::parseXMLInstallFile( $mod_djfacl_quickicon_file );
			$mod_djfacl_quickicon['version'] = "<span style='color:green;'>".$puppa['version']."</span>";
		}else{
			$mod_djfacl_quickicon['version'] = "<span style='color:red;'>".JText::_('NOT_INSTALLED')."</span>";
		}
		
		
		
		
		$this->assignRef('icons', 	$icons);
		$this->assignRef('pane', 	$pane);
		//$this->assignRef('modules', $modules);
		$this->assignRef('com_info', $com_xml);
		$this->assignRef('plg_djfcontent', $plg_djfcontent);
		$this->assignRef('plg_djfacl', $plg_djfacl);
		$this->assignRef('mod_djfacl', $mod_djfacl);
		$this->assignRef('mod_djfacl_quickicon', $mod_djfacl_quickicon);

		parent::display($tpl);
	}
}
?>