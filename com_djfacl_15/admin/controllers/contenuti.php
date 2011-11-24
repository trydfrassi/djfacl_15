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
jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'contenuti_detail.php');

class contenutiController extends JController
{
	var $_type = 'contenuti';
	function __construct( $default = array())
	{
		parent::__construct( $default );
	}

		function cancel()
	{
		$this->setRedirect( 'index.php' );
	}

	/**
	 * Method display
	 * 
	 * 1) crea il controller
	 * 2) passa il modello alla vista
	 * 3) carica il template e lo renderizza  	  	 	 
	 */

	
	function updateComponentsTable(){
		
		$queryPerUpdate = 
		" insert into #__djfacl_components (id,`option`)
		select distinct null, `option` from #__components as bb where bb.`option` != ''
		and not exists (select aa.* from #__djfacl_components as aa where aa.`option` = bb.`option`)
		order by 2;";
		utility::executeQuery($queryPerUpdate);
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_categories' from #__components
		where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_categories');";
		utility::executeQuery($queryPerUpdate);
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_sections' from #__components
		where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_sections');";
		utility::executeQuery($queryPerUpdate);
				
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_frontpage' from #__components
		where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_frontpage');";
		utility::executeQuery($queryPerUpdate);
		
		
	}
	
	function display() {
		
		//$this->updateComponentsTable();
		
		parent::display();
	}
	function copy()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_djfacl' );
		$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('contenuti_detail', 'Table');
		
		$user	= &JFactory::getUser();
		$n		= count( $cid );
		
		
		$id_group_copia = JRequest::getVar('grupposearchcopia');
	
		if ($n > 0)
		{
			foreach ($cid as $id)
			{
				//echo($id);
				
				
				if ($table->load( (int)$id ))
				{
					
					//echo("botrone = ".$table->jtask);
					//exit();
					$table->id				= 0;
					//$table->id_users		= 0;
					$table->id_group		= $id_group_copia;
					//$table->id_components	= 0;
					//$table->id_modules		= 0;
					//$table->id_section		= 0;
					//$table->id_category		= 0;
					//$table->id_item			= 0;
					//$table->id_article		= 0;
					//$table->stite_admin		= 0;
					//$table->jtask			= 0;
					//$table->css_block		= 0;

					if (!$table->check()){
						exit();
				
					}
					
					if (!$table->store()) {
					exit();
						return JError::raiseWarning( $table->getError() );
					}
				
				}
				
				else {
					return JError::raiseWarning( 500, $table->getError() );
				}
			}
			 	
		}
		else {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		$this->setMessage( JText::sprintf( 'Items copied', $n ) );
		
		$post = JRequest::get ( 'post' );

		$gruppo = $post ['grupposearch'];
		JRequest::setVar ( 'grupposearch', $gruppo );
		$searchGroupString = "";
		if ($gruppo != null && $gruppo != "") {
			$searchGroupString = "&grupposearch=$gruppo";
		}
		
		$search = $post ['search'];
		JRequest::setVar ( 'search', $search );
		$searchString = "";
		if ($search != null && $search != "") {
			$searchString = "&search=$search";
		}
		
		
		$this->setRedirect( 'index.php?option=com_djfacl&controller=contenuti'.$searchGroupString.$searchString );
	}
	
	

}	
?>
