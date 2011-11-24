<?php
/**
* @version 2.0
* @package DJ Catalog
* @subpackage DJ Catalog Component
* @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Michal Olczyk - michal.olczyk@design-joomla.eu
*
*
* DJ Catalog is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Catalog is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Catalog. If not, see <http://www.gnu.org/licenses/>.
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->templates = array();
$status->joomfish = array();





/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* PLUGIN INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$plugins = &$this->manifest->getElementByPath('plugins');
if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

	foreach ($plugins->children() as $plugin)
	{
		$pname		= $plugin->attributes('plugin');
		$pgroup		= $plugin->attributes('group');
		$porder		= $plugin->attributes('order');

		// Set the installation path
		if (!empty($pname) && !empty($pgroup)) {
			$this->parent->setPath('extension_root', JPATH_ROOT.DS.'plugins'.DS.$pgroup);
		} else {
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No plugin file specified'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// If the plugin directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		* If we created the plugin directory and will want to remove it if we
		* have to roll back the installation, lets add it to the installation
		* step stack
		*/
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		$element = &$plugin->getElementByPath('files');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy all necessary files
		$element = &$plugin->getElementByPath('languages');
		if ($this->parent->parseLanguages($element, 1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy media files
		$element = &$plugin->getElementByPath('media');
		if ($this->parent->parseMedia($element, 1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		$db = &JFactory::getDBO();

		// Check to see if a plugin by the same name is already installed
		$query = 'SELECT `id`' .
		' FROM `#__plugins`' .
		' WHERE folder = '.$db->Quote($pgroup) .
		' AND element = '.$db->Quote($pname);
		$db->setQuery($query);
		if (!$db->Query()) {
			// Install failed, roll back changes
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

		// Was there a plugin already installed with the same name?
		if ($id) {

			if (!$this->parent->getOverwrite())
			{
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Plugin').' "'.$pname.'" '.JText::_('already exists!'));
				return false;
			}

		} else {
			$row =& JTable::getInstance('plugin');
			$row->name = JText::_(ucfirst($pgroup)).' - '.JText::_(ucfirst($pname));
			$row->ordering = $porder;
			$row->folder = $pgroup;
			$row->iscore = 0;
			$row->access = 0;
			$row->client_id = 0;
			$row->element = $pname;
			$row->published = 1;
			$row->params = '';

			if (!$row->store()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}
		}

		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup);
	}
}




require_once (JPATH_PLUGINS.DS.'system'.DS.'djflibraries'.DS.'utility.php');




/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* MODULE INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$modules = &$this->manifest->getElementByPath('modules');
if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {

	foreach ($modules->children() as $module)
	{
		$mname		= $module->attributes('module');
		$mclient	= JApplicationHelper::getClientInfo($module->attributes('client'), true);

		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DS.'modules'.DS.$mname);
		} else {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('No module file specified'));
			return false;
		}

		/*
		* If the module directory already exists, then we will assume that the
		* module is already installed or another module is using that directory.
		*/
		if (file_exists($this->parent->getPath('extension_root'))&&!$this->parent->getOverwrite()) {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Another module is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the module directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		* Since we created the module directory and will want to remove it if
		* we have to roll back the installation, lets add it to the
		* installation step stack
		*/
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		$element = &$module->getElementByPath('files');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy language files
		$element = &$module->getElementByPath('languages');
		if ($this->parent->parseLanguages($element, $mclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy media files
		$element = &$module->getElementByPath('media');
		if ($this->parent->parseMedia($element, $mclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		$mtitle		= $module->attributes('title');
		$mposition	= $module->attributes('position');
		$morder		= $module->attributes('order');
		$mparams  =  'menu='.$module->attributes('menu');

		if ($mtitle && $mposition) {
			// if module already installed do not create a new instance
			$db =& JFactory::getDBO();
			$query = 'SELECT `id` FROM `#__modules` WHERE module = '.$db->Quote( $mname);
			$db->setQuery($query);
			if (!$db->Query()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}
			$id = $db->loadResult();

			if (!$id){
				$row = & JTable::getInstance('module');
				$row->title		= $mtitle;
				$row->ordering	= $morder;
				$row->position	= $mposition;
				$row->showtitle	= 0;
				$row->iscore	= 0;
				$row->access	= ($mclient->id) == 1 ? 2 : 0;
				$row->client_id	= $mclient->id;
				$row->module	= $mname;
				$row->published	= 1;
				$row->params	= $mparams;

				if (!$row->store()) {
					// Install failed, roll back changes
					$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
					return false;
				}

				// Make visible evertywhere if site module
				if ($mclient->id==0){
					$query = 'REPLACE INTO `#__modules_menu` (moduleid,menuid) values ('.$db->Quote( $row->id).',0)';
					$db->setQuery($query);
					if (!$db->query()) {
						// Install failed, roll back changes
						$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
						return false;
					}
				}


			}


		}

		$status->modules[] = array('name'=>$mname,'client'=>$mclient->name);
	}
}





/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* TEMPLATES INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$templates = &$this->manifest->getElementByPath('templates');
if (is_a($templates, 'JSimpleXMLElement') && count($templates->children())) {

	foreach ($templates->children() as $template)
	{
		$tname		= $template->attributes('template');
		$tclient	= JApplicationHelper::getClientInfo($template->attributes('client'), true);

		// Set the installation path
		if (!empty ($tname)) {
			$this->parent->setPath('extension_root', $tclient->path.DS.'templates'.DS.$tname);
		} else {
			$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('No template directory specified'));
			return false;
		}

		/*
		* If the template directory already exists, then we will assume that the
		* template is already installed or another template is using that directory.
		*/
		if (file_exists($this->parent->getPath('extension_root'))&&!$this->parent->getOverwrite()) {
			$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Another template is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the template directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		* Since we created the template directory and will want to remove it if
		* we have to roll back the installation, lets add it to the
		* installation step stack
		*/
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		$element = &$template->getElementByPath('files');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy language files
		$element = &$template->getElementByPath('languages');
		if ($this->parent->parseLanguages($element, $tclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy media files
		$element = &$template->getElementByPath('media');
		if ($this->parent->parseMedia($element, $tclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		$tdefault  =  $template->attributes('default',0);

		if ($tdefault) {

      $db =& JFactory::getDBO();
      $query = 'DELETE FROM #__templates_menu WHERE menuid=0 AND client_id=0';
			$db->setQuery($query);
      $db->query();

      $query = "INSERT INTO #__templates_menu (template, menuid, client_id) VALUES ('$tname',0,0)";
      $db->setQuery($query);
      $db->query();
		}

		$status->templates[] = array('name'=>$tname,'client'=>$tclient->name);
	}
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* JoomFish!
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
/*
if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'contentelements')){
	$elements = &$this->manifest->getElementByPath('joomfish');
	if (is_a($elements, 'JSimpleXMLElement') && count($elements->children())) {
		foreach ($elements->children() as $element) {
			JFile::copy($this->parent->getPath('source').DS.'administrator'.DS.'3dp_ext'.DS.'joomfish'.DS.$element->data(),JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS.$element->data());
			$status->joomfish[] = array('name'=>$element->data(), 'installed' => true);
		}
	}
}
else {
	$status->joomfish[] = array('name'=>JText::_('JoomFish! Content elements has not been installed, because JoomFish! component was not found. You can move them manually from /administrator/components/com_djcatalog/3dp_ext/joomfish folder'), 'installed' => false);
}
*/

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* OUTPUT TO SCREEN
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$rows = 0;







 global $mainframe, $context;

	$language = & JFactory::getLanguage();
    $language->load('com_djfacl', JPATH_ADMINISTRATOR);


	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djfacl'.DS.'libraries'.DS.'utility.php');
	
    // First make sure that this version of Joomla is 1.5 or greater
    $version = new JVersion();
    if ( (real)$version->RELEASE < 1.5 ) {
        echo "<h1 style=\"color: red;\">The 'Djf Acl' package will only work on Joomla version 1.5 or later!</h1>";
        return false;
        }

    ?>

    <div style="background-color:white;text-align:center;">

    <p><img src="components/com_djfacl/assets/images/header/djfacl.png"/></p>
    <p>

        <?php echo(JText::_ ( 'DJFACL_DESCRIZIONE' )); ?>
    </p>

    <p>
        Please report bugs and suggestions to <a href="mailto:info@davidfrassi.it">info@davidfrassi.it</a>.
    </p>

    </div>

    <?php

      $groupid = utility::getArray('select id from #__groups where name = "djfacl"');
	  $sizeArray = sizeof($groupid);
	if ($sizeArray==0){
		$maxid = utility::getArray('select max(id) massimo from #__groups');
		// creare il gruppo sapef
		$massimo = $maxid[0]->massimo;
		$massimo=$massimo+1;
		//echo($massimo);
		$queryToUpdate="insert into #__groups (id, name) values ($massimo,'djfacl')";
		//echo($queryToUpdate);
		utility::executeQuery($queryToUpdate);
	}



	$disattivaquickiconQuery = "update #__modules set published = 0 where id = 9";
	utility::executeQuery($disattivaquickiconQuery);

	/*$inserisciSectionsAndCategoriesComponentQuery =
	"INSERT INTO `#__djfacl_components` (`id`,`name`,`link`,`menuid`,`parent`,`admin_menu_link`,`admin_menu_alt`,`option`,`ordering`,`admin_menu_img`,`iscore`,`params`,`enabled`) VALUES
	(null,'Djfacl_Sections','option=com_sections',0,0,'option=com_sections&scope=content','','com_sections',0,'',0,'',0),
 	(null,'Djfacl_Categories','option=com_categories',0,0,'option=com_categories&section=com_content','','com_categories',0,'',0,'',0);";
	utility::executeQuery($inserisciSectionsAndCategoriesComponentQuery);

*/



    //return true;
    ?>


<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'Djf Acl '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->templates)) : ?>
		<tr>
			<th><?php echo JText::_('Template'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->templates as $template) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $template['name']; ?></td>
			<td class="key"><?php echo ucfirst($template['client']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->joomfish)) : ?>
		<tr>
			<th colspan="2"><?php echo JText::_('Joomfish - Content Elements'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->joomfish as $element) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key" colspan="2"><?php echo $element['name']; ?></td>
			<td><strong><?php echo ($element['installed']) ? JText::_('Installed') : JText::_('Not installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;











		$queryPerUpdate = 
		" insert into #__djfacl_components (id,`option`)
		select distinct null, `option` from #__components as bb where bb.`option` != ''
		and not exists (select aa.* from #__djfacl_components as aa where aa.`option` = bb.`option`)
		order by 2;";
		utility::executeQuery($queryPerUpdate);
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_categories' from #__components
		where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_categories');";
		utility::executeQuery($queryPerUpdate);
		
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_sections' from #__components
		where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_sections');";
		utility::executeQuery($queryPerUpdate);
				
		$queryPerUpdate = "insert into #__djfacl_components (id,`option`) select distinct null, 'com_frontpage' from #__components
		where not exists (select aa.id from #__djfacl_components as aa where aa.`option` = 'com_frontpage');";
		utility::executeQuery($queryPerUpdate);
	
 ?>

	</tbody>
</table>
