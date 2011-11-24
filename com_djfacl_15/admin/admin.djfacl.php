<?php
/**
* @package HelloWorld 02
* @version 1.5
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/



// controllo che il componente venga chiamato soltanto da joomla
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once (JPATH_PLUGINS.DS.'system'.DS.'djflibraries'.DS.'utility.php');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_banners'.DS.'tables');
$controllerName = JRequest::getVar('controller');
$activeCpanel=false;
$activeGroups=false;
$activeUSERGROUPS=false;
$activeICONGROUPS=false;
$activeContent=false;
$activeCssblock=false;
$activeJTask=false;
$activeIcon=false;
$activeLoader=false;



if ($controllerName=='cpanel') $activeCpanel = true; 
if ($controllerName=='gruppi') $activeGroups = true;
if ($controllerName=='gruppi_utenti') $activeUSERGROUPS = true;
if ($controllerName=='gruppi_icone') $activeICONGROUPS = true;
if ($controllerName=='contenuti') $activeContent = true;
if ($controllerName=='cssblock') $activeCssblock = true;
if ($controllerName=='icon') $activeIcon = true;
if ($controllerName=='jtask') $activeJTask = true;
if ($controllerName=='loader') $activeLoader = true;



	JSubMenuHelper::addEntry(JText::_('Panel'), 'index.php?option=com_djfacl&controller=cpanel',$activeCpanel);
	JSubMenuHelper::addEntry(JText::_('Groups'), 'index.php?option=com_djfacl&controller=gruppi', $activeGroups );
	JSubMenuHelper::addEntry(JText::_('USERGROUPS'), 'index.php?option=com_djfacl&controller=gruppi_utenti',$activeUSERGROUPS);
	JSubMenuHelper::addEntry(JText::_('ICONGROUPS'), 'index.php?option=com_djfacl&controller=gruppi_icone',$activeICONGROUPS);
	JSubMenuHelper::addEntry(JText::_('Content'), 'index.php?option=com_djfacl&controller=contenuti', $activeContent);
	JSubMenuHelper::addEntry(JText::_('cssblock'), 'index.php?option=com_djfacl&controller=cssblock', $activeCssblock);
	JSubMenuHelper::addEntry(JText::_('Task'), 'index.php?option=com_djfacl&controller=jtask', $activeJTask);
	JSubMenuHelper::addEntry(JText::_('Icon'), 'index.php?option=com_djfacl&controller=icon', $activeIcon);
	JSubMenuHelper::addEntry(JText::_('Import/Export'), 'index.php?option=com_djfacl&controller=loader', $activeLoader);
	
	

// questo è il controller di default se non ne viene selezionato alcuno
$controller = JRequest::getVar('controller','cpanel' );

// indirizza il controller giusto
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');

// Create the controller
$classname  = $controller.'controller';

//create a new class of classname and set the default task:display
$controller = new $classname( array('default_task' => 'display') );

// Perform the Request task
$controller->execute( JRequest::getVar('task' ));

// Redirect if set by the controller
$controller->redirect();
?>
