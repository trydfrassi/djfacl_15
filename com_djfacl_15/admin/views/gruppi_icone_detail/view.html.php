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


class gruppi_icone_detailVIEWgruppi_icone_detail extends JView {
	/**
	 * Display the view
	 */
	function display($tpl = null) {
		global $mainframe, $option;
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('contenuti') );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
	
		$uri = & JFactory::getURI ();
		$user = & JFactory::getUser ();
		$model = & $this->getModel ();
		$this->setLayout ( 'form' );
		$lists = array ();
		$detail = & $this->get ( 'data' );
		$isNew = ($detail->id < 1);
		//echo("<br>".$detail->id."<br>");
		
		$text = $isNew ? JText::_ ( 'NEW' ) : JText::_ ( 'EDIT' );
		
		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_gruppi_icone_DETTAGLIO' ), 'icon-groups' );
		JToolBarHelper::save ();
		
		if ($isNew) {
			JToolBarHelper::cancel ();
			$detail->idgroup = '18';
			$detail->typology = 'djfacl';
			
		} else {
			JToolBarHelper::cancel ( 'cancel', 'Close' );
		}
		
		
		if (! $isNew) {
			$model->checkout ( $user->get ( 'id' ) );
		}
		$post = JRequest::get ( 'post' );
		
		$acl =& JFactory::getACL();
		$gtree = $acl->get_group_children_tree( null, 'USERS', false );
		
		$idgroup = utility::getDjfVar('idgroup',$detail->idgroup);
		if ($idgroup == '0') $idgroup=$detail->idgroup;
	
		
		$gruppoext3= "SELECT distinct 
		jcaag.id as value,
		jcaag.name as text, 
		jcaag.rgt-jcaag.lft as pad
		FROM 
		#__core_acl_aro_groups as jcaag where id > 30
		order by jcaag.name"; 
		$lists ['gid']= utility::getSelectExt2($gruppoext3, 'idgroup', $idgroup, ' onChange="document.adminForm.submit();" style="width:198px;padding:0;margin: 0 8px 0 0;"  ' );
		
		//$lists['gid'] 	= JHTML::_('select.genericlist',   $gtree, 'idgroup', 'size="10" onChange="document.adminForm.id_users.disabled=\'disabled\'" ', 'value', 'text', $idgroup );
	
		
		if (isset($post['iduser'])) $iduser_ = $post['iduser'];
		else $iduser_="";
		$array = utility::getArray("select id as value, name as text from #__users", "trim(name)");
		$disabled = "";
		$selezionato="";
		if (!$isNew){
			$disabled = " disabled=\"disabled\" ";
			if (!empty($detail->iduser))
			$selezionato = $detail->iduser;
		}
		else{
			$matricola=JRequest::getVar('matricola');			
			if ($matricola != null && $matricola != ""){
				$selezionato = $matricola;
				$disabled = " disabled=\"disabled\" ";
			}
			$detail->idicon="";
		}
		$queryicone = 	"SELECT id AS value, text as text, icon as icon FROM #__djfacl_quickicon order by text asc";
		$lists ['icone_associate'] = utility::getSelectExt2 ($queryicone,'idicon',$detail->idicon,'onChange="reloadValues_idicon();"',null);
		$array = utility::getArray("select id as value, name as text from #__core_acl_aro_groups", "trim(name)");
		$gruppi = utility::getSelect($array, '' ,'idgroup', $idgroup, 'style="width:198px;padding:0;margin: 0 8px 0 0;"  ' );
		$lists ['gruppi_associati'] = $gruppi;
		
		
		
		jimport ( 'joomla.filter.filteroutput' );
		JFilterOutput::objectHTMLSafe ( $detail, ENT_QUOTES, 'description' );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef('isNew', $isNew);
		$this->assignRef ( 'detail', $detail );
		$this->assignRef ( 'request_url', $uri->toString () );
		
		parent::display ( $tpl );
	}
	
	

}

?>
