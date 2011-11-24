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
jimport('joomla.application.component.helper');
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'toolbar.php' );



class gruppi_detailVIEWgruppi_detail extends JView {
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
	
		
		
		JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_GRUPPI_DETTAGLIO' ), 'tree' );
		$uri = & JFactory::getURI ();
		$user = & JFactory::getUser ();
		$model = & $this->getModel ();
		$this->setLayout ( 'form' );
		$lists = array ();
		$detail = & $this->get ( 'data' );
		$isNew = ($detail->id < 1);
		$cid		= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));


		/**
		 *
		 *
		 * Il seguente blocco commentato evita il controllo sul campo checked_out
		 * Tale controllo mostra in lista un lucchetto sui record che in quel momento
		 * sono rimasti appesi da parte dell'utente loggato sul frontend ed in questo punto
		 * restituisce un errore sul dettaglio di editing quando un amministratore cerca
		 * di entrare sul form del record in questione.
		 * Ho commentato perchè abbiamo bisogno del controllo totale almeno da parte
		 * dell'amministratore.
		 * Nota: questa funzione viene regolarmente gestita dalla funziona Strumenti->Controllo globale
		 * di Joomla ed il componente che effettua il rilascio delle risorse è
		 * com_checkin
		 *
		 *
		 */

		// fail if checked out not by 'me'
		/*if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'THE DETAIL' ), $detail->id_utente );
			$mainframe->redirect( 'index.php?option='. $option, $msg );
		}*/

		$text = $isNew ? JText::_ ( 'NEW' ) : JText::_ ( 'EDIT' );
				JToolBarHelper::save ();

		if ($isNew) {
			JToolBarHelper::cancel ();
			$detail->id_group = '18';
			$detail->name="";
		} else {
			JToolBarHelper::cancel ( 'cancel', 'Close' );
		}

		if (! $isNew) {
			$model->checkout ( $user->get ( 'id' ) );
			//$query = 'SELECT ordering AS value FROM ' . $model->_table_prefix . 'gruppi WHERE id = ' . ( int ) $detail->id . ' ORDER BY ordering';
			//$lists ['ordering'] = JHTML::_ ( 'list.specificordering', $detail, $detail->id, $query, 1 );
		}

		$parent_id = JRequest::getVar('id_parent');
	
	
		$acl =& JFactory::getACL();
		$gtree = $acl->get_group_children_tree( null, 'USERS', false );
		$stilegroup="";
		if ($isNew){
			$lists['gid'] 	= JHTML::_('select.genericlist',   $gtree, 'parent_id', 'size="10" style="display:none;"', 'value', 'text', $parent_id );
			$stilegroup = ' style="display:none;" ';
		}
		else{
			$lists['gid'] 	= JHTML::_('select.genericlist',   $gtree, 'parent_id', 'size="10"', 'value', 'text', $parent_id );
			
		}

		jimport ( 'joomla.filter.filteroutput' );
		JFilterOutput::objectHTMLSafe ( $detail, ENT_QUOTES, 'description' );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'detail', $detail );
		$this->assignRef ( 'stilegruppotr', $stilegroup);
		$this->assignRef ( 'request_url', $uri->toString () );

		parent::display ( $tpl );
	}



	function Utenti_associati($name, $active = NULL, $javascript = NULL, $order = 'name', $size = 1, $sel_desc = 1) {
		global $mainframe;
		$model = & $this->getModel ();
		$utenti_associati [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( 'Seleziona un utente' ) . ' -' );
		$utenti_associati = array_merge ( $utenti_associati, $model->getUsers ( $order ) );
		if (count ( $utenti_associati ) < 1) {
			$mainframe->redirect ( 'index.php?option=com_djfacl', JText::_ ( 'Devi prima creare un utente associato.' ) );
		}
		$utente = JHTML::_ ( 'select.genericList', $utenti_associati, $name, 'class="inputbox" size="' . $size . '" ' . $javascript, 'value', 'text', $sel_desc );
		return $utente;
	}

	
	

}

?>
