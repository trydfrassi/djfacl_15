<?php defined('_JEXEC') or die('Restricted access'); 

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'ordering');

//onsubmit="return submitform();"

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

?>

<script language="javascript" type="text/javascript">
/**
* Submit the admin form
* 
* small hack: let jtask desides where it comes
*/
function submitform(pressbutton){
var form = document.adminForm;
   if (pressbutton)
    {form.task.value=pressbutton;}
     
	 if ((pressbutton=='add')||(pressbutton=='edit')||(pressbutton=='publish')||(pressbutton=='unpublish')||(pressbutton=='approve')||(pressbutton=='unapprove')
	 ||(pressbutton=='orderdown')||(pressbutton=='orderup')||(pressbutton=='saveorder')||(pressbutton=='remove') )
	 {
	  form.controller.value="jtask_detail";
	 }
	try {
		form.onsubmit();
		}
	catch(e){}
	
	form.submit();
}


</script>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" >
<div id="editcell">


<table>
				<tr>
					<td width="100%">
						<?php echo JText::_( 'Filter' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
					
				</tr>
			</table>

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
				<th width="15%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Name', 'h.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
			</th>
					<th width="15%" nowrap="nowrap" class="title"  style="text-align:left;">
				<?php echo JHTML::_('grid.sort', 'Task', 'h.jtask', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
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
		$link 	= JRoute::_( 'index.php?option=com_djfacl&controller=jtask_detail&jtask=edit&cid[]='. $row->id );

		$checked 	= JHTML::_('grid.checkedout',$row, $i );

		?>
		
		<?php
					$script_ceccato = "";
					$ceccato = false;
				
		?>
		
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
			
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit jtask' ); ?>">
						<?php echo $row->id; ?></a>
			
			</td>
			
		<td>
				
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit jtask' ); ?>">
						<?php echo $row->name; ?></a>
				
			</td>
			<td>
				
					<a <?php echo $script_ceccato; ?> href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit jtask' ); ?>">
						<?php echo $row->jtask; ?></a>
				
			</td>

		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
<tfoot>
		<td colspan="5">
			<?php echo $this->pagination->getListFooter(); ?>
			
		</td>
	</tfoot>
	</table>
</div>
<div>
<p><b>Note:</b> <?php echo(JText::_ ( 'TASK_DESCRIZIONE' )); ?></p>
</div>


<input type="hidden" name="controller" value="jtask" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

