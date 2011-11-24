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

//DEVNOTE: import CONTROLLER object class
jimport ( 'joomla.application.component.controller' );
jimport ( 'joomla.filesystem.archive' );
jimport ( 'joomla.filesystem.file' );



/**
 * helloworld  Controller
 *
 * @package		Joomla
 * @subpackage	helloworld
 * @since 1.5
 */

class loaderController extends JController {

	/**
	 * Custom Constructor
	 */
	function __construct($default = array()) {
		parent::__construct ( $default );
	}
	/**
	 * Cancel operation
	 * redirect the application to the begining - index.php
	 */
	function cancel() {
		$this->setRedirect ( 'index.php' );
	}

	/**
	 * Method display
	 *
	 * 1) create a helloworldVIEWhelloworld(VIEW) and a helloworldMODELhelloworld(Model)
	 * 2) pass MODEL into VIEW
	 * 3)	load template and render it
	 */

	function display() {
		parent::display ();
	}

	function saveFile() {
		global $mainframe;
		$file = JRequest::getVar ( 'upload', null, 'files', 'array' );
		jimport ( 'joomla.filesystem.file' );

		//Clean up filename to get rid of strange characters like spaces etc
		if ($_FILES ['upload'] ['size'] > 500000) {
			$mainframe->enqueueMessage ( JText::_ ( 'DIMENSIONE_ERRATA' ), 'error' );
			return false;
		}
		$filename = JFile::makeSafe ( $file ['name'] );
		if (strtolower ( JFile::getExt ( $filename ) ) == 'zip') {
			//Set up the source and destination of the file
			$src = $file ['tmp_name'];

			if (JFolder::exists ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" )) {
				JFolder::delete ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" );
			}
			JFolder::create ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS );
			$dest = JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . $filename;

			JFile::upload ( $src, $dest );
			return true;
		} else {
			$mainframe->enqueueMessage ( JText::_ ( 'ESTENSIONE_ERRATA' ), 'error' );
			return false;
		}
	}

	function restore() {
		global $mainframe;
		jimport ( 'joomla.filesystem.file' );
		$file = JRequest::getVar ( 'upload', null, 'files', 'array' );
		$filename = JFile::makeSafe ( $file ['name'] );
		if (! $this->saveFile ()) {
			$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
			return $this->execute ( 'loader' );
		}

		$zipAdapter = & JArchive::getAdapter ( 'zip' );
		$zipAdapter->extract ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . $filename, JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup" . DS );
		$files = JFolder::files ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup", '.', false, false );

		foreach ( $files as $file ) {
			if (strtolower ( JFile::getExt ( $file ) ) == 'sql') {
				$stringa = JFile::read ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup" . DS . $file );
				if (utility::executeScriptQuery ( $stringa )) {
					$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO' ), 'message' );
					return $this->execute ( 'loader' );
				} else {
					$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
					return $this->execute ( 'loader' );
				}
			} else {
				$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
				return $this->execute ( 'loader' );
			}
		}
		$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" );
		return $this->execute ( 'loader' );




	parent::display ();
	}

	function backup() {

		$sql_contenuti = "truncate table #__djfacl_contenuti;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_contenuti" );
		foreach ( $contenutiArray as $contenuti ) {
			$item = $contenuti->id_item;
			if ($item == '')
				$item = 0;
			$sql_contenuti .= "insert into #__djfacl_contenuti values (" .
			$contenuti->id . "," .
			$contenuti->id_users . "," .
			$contenuti->id_group . "," .
			$contenuti->id_components . "," .
			$contenuti->id_modules . "," .
			$contenuti->id_section . "," .
			$contenuti->id_category . "," .
			$item . "," .
			$contenuti->id_article . "," .
			$contenuti->site_admin . "," . "'" .
			addslashes($contenuti->jtask) . "'," . "'" .
			addslashes($contenuti->css_block) . "'," .
			$contenuti->published . "," .
			$contenuti->checked_out . "," . "'" .
			$contenuti->checked_out_time . "'," .
			$contenuti->ordering . ");\r\n";
		}


		$sql_contenuti .= "truncate table #__djfacl_gruppi_icone;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_gruppi_icone" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__djfacl_gruppi_icone values (" .
			$contenuti->id . ",
			" . $contenuti->idgroup . ",
			" . $contenuti->idicon . ",
			" . $contenuti->checked_out . ",
			" . "'" . $contenuti->checked_out_time . "',
			" . $contenuti->ordering . ");\r\n";
		}

		$sql_contenuti .= "truncate table #__djfacl_quickicon;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_quickicon" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__djfacl_quickicon values (" .
			$contenuti->id . ",
			" . "'" . addslashes($contenuti->text). "',
			'" . addslashes($contenuti->target) . "',
			'" . addslashes($contenuti->icon) . "',
			" . $contenuti->ordering . ",

			" . $contenuti->published . ",
			'" . addslashes($contenuti->title) . "',
			" . $contenuti->checked_out . ",
			" . "'" . $contenuti->checked_out_time . "');\r\n";
		}


		$sql_contenuti .= "truncate table #__djfacl_cssblock;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_cssblock" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__djfacl_cssblock values (" .
			$contenuti->id . "," . "'" .
			addslashes($contenuti->css_block) . "'," .
			$contenuti->published . "," .
			$contenuti->checked_out . "," . "'" .
			$contenuti->checked_out_time . "'," .
			$contenuti->ordering . ");\r\n";
		}


		$sql_contenuti .= "truncate table #__djfacl_gruppi_utenti;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_gruppi_utenti" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__djfacl_gruppi_utenti values (" .
			$contenuti->id . "," .
			$contenuti->idgroup . "," .
			$contenuti->iduser . "," . "'" .
			addslashes($contenuti->typology) . "'," .
			$contenuti->checked_out . "," . "'" .
			$contenuti->checked_out_time . "'," .
			$contenuti->ordering . ");\r\n";
		}

		$sql_contenuti .= "truncate table #__djfacl_jtask;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_jtask" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__djfacl_jtask values (" .
			$contenuti->id . "," . "'" .
			addslashes($contenuti->name) . "'," . "'" .
			addslashes($contenuti->jtask) . "'," .
			$contenuti->published . "," .
			$contenuti->checked_out . "," . "'" .
			$contenuti->checked_out_time . "'," .
			$contenuti->ordering . ");\r\n";
		}

		$sql_contenuti .= "truncate table #__groups;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__groups" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__groups values (" . $contenuti->id . "," . "'" .
			addslashes($contenuti->name) . "');\r\n";
		}

		$sql_contenuti .= "truncate table #__core_acl_aro_groups;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__core_acl_aro_groups" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__core_acl_aro_groups values (" . $contenuti->id . "," . $contenuti->parent_id . "," . "'" .
			addslashes($contenuti->name) . "'," . $contenuti->lft . "," . $contenuti->rgt . "," . "'" . $contenuti->value . "');\r\n";
		}

		$sql_contenuti .= "truncate table #__djfacl_components;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__djfacl_components" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__djfacl_components values (" . $contenuti->id . ",'" . $contenuti->option . "');\r\n";
		}

		$sql_contenuti .= "truncate table #__core_acl_aro;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__core_acl_aro" );
		foreach ( $contenutiArray as $contenuti ) {
			$name = str_replace("'", "''", $contenuti->name);
			$sql_contenuti .= "insert into #__core_acl_aro values (" .
			$contenuti->id . "," . "'" .
			$contenuti->section_value . "'," . "'" .
			$contenuti->value . "'," .
			$contenuti->order_value . "," . "'" .
			addslashes($name) . "'," .
			$contenuti->hidden . ");\r\n";
		}

		$sql_contenuti .= "truncate table #__core_acl_aro_sections;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__core_acl_aro_sections" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__core_acl_aro_sections values (" .
			$contenuti->id . "," . "'" .
			addslashes($contenuti->value) . "'," .
			$contenuti->order_value . "," . "'" .
			addslashes($contenuti->name) . "'," .
			$contenuti->hidden . ");\r\n";
		}

		$sql_contenuti .= "truncate table #__core_acl_groups_aro_map;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__core_acl_groups_aro_map" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__core_acl_groups_aro_map values (" .
			$contenuti->group_id . "," . "'" .
			$contenuti->section_value . "'," .
			$contenuti->aro_id . ");\r\n";
		}

		$sql_contenuti .= "truncate table #__users;\r\n";
		$contenutiArray = utility::getArray ( "select * from #__users" );
		foreach ( $contenutiArray as $contenuti ) {
			$sql_contenuti .= "insert into #__users values (" .
			$contenuti->id . "," . "'" .
			addslashes($contenuti->name) . "'," . "'" .
			str_replace("'", "", $contenuti->username) . "'," . "'" .
			str_replace("'", "", $contenuti->email) . "'," . "'" .
			$contenuti->password . "'," . "'" .
			$contenuti->usertype . "'," .
			$contenuti->block . "," .
			$contenuti->sendEmail . "," .
			$contenuti->gid . "," . "'" .
			$contenuti->registerDate . "'," . "'" .
			$contenuti->lastvisitDate . "'," . "'" .
			$contenuti->activation . "'," . "'" . trim ( $contenuti->params ) . "');\r\n";
		}
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup" );
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" );

		if (! file_exists ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" )) {
			mkdir ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup", 0777 );
		}

		if (! file_exists ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup" )) {
			mkdir ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup", 0777 );
		}

		$data_odierna = gmdate ( 'Y-m-d-H-i-s' );

		$identificatore = fopen ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup" . DS . "backup_djfacl_$data_odierna.sql", "w" );

		fwrite ( $identificatore, $sql_contenuti );
		fclose ( $identificatore );

		$folder = JRequest::getVar ( 'folder', null );
		$folder = str_replace ( '.', DS, $folder );
		$archive_file_name = JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup_djfacl_$data_odierna.zip";
		$excludes = array ('.svn', 'CVS', 'index.php', 'index.html', '.htaccess', 'Thumbs.db' );
		$files = JFolder::files ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup", '.', false, false, $excludes );
		$zipAdapter = & JArchive::getAdapter ( 'zip' );
		$filesArray = array ();
		foreach ( $files as $file ) {
			//In zip file we do not want to include folder
			$data = JFile::read ( JPATH_BASE . DS . "components" . DS . "com_djfacl" . DS . "backup" . DS . "backup" . DS . "backup_djfacl_$data_odierna.sql" );
			$filesArray [] = array ('name' => $file, 'data' => $data );
		}
		if (! $zipAdapter->create ( $archive_file_name, $filesArray, array () )) {
			global $mainframe;
			$mainframe->enqueueMessage ( 'Can not create zipfile.', 'message' );
		}
		global $mainframe;
		$mainframe->enqueueMessage ( JText::_ ( 'BACKUP' ), 'message' );
		return $this->execute ( 'loader' );
		parent::display ();

	}

}
?>
