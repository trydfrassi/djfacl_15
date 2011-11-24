<?php
/**
* @package HelloWorld
* @version 1.0
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/*
 * DEVNOTE: This is the 'main' file. 
 * It's the one that will be called when we go to the HELLOWORD component. 
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (JPATH_PLUGINS.DS.'system'.DS.'djflibraries'.DS.'utility.php');



global $mainframe, $context;
$document = & JFactory::getDocument();

$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
//$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
//$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
$document->addStyleSheet ( 'components/com_djfacl/assets/css/djfacl.css' );
		


// specific controller?
// Require specific controller if requested
//if no controller then default controller = 'helloworld'
$controller = JRequest::getVar('controller','field' ); 

//set the controller page  
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');

// Create the controller fieldController =MC
$classname  = $controller.'controller';

//create a new class
$controller = new $classname( array('default_task' => 'display') );

$controller->registerTask( 'new'  , 	'edit' );
$controller->registerTask( 'apply', 	'save' );
$controller->registerTask( 'apply_new', 'save' );


// Perform the Request task
$controller->execute( JRequest::getVar('task' ));

// Redirect if set by the controller
$controller->redirect();

?>
