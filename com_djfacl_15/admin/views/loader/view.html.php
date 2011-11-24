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
require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'toolbar.php');

/**
 [controller]View[controller]
 */

class loaderViewloader extends JView {
	/**
	 * Custom Constructor
	 */
	function __construct($config = array()) {
		/** set up global variable for sorting etc.
		 * $context is used in VIEW abd in MODEL
		 **/
		
		global $context;
		$context = 'loader.list.';
		
		parent::__construct ( $config );
	}
	
	function display($tpl = null)
	{
		global $mainframe, $context;

	
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('jtask') );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
		
		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_LOADER' ), 'loader' );
		//moduliHelperToolbar::customX ( $task = 'loader', $icon = 'refresh-data', 'onClick="avvia_ajax(document.adminForm.mio_testo.value);"' , 'Aggiorna dati utente', FALSE );
		//JToolBarHelper::preferences ( 'com_sapef01', '450' );

		
			//DEVNOTE: Get URL
		$uri = & JFactory::getURI ();
		
		//DEVNOTE:save a reference into view
		$this->assignRef ( 'user', JFactory::getUser () );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'items', $items );
		$this->assignRef ( 'pagination', $pagination );
		$this->assignRef ( 'request_url', $uri->toString () );
		
	
		$this->assignRef ( 'esito', $risultato );
		
		
			
		//DEVNOTE:call parent display
		parent::display ( $tpl );
	}

}
?>
