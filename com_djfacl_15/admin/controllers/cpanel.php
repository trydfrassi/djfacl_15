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

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' );



/**
 * helloworld  Controller
 *
 * @package		Joomla
 * @subpackage	helloworld
 * @since 1.5
 */
 
class cpanelController extends JController
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
	}
	/**
	 * Cancel operation
	 * redirect the application to the begining - index.php  	 
	 */
	function cancel()
	{
		$this->setRedirect( 'index.php' );
	}

	/**
	 * Method display
	 * 
	 * 1) create a helloworldVIEWhelloworld(VIEW) and a helloworldMODELhelloworld(Model)
	 * 2) pass MODEL into VIEW
	 * 3)	load template and render it  	  	 	 
	 */

	function display() {
		parent::display();
	}
	
	
	
	
	
			
	
}	
?>
