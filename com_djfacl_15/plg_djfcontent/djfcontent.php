<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.installer.installer' );

/**
 * Example system plugin
 */

require_once (JPATH_PLUGINS . DS . 'system' . DS . 'djfacl.php');

class plgContentDjfContent extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentDjfContent(&$subject, $params) {
		
		parent::__construct ( $subject, $params );
		$this->_plugin = JPluginHelper::getPlugin ( 'content', 'djfcontent' );
		$this->_params = new JParameter ( $this->_plugin->params );
		JPlugin::loadLanguage ( 'com_djfacl', JPATH_ADMINISTRATOR );
	
		//JPlugin::loadLanguage('plg_content_jumi', JPATH_ADMINISTRATOR);
	

	}
	
	/**
	 * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onPrepareContent(&$article, &$params, $limitstart) {
		global $mainframe;
		
		return '';
	}
	
	/**
	 * Example after display title method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayTitle(&$article, &$params, $limitstart) {
		global $mainframe;
		
		return '';
	}
	
	/**
	 * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onBeforeDisplayContent(&$article, &$params, $limitstart) {
		global $mainframe;
		
		return '';
	}
	
	/**
	 * Example after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayContent(&$article, &$params, $limitstart) {
		global $mainframe;
		
		return '';
	}
	
	/**
	 * Example before save content method
	 *
	 * Method is called right before content is saved into the database.
	 * Article object is passed by reference, so any changes will be saved!
	 * NOTE:  Returning false will abort the save with an error.
	 * You can set the error by calling $article->setError($message)
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	bool		If false, abort the save
	 */
	function onBeforeContentSave(&$article, $isNew) {
		global $mainframe;
		$app = & JFactory::getApplication ();
		$application = $app->getName ();
		
		if ($application == "administrator")
			return '';
		$db = & JFactory::getDBO ();
		$app = & JFactory::getApplication ();
		$option = JRequest::getCMD ( 'option' );
		$applicationName = $app->getName ();
		$user = Jfactory::getUSER ();
		$uid = $user->id;
		$gid = $user->gid;
		$id = JRequest::getVar ( 'id' );
		$view = JRequest::getVar ( 'view' );
		$frontpage = JRequest::getVar ( 'frontpage' );
		$task = JRequest::getCmd ( 'task' );
		$acl = & JFactory::getACL ();
		
			if (($task == "edit" || $task == "save") && $view == "category" && $applicationName == "site") {
				$view="article";
			}
		
		
		$post = JRequest::get ( 'post' );
		
		if ($id == null || $id == "") {
			$id = 0;
		}
		$query = 'SELECT * from #__content where id =' . $id;
		$db->setQuery ( $query );
		//echo ($query . "<br>");
		$articoli = $db->loadObjectList ();
		
		$nuovo = "si";
		
		foreach ( $articoli as $articolo ) {
			
			$categoria = $articolo->catid;
			$sezione = $articolo->sectionid;
			$title = $articolo->title;
			$nuovo = "no";
		
		}
		
		$sezione = JRequest::getVar ( 'sectionid' );
		$categoria = JRequest::getVar ( 'catid' );
		$title = JRequest::getVar ( 'title' );
		$created = JRequest::getVar ( 'created' );
		if (! isset ( $created )) {
			$created = gmdate ( 'Y-m-d H:i:s' );
		}
		
		$state = JRequest::getVar ( 'state' );
		
		//echo ("nuovo = " . $isNew);
		
		$article->state = $state; // David Frassi
		$article->created = $created;
		$article->catid = $categoria;
		$article->sectionid = $sezione;
		
		echo ('<h1>');
		echo ('isNew = ' . $isNew . '<br>');
		echo ('nuovo = ' . $nuovo . '<br>');
		echo ('frontpage = ' . $frontpage . '<br>');
		echo ('id utente = ' . $uid . '<br>');
		echo ('id articolo = ' . $id . '</br>');
		echo ('title = ' . $title . '</br>');
		echo ('sezione = ' . $sezione . '<br>');
		echo ('categoria = ' . $categoria . '<br>');
		echo ('state = ' . $state . '<br>');
		echo ('created = ' . $created . '<br>');
		echo ('option = ' . $option . '<br>');
		echo ('applicationName = ' . $applicationName . '<br>');
		echo ('gruppo utente = ' . $gid . '<br>');
		echo ('view = ' . $view . '<br>');
		echo ('task = ' . $task . '<br>');
		
		$categoryCheck = plgSystemdjfacl::checkCategoryFromDjfContent ( true, $categoria );
		
		//$check = plgSystemdjfacl::check();
		//echo("articolo = ".$articleCheck);
		

		echo ("<br>categoryCheck = " . $categoryCheck . '<br>');
		$risultato = false;
		if (($task == "edit" || $task == "save") &&$view == "article" && $applicationName == "site") {
			$queryPerEditArticolo = "
			select 
			jc.id as value 
			from 
			#__djfacl_contenuti jc,
			#__content jooc,
			#__users ju 
			where 
			jc.id_article = jooc.id and
			ju.id = " . $uid . " and 
			jc.id_article = " . $id . "	and 
			jooc.catid = ".$categoria. " and 
			jooc.sectionid = ".$sezione. " and 
			(ju.gid=jc.id_group " . plgSystemDjfAcl::getGroupIdQueryExtension () . ")";
			
			
			$ce = utility::getField ( $queryPerEditArticolo );
			if (! empty ( $ce ))
				return true;
		}
		echo ('</h1>');
		
		//exit();
		if ($categoryCheck || $risultato) {
			return true;
		} else {
			
			$Itemid = JRequest::getVar ( 'Itemid' );
			$app = & JFactory::getApplication ();
			$application = $app->getName ();
			$menu = & JSite::getMenu ();
			$ret = JRequest::getVar ( 'ret' );
			$item = $menu->getItem ( $Itemid );
			$link = $item->link;
			$link = $link . '&Itemid=' . $Itemid;
			$id = $article->id;
			
			//$msg = 'Impossibile salvare l\'articolo. Non hai accesso alla categoria selezionata!';
			$msg = JTEXT::_ ( 'MESSAGGIO_IMPOSSIBILE_SALVARE' );
			if ($link == "&Itemid=") {
				$link = base64_decode ( $ret );
			}
			
			$mainframe->redirect ( $link, $msg );
			
			return false;
		}
	
	}
	
	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	void
	 */
	function onAfterContentSave(&$article, $isNew) {
		
		global $mainframe;
		
		$Itemid = JRequest::getVar ( 'Itemid' );
		$app = & JFactory::getApplication ();
		$application = $app->getName ();
		if ($application == "administrator")
			return '';
		$menu = & JSite::getMenu ();
		$item = $menu->getItem ( $Itemid );
		
		if ($item == null || $item == "")
			return '';
		
		$link = $item->link;
		
		$redirect_article = $this->params->get ( 'redirect_article' );
		if ($redirect_article == "1") {
			$link = $link . '&Itemid=' . $Itemid;
		} else {
			$return = JRequest::getVar ( 'ret' );
			$link = base64_decode ( $return );
			echo ("link = " . $link);
		
		//exit();
		}
		
		$titolo = $article->title;
		$id = $article->id;
		$msg = JTEXT::_ ( 'MESSAGGIO_IMPOSSIBILE_SALVARE' );
		$msg = JTEXT::_ ( 'MESSAGGIO_ARTICOLO' ) . ' (' . $id . ' - ' . $titolo . ') ' . JTEXT::_ ( 'MESSAGGIO_SALVATO' );
		
		/*
		 * 
		 * $uri = JURI::getInstance();
		 * $uri->toString()
		 * queste due righe vengono usate se si vuole recuperare l'url attuale
		 *
		 *
		 *
		 */
		echo ($msg);
		echo ("link = " . $link);
		//exit();
		

		$save_frontpage = $this->params->get ( 'save_frontpage' );
		if ($save_frontpage == "1") {
			$msg = JTEXT::_ ( 'NOT_FRONTPAGE' );
			$mainframe->redirect ( $link, $msg );
		}
		
		return true;
	}

}
