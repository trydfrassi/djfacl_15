<?php defined('_JEXEC') or die('Restricted access'); 

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'ordering');

//onsubmit="return submitform();"

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');
	global $mainframe;
	jimport('joomla.html.pane');
	jimport('joomla.application.module.helper');
	$modules = &JModuleHelper::getModules('cpanel');
    // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
	$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));	

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
		  form.controller.value="gruppi_utenti_detail";
		 }
		try {
			form.onsubmit();
			}
		catch(e){}
		
		form.submit();
	}



</script>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" >
<div id="editcell" >


<!-- SEARCH -->
	<?php echo $pane->startPane("content-pane"); ?>	
	<div class="panel" style="width:100%;margin-left:0px;">
		<h3 class="jpane-toggler title" id="1" style="background-color:white;">
		<span style="color:blue;"><?php echo JText::_ ( 'SEARCH' ); ?></span></h3>
		<div class="jpane-slider content" style="background-color:white;">

<fieldset class="adminform" style="border:0px;margin-top:10px;">
<div style="float:left;margin-left:10px;">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" style="width:150px;" onchange="document.adminForm.submit();" />
 		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
</div>
<div style="float:left;margin-left:10px;" >
		<div style="float:left;"><?php echo JText::_( 'User' ); ?>:&nbsp;</div>
		<div style="float:left;"><?php echo $this->lists ['users']; ?>&nbsp;</div>
		
		
</div>
<div style="float:left;margin-left:10px;" >
		<div style="float:left;"><?php echo JText::_( 'GRUPPO' ); ?>:&nbsp;</div>
		<div style="float:left;"><?php echo $this->lists ['gruppi']; ?>&nbsp;</div>
		
		
</div>
<div style="float:left;margin-left:10px;" >
		<div style="float:left;"><?php echo JText::_( 'TYPOLOGY' ); ?>:&nbsp;</div>
		<div style="float:left;"><?php echo $this->lists ['tipologie']; ?>&nbsp;</div>
		
		
</div>

</fieldset>
	</div>	</div>
<?php echo $pane->endPane("content-pane"); ?>
	<!-- FINE PRESTAZIONI -->



<!-- ADVANCED -->
	<?php echo $pane->startPane("content-pane"); ?>	
	<div class="panel" style="width:100%;margin-left:0px;">
		<h3 class="jpane-toggler title" id="1" style="background-color:white;">
		<span style="color:blue;"><?php echo JText::_ ( 'ADVANCED' ); ?></span></h3>
		<div class="jpane-slider content" style="background-color:white;">
				<div style="float:left;margin-top:10px;">
<fieldset class="adminform" ><legend style="margin-bottom:10px;"><?php echo JText::_ ( 'COPY_TO_GROUP_DJFACL' ); ?></legend>
<div style="clear:both;"/>
<div style="float:left;width:350px;margin-left:10px;" >
	<div style="float:left;"><?php echo JText::_( 'GRUPPO' ); ?>:&nbsp;</div>
	<div style="float:left;"><?php echo $this->lists ['gruppi_copia']; ?>&nbsp;</div>
	<?php 
	$imgsrc = utility::getBaseUrl()."administrator/components/com_djfacl/assets/images/header/value-16.png";
	$linkbutton = '#';
	$title = JText::_('COPY');
	$javascript="javascript:if(document.adminForm.boxchecked.value==0){alert('Seleziona un elemento dalla lista');}else{ hideMainMenu(); submitbutton('copy')}";
	$alternativo = "alternativo";
	$id = "id";
	$name = "name";
		?>
	<div style="margin-left:">&nbsp;<?php echo(utility::getButton($imgsrc,$linkbutton,$javascript,$name,$title,$id,$alternativo));?></div>		
			
</div>
</fieldset>	
</div>
<div style="margin-top:10px;">
<fieldset class="adminform"><legend style="margin-bottom:10px;"><?php echo JText::_ ( 'MOVE_TO_GROUP_SAME_TYPE' ); ?></legend>
<div style="clear:both;"/>
<div style="float:left;width:350px;margin-left:10px;" >
	<div style="float:left;"><?php echo JText::_( 'GRUPPO' ); ?>:&nbsp;</div>
	<div style="float:left;"><?php echo $this->lists ['gruppi_sposta']; ?>&nbsp;</div>
	<?php 
	$imgsrc = utility::getBaseUrl()."administrator/components/com_djfacl/assets/images/header/field-16.png";
	$linkbutton = '#';
	$title = JText::_('MOVE');
	$javascript="javascript:if(document.adminForm.boxchecked.value==0){alert('Seleziona un elemento dalla lista');}else{ hideMainMenu(); submitbutton('move')}";
	$alternativo = "alternativo";
	$id = "id";
	$name = "name";
	?>
	<div style="margin-left:">&nbsp;<?php echo(utility::getButton($imgsrc,$linkbutton,$javascript,$name,$title,$id,$alternativo));?></div>	
</div>
</fieldset>	

</div>
		</div>
	</div>
	<?php echo $pane->endPane("content-pane"); ?>
	<!-- FINE PRESTAZIONI -->


<table class="adminlist" >
	<thead>
		<tr >
			<th width="1%" style="text-align:left;">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="1%"  style="text-align:left;">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th width="1%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Chiave', 'h.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				
			</th>
			<th width="1%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Matricola', 'u.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				
			</th>
			<th width="10%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Username', 'u.username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				
			</th>
			
			<th width="30%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Nome', 'u.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				
			</th>
			<th width="30%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Gruppo', 'g.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				
			</th>
			<th width="30%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Typology', 'h.typology', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				
			</th>
								
		</tr>
	</thead>	
	
	
	<?php 
	//echo $this->pulsanti; 
	$iconUnPublish = " <img border=\"0\" src=\"images/publish_x.png\" alt=\"add new hello world link\" />";
	$iconPublish = " <img border=\"0\" src=\"images/tick.png\" alt=\"add new hello world link\" />";		
	
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$row->checked_out="";
		$link 	= JRoute::_( 'index.php?option=com_djfacl&controller=gruppi_utenti_detail&task=edit&cid[]='. $row->id );

		$checked 	= JHTML::_('grid.checkedout',$row, $i );

		?>
		
		<?php
					$script_ceccato = "";
					$ceccato = false;
				//echo($row->gid);
					if($row->gid==25){
						$link="";
						$testo = str_replace(" ","&nbsp;",JText::_("CHECK_MSG_ADM"));
						$script_ceccato=' onClick=alert("'.$testo.'");';
					}
					
		?>
		
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php {
					echo $checked; }
					?>
			</td>
			<td>
			
					<a <?php echo $script_ceccato;?>  href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit gruppi_utenti' ); ?>">
						<?php echo $row->id; ?></a>
			
			</td>
			<td>
			
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit gruppi_utenti' ); ?>">
						<?php echo $row->matricola; ?></a>
			
			</td>
				<td>
			
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit gruppi_utenti' ); ?>">
						<?php echo $row->username; ?></a>
			
			</td>
			
			<td>
			
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit gruppi_utenti' ); ?>">
						<?php echo $row->utente; ?></a>
				
			</td>
				<td>
				
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit grouppo' ); ?>">
						<?php echo $row->gruppo; ?></a>
				
			</td>
				<td>
				
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit grouppo' ); ?>">
						<?php echo $row->typology;

						if ($row->typology == 'joomla'){
							echo(' - <a title="Edit User" href="index.php?option=com_users&view=user&task=edit&cid[]='.$row->matricola.'"><img src="components/com_djfacl/assets/images/dhtmlgoodies_j_user.png"/></a>');
						}
						
						?></a>
				
			</td>
			
		

		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
<tfoot>
		<td colspan="9">
			<?php echo $this->pagination->getListFooter(); ?>
			
		</td>
	</tfoot>
	</table>
</div>
<div>
<p><b>Note:</b> <?php echo(JText::_ ( 'GRUPPI_UTENTI_DESCRIZIONE' )); ?></p>
</div>


<input type="hidden" name="controller" value="gruppi_utenti" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

