<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.installer.installer' );

require_once (JPATH_PLUGINS . DS . 'system' . DS . 'djflibraries' . DS . 'utility.php');
require_once (JPATH_PLUGINS . DS . 'system' . DS . 'djflibraries' . DS . 'multimedia.php');

/**
 * Example system plugin
 */
class plgSystemDjfAcl extends JPlugin {
	//rrr
	var $globArrMultiGroups;
	/**
	 * Constructor
	 */
	public function plgSystemDjfAcl(&$subject, $config) {
		parent::__construct ( $subject, $config );
		$document = & JFactory::getDocument ();
		$document->addStyleSheet ( JURI::base () . 'plugins/content/djfappend.css', 'text/css', null, array () );
		$document->addScript ( JURI::root () . 'plugins/system/djflibraries/assets/players/flowplayer/flowplayer-3.2.6.min.js' );
	
	}
	
	function onPrepareContent() {
		
	/*
		$plugin = JPluginHelper::importPlugin('attachments');

		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('onPrepareContent',null);

		$dispatcher = & JDispatcher::getInstance ();
		$dispatcher->register ( 'onPrepareContent', 'addAttachments' );

		$document = &JFactory::getDocument();
		//echo($document->render());

		$dispatcher->trigger('onPrepareContet', null, false);
    	foreach($dispatcher->_observers as $observer){
			echo("<br>".$observer->_name." = ".$dispatcher->attach($observer)."<br>");
			}
						ob_start();

		$dispatcher = null;
		$dispatcher->register ('onPrepareContent', 'addAttachments');
	*/
	}
	
	function blockHider($toSearch) {
		$output = JResponse::getBody ();
		//$output = str_replace ( $toSearch, $toSearch . "\" style=\"display:none;", $output );
		$cssToHide = '<style type="text/css">.' . $toSearch . '{display:none;}</style>';
		$cssToHide .= '<style type="text/css">#' . $toSearch . '{display:none;}</style>';
		$output = str_replace ( "</body>", $cssToHide . "</body>", $output );
		$output = str_replace ( "<img alt=\"Nuovo\" ", "<img alt=\"Nuovo\" style=\"display:none\"", $output );
		
		JResponse::setBody ( $output );
	}
	
	function unblockHider($toSearch) {
		$output = JResponse::getBody ();
		$toSearch2 = trim ( $toSearch );
		//$output = str_replace ( $toSearch . "\" style=\"display:none;", $toSearch, $output );
		//echo("$toSearch\r\n");
		$toSearch = '<style type="text/css">.' . $toSearch2 . '{display:none;}</style>';
		$toSearch .= '<style type="text/css">#' . $toSearch2 . '{display:none;}</style>';
		//echo("$toSearch\r\n");
		$output = str_replace ( $toSearch, '', $output );
		
		JResponse::setBody ( $output );
	}
	
	public function blockAllBlock($debug = false) {
		
		$esito = false;
		
		global $mainframe;
		$db = & JFactory::getDBO ();
		$app = & JFactory::getApplication ();
		$option = JRequest::getCMD ( 'option' );
		$applicationName = $app->getName ();
		$user = Jfactory::getUSER ();
		$uid = $user->id;
		$articleQuery = "";
		$gid = $user->gid;
		$id = JRequest::getVar ( 'id' );
		if ($id == '' || $id == null)
			$id = '0';
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		$catid = JRequest::getVar ( 'catid' );
		$sectionid = JRequest::getVar ( 'sectionid' );
		
		$layout = JRequest::getVar ( 'layout' );
		
		$sectionQuery = "";
		$categoryQuery = "";
		
		$arid = explode ( ":", $id );
		$id = $arid [0];
		
		if ($view == "section") {
			$sectionQuery = " and (jc.id_section = 999999 or jc.id_section = " . $id . ") ";
		} elseif ($view == "category") {
			$categoryQuery = " and (jc.id_category = 999999 or jc.id_category = " . $id . ") ";
			$secQuery = "select section from #__categories where id = " . $id;
			$db->setQuery ( $secQuery );
			$arrayCategory = $db->loadObjectList ();
			$sectionid = "";
			foreach ( $arrayCategory as $questoGid ) {
				$sectionid = $questoGid->section;
			}
			$categoryQuery = $categoryQuery . " or jc.id_section = " . $sectionid . ")";
		
		} elseif ($view == "article" && ! empty ( $id )) {
			$arid = explode ( ":", $id );
			$id = $arid [0];
			
			$artQuery = "select id, sectionid, catid from #__content where id = " . $id;
			$db->setQuery ( $artQuery );
			$arrayArt = $db->loadObjectList ();
			$artid = "";
			if (sizeOf ( $arrayArt ) > 0)
				foreach ( $arrayArt as $questoGid ) {
					$sectionid = $questoGid->sectionid;
					$catid = $questoGid->catid;
				}
			
			$articleQuery = " and (jc.id_article = $id or jc.id_category = 999999 or jc.id_section = 999999 or jc.id_category = $catid or jc.id_section = $sectionid) ";
			if ($sectionid == "0")
				$articleQuery = " and (jc.id_article = $id or jc.id_category = 999999 or jc.id_section = 999999 ) ";
		
		}
		
		$orGroupQuery = plgSystemDjfAcl::getGroupIdQueryExtension ();
		
		$queryGid = "select css_block from #__djfacl_cssblock";
		$db->setQuery ( $queryGid );
		
		$arrayGid = $db->loadObjectList ();
		$allgidquery = "";
		if (sizeof ( $arrayGid ) > 0)
			foreach ( $arrayGid as $questoGid ) {
				if ($applicationName == "site") {
					plgSystemDjfAcl::blockHider ( $questoGid->css_block );
				} 
				/*else if ($applicationName == "administrator" && $gid != "25") {
					plgSystemDjfAcl::blockHider ( $questoGid->css_block );
				}*/
			}
		
		$queryyGid = "";
		
		$css_block_frontend = $this->params->get ( 'css_block_frontend' );
		
		if ($debug)
			echo ("<h1>$css_block_frontend</h1>");
		
		if ($view == "article" && $applicationName == "site") {
			
			$queryyGid = "select a.css_block as css_block from #__djfacl_cssblock a where 0 < (
						select count(*)
						from #__djfacl_contenuti jc,  #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')
			and (c.`option` = '" . $option . "' ) and
						(jc.css_block = a.id or jc.css_block = '999999') and
						jc.site_admin = 1 and

						(jc.id_users=$uid $orGroupQuery) $articleQuery $sectionQuery $categoryQuery )";
		} elseif ($view == "category" && $applicationName == "site") {
			$queryyGid = "select a.css_block as css_block from #__djfacl_cssblock a where

						 0 < (
						select count(*)
						from #__djfacl_contenuti jc,  #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')
			and (c.`option` = '" . $option . "') and
						(jc.css_block = a.id or jc.css_block = '999999') and
						jc.site_admin = 1 and
						(jc.id_users=$uid $orGroupQuery)   $categoryQuery ";
		
		} elseif ($view == "section" && $applicationName == "site") {
			$queryyGid = "select a.css_block as css_block from #__djfacl_cssblock a where

						 0 < (
						select count(*)
						from #__djfacl_contenuti jc,  #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')
			and (c.`option` = '" . $option . "') and
						(jc.css_block = a.id or jc.css_block = '999999') and
						jc.site_admin = 1 and
						(jc.id_users=$uid $orGroupQuery)  $sectionQuery )";
		} elseif ($view == "field" && $option == "com_djfacl" && $applicationName == "site") {
			$params = &$mainframe->getParams ();
			
			if (empty ( $catid ))
				$catid = $params->get ( 'catid' );
			if (empty ( $sectionid )) {
				if (! empty ( $catid )) {
					$sectionid = utility::getField ( 'select section as value from #__categories where id = ' . $catid );
				}
			}
			
			//echo("<br>catid = ".$catid);
			//echo("sectionid = ".$sectionid."<br>");
			$queryyGid = "select a.css_block as css_block from #__djfacl_cssblock a where 0 < (
						select count(*)
						from #__djfacl_contenuti jc,  #__djfacl_components c
						where (jc.id_components = c.id or jc.id_components = '999999')
						and (c.`option` = '" . $option . "') and
						(jc.css_block = a.id or jc.css_block = '999999') and
						(jc.id_users=$uid $orGroupQuery) and
						jc.site_admin = 1 and
						(false or jc.id_category = $catid or jc.id_category = 999999
						 or jc.id_section = $sectionid or jc.id_section = 999999)
						)";
		
		} // aggiunto else dalla 1.3.6
elseif ($css_block_frontend == 1 && $applicationName == "site") {
			$queryyGid = "select a.css_block as css_block from #__djfacl_cssblock a where 0 < (
						select count(*)
						from #__djfacl_contenuti jc,  #__djfacl_components c
						where (jc.id_components = c.id or jc.id_components = '999999')
						and (c.`option` = '" . $option . "') and
						(jc.css_block = a.id or jc.css_block = '999999') and
						(jc.id_users=$uid $orGroupQuery) and
						jc.site_admin = 1 )";
			
			
		}
		// aggiunto per gestire la scomparsa dei css anche sul backend
		/*else if ($applicationName=="administrator"){
			$queryyGid = "select a.css_block as css_block from #__djfacl_cssblock a where 0 < (
						select count(*)
						from #__djfacl_contenuti jc,  #__djfacl_components c
						where (jc.id_components = c.id or jc.id_components = '999999')
						 and
						(jc.css_block = a.id or jc.css_block = '999999') and
						(jc.id_users=$uid $orGroupQuery) and
						jc.site_admin = 0 )";
		}*/
		
		if ($debug)
				echo ("<br><br><p style=\"color:black\">$queryyGid</p><br><br>");
		
		if ($debug)
			echo ("<br>-----------------------------------------------------------------------------------------><br>" . $queryyGid . "-----------------------------------------------------------------------------------------><br>");
		
			if ($debug) echo("sono qui = ".$queryyGid);
			
		if (! empty ( $queryyGid )) {
			$arrayyGid = utility::getQueryArray ( $queryyGid );
			if (! empty ( $arrayyGid ))
				foreach ( $arrayyGid as $questoyGid ) {
					//echo("<br><br>---".$questoyGid->css_block);
					if ($applicationName == "site") {
						
						plgSystemDjfAcl::unblockHider ( $questoyGid->css_block );
					
		//plgSystemDjfAcl::unblockIdHider ( $questoyGid->css_block );
					} 
					
					/*else if ($applicationName == "administrator" && $gid != "25") {
						
						plgSystemDjfAcl::unblockHider ( $questoGid->css_block );
					}*/
				}
		
		}
		// aggiunto da david frassi il 13.5.2011
		if ($view == "article") {
			$ce = utility::getField ( "select id as value from #__djfacl_contenuti where id_article = " . $id );
			if (! empty ( $ce )) {
				$querycss = "select a.css_block as css_block from #__djfacl_cssblock a";
				$arraycss = utility::getQueryArray ( $querycss );
				if (! empty ( $arraycss )) {
					foreach ( $arraycss as $questocss ) {
						if ($applicationName == "site") {
							plgSystemDjfAcl::unblockHider ( $questocss->css_block );
						} else if ($applicationName == "administrator" && $gid != "25") {
						plgSystemDjfAcl::unblockHider ( $questoGid->css_block );
					}
					
					}
				}
			
			}
		
		}
	
		//exit();
	

	}
	
	function onAfterRender() {
		global $mainframe;
		
		if (! plgSystemDjfAcl::check () == true) {
			
			plgSystemDjfAcl::blockAllBlock ( false );
		}
		
		//plgSystemDjfAcl::blockHider('at_edit');
		//plgSystemDjfAcl::blockHider('addattach');
		//plgSystemDjfAcl::blockHider('Nuovo');
		//plgSystemDjfAcl::blockHider('mceIcon mce_fullscreen');
		

		/*$output = JResponse::getBody ();
		//$output = str_replace ( "attachmentsContainer" , "attachmentsContainer\"; style=\"display:none;", $output );
		//$output = str_replace ( "add_attachment" , "add_attachment\"; style=\"display:none;", $output );

		JResponse::setBody ( $output );*/
		
		$app = JFactory::getApplication ();
		if ($app->isAdmin ()) {
			return;
		}
		
		/*	$dispatcher = JDispatcher::getInstance();
    	$article = new stdClass();
    	$body = JResponse::getBody();
   		$article->text = $body;
    	$params = array();
    	//JPluginHelper::importPlugin('attachments_for_content');
    	$plugin =& JPluginHelper::getPlugin( 'content', 'attachments_for_content' );

		//$view->assignRef('attachPlugins', $attachPlugins);

    	$dispatcher->trigger('onPrepareContet', null, false);
    	foreach($dispatcher->_observers as $observer){
			echo("<br>".$observer->_name." = ".$dispatcher->attach($observer)."<br>");
			}
    	//$article->text = JHTML::_('content.prepare', 'attachments');
    	//JResponse::setBody($article->text);
	  	*/
		
		$category_check = $this->params->get ( 'category_check' );
		if ($category_check == "1") {
			$output = JResponse::getBody ();
			
			$output = str_replace ( "new.png\"", "#\" style=\"display:none;\"", $output );
			//echo($output);
			JResponse::setBody ( $output );
		}
		
		$back_link = $this->params->get ( 'back_link' );
		if ($back_link == "1") {
			$output = JResponse::getBody ();
			$base_url = 'components/com_djfacl/assets/images/back-arrow.png';
			
			if (JRequest::getVar ( 'return' ) != "") {
				$link = "<a  href=\"" . base64_decode ( JRequest::getVar ( 'return' ) ) . "\" title=\"back\" >";
			} else {
				$link = "<a  href=\"javascript:history.back();\" title=\"Back\" >";
			}
			$output = str_replace ( "breadcrumbs pathway\">", "breadcrumbs pathway\">" . $link . "<img style='margin-right:4px;margin-bottom:-2px;' src=\"" . $base_url . "\"/></a>   ", $output );
			//echo($output);
			JResponse::setBody ( $output );
		}
		
		return true;
	}
	
	public function checkBlock($debug = false) {
		
		$esito = false;
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
		
		if ($view == "section") {
			$querySpecifica = "select * from #__djfacl_contenuti jc
			where (jc.id_section = 999999 or jc.id_section = " . $id . ")" . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkBlock -> " . $querySpecifica);
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		} else
			$esito = true;
		
		if ($debug)
			echo ("<br>checkBlock -> esito =  " . $esito);
		return $esito;
	
	}
	
	public function checkSection($debug = false, $idSection = '') {
		
		$esito = false;
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
		
		if (empty ( $id ))
			$id = 0;
		
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		$catid = JRequest::getVar ( 'catid' );
		$sectionid = JRequest::getVar ( 'sectionid' );
		
		if ($idSection != "")
			$id = $idSection;
		else
			$id = $sectionid;
		
		if ($applicationName == "administrator" && $id == - 1)
			$id = 0;
		
		if (($view == "section" || ! empty ( $idSection )) && $applicationName == "site") {
			$querySpecifica = "select * from #__djfacl_contenuti jc
			where (jc.id_section = 999999 or jc.id_section = " . $id . ")" . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkSection -> " . $querySpecifica);
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		} else if (! empty ( $id ) && $applicationName == "administrator" && $task != "cancel") {
			$querySpecifica = "select * from #__djfacl_contenuti jc
			where (jc.id_section = 999999 or jc.id_section = " . $id . ")" . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkSection -> " . $querySpecifica);
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		}
		$esito = true;
		
		if ($debug)
			echo ("<br>checkSection -> esito =  " . $esito);
		return $esito;
	
	}
	
	public function checkCategoryFromDjfContent($debug = false, $idCategory = '') {
		
		$esito = false;
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
		if ($id == null || $id == "")
			$id = 0;
		
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		$catid = JRequest::getVar ( 'catid' );
		
		if ($idCategory != "")
			$id = $idCategory;
		
		if ($view == "category" || $idCategory != "") {
			$sectionQuery = "select section from #__categories where id = " . $id;
			$db->setQuery ( $sectionQuery );
			$arrayCategory = $db->loadObjectList ();
			$sectionid = "";
			
			foreach ( $arrayCategory as $questoGid ) {
				$sectionid = $questoGid->section;
			}
			if ($sectionid != "")
				$arraySection = " or jc.id_section = 999999 or jc.id_section = " . $sectionid;
			
			$querySpecifica = "
			select
			*
			from #__djfacl_contenuti jc, #__djfacl_components compo
			where (jc.id_category = 999999 or jc.id_category = " . $id . $arraySection . ") " . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ") and
			(jc.id_components = 999999 or (compo.id=jc.id_components and compo.`option`='$option'))";
			if ($debug)
				echo ("<br>checkCategory -> " . $querySpecifica);
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		} else
			$esito = true;
		
		if ($debug)
			echo ("<br>checkCategory -> esito =  " . $esito);
		return $esito;
	}
	
	public function checkCategory($debug = false, $idCategory = '') {
		$esito = false;
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
		
		if (empty ( $id ))
			$id = 0;
		
		$view = JRequest::getVar ( 'view' );
		
		$task = JRequest::getCmd ( 'task' );
		$catid = JRequest::getVar ( 'catid' );
		
		if (! empty ( $idCategory ))
			$id = $idCategory;
		else if (! empty ( $catid ))
			$id = $catid;
		
		if ($applicationName == "administrator" && $id == - 1)
			$id = 0;
		
		if (($view == "category" || $idCategory != "") && ($task != "edit" && $task != "save") && $applicationName == "site") {
			
			// aggiunto per gestire l'eccezione sugli url seo-sef nei link ad articolo provenienti da home
			if ($task == "edit" && $view == "category") {
				$view = "article";
			}
			
			$sectionQuery = "select section from #__categories where id = " . $id;
			if ($debug)
				echo ("selectionQuery = " . $sectionQuery);
			$db->setQuery ( $sectionQuery );
			$arrayCategory = $db->loadObjectList ();
			$sectionid = "";
			if (! empty ( $arrayCategory ))
				foreach ( $arrayCategory as $questoGid ) {
					$sectionid = $questoGid->section;
				}
			
			if ($sectionid != "" && is_numeric ( $sectionid ))
				$arraySection = " or jc.id_section = 999999 or jc.id_section = " . $sectionid;
			else
				$arraySection = "";
			$querySpecifica = "
			select
			*
			from #__djfacl_contenuti jc, #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')
			and (c.`option` = '" . $option . "')
			and  (jc.id_category = 999999 or jc.id_category = " . $id . $arraySection . ") " . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkCategory -> " . $querySpecifica);
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		} else 

		if ($applicationName == "administrator" && ! empty ( $id ) && $task != "cancel") {
			$sectionQuery = "select section from #__categories where id = " . $id;
			if ($debug)
				echo ($sectionQuery);
			$db->setQuery ( $sectionQuery );
			$arrayCategory = $db->loadObjectList ();
			$sectionid = "";
			foreach ( $arrayCategory as $questoGid ) {
				$sectionid = $questoGid->section;
			}
			
			if ($sectionid != "" && is_numeric ( $sectionid ))
				$arraySection = " or jc.id_section = 999999 or jc.id_section = " . $sectionid;
			else
				$arraySection = "";
			$querySpecifica = "
			select
			*
			from #__djfacl_contenuti jc, #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')
			and (c.`option` = '" . $option . "')
			and  (jc.id_category = 999999 or jc.id_category = " . $id . $arraySection . ") " . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkCategory -> " . $querySpecifica);
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		} else
			$esito = true;
		
		if ($debug)
			echo ("<br>checkCategory -> esito =  " . $esito);
		return $esito;
	}
	
	public function checkArticle($debug = false) {
		$esito = false;
		global $mainframe;
		$db = & JFactory::getDBO ();
		$app = & JFactory::getApplication ();
		$option = JRequest::getCMD ( 'option' );
		$applicationName = $app->getName ();
		$user = Jfactory::getUSER ();
		$uid = $user->id;
		$gid = $user->gid;
		$id = JRequest::getVar ( 'id' );
		if (empty ( $id )) {
			$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
			$id = $cid [0];
			if (empty ( $id ))
				$id = '0';
		}
		$arid = explode ( ":", $id );
		$id = $arid [0];
		
		$view = JRequest::getVar ( 'view' );
		$task = JRequest::getCmd ( 'task' );
		
		$catid = JRequest::getVar ( 'catid' );
		$arraId = explode ( ":", $id );
		$id = $arraId [0];
		
		// aggiunto per gestire l'eccezione sugli url seo-sef nei link ad articolo provenienti da home
		if (($task == "edit" || $task == "save") && $option=="com_content" && $view == "category" && $applicationName == "site") {
			$view = "article";
			
			$querySpecifica = "

			select
			*
			from #__djfacl_contenuti jc, #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '0')  and (c.`option` = '" . $option . "')
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkArticle -> " . $querySpecifica);
			
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		
		//echo("caso strano");
		

		} else 

		if (($task == "edit" || $task == "save") && $option == "com_content" && $view == "article" && $applicationName == "site") {
			
			$sectionQuery = "
			select
			catid as categoria,
			sectionid as section
			from
			#__content
			where id = " . $id;
			$db->setQuery ( $sectionQuery );
			
			$arrayCategory = $db->loadObjectList ();
			$sectionid = "";
			
			if (! empty ( $arrayCategory ))
				foreach ( $arrayCategory as $questoGid ) {
					$sectionid = $questoGid->section;
					$catid = $questoGid->categoria;
				}
			if ($sectionid != "" && $sectionid != "0")
				$arraySection = "and (jc.id_section = 999999 or jc.id_section = " . $sectionid . "
				or jc.id_category = 999999 or jc.id_category = " . $catid . "
				or jc.id_article = 999999 or jc.id_article = " . $id . ')';
			
			if ($sectionid == "0")
				$arraySection = "and (jc.id_section = 999999
				or jc.id_category = 999999
				or jc.id_article = 999999 or jc.id_article = " . $id . ')';
			
			if (empty ( $arraySection ))
				$arraySection = "";
			$querySpecifica = "

			select
			*
			from #__djfacl_contenuti jc, #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')  " . $arraySection . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (c.`option` = '" . $option . "')
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkArticle -> " . $querySpecifica);
			
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		
		} else 

		if ($applicationName == "administrator" && $option=="com_content" && ! empty ( $id )) {
			$sectionQuery = "
			select
			catid as categoria,
			sectionid as section
			from
			#__content
			where id = " . $id;
			$db->setQuery ( $sectionQuery );
			$arrayCategory = $db->loadObjectList ();
			$sectionid = "";
			if (! empty ( $arrayCategory ))
				foreach ( $arrayCategory as $questoGid ) {
					$sectionid = $questoGid->section;
					$catid = $questoGid->categoria;
				}
			if ($sectionid != "" && $sectionid != "0")
				$arraySection = "and (jc.id_section = 999999 or jc.id_section = " . $sectionid . "
				or jc.id_category = 999999 or jc.id_category = " . $catid . "
				or jc.id_article = 999999 or jc.id_article = " . $id . ')';
			
			if ($sectionid == "0")
				$arraySection = "and (jc.id_section = 999999
				or jc.id_category = 999999
				or jc.id_article = 999999 or jc.id_article = " . $id . ')';
			
			if (empty ( $arraySection ))
				$arraySection = "";
			$querySpecifica = "

			select
			*
			from #__djfacl_contenuti jc, #__djfacl_components c
			where (jc.id_components = c.id or jc.id_components = '999999')  " . $arraySection . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (c.`option` = '" . $option . "')
			and (jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . " or jc.id_users = " . $uid . ")";
			if ($debug)
				echo ("<br>checkArticle -> " . $querySpecifica);
			
		//exit();
			$db->setQuery ( $querySpecifica );
			$arrayRisultati = $db->loadObjectList ();
			if (sizeof ( $arrayRisultati ) > 0)
				$esito = true;
		} else 

		if ($applicationName == "administrator" && $option=="com_content" && empty ( $id ) && empty ( $catid ) && empty ( $sectionid ) && $task == "save") {
			$esito = false;
		} 

		else
			$esito = true;
		
		if ($debug)
			echo ("<br>checkArticle -> esito =  " . $esito);
		return $esito;
	}
	
	public function checkComponents($debug = false, $optionExt = '') {
		$esito = false;
		global $mainframe;
		//$debug = true;
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
		if ($optionExt != "")
			$option = $optionExt;
		
		$querySpecifica = "
			select distinct
			jc.*
			from #__djfacl_contenuti jc, #__djfacl_components c
			where
			(jc.id_components = c.id or jc.id_components = '999999') " . plgSystemDjfAcl::getTaskQueryExtension () . plgSystemDjfAcl::getSiteAdminQueryExtension () . "
			and (c.`option` = '" . $option . "')
			and
			(jc.id_group = 0 " . plgSystemDjfAcl::getGroupIdQueryExtension () . "
			or jc.id_users = " . $uid . ")";
		
		if ($debug)
			echo ("<br>checkComponents -> " . $querySpecifica);
		
		//exit();
		$db->setQuery ( $querySpecifica );
		$arrayRisultati = $db->loadObjectList ();
		
		if (sizeof ( $arrayRisultati ) > 0) {
			$esito = true;
		}
		
		if ($option == "" || $option == "com_login" || $option == "com_user" || $gid == 25)
			$esito = true;
		
		if ($esito == false && $option == "com_cpanel" && $applicationName == "administrator")
			$esito = true;
		
		if (($task == "edit" || $task == "save") && $option == "com_content" && $view == "category" && $applicationName == "site") {
			$view = "article";
		}
		
		if (($task == "edit" || $task == "save") && $option == "com_content" && $view == "article" && $applicationName == "site") {
			$queryPerEditArticolo = "select jc.id as value from #__djfacl_contenuti jc, #__users ju where ju.id = " . $uid . " and jc.id_article = " . $id . " and (ju.gid=jc.id_group " . plgSystemDjfAcl::getGroupIdQueryExtension () . ")";
			if ($debug) {
				echo ($queryPerEditArticolo);
			}
			$ce = utility::getField ( $queryPerEditArticolo );
			
			if (! empty ( $ce ))
				$esito = true;
		
		}
		
		if ($debug)
			echo ("<br>checkComponents -> esito =  " . $esito);
		
		return $esito;
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
	
	public function scanAllParam() {
		global $mainframe;
		$stringona = "";
		$requestArray = JRequest::get ();
		
		$db = & JFactory::getDBO ();
		
		if (plgSystemDjfAcl::check_if_table_exists ( "#__djfacl_jtask" )) {
			
			$db->setQuery ( 'select distinct name from #__djfacl_jtask' );
			
			$nomi = $db->loadObjectList ();
			if (sizeof ( $nomi ) > 0)
				foreach ( $nomi as $questonome ) {
					
					foreach ( $requestArray as $key => $valore ) {
						
						if ($key == $questonome->name)
							
							$stringona .= plgSystemDjfAcl::getTaskQueryExtensionParam ( $questonome->name );
					}
				
				}
			if ($stringona != "")
				$stringona = "and (false " . $stringona . " or jc.jtask='999999')";
		
		//$stringona = "and (false " . $stringona .")";
		

		//echo ("<br>stringona scan all param = " . $stringona);
		}
		return $stringona;
	
	}
	public function getTaskQueryExtension() {
		return plgSystemDjfAcl::scanAllParam ();
	
	}
	public function getTaskQueryExtensionParam($param_name) {
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
		$task = JRequest::getVar ( $param_name );
		$catid = JRequest::getVar ( 'catid' );
		
		//if ($task != "" && $task != null && $gid != 25) {
		if ($gid != 25 && $task != "" && $task != null) {
			$queryTask = " or jc.jtask in (select id from #__djfacl_jtask where jtask = '$task' and name = '$param_name')  ";
		} else
			$queryTask = "";
		
		return $queryTask;
	}
	
	public function getSiteAdminQueryExtension() {
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
		
		if ($applicationName == "site")
			$site_admin_reale = 1;
		else
			$site_admin_reale = 0;
		if ($gid != 25) {
			$queryTask = " and (jc.site_admin =$site_admin_reale) ";
		} else
			$queryTask = "";
		
		return $queryTask;
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
		$allgidquery = "";
		if ($gid != 25 && sizeof ( $arrayGid ) > 0) {
			foreach ( $arrayGid as $questoGid ) {
				$allgidquery .= " or jc.id_group = '$questoGid->idgroup' ";
			}
		
		//echo ("<br>sono nel primo if");
		} else if (sizeof ( $arrayGid ) == 0) {
			$allgidquery .= " or jc.id_group = '$gid'";
		
		//echo ("<br>sono nel secondo if");
		}
		if ($uid == 0) {
			$allgidquery .= " or jc.id_group = '29' ";
		}
		
		//echo ("<br>getGroupIdQueryExtension -> " . $allgidquery);
		return $allgidquery;
	}
	
	/**
	 * Do load rulles and start checking function
	 */
	
	public function onAfterRoute() {
		global $mainframe;
		
		if (plgSystemDjfAcl::check () == true) {
			return true;
		} else {
			
			$mainframe->redirect ( "index.php", JText::_ ( 'ALERTNOTAUTH' ) );
		}
	}
	
	public function check() {
		
		global $mainframe;
		
		$this->scanAllParam ();
		
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
		
		if ($applicationName == "administrator") {
			$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
			$id = $cid [0];
		}
		
		$view = JRequest::getVar ( 'view' );
		//echo("<br>".$view."<br>");
		$task = JRequest::getCmd ( 'task' );
		$catid = JRequest::getVar ( 'catid' );
		$sectionid = JRequest::getVar ( 'sectionid' );
		$acl = & JFactory::getACL ();
		
		$risultato = false;
		
		$debug = false;
		//$debug = true;
		

		plgSystemDjfAcl::blockAllBlock ( $debug );
		
		$cSection = plgSystemDjfAcl::checkSection ( $debug );
		$cCategory = plgSystemDjfAcl::checkCategory ( $debug );
		$cArticle = plgSystemDjfAcl::checkArticle ( $debug );
		$cComponents = plgSystemDjfAcl::checkComponents ( $debug );
		
		$risultato = $cSection && $cCategory && $cArticle && $cComponents;
		
		if (($task == "edit" || $task == "save") && $view == "article" && $applicationName == "site") {
			$queryPerEditArticolo = "select jc.id as value from #__djfacl_contenuti jc, #__users ju where ju.id = " . $uid . " and jc.id_article = " . $id . " and (ju.gid=jc.id_group " . plgSystemDjfAcl::getGroupIdQueryExtension () . ")";
			//echo($queryPerEditArticolo);
			$ce = utility::getField ( $queryPerEditArticolo );
			if (! empty ( $ce ))
				$risultato = true;
		
		//else $risultato=false;
		}
		
		// da qui in poi si ragiona senza regole esplicite sui componenti
		

		if ($gid == 25 || $view == "frontpage" || ($task == "" || $task == null) && ($option == "" || $option == null))
			$risultato = true;
		
		if ($debug) {
			echo ('<h1>');
			echo ('option = ' . $option . '<br>');
			echo ('applicationName = ' . $applicationName . '<br>');
			echo ('gruppo utente = ' . $gid . '<br>');
			echo ('view = ' . $view . '<br>');
			echo ('task = ' . $task . '<br>');
			echo ('catid = ' . $catid . '<br>');
			echo ('sectionid = ' . $sectionid . '<br>');
			echo ('id = ' . $id . '</br>');
			echo ('risultato = ' . $risultato);
			echo ('</h1>');
		}
		
		// controlla se nel caso di aggiunta di un nuovo articolo viene passata anche la categoria
		// altrimenti esce
		

		$category_check = $this->params->get ( 'category_check' );
		
		if ($gid != 25 && ($option == "com_content" || $option == "com_user") && $category_check == 1 && $task == "new" && ($catid == null || $catid == "")) {
			$risultato = false;
		}
		
		/*if ($option == "com_search")
			$risultato = true;
		*/
		/*if ($gid != 25 && 
				$applicationName == "administrator" && 
				$option == "com_content" && 
				empty ($catid) && 
				empty ($sectionid) && 
				!empty ($task)
				)
			$risultato = false;*/
		
		if ($debug) {
			echo ('risultato = ' . $risultato);
		
		}
		
		if ($debug)
			exit ();
		
		return $risultato;
	}

}
