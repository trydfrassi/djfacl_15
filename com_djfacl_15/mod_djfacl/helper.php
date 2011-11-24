<?php
/**
 * @version		2.2
 * @copyright	Copyright (C) 2007-2009 Stephen Brandon
 * @license		GNU/GPL
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class moddjfaclHelper {
	function displayModules() {
		global $mainframe;
		$db = & JFactory::getDBO ();
	
		if (true) {
			
			$user = & JFactory::getUser ();
			$uid = $user->get ( 'id' );
			$ugid = $user->get ( 'gid' );
			
			
			$queryGid = "select idgroup from #__djfacl_gruppi_utenti where iduser = ".$uid;
			$db->setQuery ( $queryGid );
			$arrayGid = $db->loadObjectList ();
			$allgidquery="";
			foreach ($arrayGid as $questoGid){
				$allgidquery.=" or jm.id_group = '$questoGid->idgroup' "; 
			}
			
	
			
			
			$db = & JFactory::getDBO ();
			
			if ($ugid == "25") $andAdmin = "";
			else $andAdmin = " AND (jm.id_users='" . $uid . "' OR jm.id_group='" . $ugid . "' $allgidquery) ";
			
			if ($uid == 0) return "";
				
			$query = "
			
			SELECT m.* 
			FROM #__modules AS m, #__djfacl_contenuti as jm 
			WHERE 
			m.module <> 'mod_djfacl' AND 
			m.id=jm.id_modules ".$andAdmin.
			" and m.position = (select aa.position from #__modules as aa where aa.module = 'mod_djfacl') 
			ORDER BY m.ordering ";
			
			//echo($query);
			
			$db->setQuery ( $query );
			$modules = array ();
			
			if (! ($modules = $db->loadObjectList ())) {
				//JError::raiseWarning ( 'SOME_ERROR_CODE', "Error loading Modules: " . $db->getErrorMsg () );
				//echo ("non va");
				return false; // FIXME - what to do here? Ignore?
			}
			
			$document = &JFactory::getDocument ();
			$renderer = $document->loadRenderer ( 'module' );
			$contents = '';
			
			
			foreach ( $modules as $mod ) {
				if ($mod->published == 1 || $ugid == 25){
					$mod->user=0;
					
					$contents .= '<div><h3>' . $mod->title . '</h3>' . $renderer->render ( $mod ) . "</div><br>";
				}
			}
			
			return $contents;
		
		}
	
	}

}