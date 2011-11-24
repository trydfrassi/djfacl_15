<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.installer.installer' );

/**
 * Example system plugin
 */
class plgSystemdjfintegrity extends JPlugin {
	//rrr
	var $globArrMultiGroups;
	/**
	 * Constructor
	 */
	public function plgSystemdjfintegrity(&$subject, $config) {
		parent::__construct ( $subject, $config );
	}
	
	/**
	 * Do load rulles and start checking function
	 */
	
	public function checkRemoveUser() {
		global $mainframe;
		
		$db = & JFactory::getDBO ();
		$option = JRequest::getCMD ( 'option' );
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		if ($option == "com_users" && $task == "remove") {
			$risultato = true;
			$cid = JRequest::getVar ( 'cid', array (), '', 'array' );
			JArrayHelper::toInteger ( $cid );
			if (count ( $cid ) < 1) {
				JError::raiseError ( 500, JText::_ ( 'Select a User to delete', true ) );
			}
			foreach ( $cid as $id ) {
				$queryy = 'select * 
						from 
							#__djfacl_gruppi_utenti as dgu, 
							#__users as jc 							
						where dgu.iduser = jc.id and jc.id =  ' . $id;
				//echo ($queryy);
				//exit();
				

				$risultati = utility::getQueryArray ( $queryy );
				if (empty ( $risultati )) {
					$risultato = true;
				} else {
					$risultato = false;
					$MSG = JText::_ ( 'USER_USED' );
					$REDIRECT = "index.php?option=com_users";
					$mainframe->redirect ( $REDIRECT, $MSG );
					exit ();
					break;
				}
			}
		
		}
	
	}
	
	public function checkSaveUser() {
		global $mainframe;
		
		$db = & JFactory::getDBO ();
		$option = JRequest::getCMD ( 'option' );
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		if ($option == "com_users" && $task == "save") {
			$risultato = true;
			$cid = JRequest::getVar ( 'cid', array (), '', 'array' );
			JArrayHelper::toInteger ( $cid );
			if (count ( $cid ) < 1) {
				JError::raiseError ( 500, JText::_ ( 'Select a User to delete', true ) );
			}
			$gid = JRequest::getVar ( 'gid' );
			foreach ( $cid as $id ) {
				
				$queryy = 'update #__djfacl_gruppi_utenti set idgroup = ' . $gid . ' where typology="joomla" and iduser = ' . $id;
				$risultati = utility::executeQuery ( $queryy );
				if ($risultati) {
					$risultato = true;
				} else {
					$risultato = false;
					$MSG = JText::_ ( 'ANTANI' );
					$REDIRECT = "index.php?option=com_users";
					$mainframe->redirect ( $REDIRECT, $MSG );
					break;
				}
			}
		}
	}
	
	public function updateComponentsTable() {
		
		$queryPerUpdate = " insert into #__djfacl_components (id,`option`)
			select distinct null, `option` from #__components as bb where bb.`option` != ''
			and not exists (select aa.* from #__djfacl_components as aa where aa.`option` = bb.`option`)
			order by 2;";
		utility::executeQuery ( $queryPerUpdate );
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_categories' from #__components
			where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_categories');";
		utility::executeQuery ( $queryPerUpdate );
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_sections' from #__components
			where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_sections');";
		utility::executeQuery ( $queryPerUpdate );
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_frontpage' from #__components
			where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_frontpage');";
		utility::executeQuery ( $queryPerUpdate );
	
	}
	
	function showAppendInText($output) {
		global $plugin;
		$baseIconPath = "components/com_djfappend/assets/images/icons/";
		$regex = "#{djfappend}(.*?){/djfappend}#s"; // this the MP3 URL is in matches[2], even when autostart is not set
		preg_match_all ( $regex, $output, $out, PREG_SET_ORDER );
		
		for($i = 0; $i < sizeOf ( $out ); $i ++) {
			$idToFind = $out [$i] [1];
			$toReplace = "{djfappend}" . $idToFind . "{/djfappend}";
			$iconaRender = "";
			$output = str_replace ( $toReplace, $iconaRender, $output );
		}
		return $output;
	}
	
	public function onAfterRender() {
		$output = JResponse::getBody ();
		$view = Jrequest::getVar ( 'view' );
		$task = Jrequest::getVar ( 'task' );
		
		$app = & JFactory::getApplication ();
		$option = JRequest::getCMD ( 'option' );
		$applicationName = $app->getName ();
		
		if ($applicationName == "site"){
			
		if (($task == "edit" || $task == "save") && $view == "category" && $applicationName == "site") {
				$view="article";
			}
			
			
		if (! empty ( $view ) && $view != "article") {
			$output = $this->showAppendInText ( $output );
			JResponse::setBody ( $output );
		}
		if (! empty ( $view ) && $view == "article" && (empty ( $task ) || $task != "edit")) {
			$output = $this->showAppendInText ( $output );
			JResponse::setBody ( $output );
		}
		if (empty ( $view )) {
			
			$output = $this->showAppendInText ( $output );
			JResponse::setBody ( $output );
		}
		}
	}
	
	public function onAfterRoute() {
		global $mainframe;
		$doc = & JFactory::getDocument ();
		$js = ' <script type="text/javascript" src="http://localhost/joomladev/components/com_djfappend/assets/players/flowplayer/flowplayer-3.2.6.min.js"></script>
  				<script type="text/javascript" src="http://localhost/joomladev/plugins/system/djflibraries/assets/players/flowplayer/flowplayer-3.2.6.min.js"></script>';
		//$doc->addScriptDeclaration ( $js );
		$language = & JFactory::getLanguage ();
		$language->load ( 'com_djfacl', JPATH_ADMINISTRATOR );
		
		//plgSystemdjfintegrity::checkSaveUser();
		plgSystemdjfintegrity::checkRemoveUser ();
		plgSystemdjfintegrity::updateComponentsTable ();
		
		$option = JRequest::getVar ( 'option' );
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getVar ( 'task' );
		if ((! empty ( $option ) && $option == "com_trash") && (! empty ( $task ) && $task == "delete")) {
			
			plgSystemdjfintegrity::removeAppendFileFromTrash ();
		}
	}
	
	function removeAppendFileFromTrash(){
		global $mainframe;
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		
		
		foreach ( $cid as $id ) {
			
			$path = getcwd();
			$path = str_replace("\\","/",$path);
			$path = str_replace("administrator","",$path);
			$path.="images/stories/com_djfappend/uploads/".$id;
    		
    		jimport( 'joomla.filesystem.folder' );

    		$queryFromDelete = 'delete from #__djfappend_field where id_jarticle = '.$id;
    		utility::executeQuery ( $queryFromDelete );
    		
    		JFolder::delete($path);
			
		
		}
	}
}