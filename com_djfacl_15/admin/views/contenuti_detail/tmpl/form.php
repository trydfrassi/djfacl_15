<?php
/**
 * @version $Id: component.php 5173 2006-09-25 18:12:39Z Jinx $
 * @package Joomla
 * @subpackage Config
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 *
 * php echo $lang->getName();
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

//DEVNOTE: import html tooltips
JHTML::_ ( 'behavior.modal' );
JHTML::_ ( 'behavior.tooltip' );
global $mainframe;
jimport ( 'joomla.html.pane' );
jimport ( 'joomla.application.module.helper' );
$modules = &JModuleHelper::getModules ( 'cpanel' );
// TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
$pane = &JPane::getInstance ( 'sliders', array ('allowAllClose' => true ) );

?>


<?php
$tab = Jrequest::getVar ( 'tab' );
?>
<?php

function getNode($typology = 'nodo', $id_padre, $i, $name, $id, $djfacl = 'joomla', $grupposearch) {
	global $mainframe, $context;
	$stringHtmlUscita = "uscita";
	if ($typology == 'foglia') {
		if ($id_padre == 25) {
			if ($djfacl == 'djfacl') {
				$icon_user = "dhtmlgoodies_user_ad.png";
			} else {
				$icon_user = "dhtmlgoodies_j_user_ad.png";
			}
		
		} else {
			if ($djfacl == 'djfacl') {
				$icon_user = "dhtmlgoodies_user.png";
			} else {
				$icon_user = "dhtmlgoodies_j_user.png";
			}
		}
	} else {
		$style = "";
		$gs = $grupposearch;
		if ($grupposearch == $id) {
			$style = "style='color:red;'";
		}
		
		if ($id > 30) {
			if ($id == $grupposearch) {
				$stringHtmlUscita = '<li id="node' . $i . '" noDelete="true" noDrag="true" noRename="true"><a href="#" ' . $style . ' id ="a' . $i . '" onClick="sub(' . $id . ',' . $i . ')">' . $name . '</a>';
			} else {
				$stringHtmlUscita = '<li id="node' . $i . '" noDelete="true" noDrag="true" noRename="true"><a href="#" id ="a' . $i . '" onClick="sub(' . $id . ',' . $i . ')">' . $name . '</a>';
			}
		
		} else {
			$stringHtmlUscita = '<li id="node' . $i . '" noDelete="true" noDrag="true"  noRename="true"><a disabled><span style="color:#CCCCCC;">' . $name . '</span></a>';
		}
	}
	return $stringHtmlUscita;
}

function getButtonNode($id_padre, $id, $grupposearch) {
	global $mainframe, $context;
	$stringHtmlUscita = "";
	if ($id_padre != 0 && $id_padre != 17) {
		$stringHtmlUscita = '';
		if ($id > 30) {
			$stringHtmlUscita .= '';
			$qry2 = "select id, name, value from #__core_acl_aro_groups where parent_id = $id order by name";
			$db = & JFactory::getDBO ();
			$db->setQuery ( $qry2 );
			$arrayDb = $db->loadObjectList ();
			$haFigli = 0;
			if (! empty ( $arrayDb )) {
				$haFigli = 1;
			}
			if (! $haFigli) {
				$stringHtmlUscita .= '';
			}
		}
	}
	return $stringHtmlUscita;
}

?>


<script language="javascript" type="text/javascript">



function check(){
	var form = document.adminForm;
	if (form.id_group.value == "" || form.id_group.value == "0"){
		alert('Attenzione! Devi aver selezionato almeno un gruppo!');
		return false;
	}
	return true;
}



	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (pressbutton == 'save') {
			
			if (check()==true) {
<?php
if (empty ( $tab )) {
	?>
				var nuovaOpzione = new Option("Nessuno", "0");
				document.getElementById('id_modules').options[0] = nuovaOpzione;
				document.getElementById('id_modules').value = '0';
				
				document.getElementById('id_article').value = '0';
				<?php
}
?>
				submitform('save');
				
			}
			
		}
		else if (pressbutton == 'savearticolo') {
			if (check()==true) {
				document.getElementById('id_components').value = '0';
				document.getElementById('id_category').value = '0-0';
				document.getElementById('jtask').value = '0';
				document.getElementById('css_block').value = '0';
				<?php
						if (empty ( $tab )) {
							?>
							objs=document.getElementsByName("site_admin");
							objs[0].checked=true;
							
					<?php } ?>
					var nuovaOpzione = new Option("Nessuno", "0");
					document.getElementById('id_modules').options[0] = nuovaOpzione;
					document.getElementById('id_modules').value = '0';
				
				submitform('save');
			}
			
		}
		else if (pressbutton == 'savemodulo') {
		if (check()==true) {
			document.getElementById('id_components').value = '0';
			document.getElementById('id_category').value = '0-0';
			document.getElementById('jtask').value = '0';
			document.getElementById('css_block').value = '0';
			<?php
					if (empty ( $tab )) {
						?>
						objs=document.getElementsByName("site_admin");
						objs[0].checked=true;

				<?php } ?>
			document.getElementById('id_article').value = '0';
			submitform('save');
		}
		
	}

	}


	
</script>

<script type="text/javascript">

function sbiancaArticolo(){
	document.getElementById('id_article').value = 0;
    document.getElementById('article_title').value = '';
 }

function jSelectArticle(id, title) {
	
      document.getElementById('id_article').value = id;
      document.getElementById('article_title').value = title;
      document.getElementById('sbox-window').close();
                  
}

function blackAll(id){
	var els=document.getElementsByTagName('a');
	var sOut="";
	for (var i=0;i<els.length;i++){
		els[i].style.color="black";
	};
}

function sub( id,name){
		if (id >0){
		document.getElementById('id_group').value = id;
		var nodename = "nodeATag"+name;
		blackAll();
		document.getElementById(nodename).style.color="red";
		document.getElementById('grupposearch').value = id;
		}
	
}

window.addEvent('domready', function() {
	SqueezeBox.initialize({});
	$$('a.modal-button').each(function(el) {
			el.addEvent('click', function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
			});
	});
});


		<?php
		
		//$setJs = utility::setJSProperty('id_category','value','6');
		//utility::onBodyLoad($setJs);
		

		// SEZIONE DI CONTROLLO AJAX E AUTOCOMPLETAMENTO AUTOMATICO
		$taskControllerUrl = "index.php?option=com_djfacl&controller=contenuti_detail&task=rebuildselect&format=raw";
		echo (utility::getAjaxCheck ( $taskControllerUrl, "adminForm", "id_section", "categoria", "id_category" ));
		
		?>


		
</script>

<style type="text/css">
table.paramlist td.paramlist_key {
	width: 92px;
	text-align: left;
	height: 30px;
}
</style>

<form action="<?php
echo JRoute::_ ( $this->request_url )?>"
	method="post" name="adminForm" id="adminForm"><input type="hidden"
	id="task" name="task" value="" /> <input type="hidden" name="cid[]"
	value="<?php
	echo $this->detail->id;
	?>" /> <input type="hidden" id="grupposearch" name="grupposearch"
	value="<?php
	echo $this->grupposearch;
	?>" /> <input type="hidden" name="controller" value="contenuti_detail" />
<input id="id_group" type="hidden" name="id_group"
	value="<?php
	echo $this->grupposearch;
	?>" />


<div class="col50">
<fieldset class="adminform"><legend><?php
echo JText::_ ( 'GESTIONE_CONTENUTI_DETTAGLIO' );
?></legend>
<table width="100%">
	<tr style="vertical-align: top;">
		<td>


		<div class="col50">
		<fieldset class="adminform"><legend><?php
		echo JText::_ ( 'GRUPPO' );
		?></legend>

		<ul id="dhtmlgoodies_tree2" class="dhtmlgoodies_tree">


			<li id="node0" noDrag="true" noSiblings="true" noDelete="true"
				noRename="true" style="margin-top: 5px;"><a href="#">Root node</a>
										<?php
										
										function select($id_padre, $i, $gruppog) {
											$i ++;
											
											echo ('<ul>');
											
											// cerco le foglie
											

											$qry_users = "select u.id as id_users, u.name as name_users, g.idgroup as gid, g.typology as typology from #__users as u, #__djfacl_gruppi_utenti g 
												  where u.id = g.iduser and g.idgroup = $id_padre order by u.name";
											$db2 = & JFactory::getDBO ();
											$db2->setQuery ( $qry_users );
											$arrayDb2 = $db2->loadObjectList ();
											
											// cerco i nodi
											

											$qry_groups = "select id, name, value from #__core_acl_aro_groups where parent_id = $id_padre order by name";
											$db = & JFactory::getDBO ();
											$db->setQuery ( $qry_groups );
											$arrayDb = $db->loadObjectList ();
											$sizeArrayDb = sizeof ( $arrayDb );
											
											foreach ( $arrayDb as $risultato1 ) {
												$i = $i * 2;
												$id = $risultato1->id;
												$name = $risultato1->name;
												
												echo (getNode ( 'nodo', $id_padre, $i, $name, $id, null, $gruppog ));
												echo (getButtonNode ( $id_padre, $id, $gruppog ));
												
												select ( $id, $i, $gruppog ); // ricorsività
												

												echo ('</li>');
											}
											echo ('</ul>');
										}
										if (isset ( $this->detail->gid ))
											$gruppog = $this->detail->gid;
										elseif ($this->grupposearch != "")
											$gruppog = $this->grupposearch;
										else
											$gruppog = 0;
										
										echo (select ( 0, 0, $gruppog ));
										
										echo ('</li>');
										echo ('</ul>'); // end while
										?>
							</li>
		</ul>
		<br>

		<script defer="defer" type="text/javascript">
	treeObj = new JSDragDropTree('components/com_djfacl/assets/images/');
	treeObj.setTreeId('dhtmlgoodies_tree2');
	treeObj.setMaximumDepth(15);
	treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
	treeObj.initTree();
	treeObj.expandAll();
	</script></fieldset>
		</div>
		</td>


		<td>

<?php

echo $pane->startPane ( "content-pane" );
?>	

<?php

if ($tab == "1" || empty ( $tab )) {
	
	if ($tab == "1") {
		echo ('<input type="hidden" id="id_article" name="id_article" />');
		echo ('<input type="hidden" id="id_modules" name="id_modules" />');
	}
	
	?>
	<div class="panel" style="width: 100%; margin-left: 0px;">
		<h3 class="jpane-toggler title" id="1"
			style="background-color: white;"><span style="color: blue;"><?php
	echo JText::_ ( 'GESTIONE_CONTENUTI_COMPONENTE' );
	?></span></h3>
		<div class="jpane-slider content" style="background-color: white;">
		<div style="text-align: right;"><?php
	utility::getJoomlaButton ( 'components/com_djfacl/assets/images/save.png', 'save' );
	?></div>
		<table class="admintable">
			<tr>
				<td width="100" align="right" class="key"><label for="title"><?php
	echo JText::_ ( 'Id' );
	?>: </label></td>
				<td><input class="text_area" disabled="disabled" type="text"
					name="id" id="id" size="32" maxlength="250"
					value="<?php
	echo $this->detail->id;
	?>" /></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><label
					for="id_components"><?php
	echo JText::_ ( 'Componente' );
	?>:	</label></td>
				<td><?php
	echo $this->lists ['componenti_associati'];
	?> </td>
			</tr>

			<?php 
			
			$catid=0;
			$sectionid=0;
			$catid = $this->detail->id_category;
			if ($catid !="0" && $catid!="999999"){
				$sectionid = utility::getField('select section as value from #__categories where id = '.$catid);
			} else
			$sectionid = $this->detail->id_section;
			$valoreSelezionato = $sectionid."-".$catid;
			//echo($valoreSelezionato);
			
			
				$select_custom_category = utility::addArrayItemToSelect ( array ("Tutti" => "999999-999999", "Nessuno" => "0-0" ) );
	if (empty ( $this->detail->id_category ))
		$this->detail->id_category = "";
	$query_select_category1 = '
		select concat(sec.id,"-",cat.id) as value, concat(sec.title,"/",cat.title) as text
		from #__sections as sec left join #__categories as cat on (cat.section = sec.id)
		union select concat(sec2.id,"-0") as value, sec2.title as text
		from #__sections as sec2
		order by 1,2';
	utility::getFormSelectRow ( $paramName = 'id_category', $paramValue = $valoreSelezionato, $select_custom = $select_custom_category, $query_select = $query_select_category1, $inputTags = '' );

?>

			<tr>
				<td valign="top" align="right" class="key"><label for="id_modules">
					<?php
	echo JText::_ ( 'Site/Admin' );
	?>:
				</label></td>
				<td>
		
<?php
	
	$checked_1 = 0;
	$checked_0 = 0;
	
	if ($this->detail->site_admin == 1)
		$checked_1 = "checked";
	if ($this->detail->site_admin == 0)
		$checked_0 = "checked";
	?>


<?php
	utility::onBodyLoad ( 'disableOtherField()' );
	?>
<input id="site_admin" type="radio" name="site_admin" <?php
	echo $checked_1;
	?>
					value="1" onClick="disableOtherField();">Site<br>
				<input type="radio" name="site_admin" <?php
	echo $checked_0;
	?>
					value="0" onClick="disableOtherField();">Administrator<br>

				</td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><label for="id_modules">
					<?php
	echo JText::_ ( 'Task' );
	?>:
				</label></td>
				<td>
		<?php
	echo $this->lists ['jtask_associati'];
	?></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><label for="css_block">
					<?php
	echo JText::_ ( 'Css Block' );
	?>:
				</label></td>
				<td>
				<?php
	echo $this->lists ['cssblock_associati'];
	?>
			</td>
			</tr>
		</table>
		</div>
		</div>
<?php
	echo $pane->endPane ( "content-pane" );
	?>
<?php
}
?>











<?php
if ($tab == "2" || empty ( $tab )) {
	
	if ($tab == "2") {
		echo ('<input type="hidden" id="id_components" name="id_components" />');
		echo ('<input type="hidden" id="id_section" name="id_section" />');
		echo ('<input type="hidden" id="id_category" name="id_category" />');
		echo ('<input type="hidden" id="jtask" name="jtask" />');
		
		echo ('<input type="hidden" id="id_article" name="id_article" />');
		echo ('<input type="hidden" id="css_block" name="css_block" />');
	}
	
	?>
<?php

	echo $pane->startPane ( "content-pane" );
	?>	
	<div class="panel" style="width: 100%; margin-left: 0px;">
		<h3 class="jpane-toggler title" id="2"
			style="background-color: white;"><span style="color: blue;"><?php
	echo JText::_ ( 'GESTIONE_CONTENUTI_MODULO' );
	?></span></h3>
		<div class="jpane-slider content" style="background-color: white;">
		<div style="text-align: right;"><?php
	utility::getJoomlaButton ( 'components/com_djfacl/assets/images/save.png', 'savemodulo' );
	?></div>
		<table class="admintable">
	<?php
	$select_custom_moduli = utility::addArrayItemToSelect ( array ("Tutti" => "999999" ) );
	if (empty ( $this->detail->idcategoria1 ))
		$this->detail->idcategoria1 = "";
	$query_select_moduli1 = "SELECT id AS value, concat(position,\"_______________     \",title) AS text FROM #__modules where client_id = 0 and module != 'mod_djfacl' ORDER BY position, trim(title)";
	utility::getFormSelectRow ( $paramName = 'id_modules', $paramValue = $this->detail->id_modules, $select_custom = $select_custom_moduli, $query_select = $query_select_moduli1, $inputTags = '' );
	?>
</table>
		</div>
		</div>
<?php
	echo $pane->endPane ( "content-pane" );
	?>
<?php
}
?>










<?php
if ($tab == "3" || empty ( $tab )) {
	
	if ($tab == "3") {
		echo ('<input type="hidden" id="id_components" name="id_components" />');
		echo ('<input type="hidden" id="id_section" name="id_section" />');
		echo ('<input type="hidden" id="id_category" name="id_category" />');
		echo ('<input type="hidden" id="jtask" name="jtask" />');
		echo ('<input type="hidden" id="css_block" name="css_block" />');
		echo ('<input type="hidden" id="id_modules" name="id_modules" />');
	
	}
	
	?>
<?php

	echo $pane->startPane ( "content-pane" );
	?>	
	<div class="panel" style="width: 100%; margin-left: 0px;">
		<h3 class="jpane-toggler title" id="3"
			style="background-color: white;"><span style="color: blue;"><?php
	echo JText::_ ( 'GESTIONE_CONTENUTI_ARTICOLO' );
	?></span></h3>
		<div class="jpane-slider content" style="background-color: white;">
		<div style="text-align: right;"><?php
	utility::getJoomlaButton ( 'components/com_djfacl/assets/images/save.png', 'savearticolo' );
	?></div>
		<table class="admintable">
			<tr>
				<td valign="top" align="right" class="key"><label for="id_article">
<?php
	echo JText::_ ( 'Articolo' );
	?>: 
</label></td>
				<td>
				<p><input id="article_title" disabled="disabled" type="text"
					size="60" value="<?php
	echo $this->titoloArticolo;
	?>"
					onChange="disableOtherFieldFromArticle();" />&nbsp; <a
					class="modal-button" type="button"
					href="index.php?option=com_content&amp;task=element&amp;tmpl=component"
					rel="{handler: 'iframe', size: {x: 650, y: 375}}">
<?php
	$pathImage = $this->baseurl . '/components/com_djfacl/assets/images/insert.png';
	?>
<img src="<?php
	echo $pathImage;
	?>" /> </a> <a href="#" onClick="sbiancaArticolo();">
<?php
	$pathImage = $this->baseurl . '/components/com_djfacl/assets/images/erase.png';
	?>
<img src="<?php
	echo $pathImage;
	?>" /> </a> <input id="id_article" name="id_article" type="hidden"
					value=" <?php
	echo $this->idArticolo;
	?>" /></p>
				</td>
			</tr>
		</table>
		</div>
		</div>
<?php
	echo $pane->endPane ( "content-pane" );
	?>
<?php
}
?>
</td>
	</tr>
</table>
</fieldset>
</div>




</form>


<b>Nota:</b>
<?php
echo JText::_ ( 'CONTENUTI_DETTAGLIO_DESCRIZIONE' );
?>
