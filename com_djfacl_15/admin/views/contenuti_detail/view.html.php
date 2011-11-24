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


class contenuti_detailVIEWcontenuti_detail extends JView {

	/**
	 * Display the view
	 */
	function display($tpl = null) {

		global $mainframe, $option;
		$document = & JFactory::getDocument ();
		$document->setTitle ( JText::_ ( 'contenuti' ) );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/icon.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/general.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/modal.css' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/menu.css' );
		$document->addScriptDeclaration ( "var imageFolder =  'components/com_djfacl/assets/images/'" ); // Path to images
		$document->addScript ( 'components/com_djfacl/assets/js/drag-drop-folder-tree.js' );
		$document->addStyleSheet ( 'components/com_djfacl/assets/css/drag-drop-folder-tree.css' );
		
		
$document->addStyleSheet ( 'components/com_djfacl/assets/css/djfacl.css' );
		
		
		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_CONTENUTI_DETTAGLIO' ), 'content' );
		$uri = & JFactory::getURI ();
		$user = & JFactory::getUser ();
		$model = & $this->getModel ();
		$this->setLayout ( 'form' );
		$lists = array ();
		$detail = & $this->get ( 'data' );
		$isNew = ($detail->id < 1);

		$text = $isNew ? JText::_ ( 'NEW' ) : JText::_ ( 'EDIT' );

		//JToolBarHelper::save ();

		if ($isNew) {
			JToolBarHelper::cancel ();
			$detail->id_group = '18';
		} else {
			JToolBarHelper::cancel ( 'cancel', 'Close' );
		}

		if (! $isNew) {
			$model->checkout ( $user->get ( 'id' ) );
		}

		$parent_id = JRequest::getVar ( 'id_parent' );

		$acl = & JFactory::getACL ();
		$gtree = $acl->get_group_children_tree ( null, 'USERS', false );

		$grupposearch = utility::getDjfVar('idgroup',$detail->id_group );

		if ($grupposearch != "" && $grupposearch != null && $isNew) {
			$lists ['gid'] = JHTML::_ ( 'select.genericlist', $gtree, 'id_group', 'size="10"  onChange="document.adminForm.id_users.disabled=\'disabled\'" ', 'value', 'text', $grupposearch );
		} else {
			$lists ['gid'] = JHTML::_ ( 'select.genericlist', $gtree, 'id_group', 'size="10"  onChange="document.adminForm.id_users.disabled=\'disabled\'" ', 'value', 'text', $detail->id_group );
			$grupposearch = $detail->id_group;
		}


		$select = utility::addItemToSelect(NULL,'TUTTI','999999');
		$select = utility::addItemToSelect($select,'NESSUNO','0');


		$lists ['sezioni_associate'] = utility::getSelectExt2 (
		"select id as value, title as text from #__sections order by trim(title)",
		'id_section',
		$detail->id_section,
		'onChange="reloadValues_id_section();"',$select,'CREARE_UNA_SEZIONE','index.php?option=com_sections&scope=content');


		


		$lists ['utenti_associati'] = utility::getSelectExt ( "select id as value, name as text from #__users order by trim(name)", 'id_users', 'id_users', $detail->id_users, 'onChange="document.adminForm.id_group.disabled=\'disabled\'"' );





		//echo($detail->id_category);

		$queryPerCategorie = "select
				cat.id as value,
				cat.title as text
				from #__categories as cat where cat.section = $detail->id_section

				order by trim(cat.title)";

		if ($detail->id_section == 999999 || $detail->id_section == 0){
		$queryPerCategorie = "select
						cat.id as value,
						cat.title as text
						from #__categories as cat

						order by trim(cat.title)";

		}

	
		
			
		
		$lists ['menu_associati'] = utility::getSelectExt ( "select id as value, name as text from #__menu order by trim(name)", 'id_item', 'id_item', $detail->id_item, 'onChange="checkDisabled();"' );
		$lists ['componenti_associati'] = utility::getSelectExt ( "select distinct id as value, `option` as text from #__djfacl_components order by trim(`option`)", 'id_components', 'id_components', $detail->id_components, 'onChange="checkDisabled();"' );
		//$lists ['componenti_associati'] = utility::getSelectExt ( "select distinct `option` as value, `option` as text from #__djfacl_components where parent=0 order by trim(`option`)", 'id_components', 'id_components', $detail->id_components, 'onChange="checkDisabled();"' );
		
		
		
		
		
		
		
		$lists ['cssblock_associati'] = utility::getSelectExt ( "SELECT id AS value, css_block AS text FROM #__djfacl_cssblock order by css_block asc, trim(css_block)", 'css_block', 'css_block', $detail->css_block, 'onChange="checkDisabled();"' );
		$lists ['jtask_associati'] = utility::getSelectExt ( "SELECT id AS value, concat(name,' - ',jtask) AS text FROM #__djfacl_jtask order by name asc,jtask asc, trim(jtask)", 'jtask', 'jtask', $detail->jtask, 'onChange="checkDisabled();"' );

		$queryArticolo = "select c.id, c.title from #__content c, #__djfacl_contenuti jc where jc.id_article = c.id and jc.id = $detail->id";
		//echo($queryArticolo);
		$arrayArticoli = utility::getArray ( $queryArticolo );
		if (sizeof ( $arrayArticoli ) > 0) {
			$idArticolo = $arrayArticoli [0]->id;
			$titoloArticolo = $arrayArticoli [0]->title;
		}

		jimport ( 'joomla.filter.filteroutput' );
		JFilterOutput::objectHTMLSafe ( $detail, ENT_QUOTES, 'description' );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'idArticolo', $idArticolo );
		$this->assignRef ( 'titoloArticolo', $titoloArticolo );
		$this->assignRef ( 'detail', $detail );
		$this->assignRef ( 'grupposearch', $grupposearch );
		$this->assignRef ( 'request_url', $uri->toString () );

		parent::display ( $tpl );
	}

}

?>
