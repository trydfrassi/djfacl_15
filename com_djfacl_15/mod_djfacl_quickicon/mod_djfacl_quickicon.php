<?php
/**
 * @version		$Id: mod_quickicon.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
require_once (JPATH_PLUGINS . DS . 'system' . DS . 'djflibraries' . DS . 'utility.php');

if (! defined ( '_#__QUICKICON_MODULE' )) {
	/** ensure that functions are declared only once */
	define ( '_#__QUICKICON_MODULE', 1 );
	
	function quickDjfIconButton($link, $image, $text) {
		global $mainframe;
		$lang = & JFactory::getLanguage ();
		$template = $mainframe->getTemplate ();
		?>
		<div style="float:<?php
		echo ($lang->isRTL ()) ? 'right' : 'left';?>;">
		<div class="icon"><a href="<?php echo $link; ?>">
			<img src="<?php echo ($image); ?>" title="<?php echo ($text); ?>" /> <span><?php echo $text; ?></span></a>
		</div>
		</div>
<?php
	}
	
	?>
<div id="cpanel">
		<?php
	$user = &JFactory::getUser ();
	$allgidquery = utility::getGroupIdQueryExtension ();
	$i = 0;
	$orstring="";
	foreach ( $allgidquery as $questoGruppo ) {
		
		if ($i == 0)
			$orstring .= 'and (idgroup = ' . $questoGruppo->idgroup . " or ";
		if ($i == sizeof ( $allgidquery ) - 1)
			$orstring .= 'idgroup = ' . $questoGruppo->idgroup . ')';
		else
			$orstring .= 'idgroup = ' . $questoGruppo->idgroup . " or ";
	$i++;
	}
	
	if ($orstring == ""){
		$orstring = " and idgroup = ".$user->gid;
		$classicMode = "si";
	}
	else $classicMode = "no";
	
	 $altrowhere  = ', #__djfacl_gruppi_icone as gi where gi.idicon = qi.id ' . $orstring;
	
	$querylistaicone = 'select * from #__djfacl_quickicon as qi'.$altrowhere;
	$lista_icone = utility::getQueryArray ( $querylistaicone );
	//echo($querylistaicone);
	//echo(sizeof($lista_icone));
	if (sizeof ( $lista_icone > 0 ) ) {
		foreach ( $lista_icone as $questicona ) {
			$link = $questicona->target;
			$src = utility::getBaseUrl () . $questicona->icon;
			quickDjfIconButton ( $link, $src, JText::_ ( $questicona->text ) );
		}
	
	}
	
	
	function quickiconButton( $link, $image, $text )
	{
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
		?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image.site',  $image, '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
		<?php
	}
	
	if ($classicMode=="si"){
	?>
	</div>
	
	<div id="cpanel">
		<?php
		$query = 'select * from #__djfacl_quickicon 
		where 
		target<>"index.php?option=com_content&task=add"
		and target<>"index.php?option=com_content"
		and target<>"index.php?option=com_frontpage"
		and target<>"index.php?option=com_sections&scope=content"
		and target<>"index.php?option=com_categories&section=com_content"
		and target<>"index.php?option=com_media"
		and target<>"index.php?option=com_menus"
		and target<>"index.php?option=com_languages&client=0"
		and target<>"index.php?option=com_users"
		and target<>"index.php?option=com_config"
		
		
		group by target';
		//echo($query);
		//exit();
		$iconetutte = utility::getQueryArray($query);
		foreach ($iconetutte as $questiconatutte){
			$link = $questiconatutte->target;
			$src = utility::getBaseUrl () . $questiconatutte->icon;
			quickDjfIconButton ( $link, $src, JText::_ ( $questiconatutte->text ) );
		}
		
		
		
		$link = 'index.php?option=com_content&amp;task=add';
		quickiconButton( $link, 'icon-48-article-add.png', JText::_( 'Add New Article' ) );

		$link = 'index.php?option=com_content';
		quickiconButton( $link, 'icon-48-article.png', JText::_( 'Article Manager' ) );

		$link = 'index.php?option=com_frontpage';
		quickiconButton( $link, 'icon-48-frontpage.png', JText::_( 'Frontpage Manager' ) );

		$link = 'index.php?option=com_sections&amp;scope=content';
		quickiconButton( $link, 'icon-48-section.png', JText::_( 'Section Manager' ) );

		$link = 'index.php?option=com_categories&amp;section=com_content';
		quickiconButton( $link, 'icon-48-category.png', JText::_( 'Category Manager' ) );

		$link = 'index.php?option=com_media';
		quickiconButton( $link, 'icon-48-media.png', JText::_( 'Media Manager' ) );

		// Get the current JUser object
		$user = &JFactory::getUser();

		if ( $user->get('gid') > 23 ) {
			$link = 'index.php?option=com_menus';
			quickiconButton( $link, 'icon-48-menumgr.png', JText::_( 'Menu Manager' ) );
		}

		if ( $user->get('gid') > 24 ) {
			$link = 'index.php?option=com_languages&amp;client=0';
			quickiconButton( $link, 'icon-48-language.png', JText::_( 'Language Manager' ) );
		}

		if ( $user->get('gid') > 23 ) {
			$link = 'index.php?option=com_users';
			quickiconButton( $link, 'icon-48-user.png', JText::_( 'User Manager' ) );
		}

		if ( $user->get('gid') > 24 ) {
			$link = 'index.php?option=com_config';
			quickiconButton( $link, 'icon-48-config.png', JText::_( 'Global Configuration' ) );
		}
		?>
	</div>
	
	
<?php
	}
}