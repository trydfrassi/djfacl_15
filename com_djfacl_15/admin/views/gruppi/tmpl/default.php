<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

//Ordering allowed ?
$ordering = ($this->lists ['order'] == 'ordering');
JHTML::_ ( 'behavior.tooltip' );
JHTML::_ ( 'behavior.modal' );



function getNode($typology = 'nodo', $id_padre, $i, $name, $id, $djfacl='joomla') {
	
	global $mainframe, $context;
	$stringHtmlUscita = "uscita";		

	if ($typology=='foglia'){
		
		  if ($id_padre == 25) {
		 	 if ($djfacl == 'djfacl'){
						$icon_user = "dhtmlgoodies_user_ad.png";
		  	 }else{
		  			$icon_user = "dhtmlgoodies_j_user_ad.png";
		  	}
					
		  } else{
		  	if ($djfacl == 'djfacl'){
					$icon_user = "dhtmlgoodies_user.png";
		  	}else{
		  		$icon_user = "dhtmlgoodies_j_user.png";
		  	}
		  }
		  if ($id_padre!="25")
		  $stringHtmlUscita = '<li noDrag="true" id="node'.$i.'" class ="'.$icon_user.'" >
						       <a href="index.php?option=com_djfacl&controller=gruppi_utenti&idgroup=0&tipologia=0&search=&matricola=' . $id . '">'.$name.'</a>
							   </li>';
		  else  $stringHtmlUscita = '<li noDrag="true" id="node'.$i.'" class ="'.$icon_user.'" >
						       <a href="#">'.$name.'</a>
							   </li>';
	}
	else{
		
		if ($id > 30) {
				$stringHtmlUscita= '<li id="node' . $i . '" noDelete="true" noDrag="true" noRename="true"><a href="index.php?option=com_djfacl&controller=gruppi_utenti&matricola=&search=&idgroup=' . $id . '">' . $name . '</a>';
		} else {
				$stringHtmlUscita = '<li id="node' . $i . '" noDelete="true" noDrag="true" noRename="true"><a href="index.php?option=com_djfacl&matricola=&search=&idgroup=' . $id . '&controller=gruppi_utenti" style="color:#cccccc;">' . $name . '</a>';
		}
	}
	return $stringHtmlUscita;								
}
	
	
function getButtonNode($id_padre, $id) {
	global $mainframe, $context;
	$stringHtmlUscita = "";
	if (  $id>=29){
			$stringHtmlUscita = '<span class="editlinktip hasTip" title="Inserisci nel mezzo">
								 <a href="index.php?option=com_djfacl&controller=gruppi_detail&task=edit&cid[]=0&id_parent=' . $id . '" style="cursor:pointer" >
								 <img style="margin-top:0px; padding:0 0px 0 0px;" src="components/com_djfacl/assets/images/insert.png"></a>
								 </span>';
			if ($id > 30) {
						$stringHtmlUscita.='<span class="editlinktip hasTip" title="Modifica">
											<a href="index.php?option=com_djfacl&controller=gruppi_detail&id_parent=' . $id_padre . '&task=edit&cid[]=' . $id . '" style="cursor:pointer" > 
											<img style="margin-top:0px; padding:0 0px 0 0px;" src="components/com_djfacl/assets/images/edit.png"></a>
											</span>';

						
							$qry2 = "select id, name, value from #__core_acl_aro_groups where parent_id = $id order by name";
								  $db = & JFactory::getDBO ();
										  $db->setQuery ( $qry2 );
										  $arrayDb = $db->loadObjectList ();
																					
						$haFigli=0;
						if (sizeof($arrayDb)>0) {
							$haFigli = 1;
						}
						
						$qry2 = "select * from #__djfacl_gruppi_utenti where idgroup = $id";
								  $db = & JFactory::getDBO ();
										  $db->setQuery ( $qry2 );
										  $arrayDb = $db->loadObjectList ();
																					
						$haFigli2=0;
						if (sizeof($arrayDb)>0) {
							$haFigli2 = 1;
						}
						
						if (!$haFigli&&!$haFigli2){
								$stringHtmlUscita.= '<span class="editlinktip hasTip" title="Cancella">
	 												 <a href="index.php?option=com_djfacl&controller=gruppi_detail&task=remove&toremove=' . $id . '" style="cursor:pointer" > 
													 <img style="margin-top:0px; padding:0 0px 0 0px;" src="components/com_djfacl/assets/images/erase.png"></a>
													 </span>';
									  }
						}
			}
			return $stringHtmlUscita;
	}

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
	  form.controller.value="gruppi_detail";
	 }
	try {
		form.onsubmit();
		}
	catch(e){}
	
	form.submit();
}


</script>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm"> 
<div id="editcell">
	<div style="width: 100%; margin-top: 3px;">
		<table class="adminlist">
			<tr>
				<td>
					<style type="text/css">
						/* CSS for the demo */
						img {
							border: 0px;
						}
					</style>

						<ul id="dhtmlgoodies_tree2" class="dhtmlgoodies_tree">
						
						
							<li id="node0" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><a href="#" style="color:#cccccc;">Root node</a>
										<?php
										
										function select($id_padre, $i) {
										  $i++;
										  
										  echo('<ul>');
										  
												  // cerco le foglie
												  
												  $qry_users = "select u.id as id_users, u.name as name_users, g.idgroup as gid, g.typology as typology from #__users as u, #__djfacl_gruppi_utenti g 
												  where u.id = g.iduser and u.block = 0 and g.idgroup = $id_padre order by u.name";
												  $db2 = & JFactory::getDBO ();
												 /* $db2->setQuery ( $qry_users );
										  		  $arrayDb2 = $db2->loadObjectList ();
										  		 
												  foreach ($arrayDb2 as $risultato2){
														echo(getNode('foglia',$risultato2->gid, $i, $risultato2->name_users, $risultato2->id_users, $risultato2->typology));
												  }*/
												  
												  // cerco i nodi
												  
												  $qry_groups = "select id, name, value from #__core_acl_aro_groups where parent_id = $id_padre order by name";
										  		  $db = & JFactory::getDBO ();
										  		  $db->setQuery ( $qry_groups );
										  		  $arrayDb = $db->loadObjectList ();
										  		  $sizeArrayDb = sizeof($arrayDb);
												  
										  		  foreach ($arrayDb as $risultato1){
												  	 $i=$i*2;
												  	 $id = $risultato1->id;
													 $name = $risultato1->name;
													 echo(getNode('nodo', $id_padre, $i, $name, $id));
													 echo(getButtonNode($id_padre, $id));
													 
													 select($id,$i); // ricorsività
													 
													 echo('</li>');
												  }
												  echo('</ul>');
											}
										  
										 echo(select(0,0));
										 
								echo('</li>');
								echo('</ul>');// end while
								?>
							</li>
						</ul>
					<br>
				</td>
			</tr>
		</table>
	</div>
</div>

<table style="margin-top: 0px;">
	<tr>
		<td>
		<p><b>Note:</b> <?php echo(JText::_ ( 'GRUPPI_DESCRIZIONE' )); ?></p>
		</td>
	</tr>
</table>

<input type="hidden" name="controller" value="gruppi" /> 
<input type="hidden" name="task" value="" /> <input type="hidden" name="boxchecked" value="0" /> 
<input type="hidden" name="filter_order" value="<?php echo $this->lists ['order']; ?>" /> 
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists ['order_Dir']; ?>" />
</form>




<script defer="defer" type="text/javascript">
	treeObj = new JSDragDropTree('components/com_djfacl/assets/images/');
	treeObj.setTreeId('dhtmlgoodies_tree2');
	treeObj.setMaximumDepth(15);
	treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
	treeObj.initTree();
	treeObj.expandAll();
	</script>
	
	
	
	