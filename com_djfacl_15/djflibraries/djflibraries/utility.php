<?php
/**

 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

class utility extends JModel {
	
	function __construct() {
		global $mainframe, $context;
	
	}
	
	function unsetBuildRoute($fieldName) {
		if (isset ( $query [$fieldName] )) {
			$segments [] = $fieldName;
			$segments [] = $query [$fieldName];
			unset ( $query [$fieldName] );
		}
		return $query;
	}
	
	function parseRoute($segments) {
		$count = count ( $segments );
		$vars = array ();
		$menu = &JSite::getMenu ();
		$item = &$menu->getActive ();
		
		for($i = 0; $i < count ( $segments ); $i ++) {
			if (isset ( $segments [$i + 1] )) {
				if ($segments [$i] == "id_field") {
					$vars [$segments [$i]] = $segments [$i + 1];
				}
				echo ("<br>" . $segments [$i] . " = " . $segments [$i + 1]);
			}
		}
		return $vars;
	}
	
	function setBuildRoute($fieldName) {
		if (isset ( $query [$fieldName] )) {
			$segments [] = $fieldName;
			$segments [] = $query [$fieldName];
			unset ( $query [$fieldName] );
		}
	}
	
	function contains($stringa, $sottostringa) {
		$pos = strpos ( $stringa, $sottostringa );
		if ($pos === false)
			return false;
		else
			return true;
	}
	
	function getPagination($pagination) {
		if (! utility::contains ( $pagination->getListFooter (), "pag=si" ))
			$paginator_string = str_replace ( "index.php?", "index.php?pag=si&", $pagination->getListFooter () );
		else
			$paginator_string = $pagination->getListFooter ();
		
		echo ($paginator_string);
	
	}
	
	function getRetStringForBack() {
		global $mainframe;
		$uri = & JFactory::getURI ();
		$ret = $uri->toString ();
		$ret = base64_encode ( $ret );
		return $ret;
	}
	
	function canJAccess() {
		global $mainframe;
		$acl = & JFactory::getACL ();
		
		$acl->addACL ( 'com_content_djfacl', 'edit', 'users', 'editor', 'content', 'all' );
		$acl->addACL ( 'com_content_djfacl', 'edit', 'users', 'publisher', 'content', 'all' );
		$acl->addACL ( 'com_content_djfacl', 'edit', 'users', 'manager', 'content', 'all' );
		$acl->addACL ( 'com_content_djfacl', 'edit', 'users', 'administrator', 'content', 'all' );
		$acl->addACL ( 'com_content_djfacl', 'edit', 'users', 'super administrator', 'content', 'all' );
		
		$user = & JFactory::getUser ();
		
		$canAccess = ($user->authorize ( 'com_content_djfacl', 'edit' ));
		return $canAccess;
	
	}
	
	function endsWith($string, $ending) {
		$len = strlen ( $ending );
		$string_end = substr ( $string, strlen ( $string ) - $len );
		return $string_end == $ending;
	}
	
	function backup($componente = "djfappend", $arrayTabelle = array("field","field_value","field_type")) {
		
		global $mainframe;
		$postfix = $componente;
		$componente = "com_" . $postfix;
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" );
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" );
		
		if (! file_exists ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" )) {
			mkdir ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup", 0777 );
		}
		
		if (! file_exists ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" )) {
			mkdir ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup", 0777 );
		}
		
		$data_odierna = gmdate ( 'Y-m-d-H-i-s' );
		
		foreach ( $arrayTabelle as $questaTabella ) {
			$tableName = $questaTabella;
			$path_back = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . "backup_" . $componente . "_" . $tableName . ".sql";
			$path_back = str_replace ( "\\", "/", $path_back );
			$identificatore = fopen ( $path_back, "w" );
			$sql_contenuti = utility::getBackupScript ( $postfix, $questaTabella );
			fwrite ( $identificatore, $sql_contenuti );
			fclose ( $identificatore );
		}
		
		$folder = JRequest::getVar ( 'folder', null );
		$folder = str_replace ( '.', DS, $folder );
		$archive_file_name = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup_" . $componente . "_" . $data_odierna . ".zip";
		$excludes = array ('.svn', 'CVS', 'index.php', 'index.html', '.htaccess', 'Thumbs.db' );
		$files = JFolder::files ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup", '.', false, false, $excludes );
		$zipAdapter = & JArchive::getAdapter ( 'zip' );
		$filesArray = array ();
		
		foreach ( $files as $file ) {
			$data = JFile::read ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . $file );
			$filesArray [] = array ('name' => $file, 'data' => $data );
		}
		
		if (! $zipAdapter->create ( $archive_file_name, $filesArray, array () )) {
			$mainframe->enqueueMessage ( 'Can not create zipfile.', 'message' );
			return false;
		}
		
		$mainframe->enqueueMessage ( JText::_ ( 'BACKUP_CORRETTO' ), 'message' );
		return true;
	
	}
	function backup_old($componente, $arrayTabelle) {
		
		global $mainframe;
		
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" );
		JFolder::delete ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" );
		
		if (! file_exists ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" )) {
			mkdir ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup", 0777 );
		}
		
		if (! file_exists ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" )) {
			mkdir ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup", 0777 );
		}
		
		$data_odierna = gmdate ( 'Y-m-d-H-i-s' );
		
		$pathF = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . "backup_$componente_$data_odierna.sql";
		$path_backslashes = str_replace ( "\\", "/", $pathF );
		
		foreach ( $arrayTabelle as $questaTabella ) {
			
			$postfix = $questaTabella;
			$tableName = $questaTabella;
			$path_back = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . "backup_$componente_$postfix.sql";
			$path_back = str_replace ( "\\", "/", $path_back );
			$query = "SELECT * INTO LOCAL OUTFILE '" . $path_back . "' from " . $tableName . ";";
			utility::executeQuery ( $query );
		
		}
		
		$folder = JRequest::getVar ( 'folder', null );
		$folder = str_replace ( '.', DS, $folder );
		$archive_file_name = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup_" . $componente . "_" . $data_odierna . ".zip";
		$excludes = array ('.svn', 'CVS', 'index.php', 'index.html', '.htaccess', 'Thumbs.db' );
		$files = JFolder::files ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup", '.', false, false, $excludes );
		$zipAdapter = & JArchive::getAdapter ( 'zip' );
		$filesArray = array ();
		
		foreach ( $files as $file ) {
			//In zip file we do not want to include folder
			$data = JFile::read ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . $file );
			$filesArray [] = array ('name' => $file, 'data' => $data );
		}
		
		if (! $zipAdapter->create ( $archive_file_name, $filesArray, array () )) {
			$mainframe->enqueueMessage ( 'Can not create zipfile.', 'message' );
			return false;
		}
		
		$mainframe->enqueueMessage ( JText::_ ( 'BACKUP_CORRETTO' ), 'message' );
		return true;
	
	}
	
	function restore($componente = "djfappend") {
		
		global $mainframe;
		$postfix = $componente;
		$componente = "com_" . $postfix;
		jimport ( 'joomla.filesystem.file' );
		$file = JRequest::getVar ( 'upload', null, 'files', 'array' );
		$filename = JFile::makeSafe ( $file ['name'] );
		if (! $this->saveFile ()) {
			$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
			return false;
		}
		
		$zipAdapter = & JArchive::getAdapter ( 'zip' );
		$zipAdapter->extract ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . $filename, JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS );
		$files = JFolder::files ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup", '.', false, false );
		
		foreach ( $files as $file ) {
			
			if (strtolower ( JFile::getExt ( $file ) ) == 'sql') {
				
				$path = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . $file;
				$path = str_replace ( "\\", "/", $path );
				
				$tabella = str_replace ( "backup_", "", $file );
				$tabella = str_replace ( ".sql", "", $tabella );
				$tabella = "#__" . $postfix . "_" . $tabella;
				//echo($tabella);
				$sql_truncate = "truncate table " . $tabella . ";";
				utility::executeQuery ( $sql_truncate );
				
				$stringa = JFile::read ( $path );
				if (! empty ( $stringa )) {
					if (! utility::executeScriptQuery ( $stringa )) {
						$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
						return false;
					}
				}
			
			} else {
				$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
				return false;
			}
		}
		
		$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_CORRETTO' ) );
		return true;
	
	}
	function restore_old($componente) {
		
		global $mainframe;
		
		jimport ( 'joomla.filesystem.file' );
		$file = JRequest::getVar ( 'upload', null, 'files', 'array' );
		$filename = JFile::makeSafe ( $file ['name'] );
		if (! $this->saveFile ()) {
			$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
			return false;
		}
		
		$zipAdapter = & JArchive::getAdapter ( 'zip' );
		
		$zipAdapter->extract ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . $filename, JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS );
		$files = JFolder::files ( JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup", '.', false, false );
		
		foreach ( $files as $file ) {
			
			if (strtolower ( JFile::getExt ( $file ) ) == 'sql') {
				$path = JPATH_BASE . DS . "components" . DS . $componente . DS . "backup" . DS . "backup" . DS . $file;
				$path = str_replace ( "\\", "/", $path );
				
				$tabella = str_replace ( "backup_", "", $file );
				$tabella = str_replace ( ".sql", "", $tabella );
				$sql_truncate = "truncate table " . $tabella . ";";
				utility::executeQuery ( $sql_truncate );
				$query = "LOAD DATA LOCAL INFILE '" . $path . "' INTO TABLE " . $tabella;
				utility::executeQuery ( $query );
			
			} else {
				$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
				return false;
			}
		}
		
		$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_CORRETTO' ) );
		return true;
	
	}
	
	function getListUsersFromGroup($gruppo) {
		
		$queryGruppi = " select iduser from #__djfacl_gruppi_utenti where idgroup = " . $gruppo;
		$listaGruppi = utility::getQueryArray ( $queryGruppi );
		return $listaGruppi;
	}
	
	function getJavascriptListeForm($nomeForm = "") {
		?>
		function submitform(pressbutton){
		var form = document.adminForm;
   		if (pressbutton)
    			{form.task.value=pressbutton;}
     			if ((pressbutton=='add')||(pressbutton=='edit')||(pressbutton=='publish')||(pressbutton=='unpublish')||(pressbutton=='approve')||(pressbutton=='unapprove')
	 			||(pressbutton=='orderdown')||(pressbutton=='orderup')||(pressbutton=='saveorder')||(pressbutton=='remove') )
	 			{
	  			form.controller.value="<?php
		echo $nomeForm;
		?>";
	 			}
		try {
			form.onsubmit();
		}
		catch(e){}
		form.submit();
		}
		<?php
	}
	
	function getTDListe($linkEdit, $valoreVariabile, $checked_out = "0", $style = "") {
		
		if ($checked_out != "0") {
			$nomeutente = utility::getField ( 'select name as value from #__users' );
			$messaggio = "Record in uso da parte di " . $nomeutente;
			$a = '<a href="#" style="color:#cccccc" title="' . $messaggio . '" onclick="alert(\'' . $messaggio . '\');"> ';
		} else {
			$a = '<a style="' . $style . '" href="' . $linkEdit . '" title="' . JText::_ ( 'Edit record' ) . '">';
		}
		
		?>
<td> <?php
		echo $a;
		?>
			<?php
		echo $valoreVariabile;
		?></a></td>
<?php
	
	}
	
	function getThListe($stileWidth = ' style="width:1%" ', $label = "NUM", $nomeVariabile, $sortable = true, $type = "text") {
		
		if ($type == "check") {
			?>
<th <?php
			echo $stileWidth;
			?>><input type="checkbox" name="toggle" value=""
	onclick="checkAll(<?php
			echo count ( $this->items );
			?>);" /></th>
<?php
			return;
		
		}
		
		if ($sortable) {
			?>
<th <?php
			echo $stileWidth;
			?>>
			<?php
			echo JHTML::_ ( 'grid.sort', $label, $nomeVariabile, $this->lists ['order_Dir'], $this->lists ['order'] );
			?>
		</th>
<?php
		} else {
			?>

<th <?php
			echo $stileWidth;
			?>>
			<?php
			echo JText::_ ( $label );
			?>
		</th>
<?php
		}
	
	}
	
	function getSearchForm($styleTd = ' style="width:100%" ', $nomeLabel = "Filter", $nomeVariabile = "search", $valoreVariabile = "") {
		
		$valoreVariabile = utility::getDjfVar ( $nomeVariabile, '' );
		?>

<td <?php
		echo $styleTd;
		?>>
<div class="search-label"><?php
		echo JText::_ ( $nomeLabel );
		?>:</div>
<div class="search-field"><input type="text"
	name="<?php
		echo $nomeVariabile;
		?>"
	id="<?php
		echo $nomeVariabile;
		?>"
	value="<?php
		echo $valoreVariabile;
		?>" class="text_area"
	onchange="document.adminForm.submit();" /></div>
<div class="search-button-go">
<button onclick="this.form.submit();"><?php
		echo JText::_ ( 'Go' );
		?></button>
</div>
<div class="search-button-reset">
<button
	onclick="document.getElementById('<?php
		echo $nomeVariabile;
		?>').value='';this.form.submit();"><?php
		echo JText::_ ( 'Reset' );
		?></button>
</div>
</td>
<?php
	
	}
	
	function getFormTextRow($paramName, $paramValue = '', $inputTags = '') {
		$inputTags = ' class="text_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
	<td><input <?php
		echo $inputTags;
		?> type="text"
		name="<?php
		echo $paramName;
		?>"
		id="<?php
		echo $paramName;
		?>"
		value="<?php
		echo $paramValue;
		?>" /></td>
</tr>
<?php
	}
	
	function getFormTextRowOnlyShow($paramName, $paramValue = '', $inputTags = '', $stringPattern = '', $img = '', $link = '', $linkTitle = '') {
		$inputTags = ' class="text_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<input type="hidden" name="<?php
		echo $paramName;
		?>"
	value="<?php
		echo $paramValue;
		?>" />
<?php
		if ($img != "") {
			$paramValue = '<img src="' . $img . '"/>&nbsp;&nbsp;<span style="font-weight:bold;">' . $paramValue . '</span>';
		}
		
		if ($link != "") {
			$paramValue = '<a href="' . $link . '" title="' . JText::_ ( $linkTitle ) . '">' . $paramValue . '</a>';
		}
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
	<td <?php
		echo $inputTags;
		?>><?php
		echo $paramValue;
		?></td>

</tr>
<?php
	}
	
	function getFormCalendarRowOnlyShow($paramName, $paramValue = '', $inputTags = '', $stringPattern = '') {
		
		if ($stringPattern != "")
			$paramValue = JHTML::_ ( 'date', $paramValue, $stringPattern );
		
		$inputTags = ' class="calendar_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
	<td><?php
		echo $paramValue;
		?></td>
</tr>
<?php
	}
	
	function getFormCalendarRow($paramName, $paramValue = '', $inputTags = '') {
		$inputTags = ' class="calendar_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
	<td><?php
		echo JHTML::_ ( 'calendar', $paramValue, $paramName, $paramName, '%Y-%m-%d %H:%M:%S', $inputTags );
		?></td>
</tr>
<?php
	}
	
	function getFormSelectRow($paramName, $paramValue = '', $select_custom = NULL, $query_select = '', $inputTags = '', $td = true, $debug = false) {
		
		if ($debug)
			echo ("<BR>$query_select</BR>");
		
		$javascript_mio = 'onChange="reloadValues_' . $paramName . '();"  class="list_row" ';
		
		if ($inputTags != '')
			$javascript_mio = $inputTags;
		
		if ($query_select == '' || $query_select == null) {
			$selectGenerato = JHTML::_ ( 'select.genericList', $select_custom, $paramName, $inputTags, 'value', 'text', $paramValue );
		} else {
			
			$listRis = utility::getQueryArray ( $query_select );
			if (sizeof ( $listRis ) > 0) {
				
				$selectGenerato = utility::getSelectExt2 ( $query = $query_select, $idSelezione = $paramName, $active = $paramValue, $javascript = $javascript_mio, $select = $select_custom );
			} else {
				$select_custom = utility::addArrayItemToSelect ( array ("Nessuna" => "0" ) );
				$selectGenerato = JHTML::_ ( 'select.genericList', $select_custom, $paramName, $inputTags, 'value', 'text', $paramValue );
			}
		}
		$inputTags = ' class="list_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		
		if ($td) {
			?>
<tr id="tr_<?php
			echo $paramName;
			?>">
	<td <?php
			echo $td1Tags;
			?>><label for="title"><?php
			echo JText::_ ( strtoupper ( $paramName ) );
			?>:	</label></td>
	<td <?php
			echo $inputTags;
			?>>
	<div id="<?php
			echo $paramName;
			?>_div">
					<?php
			echo $selectGenerato;
			?>
				</div>
	</td>
</tr>
<?php
		} else
			echo ($selectGenerato);
	
	}
	
	function getFormListRow($paramName, $paramValue = '', $inputTags = '') {
		$inputTags = ' class="list_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
	<td <?php
		echo $inputTags;
		?>>
	<div id="<?php
		echo $paramName;
		?>"><?php
		echo $paramValue;
		?></div>
	</td>
</tr>
<?php
	}
	
	function getFormRadioRow($paramName, $paramValue, $radioName, $radioValue, $inputTags = '') {
		$inputTags = ' class="radio_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $radioName ) );
		?>:	</label></td>
	<td><input <?php
		echo $inputTags;
		?> type="radio"
		<?php
		if ($paramValue == $radioValue) {
			echo ('checked');
		}
		?>
		name="<?php
		echo $paramName;
		?>"
		id="<?php
		echo $paramName;
		?>"
		value="<?php
		echo $radioValue;
		?>" /></td>
</tr>
<?php
	}
	
	function getFormCheckboxRow($paramName, $paramValue = '', $valueSeSi = '1', $inputTags = '') {
		$inputTags = ' class="checkbox_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
	<td><input <?php
		echo $inputTags;
		?> type="checkbox"
		<?php
		if ($paramValue == $valueSeSi) {
			echo ('checked');
		}
		?>
		name="<?php
		echo $paramName;
		?>"
		id="<?php
		echo $paramName;
		?>"
		value="<?php
		echo $valueSeSi;
		?>" /></td>
</tr>
<?php
	}
	
	function getFormEditorRow($paramName, $paramValue = '', $inputTags = '', $width = 400, $height = 394, $editor_xtd = true) {
		$inputTags = ' class="editor_row" ' . $inputTags;
		$td1Tags = ' class="key2" ';
		?>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<td colspan="2" <?php
		echo $td1Tags;
		?>><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td>
</tr>
<tr id="tr_<?php
		echo $paramName;
		?>">
	<!-- td <?php
		echo $td1Tags;
		?> ><label for="title"><?php
		echo JText::_ ( strtoupper ( $paramName ) );
		?>:	</label></td-->
	<td colspan="2"><?php
		echo $this->editor->display ( $paramName, $paramValue, $width, $height, '60', '15', $editor_xtd );
		?></td>
</tr>
<?php
	}
	
	function getBaseUrl() {
		return JURI::root ();
	}
	
	function getButton($imgsrc, $linkbutton, $javascript = '', $name = '', $title = '', $id = '', $alt = '') {
		$img = '<a href="' . $linkbutton . '" name="' . $name . '" title="' . $title . '" id="' . $id . '" alt="' . $alt . '" onClick="' . $javascript . '"><img src="' . $imgsrc . '"/></a>';
		return $img;
	}
	
	function getBoxConferma($delete_url, $warning, $warning_msg, $submit_button, $confirm) {
		
		$echofori = '
		<div class="deleteWarning">
	        <h1>' . JText::_ ( $warning ) . '</h1>
	        <h2 id="warning_msg">' . JText::_ ( $warning_msg ) . '</h2>
	        <form action="' . $delete_url . '" name="delete_warning_form" method="post">
		        <div align="center">
		            <input type="submit" name="submit" value="' . JText::_ ( $submit_button ) . '" />
		        </div>
	        </form>
        </div>';
		echo ($echofori);
		
		if ($confirm == "yes")
			utility::closePopup ();
	
	}
	function closePopup() {
		echo "
			<script language=\"javascript\" type=\"text/javascript\">
	            window.parent.document.getElementById('sbox-window').close();
	            window.parent.location.reload();
            </script>";
	}
	
	function getJoomlaButton($imgSrc = '', $type = '') {
		$linkbutton = "#";
		$name = JText::_ ( $type );
		$title = $name;
		$custom = ' name="' . $name . '" title="' . $title . '" href="' . $linkbutton . '" onclick="javascript:submitbutton(\'' . $type . '\');"';
		?>
<div class="joomla-single-button"><?php
		echo (utility::getButtonCustom ( $custom, $imgSrc, $mode = '' ));
		?></div><?php
	}
	
	function getButtonCustom($custom, $imgsrc, $mode = "popup", $xIframe = '655', $yIframe = '500') {
		
		global $mainframe, $option;
		$document = & JFactory::getDocument ();
		$document->addStyleSheet ( JURI::root ( true ) . '/media/system/css/modal.css' );
		$document->addScript ( JURI::root ( true ) . '/media/system/js/modal.js' );
		
		$document->addScriptDeclaration ( "
		
		window.addEvent('domready', function() {

	SqueezeBox.initialize({});

	$$('a.modal-button').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
		});
	});
});
	
window.addEvent('domready', function() {

	SqueezeBox.initialize({});

	$$('a.modal').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
		});
	});
});
		" );
		
		if ($mode == "popup")
			$custom .= " class=\"modal-button\"   rel=\"{handler: 'iframe', size:{x:" . $xIframe . ", y:" . $yIframe . "}}\"";
		$img = '<a ' . $custom . ' type="button"><img src="' . $imgsrc . '"/></a>';
		
		return $img;
	}
	
	function _rebuild_tree($table, $group_id, $left = 1) {
		
		$query = 'SELECT id FROM ' . $table . ' WHERE parent_id=' . ( int ) $group_id;
		echo ($query);
		$rs = utility::getQueryArray ( $query );
		$right = $left + 1;
		
		foreach ( $rs as $row ) {
			$right = utility::_rebuild_tree ( $table, $row->id, $right );
			if ($right === FALSE) {
				return FALSE;
			}
		}
		$query = 'UPDATE ' . $table . ' SET lft=' . ( int ) $left . ', rgt=' . ( int ) $right . ' WHERE id=' . ( int ) $group_id;
		utility::executeQuery ( $query );
		return $right + 1;
	}
	
	function startsWith($stringa, $chiave) {
		// Recommended version, using strpos
		return strpos ( $stringa, $chiave ) === 0;
	}
	
	function getBackupScript($componente_senzacom, $nome_tabella) {
		
		global $mainframe;
		$db = & JFactory::getDBO ();
		$path_table = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_' . $componente_senzacom . DS . 'tables';
		JTable::addIncludePath ( $path_table );
		$oggetto_tabella = & JTable::getInstance ( $nome_tabella . "_detail", 'Table' );
		$nome_tabella = "#__" . $componente_senzacom . "_" . $nome_tabella;
		$sql_contenuti = "truncate table " . $nome_tabella . ";";
		$query = "select * from " . $nome_tabella . ";";
		$contenutiArray = utility::getQueryArray ( $query );
		
		foreach ( $contenutiArray as $questo ) {
			$i = 0;
			$listacampi = "";
			$listavalori = "";
			while ( list ( $chiave, $valore ) = each ( $questo ) ) {
				
				if (utility::getIfFieldTypeIsInt ( $oggetto_tabella, $chiave ))
					$valoreVero = $valore;
				else
					$valoreVero = "'" . addslashes ( $valore ) . "'";
				
				if ($i == 0) {
					$listavalori .= $valoreVero;
					$listacampi .= $chiave;
				} else {
					$listavalori .= ", " . $valoreVero;
					$listacampi .= ", " . $chiave;
				}
				$i ++;
			}
			
			$sql_contenuti .= "insert into " . $nome_tabella . " (" . $listacampi . ") 
			 values (" . $listavalori . ");\r\n";
		}
		
		return $sql_contenuti;
	
	}
	
	function getIfFieldTypeIsInt($oggetto_tabella, $campo) {
		
		$properties = get_object_vars ( $oggetto_tabella );
		$setfields = '';
		while ( list ( $chiave, $valore ) = each ( $properties ) ) {
			if (! utility::startsWith ( $chiave, "_" )) {
				if ($chiave == $campo) {
					if (is_int ( $valore )) {
						return true;
					}
				}
			
			}
		
		}
		return false;
	
	}
	
	function executeScriptQuery($query) {
		global $mainframe;
		$db = & JFactory::getDBO ();
		// Create an array of queries from the sql file
		jimport ( 'joomla.installer.helper' );
		$queries = JInstallerHelper::splitSql ( $query );
		
		if (count ( $queries ) == 0) {
			// No queries to process
			return 0;
		}
		
		// Process each query in the $queries array (split out of sql file).
		foreach ( $queries as $query ) {
			$query = trim ( $query );
			if ($query != '' && $query {0} != '#') {
				$db->setQuery ( $query );
				if (! $db->query ()) {
					JError::raiseWarning ( 1, 'JInstaller::install: ' . JText::_ ( 'SQL Error' ) . " " . $db->stderr ( true ) );
					return false;
				}
			}
		}
		return true;
	}
	function log($stringa){
		
		echo($stringa."<br>");
	}
	function executeQuery($query) {
		global $mainframe;
		$db = & JFactory::getDBO ();
		$db->setQuery ( $query );
		//echo($query);
		return $db->queryBatch ( true, true ); // effettua un salvataggio con transaction attiva per gestire la concorrenzialità
	}
	
	function getArray($query, $order = '1') {
		global $mainframe;
		$db = & JFactory::getDBO ();
		$query .= ' ORDER BY ' . $order;
		$db->setQuery ( $query );
		
		/*
		 * 
		 * esempio di come si usa
		 * 
		 * 	$arraymynumbers = utility::getArray("
		 *  select a.boxbuy from #__joomuser_idr_log_user a , #__joomuser_user b where a.idlottery = ".$detail->id." and a.iduser = b.id and b.idjusers = ".$user->id. " and a.playgame = 1 ", "a.boxbuy");
  		 *   foreach ( $arraymynumbers as $row ) {
		 *		$mynumbers.="_".$row->boxbuy;
		 *   }
		 * 
		 */
		
		return $db->loadObjectList ();
	}
	
	function getQueryArray($query) {
		global $mainframe;
		$db = & JFactory::getDBO ();
		$db->setQuery ( $query );
		return $db->loadObjectList ();
	}
	function check_if_table_exists($table) {
		// checks to see if a table in the database exists
		$db = & JFactory::getDBO ();
		$query = 'select * from ' . $table;
		//echo($query."<br>");
		$db->setQuery ( $query );
		if (! $db->query ()) {
			//JError::raiseWarning ( 1, 'JInstaller::install: ' . JText::_ ( 'SQL Error' ) . " " . $db->stderr ( true ) );
			$esito = false;
		} else {
			$esito = true;
		}
		
		//echo("esito = ".$esito);
		return $esito;
	
	}
	
	function getFieldFirstRecord($query, $field, $debug = false) {
		
		global $mainframe;
		$db = & JFactory::getDBO ();
		if ($debug)
			echo ($query);
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if ($rows) {
			$i = 1;
			foreach ( $rows as $row ) {
				return $row->$field;
				$i ++;
			}
		}
		return "";
	
	}
	
	function getField($query) {
		
		global $mainframe;
		$db = & JFactory::getDBO ();
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if ($rows) {
			$i = 1;
			foreach ( $rows as $row ) {
				return $row->value;
				$i ++;
			}
		}
		return $db->loadObjectList ();
	}
	
	function getSelectNoQuery($array, $name, $active, $javascript) {
		global $mainframe;
		$select_select = JHTML::_ ( 'select.genericList', $array, $name, $javascript, 'value', 'text', $active );
		return $select_select;
	
	}
	
	function getSelect($array, $testoSelect, $name, $active = NULL, $javascript = NULL, $order = 'descript', $size = 1, $sel_cat = 1, $errorMSG = 'YOU MUST CREATE AN ITEM FIRST.', $redirectPage = 'index.php') {
		global $mainframe;
		
		if ($testoSelect == "") {
			$select = $array;
		} else {
			$select [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( $testoSelect ) . ' -' );
			$select = array_merge ( $select, $array );
		}
		if (count ( $select ) < 1) {
			$mainframe->redirect ( $redirectPage, JText::_ ( $errorMSG ) );
		}
		$select_select = JHTML::_ ( 'select.genericList', $select, $name, $javascript, 'value', 'text', $active );
		return $select_select;
	}
	
	function setDjfVar($nome, $valore) {
		$session = JSession::getInstance ( 'none', array () );
		//$valore = utility::generalEscaping($valore);
		$session->set ( $nome, $valore );
	
	}
	
	function getDjfVar($nome, $default = '') {
		if (empty ( $default ))
			$default = JRequest::getVar ( $nome );
		$session = JSession::getInstance ( 'none', array () );
		$variabile = JRequest::getVar ( $nome );
		if (! isset ( $variabile )) {
			$variabile = $session->get ( $nome );
			if (! isset ( $variabile )) {
				$variabile = $default;
			}
		}
		//$variabile = utility::generalEscaping($variabile);
		$session->set ( $nome, $variabile );
		return $variabile;
	}
	
	function generalEscaping($stringa) {
		$stringa = str_replace ( "'", "''", $stringa );
		return $stringa;
	}
	
	function executeScriptFromFile($filepath) {
		
		global $mainframe;
		jimport ( 'joomla.filesystem.file' );
		$stringa = JFile::read ( $filepath );
		if (utility::executeScriptQuery ( $stringa )) {
			$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO' ), 'message' );
			return true;
		} else {
			$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
			return false;
		}
		$mainframe->enqueueMessage ( JText::_ ( 'RIPRISTINO_ERRORE' ), 'error' );
		return true;
	}
	function findXmlAttribute($object, $attribute) {
		foreach ( $object->attributes () as $a => $b ) {
			if ($a == $attribute) {
				$return = $b;
			}
		}
		if ($return) {
			return $return;
		}
	}
	function showAllBlockFromXml($file = "hider.xml", $nomeVariabile = "", $display = "yes") {
		$pathFile = JPATH_COMPONENT_ADMINISTRATOR . DS . $file;
		if ($display == "no")
			$display = "none";
		else
			$display = "";
		$xml = @simplexml_load_file ( $pathFile );
		$risultatoArray [] = null;
		$risultatoJS = "";
		foreach ( $xml->hider as $questoHider ) {
			if (utility::findXmlAttribute ( $questoHider, 'name' ) == $nomeVariabile) {
				foreach ( $questoHider as $questoHide ) {
					$risultatoJS .= ' document.getElementById("' . $questoHide . '").style.display="' . $display . '"; ';
				}
			}
		}
		return $risultatoJS;
	}
	function hideBlockFromXml($file = "hider.xml", $nomeVariabile = "", $valoreVariabile = "", $display = "yes") {
		$pathFile = JPATH_COMPONENT_ADMINISTRATOR . DS . $file;
		if ($display == "yes")
			$display = "none";
		else
			$display = "";
		$xml = @simplexml_load_file ( $pathFile );
		$risultatoArray [] = null;
		$risultatoJS = "";
		foreach ( $xml->hider as $questoHider ) {
			if (utility::findXmlAttribute ( $questoHider, 'id' ) == $valoreVariabile && utility::findXmlAttribute ( $questoHider, 'name' ) == $nomeVariabile) {
				foreach ( $questoHider as $questoHide ) {
					$risultatoJS .= ' document.getElementById("' . $questoHide . '").style.display="' . $display . '"; ';
				}
			}
		}
		return $risultatoJS;
	}
	
	function addArrayItemToSelectFromXml($nomeVariabile, $arrayItem = null) {
		
		$arrayXml = utility::getXmlTagValue ( "var.xml", $nomeVariabile );
		
		if ($arrayItem != null) {
			foreach ( $arrayItem as $questoItem ) {
				$valore = utility::getXmlTagValue ( "var.xml", $nomeVariabile, $questoItem );
				$nome = $questoItem;
				if ($uscita == null)
					$uscita = array ("$valore" => "$nome" );
				else
					$uscita = array_merge ( $uscita, array ("$valore" => "$nome" ) );
			}
		} else {
			
			while ( list ( $chiave, $valore ) = each ( $arrayXml ) ) {
				
				if (! empty ( $valore )) {
					
					if (empty ( $uscita ))
						$uscita = array ("$valore" => "$chiave" );
					else
						$uscita += array ("$valore" => "$chiave" );
				}
			}
		
		}
		return utility::addArrayItemToSelect ( $uscita );
	}
	
	function addArrayItemToSelect($arrayItem) {
		global $mainframe;
		$select [] = null;
		$i = 0;
		while ( list ( $chiave, $valore ) = each ( $arrayItem ) ) {
			if ($i == 0)
				$select = array (JHTML::_ ( 'select.option', $valore, JText::_ ( $chiave ) ) );
			else
				$select = array_merge ( $select, array (JHTML::_ ( 'select.option', $valore, JText::_ ( $chiave ) ) ) );
			$i ++;
		}
		return $select;
	}
	
	function addItemToSelect($select, $name, $value) {
		global $mainframe;
		$select2 [] = JHTML::_ ( 'select.option', $value, '- ' . JText::_ ( $name ) . ' -' );
		if (count ( $select ) < 1) {
			$select = $select2;
		} else {
			$select = array_merge ( $select, $select2 );
		}
		return $select;
	}
	
	function getSelectExt2($query, $idSelezione, $active = NULL, $javascript = "", $select2 = NULL, $errorMSG = 'YOU MUST CREATE AN ITEM FIRST.', $redirectPage = 'index.php') {
		
		global $mainframe;
		
		if ($query != null && $query != "") {
			//echo("<br>".$query."<br>");
			$array = utility::getQueryArray ( $query );
			
			if (count ( $array ) < 1) {
				echo ($redirectPage);
				echo (JText::_ ( $errorMSG ));
				//exit();
				$mainframe->redirect ( $redirectPage, JText::_ ( $errorMSG ) );
			}
			if (! empty ( $select2 )) {
				//echo($select2);
				$select = array_merge ( $select2, $array );
			} else {
				
				//$select2 = utility::addItemToSelect(NULL,'TUTTI','999999');
				//$select2 = utility::addItemToSelect($select2,'NESSUNO','0');
				

				$select = $array;
			}
			
			$select_select = JHTML::_ ( 'select.genericList', $select, $idSelezione, $javascript, 'value', 'text', $active );
			$select_select = '<div style="float:left;z-index:-1;">' . $select_select . '</div>';
			$select_select .= '<div class="preloader"><img id="my-pic-' . $idSelezione . '" style="visibility: hidden;" /></div>';
		
		}
		return $select_select;
	}
	
	function getSelectExt(
	$query, 
	$testoIniziale = "seleziona una voce dalla lista", 
	$idSelezione, 
	$active = NULL, 
	$javascript = "", 
	$tuttienessuno = true, 
	$errorMSG = 'YOU MUST CREATE AN ITEM FIRST.', 
	$redirectPage = 'index.php', 
	$noInitial='no') {
		global $mainframe;
		$array = utility::getQueryArray ( $query );
		if ($testoIniziale == "") {
			$select = $array;
		} else {
			if ($tuttienessuno == true) {
				$select1 [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( 'NESSUNO' ) . ' -' );
				$select2 [] = JHTML::_ ( 'select.option', '999999', '- ' . JText::_ ( 'TUTTI' ) . ' -' );
				$select = array_merge ( $select1, $select2 );
				$select = array_merge ( $select, $array );
			} else {
				$select [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( $testoIniziale ) . ' -' );
				$select = array_merge ( $select, $array );
			}
			if ($noInitial=="si"){
				$select = $array;
			}
		}
		if (count ( $select ) < 1) {
			$mainframe->redirect ( $redirectPage, JText::_ ( $errorMSG ) );
		}
		$select_select = JHTML::_ ( 'select.genericList', $select, $idSelezione, $javascript, 'value', 'text', $active );
		$select_select = '<div style="float:left;z-index:-1;">' . $select_select . '</div>';
		$select_select .= '<div class="preloader"><img id="my-pic-' . $idSelezione . '" style="visibility: hidden;margin-left:5px;" /></div>';
		
		return $select_select;
	}
	
	function getDataOdierna($formato = 'd-m-Y') {
		// formato in giorni d-m-Y H:i:s
		return gmdate ( $formato );
	}
	
	function getDistanzaDateGiorni($data1, $data2, $formato = 'd-m-Y') {
		$data1 = strtotime ( date ( $formato, strtotime ( $data1 ) ) );
		$data2 = strtotime ( date ( $formato, strtotime ( $data2 ) ) );
		$differenza = ($data2 - $data1) / (86400);
		return $differenza;
	}
	
	function getAnniDaGiorni($differenzaInGiorni) {
		$anni = floor ( $differenzaInGiorni / 365 );
		return $anni;
	}
	
	public function getXmlTagValue($file = "var.xml", $nomeVariabile = "", $valoreVariabile = "") {
		
		$pathFile = JPATH_COMPONENT_ADMINISTRATOR . DS . $file;
		$xml = simplexml_load_file ( $pathFile );
		$risultatoArray [] = null;
		foreach ( $xml as $key0 => $value ) {
			$passa = false;
			foreach ( $value->attributes () as $attributeskey0 => $attributesvalue1 ) {
				if ($attributeskey0 == "name" && $attributesvalue1 == $nomeVariabile) {
					
					foreach ( $value as $key1 => $value ) {
						
						foreach ( $value->attributes () as $attributeskey0 => $attributesvalue1 ) {
							
							if ($attributeskey0 == "id" && $attributesvalue1 == $valoreVariabile) {
								return $value;
							} else {
								$risultatoArray ["$attributesvalue1"] = "$value";
							
							}
						
						}
					
					}
				
				}
			
			}
		
		}
		
		return $risultatoArray;
	
	}
	
	function getXmlConfigurationParams($componentName, $paramName) {
		//global $mainframe;
		$params = JComponentHelper::getParams ( $componentName );
		$valore = $params->get ( $paramName );
		return $valore;
	}
	
	function setJSProperty($nomefield, $attributo, $valore) {
		$javascript = '	document.getElementById("' . $nomefield . '").' . $attributo . '="' . $valore . '";	';
		return $javascript;
	}
	
	function endIfJSCheck() {
		$javascript = ' } ';
		return $javascript;
	}
	
	function ifJSCheck($nomefield, $condizione) {
		$javascript = ' if (form.' . $nomefield . '.value ' . $condizione . '){	';
		return $javascript;
	}
	
	function getJSCheckForm($nomefield, $condizione, $messaggio, $operatore_booleano = "and") {
		
		$condizioniLista = explode ( "#$#", $condizione );
		$condizioniNome = explode ( "#$#", $nomefield );
		$andStringa = "";
		if ($operatore_booleano == "and")
			$operatore_booleano = "&&";
		else
			$operatore_booleano = "||";
		$i = 0;
		foreach ( $condizioniLista as $questaCondizione ) {
			if ($i == 0)
				$andStringa .= ' (document.getElementById("' . $condizioniNome [$i] . '").value ' . $questaCondizione . ')';
			else
				$andStringa .= ' ' . $operatore_booleano . ' (document.getElementById("' . $condizioniNome [$i] . '").value ' . $questaCondizione . ')';
			$i ++;
		}
		
		$javascript = '
			document.getElementById("' . $condizioniNome [0] . '").style.backgroundColor="white";
			if (' . $andStringa . '){
				alert("' . $messaggio . '");
				document.getElementById("' . $condizioniNome [0] . '").style.backgroundColor="#feff98";
				document.getElementById("' . $condizioniNome [0] . '").focus();
				return;					
			} 
		';
		return $javascript;
	}
	
	function onBodyLoad($js) {
		
		$document = & JFactory::getDocument ();
		$document->addScript ( JURI::base () . 'plugins/system/djflibraries/assets/script/tw-sack.js' );
		$js = '
  			window.addEvent(\'load\', function() {
    		' . $js . '
  			});
		';
		$document->addScriptDeclaration ( $js );
	}
	
	function getAjaxRebuildField($type, $field, $query, $select_custom = NULL) {
		
		$risultati = utility::getQueryArray ( $query );
		$uscita = "";
		if (sizeof ( $risultati ) > 0) {
			foreach ( $risultati as $risultato ) {
				//echo($type." - ".$field." - ".$query." - ".$select_custom);
				$lists ['valori'] = utility::getSelectExt2 ( $query, $field, $risultato->value, '', $select_custom );
				$uscita = $lists ['valori'];
			}
		}
		echo ($uscita);
	}
	
	function getAjaxCheckSystem($taskControllerUrl, $nomeForm, $nomeField, $nomeDivDest, $nomeFieldDest) {
		// deprecated
		$document = & JFactory::getDocument ();
		$document->addScript ( JURI::root () . 'plugins/system/djflibraries/assets/script/tw-sack.js' );
		
		$returnScript = '
			function reloadValues(){
				avvia_ajax(document.' . $nomeForm . '.' . $nomeField . '.value, "0");
			}
			function avvia_ajax(testo, valore){				
				var ajax = new sack();
				ajax.requestFile = "' . $taskControllerUrl . '&field=' . $nomeFieldDest . '&value="+testo;
				ajax.method = "POST";
				ajax.element = "' . $nomeDivDest . '";
				ajax.onLoaded = showContent;
				ajax.onLoading = showWaitMessage;
				ajax.runAJAX();				
			}
			function showWaitMessage(){			
				document.getElementById("my-pic-' . $nomeField . '").src = "' . JURI::root () . 'plugins/system/djflibraries/assets/script/loader.gif";
				document.getElementById("my-pic-' . $nomeField . '").style.visibility="visible";
				document.getElementById("my-pic-' . $nomeField . '").style.width="20px";
			}
			function showContent(){			
				document.getElementById("my-pic-' . $nomeField . '").style.visibility="hidden";				
			}';
		
		return $returnScript;
	
	}
	
	function getAjaxCheck($taskControllerUrl, $nomeForm, $nomeField, $nomeDivDest, $nomeFieldDest) {
		
		$document = & JFactory::getDocument ();
		$document->addScript ( JURI::root () . 'plugins/system/djflibraries/assets/script/tw-sack.js' );
		
		$returnScript = '
			function reloadValues_' . $nomeField . '(){
				avvia_ajax_' . $nomeField . '(document.' . $nomeForm . '.' . $nomeField . '.value, "0");
			}
			function avvia_ajax_' . $nomeField . '(testo, valore){				
				var ajax = new sack();
				ajax.requestFile = "' . $taskControllerUrl . '&field=' . $nomeFieldDest . '&value="+testo;
				ajax.method = "POST";
				ajax.element = "' . $nomeDivDest . '";
				ajax.onLoaded = showContent_' . $nomeField . ';
				ajax.onLoading = showWaitMessage_' . $nomeField . ';
				ajax.runAJAX();				
			}
			function showWaitMessage_' . $nomeField . '(){			
				document.getElementById("my-pic-' . $nomeField . '").src = "' . JURI::root () . 'plugins/system/djflibraries/assets/script/loader.gif";
				document.getElementById("my-pic-' . $nomeField . '").style.visibility="visible";
				document.getElementById("my-pic-' . $nomeField . '").style.width="20px";
			}
			function showContent_' . $nomeField . '(){		
				document.getElementById("my-pic-' . $nomeField . '").style.visibility="hidden";		
					
			}';
		
		return $returnScript;
	
	}
	
	function deleteFileAndDirectory($nomefile) {
		//echo($nomefile);
		

		//exit();
		

		jimport ( 'joomla.filesystem.file' );
		if (JFile::exists ( $nomefile )) {
			JFile::delete ( $nomefile );
		}
		
	/*if (sizeof(JFolder::listFolderTree($nomepath))==0)
			JFolder::delete($nomepath);
		
			*/
	
	}
	
	function right($value, $count) {
		
		if (! empty ( $value ))
			return substr ( $value, ($count * - 1) );
		else
			return "";
	
	}
	function right_truncate($value, $count) {
		
		if (! empty ( $value ))
			return substr ( $value, 0, strlen ( $value ) - ($count) );
		else
			return "";
	
	}
	function left($string, $count) {
		
		if (! empty ( $string ))
			return substr ( $string, 0, $count );
		else
			return "";
	
	}
	function left_truncate($string, $count) {
		
		if (! empty ( $string )) {
			return substr ( $string, $count, strlen ( $string ) );
		} else
			return "";
	
	}
	
	function leftTokenize($stringaTutta, $token) {
		if (! empty ( $stringaTutta )) {
			$arrayTok = explode ( $token, $stringaTutta );
			if (! empty ( $arrayTok ))
				return $arrayTok [0];
			else
				return $stringaTutta;
		} else
			return "";
	}
	
	function rightTokenize($stringaTutta, $token) {
		if (! empty ( $stringaTutta )) {
			$arrayTok = explode ( $token, $stringaTutta );
			if (! empty ( $arrayTok ))
				return $arrayTok [sizeOf ( $arrayTok ) - 1];
			else
				return $stringaTutta;
		} else
			return "";
	
	}
	
	function checkLegalExtensions($daTestare, $listaConsenstite = array('jpg','png','gif')) {
		foreach ( $listaConsenstite as $questaEstensione ) {
			if (strtolower ( $daTestare ) == strtolower ( $questaEstensione ))
				return true;
		}
		return false;
	
	}
	
	function saveFile($maxsize, $dest_thumb, $dest, $thumbwidth = 150, $thumb = 'si', $extensions = array('jpg','png','gif')) {
		
		$thumbwidth1 = explode ( ',', $thumbwidth );
		
		$file = JRequest::getVar ( 'upload', null, 'files', 'array' );
		jimport ( 'joomla.filesystem.file' );
		
		if ($_FILES ['upload'] ['size'] > $maxsize) {
			$msg = JTEXT::_ ( "IMAGE_NOT_CHARGE_MAXSIZE" ) . $maxsize;
			return $msg;
		}
		
		$filename = JFile::makeSafe ( $file ['name'] );
		$src = $file ['tmp_name'];
		
		$dest = $dest . DS . $filename;
		
		if (utility::checkLegalExtensions ( strtolower ( JFile::getExt ( $filename ) ), $extensions )) {
			if (JFile::upload ( $src, $dest )) {
				JFolder::create ( $dest_thumb );
				JFolder::create ( $dest_thumb . DS . 'micro' );
				if ($thumb == 'si' && strtolower ( JFile::getExt ( $filename ) ) != 'png') {
					
					$image = new SimpleImage ();
					$image->load ( $dest );
					$height = $image->getHeight ();
					$width = $image->getWidth ();
					
					$y = $thumbwidth1 [0] * $height / $width;
					
					$image->resize ( $thumbwidth1 [0], $y );
					$image->save ( $dest_thumb . DS . $filename );
					
					if (sizeOf ( $thumbwidth1 ) > 1) {
						$y = $thumbwidth1 [1] * $height / $width;
						
						$image->resize ( $thumbwidth1 [1], $y );
						$image->save ( $dest_thumb . DS . 'micro' . DS . $filename );
					}
				
				} else {
					if ($thumb != "no") {
						JFile::copy ( $dest, $dest_thumb . DS . $filename );
						if (sizeOf ( $thumbwidth1 ) > 1) {
							JFile::copy ( $dest_thumb . DS . 'micro' . DS . $filename );
						}
					}
				
				}
				return "";
			} else {
				$msg = JTEXT::_ ( "IMAGE_NOT_CHARGE" );
				return $msg;
			}
		} else {
			$msg = JTEXT::_ ( "IMAGE_NOT_CHARGE" );
			return $msg;
		}
	}
	
	public function getGroupIdQueryExtension() {
		global $mainframe;
		$db = & JFactory::getDBO ();
		$app = & JFactory::getApplication ();
		$option = JRequest::getCMD ( 'option' );
		$applicationName = $app->getName ();
		$user = Jfactory::getUSER ();
		$uid = $user->id;
		$gid = $user->gid;
		
		$id = JRequest::getVar ( 'id' );
		
		$arid = explode ( ":", $id );
		$id = $arid [0];
		
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		$catid = JRequest::getVar ( 'catid' );
		
		$queryGid = "
		select 
		idgroup 
		from #__djfacl_gruppi_utenti 
		where typology = 'djfacl' and  
		iduser = " . $uid;
		$db->setQuery ( $queryGid );
		
		$arrayGid = $db->loadObjectList ();
		
		//echo ("<br>getGroupIdQueryExtension -> " . $allgidquery);
		return $arrayGid;
	}
	
	public function getTaskListValue($task, $option) {
		
		$listaGroup = utility::getGroupIdQueryExtension ();
		$gruppistringa = "";
		foreach ( $listaGroup as $questoGroup ) {
			$gruppistringa .= " or jc.id_group = " . $questoGroup->idgroup;
		}
		
		//exit();
		

		global $mainframe;
		$db = & JFactory::getDBO ();
		$app = & JFactory::getApplication ();
		$option = JRequest::getCMD ( 'option' );
		$applicationName = $app->getName ();
		$user = Jfactory::getUSER ();
		$uid = $user->id;
		$gid = $user->gid;
		
		$id = JRequest::getVar ( 'id' );
		
		$arid = explode ( ":", $id );
		$id = $arid [0];
		
		$queryGid = "
		
		select
			distinct jt.jtask as task
			from #__djfacl_contenuti jc,
			#__djfacl_components c,
			#__djfacl_jtask jt
			where
			jc.id_components = c.id and jt.id = jc.jtask  
 			and (jc.jtask in (select id from #__djfacl_jtask where name = '" . $task . "'))
 			and (jc.site_admin =0)
 			and (c.`option` = '" . $option . "')
 			and (false $gruppistringa)";
		
		//echo($queryGid);
		

		return utility::getQueryArray ( $queryGid );
	
	}
	
	public function getMappa($x = '43.66278', $y = '10.637319', $width = '640', $height = '480', $descrizione = 'descrizione', $googleKey = 'ABQIAAAAVVR7YzWsyI8ZXltTqXou7RRVB8em1tDik6w-Vjb3kxIFv5IyNhTA0a6CpQHqSRfhJqphUNH4lfi_3w') 

	{
		
		echo ('
		   	<script type="text/javascript" src="http://www.google.com/jsapi?key=' . $googleKey . '"></script>
			<script type="text/javascript">
			      google.load("maps", "2");

			      function initialize() {
		    	    var map = new google.maps.Map2(document.getElementById("map"));
		        	map.setMapType(G_NORMAL_MAP);
					var control = new GSmallMapControl();
					var tcontrol = new GMapTypeControl();
					map.addControl(control);
					map.addControl(tcontrol);
					map.enableScrollWheelZoom();
					map.enableContinuousZoom();
		        	map.setCenter(new google.maps.LatLng(' . $x . ',' . $y . '), 16);
		        	var point = new GLatLng(' . $x . ', ' . $y . ');
		        	var marker = new GMarker(point);
		        	marker.openInfoWindowHtml("' . $descrizione . '");
		        	map.addOverlay(marker);
				
		      }
		      google.setOnLoadCallback(initialize);
		    </script>

		    <div id="map" style="width:' . $width . 'px; height:' . $height . 'px"></div>');
	
	}

}

class SimpleImage {
	
	var $image;
	var $image_type;
	
	function load($filename) {
		if (! empty ( $filename )) {
			
			$filename = str_replace("https","http",$filename);
			$image_info = getimagesize ( $filename );
			$this->image_type = $image_info [2];
			if ($this->image_type == IMAGETYPE_JPEG) {
				$this->image = imagecreatefromjpeg ( $filename );
			} elseif ($this->image_type == IMAGETYPE_GIF) {
				$this->image = imagecreatefromgif ( $filename );
			} elseif ($this->image_type == IMAGETYPE_PNG) {
				$this->image = imagecreatefrompng ( $filename );
			}
		}
	}
	function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
		if ($image_type == IMAGETYPE_JPEG) {
			imagejpeg ( $this->image, $filename, $compression );
		} elseif ($image_type == IMAGETYPE_GIF) {
			imagegif ( $this->image, $filename );
		} elseif ($image_type == IMAGETYPE_PNG) {
			imagepng ( $this->image, $filename );
		}
		if ($permissions != null) {
			chmod ( $filename, $permissions );
		}
	}
	function output($image_type = IMAGETYPE_JPEG) {
		if ($image_type == IMAGETYPE_JPEG) {
			imagejpeg ( $this->image );
		} elseif ($image_type == IMAGETYPE_GIF) {
			imagegif ( $this->image );
		} elseif ($image_type == IMAGETYPE_PNG) {
			imagepng ( $this->image );
		}
	}
	function getWidth() {
		return imagesx ( $this->image );
	}
	function getHeight() {
		return imagesy ( $this->image );
	}
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight ();
		$width = $this->getWidth () * $ratio;
		$this->resize ( $width, $height );
	}
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth ();
		$height = $this->getheight () * $ratio;
		$this->resize ( $width, $height );
	}
	function scale($scale) {
		$width = $this->getWidth () * $scale / 100;
		$height = $this->getheight () * $scale / 100;
		$this->resize ( $width, $height );
	}
	function resize($width, $height) {
		$new_image = imagecreatetruecolor ( $width, $height );
		imagecopyresampled ( $new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth (), $this->getHeight () );
		$this->image = $new_image;
	}

}