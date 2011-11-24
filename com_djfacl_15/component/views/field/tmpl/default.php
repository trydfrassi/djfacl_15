<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

//Ordering allowed ?
$ordering = ($this->lists ['order'] == 'ordering');

//onsubmit="return submitform();"


//DEVNOTE: import html tooltips
JHTML::_ ( 'behavior.tooltip' );
$user = & JFactory::getUser ();

$span = 0;

?>

<script language="javascript" type="text/javascript">
/**
* Submit the admin form
*
* small hack: let task desides where it comes
*/
function submitform(pressbutton){
var form = document.adminForm;
   if (pressbutton)
    {form.task.value=pressbutton;}

	 if ((pressbutton=='add')||(pressbutton=='edit')||(pressbutton=='publish')||(pressbutton=='unpublish')||(pressbutton=='approve')||(pressbutton=='unapprove')
	 ||(pressbutton=='orderdown')||(pressbutton=='orderup')||(pressbutton=='saveorder')||(pressbutton=='remove') )
	 {
	  form.controller.value="field_detail";
	 }
	 if ((pressbutton=='publish')||(pressbutton=='unpublish'))
			 {
			  form.controller.value="field";
			 }
	try {
		form.onsubmit();
		}
	catch(e){}

	form.submit();
}

function submitbutton(pressbutton) {

	var form = document.adminForm;

	<?php
	/*global $mainframe;
			$uri =& JFactory::getURI();
			$ret = $uri->toString();
			$linkNew = $uri->toString().'index.php?controller=field_detail&ret='.base64_encode($ret).'&id=0&catid='.JRequest::getVar('catid');
			$post = JRequest::get ( 'post' );
			$post['ret']=base64_encode($ret);*/
	?>
	submitform( pressbutton );
}

</script>
<?php
global $mainframe;
$params = &$mainframe->getParams ();
echo ('<h1 class="componentheading">' . $params->get ( 'page_title' ) . '</h1>');
//$uri =& JFactory::getURI();
$post = JRequest::get ( 'post' );

$search = JRequest::getVar("search");
$Itemid = JRequest::getVar ( 'Itemid' );
$uristring = JRoute::_("index.php?option=com_djfacl&view=field&Itemid=" . $Itemid);
$params = &$mainframe->getParams ();
$catid_fromparam = $params->get ( 'catid' );
if (! empty ( $catid_fromparam )) {
	$catt = $catid_fromparam;
} else
	$catt = "";

$sectionid_fromparam = $params->get ( 'sectionid' );
if (! empty ( $sectionid_fromparam )) {
	$sectt = $sectionid_fromparam;
} else
	$sectt = "";
$filtro_tipologia_fromparam = $params->get ( 'filtro_tipologia' );
if (! empty ( $filtro_tipologia_fromparam )) {
	$filtro_tipologiat = $filtro_tipologia_fromparam;
} else
	$filtro_tipologiat = "";
	
	
$filtro_categoria_fromparam = $params->get ( 'filtro_categoria' );
if (! empty ( $filtro_categoria_fromparam )) {
	$filtro_categoriat = $filtro_categoria_fromparam;
} else
	$filtro_categoriat = "";
	
$mostra_catsez = $params->get ( 'mostra_catsez' );
$mostra_sez = $params->get ( 'mostra_sez' );
$mostra_catdesc = $params->get ( 'mostra_catdesc' );
$mostra_tipologia = $params->get ( 'mostra_tipologia' );
$group_year = $params->get ( 'group_year' );

$year = $params->get ( 'year' );

if ($mostra_sez == 1) {
	$sez = utility::getField ( 'select title as value from #__sections where id = ' . $sectt );
	//echo('select title as value from #__categories where id = '.$catt);
	if (! empty ( $sez ))
		echo ('<h3>' . JText::_ ( 'SECTION' ) . ': ' . $sez . '</h3>');
}
if ($mostra_catsez == 1) {
	$catsez = utility::getField ( 'select title as value from #__categories where id = ' . $catt );
	//echo('select title as value from #__categories where id = '.$catt);
	if (! empty ( $catsez ))
		echo ('<h3>' . JText::_ ( 'CATEGORY' ) . ': ' . $catsez . '</h3>');
}

?>

<?php

$params = &$mainframe->getParams ();
$tipologia = $params->get ( 'tipologia' );
//echo("tipologia = ".$tipologia);
$valore = $params->get ( 'valore' );

$esito = utility::check_if_table_exists ( '#__djfappend_field' );

if (! empty ( $tipologia ) && $esito && $mostra_tipologia == 1) {
	echo ("<h3 style='font-size:1em;'>" . JText::_ ( 'TYPOLOGY' ) . ": </b>$tipologia");
	if ($valore != "" && $valore != "0") {
		echo ("<br/><b>Valore: </b>$valore");
	}
	echo ("</h3>");
}

if (! empty ( $year ) && $esito) {
	echo ("<h3 style='font-size:1em;'>" . JText::_ ( 'YEAR' ) . ": </b>$year");
	echo ("</h3>");
}

$queryPerDesc = 'select description as value from #__categories where id = ' . $catt;

$category_desc = utility::getField ( $queryPerDesc );

if ($mostra_catdesc == 1 && ! empty ( $category_desc ))
	echo ("<div class='contentdescription'><p>" . $category_desc . "</p></div>");

?>


<form method="post" name="adminForm">


<?php

if (! empty ( $tipologia ) && $esito) {
	?>

<input type="hidden" name="tipologia"
	value="<?php
	echo ($tipologia);
	?>" />

<?php
}

if (! empty ( $valore ) && $esito) {
	?>


<input type="hidden" name="valore" value="<?php
	echo ($valore);
	?>" />

<?php
}

?>
<div style="clear: both;"></div>
<table>
	<tr>
		<td>
		<div class="search-button">
		<table class="search-button">
			<tr>


			<?php
			
			if (empty ( $catid_fromparam ) && $filtro_categoriat) {
				echo ('<div class="blocco-cate" >');
				echo ('<div  class="search_label_cate">');
				echo (JText::_ ( 'CATEGORY' ));
				echo ("</div>");
				echo ('<div  class="search_div_cate">');
				$catid = utility::getDjfVar ( 'catid' );
				$select_custom = utility::addArrayItemToSelect ( array ("Tutti" => "0" ) );
				if (! empty ( $sectt ))
					$query_sect = " and sec.id = " . $sectt;
				else
					$query_sect = "";
				$query_select = "select cat.id as value, concat(sec.title, ' - ', cat.title) as text 
				from #__categories as cat, #__sections as sec 
				where sec.id = cat.section $query_sect order by trim(sec.title), trim(cat.title)";
				//echo($query_select);
				utility::getFormSelectRow ( $paramName = 'catid', $paramValue = $catid, $select_custom, $query_select, $inputTags = ' onChange="submit();"  ', $td = false );
				echo ("</div>");
				echo ("</div>");
			
			} else {
				echo ('<input type="hidden" name="catid" value="' . $catid_fromparam . '"/>');
			}
			//echo("filtro_tipologiat = ".$filtro_tipologiat);
			

			if ($filtro_tipologiat)
				
				if (empty ( $tipologia )) {
					echo ('<div class="blocco-cate" >');
					echo ('<div  class="search_label_cate">');
					echo (JText::_ ( 'TYPOLOGY' ));
					echo ("</div>");
					echo ('<div  class="search_div_cate">');
					$tipologia = utility::getDjfVar ( 'tipologia' );
					$select_custom = utility::addArrayItemToSelect ( array ("Tutti" => "0" ) );
					if (! empty ( $sectt ))
						$query_sect = " and sec.id = " . $sectt;
					else
						$query_sect = "";
					$query_select = "select name as value, name as text from #__djfappend_field_type order by trim(name)";
					//echo($query_select);
					utility::getFormSelectRow ( $paramName = 'tipologia', $paramValue = $tipologia, $select_custom, $query_select, $inputTags = ' onChange="submit();"  ', $td = false );
					echo ("</div>");
					echo ("</div>");
				}
			
			if ($group_year == 1) {
				echo ('<div class="blocco-cate" >');
				echo ('<div  class="search_label_cate">');
				echo (JText::_ ( 'YEAR' ));
				echo ("</div>");
				echo ('<div  class="search_div_cate">');
				$year = utility::getDjfVar ( 'year' );
				$select_custom = utility::addArrayItemToSelect ( array ("Tutti" => "0" ) );
				
		
				$listaItems="";
			
				
			
				
				if (empty ( $tipologia ))
					$query_select = "select distinct year(created) as value, year(created) as text from #__content  order by year(created)";
				else
					$query_select = "select distinct year(event_date) as value, year(event_date) as text from #__djfappend_field  order by year(event_date)";
				
				
					utility::getFormSelectRow ( $paramName = 'year', $paramValue = $year, $select_custom, $query_select, $inputTags = ' onChange="submit();"  ', $td = false );
				echo ("</div>");
				echo ("</div>");
			}
			
			$search = utility::getDjfVar( 'search' );
			utility::getSearchForm ( $styleTd = 'class="search-button"', $nomeLabel = "Filter", $nomeVariabile = "search", $valoreVariabile = $search );
			
			?>
			
</tr>
		</table>
		
		</td>

		</div>

		<td>				
							<?php
							$gid = $this->user->gid;
							//echo("<h1>$gid</h1>");
							

							$canUpload = utility::canJAccess ();
							
							if ($canUpload > 0) {
								echo ('<div class="edit-button">');
								echo ('
		<div id="toolbar-new" style="float: left;">');
								utility::getJoomlaButton ( 'components/com_djfacl/assets/images/add.png', 'add' );
								echo ('</div>
		');
								echo ('
		<div id="toolbar-publish" style="float: left;">');
								utility::getJoomlaButton ( 'components/com_djfacl/assets/images/publish.png', 'publish' );
								echo ('</div>
		');
								echo ('
		<div id="toolbar-unpublish">');
								utility::getJoomlaButton ( 'components/com_djfacl/assets/images/unpublish.png', 'unpublish' );
								echo ('</div>
		');
								echo ("</div>
		<div style='clear: both;'></div>
		");
							}
							?></td>
	</tr>
</table>			
					
					
					
					<?php
					
					?>		

<table cellspacing="0" cellpadding="0" border="0">

				<?php
				$params = &$mainframe->getParams ();
				
				$l_id = $params->get ( 'l_id' );
				$l_id_size = $params->get ( 'l_id_size' );
				
				$l_title = $params->get ( 'l_title' );
				$l_title_size = $params->get ( 'l_title_size' );
				
				$l_introtext = $params->get ( 'l_introtext' );
				$l_introtext_size = $params->get ( 'l_introtext_size' );
				
				$l_state = $params->get ( 'l_state' );
				$l_state_size = $params->get ( 'l_state_size' );
				
				$l_sec = $params->get ( 'l_sec' );
				$l_sec_size = $params->get ( 'l_sec_size' );
				
				$l_cat = $params->get ( 'l_cat' );
				$l_cat_size = $params->get ( 'l_cat_size' );
				
				$l_created = $params->get ( 'l_created' );
				$l_created_size = $params->get ( 'l_created_size' );
				
				$l_created_by = $params->get ( 'l_created_by' );
				$l_created_by_size = $params->get ( 'l_created_by_size' );
				
				$l_modified = $params->get ( 'l_modified' );
				$l_modified_size = $params->get ( 'l_modified_size' );
				
				$l_modified_by = $params->get ( 'l_modified_by' );
				$l_modified_by_size = $params->get ( 'l_modified_by_size' );
				
				$l_version = $params->get ( 'l_version' );
				$l_version_size = $params->get ( 'l_version_size' );
				
				$l_hits = $params->get ( 'l_hits' );
				$l_hits_size = $params->get ( 'l_hits_size' );
				
				$l_tipologia_size = $params->get ( 'l_tipologia_size' );
				$l_valore_size = $params->get ( 'l_valore_size' );
				
				$l_event_date_size = $params->get ( 'l_event_date_size' );
				$l_event_date = $params->get ( 'l_event_date' );
				
				$l_tipologia_size = $params->get ( 'l_tipologia_size' );
				$l_tipologia = $params->get ( 'l_tipologia' );
				
				$l_valore_size = $params->get ( 'l_valore_size' );
				$l_valore = $params->get ( 'l_valore' );
				
				$year = $params->get ( 'year' );
				
				$user = JFactory::getUser ();
				$usertype = $user->usertype;
				
				$esitoUserType = 0;
				if (($usertype == "Author" || $usertype == "Editor" || $usertype == "Publisher" || $usertype == "Manager" || $usertype == "Administrator" || $usertype == "Super Administrator")) {
					$esitoUserType = 1;
				
	//echo($usertype);
				}
				?> 	
							
				
			
		
	

<tr>
		<td>
		<table>
			<tr>
<?php
if ($canUpload) {
	?>
			<td align="center" width="1%" class="sectiontableheader"><input
					type="checkbox" name="toggle" value=""
					onclick="checkAll(<?php
	echo count ( $this->items );
	?>);" /></td>
			<?php
}
?>

		<?php
		if ($l_id != "") {
			
			?>
		<td align="right" width="<?php
			echo ($l_id_size);
			?>"
					class="sectiontableheader">
			<?php
			echo JHTML::_ ( 'grid.sort', $l_id, 'trim(h.id)', $this->lists ['order_Dir'], $this->lists ['order'] );
			?>
		</td>
		<?php
		}
		?>
		
		
			<?php
			if ($l_title != "") {
				?>
			<td align="left" width="<?php
				echo ($l_title_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_title, 'trim(h.title)', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
			<?php
			}
			?>
			<?php
			if ($l_introtext != "") {
				
				?>
						<td align="left"
					width="<?php
				echo ($l_introtext_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_introtext, 'trim(h.introtext)', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
				<?php
			}
			?>
			<?php
			if ($l_state != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_state_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_state, 'h.state', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
			<?php
			}
			?>
			<?php
			if ($l_sec != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_sec_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_sec, 'trim(section_name)', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
			<?php
			}
			?>
			<?php
			if ($l_cat != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_cat_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_cat, 'trim(category_name)', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
			<?php
			}
			?>
			<?php
			if ($l_created != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_created_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_created, 'h.created', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
				<?php
			}
			?>
			<?php
			if ($l_created_by != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_created_by_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_created_by, 'trim(h.created_by)', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
			<?php
			}
			?>
			<?php
			if ($l_modified != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_modified_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_modified, 'h.modified', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
				<?php
			}
			?>
			<?php
			if ($l_modified_by != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_modified_by_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_modified_by, 'trim(h.modified_by)', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
		
			<?php
			}
			?>
			<?php
			if ($l_version != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_version_size);
				?>"
					class="sectiontableheader">
		<?php
				echo JHTML::_ ( 'grid.sort', $l_version, 'h.version', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>
			
			<?php
			}
			?>
			<?php
			if ($l_hits != "") {
				
				?>
										<td align="left"
					width="<?php
				echo ($l_hits_size);
				?>"
					class="sectiontableheader">
			<?php
				echo JHTML::_ ( 'grid.sort', $l_hits, 'h.hits', $this->lists ['order_Dir'], $this->lists ['order'] );
				?>
			</td>

			
					<?php
			}
			?>
						<?php
						
						if ($esito && ! empty ( $tipologia )) {
							$span + 2;
							?>
<?php

							if ($l_tipologia != "") {
								?>
			<td align="left"
					width="<?php
								echo ($l_tipologia_size);
								?>"
					class="sectiontableheader">
				<?php
								echo JHTML::_ ( 'grid.sort', $l_tipologia, 'dft.name', $this->lists ['order_Dir'], $this->lists ['order'] );
								?>

			</td>
<?php
							}
							?>
<?php

							if ($l_valore != "") {
								?>
			<td align="left"
					width="<?php
								echo ($l_valore_size);
								?>"
					class="sectiontableheader">
				<?php
								echo JHTML::_ ( 'grid.sort', $l_valore, 'df.field_value', $this->lists ['order_Dir'], $this->lists ['order'] );
								?>

			</td>
<?php
							}
							?>
<?php

							if ($l_event_date != "") {
								?>
			<td align="left"
					width="<?php
								echo ($l_event_date_size);
								?>"
					class="sectiontableheader">
				<?php
								echo JHTML::_ ( 'grid.sort', $l_event_date, 'df.event_date', $this->lists ['order_Dir'], $this->lists ['order'] );
								?>

			</td>
<?php
							}
							?>

<?php
						}
						?>
</tr>






	<?php
	//echo $this->pulsanti;
	$iconUnPublish = " <img border=\"0\" src=\"images/publish_x.png\" alt=\"add new hello world link\" />";
	$iconPublish = " <img border=\"0\" src=\"images/tick.png\" alt=\"add new hello world link\" />";
	
	$k = 0;
	
	for($i = 0, $n = count ( $this->items ); $i < $n; $i ++) {
		$row = &$this->items [$i];
		global $mainframe;
		$row->checked_out = "";
		//$catid = JRequest::getVar('catid');
		//$sectionid = JRequest::getVar('sectionid');
		
		$link = JRoute::_ ( 'index.php?option=com_content&view=article&catid=' . $row->catid . '&sectionid=' . $row->sectionid . '&Itemid=' . $this->itemid . '&id=' . $row->id, true );
		$link = JRoute::_ ( 'index.php?option=com_content&view=article&id=' . $row->id, true );
		
		
	//	$link =  JRouterSite::_parseSefRoute($link);
		
		$checked = JHTML::_ ( 'grid.checkedout', $row, $i );
		
		/*$fullURL = new JURI($link);
		$fullURL->setVar('return', base64_encode($ret));
		$link = $fullURL->toString();*/
		
		$checked = JHTML::_ ( 'grid.checkedout', $row, $i );
		?>

		<?php
		
		$script_ceccato = "";
		$ceccato = false;
		
		if ($row->state == 0) {
			$color = "#cccccc";
			if ($esitoUserType == 0)
				
				$link = "#";
		} else
			$color = "";
		
		?>



<?php
		$span ++;
		if ($span % 2 == 0)
			$classtd = "sectiontableentry2";
		else
			$classtd = "sectiontableentry1";
		?>

		<tr class="<?php
		echo $classtd;
		?>">

					<?php
		if ($canUpload) {
			?>	<td align="center">
				<?php
			echo $checked;
			?>
			</td>
			<?php
		}
		?>

			<?php
		if ($l_id != "") {
			?>
			<td>

					
							<?php
			echo $row->id;
			?>

					</td>
					<?php
		}
		?>
					<?php
					
					
					
					
		if ($l_title != "") {
			?>
					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
							<?php
			echo $row->title;
			?></a>

							<?php
			?>
							
						<?php
			if ($canUpload){
					$ret = utility::getRetStringForBack ();
					$urledit = JRoute::_('index.php?view=article&ret=' . $ret . '&option=com_content&id=' . $row->id . '&task=edit');
			$linkEdit = ' class="hasTipDjf" title="Edit" href="'.$urledit.'"';
			$button = utility::getButtonCustom ( $linkEdit, 'images/M_images/edit.png', $mode = "", $xIframe = '655', $yIframe = '500' );
		
				echo ($button);
			}
			?>
					</td><?php
		}
		?>
					<?php
		if ($l_introtext != "") {
			?>
					<td>

					<?php
			
			$maxchars = $params->get ( 'maxchars_introtext', 'default' );
			if ($maxchars == "") {
				$maxchars = utility::getDjfVar ( 'maxchars_introtext' );
			}
			//if ($maxchars=="")
			//$maxchars=200;
			$quante = 1;
			
			require_once ('components' . DS . 'com_djfacl' . DS . 'libraries' . DS . 'simple_html_dom.php');
			
			$titolo_troncato = $row->introtext;
			$html = new simple_html_dom ();
			$html->load ( $titolo_troncato );
			$titolo_troncato = $html->plaintext;
			if (strlen ( $titolo_troncato ) <= $maxchars) {
				$titolo_troncato = $titolo_troncato; //do nothing
			} else {
				$titolo_troncato = wordwrap ( $titolo_troncato, $maxchars );
				$titolo_troncato = substr ( $titolo_troncato, 0, strpos ( $titolo_troncato, "\n" ) );
			}
			
			?>

							<?php
			echo $titolo_troncato?>

					</td><?php
		}
		?>
					<?php
		if ($l_state != "") {
			?>
					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
							<?php
			echo $row->state;
			?></a></td><?php
		}
		?>
					<?php
		if ($l_sec != "") {
			?>
					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
							<?php
			echo $row->section_name;
			?></a></td><?php
		}
		?>
					<?php
		if ($l_cat != "") {
			?>
					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
							<?php
			echo $row->category_name;
			?></a></td><?php
		}
		?>
					<?php
		if ($l_created != "") {
			?>

			<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
						<?php
			echo JHTML::_ ( 'date', $row->created, $this->date_format );
			?></a></td><?php
		}
		?>
					<?php
		if ($l_created_by != "") {
			?>

					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
						<?php
			$create_by_name = utility::getField ( 'select name as value from #__users where id = ' . $row->created_by );
			echo $create_by_name;
			?></a></td><?php
		}
		?>
					<?php
		if ($l_modified != "") {
			?>

					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
						<?php
			echo JHTML::_ ( 'date', $row->modified, $this->date_format );
			?></a></td><?php
		}
		?>
					<?php
		if ($l_modified_by != "") {
			?>

					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
						<?php
			echo $row->modified_by;
			?></a></td>
		<?php
		}
		?>
					<?php
		if ($l_version != "") {
			?>
					<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
						<?php
			echo $row->version;
			?></a></td><?php
		}
		?>
					<?php
		if ($l_hits != "") {
			?>

			<td><a <?php
			echo $script_ceccato;
			?> style="color:<?php
			echo $color;
			?>" href="<?php
			echo $link;
			?>" title="<?php
			echo JText::_ ( 'Edit field' );
			?>">
						<?php
			echo $row->hits;
			?></a></td><?php
		}
		?>

								<?php
		
		if ($esito && ! empty ( $tipologia )) {
			
			?>
<?php

			if ($l_tipologia != "") {
				?>
			<td><a <?php
				echo $script_ceccato;
				?> style="color:<?php
				echo $color;
				?>" href="<?php
				echo $link;
				?>" title="<?php
				echo JText::_ ( 'Edit field' );
				?>">
						<?php
				if (! empty ( $row->tiponome ))
					echo $row->tiponome;
				?></a></td>
			<?php
			}
			?>
			<?php
			if ($l_valore != "") {
				?>
			<td><a <?php
				echo $script_ceccato;
				?> style="color:<?php
				echo $color;
				?>" href="<?php
				echo $link;
				?>" title="<?php
				echo JText::_ ( 'Edit field' );
				?>">
						<?php
				if (! empty ( $row->valore ))
					echo $row->valore;
					else echo($row->filename);
				?></a></td>
			<?php
			}
			?>
			<?php
			if ($l_event_date != "") {
				?>
			<td>
			<?php
			
				if (empty ( $row->event_date ))
					$dataevento = $row->created;
				else
					$dataevento = $row->event_date;
				?>
					<a <?php
				echo $script_ceccato;
				?> style="color:<?php
				echo $color;
				?>" href="<?php
				echo $link;
				?>" title="<?php
				echo JText::_ ( 'Edit field' );
				?>">
						<?php
				echo JHTML::_ ( 'date', $dataevento, $this->date_format );
				?></a></td>
			<?php
			}
			?>

<?php
		}
		?>
					</tr>
		<?php
		$k = 1 - $k;
	
	}
	?>
				
	</tr>

		</table>
		</td>
	</tr>

</table>


<table style="width: 100%; margin-top: 10px;">
	<tr>
		<td align="center"><?php
		
		
		//echo($this->pagination->getPagesLinks());
		echo(utility::getPagination($this->pagination));
		
		?></td>
	</tr>
</table>



<input type="hidden" name="daform" value="si" /> 
<input type="hidden" name="controller" value="field" /> 
<input type="hidden" name="task" value="" /> 
<input type="hidden" name="boxchecked" value="0" /> 
<input type="hidden" name="filter_order" value="<?php echo $this->lists ['order']; ?>" /> 
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists ['order_Dir']; ?>" /></form>

<?php //include JPATH_COMPONENT_ADMINISTRATOR.DS."version.php"; ?>
