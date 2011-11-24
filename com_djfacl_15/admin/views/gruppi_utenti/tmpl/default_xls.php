<?php defined('_JEXEC') or die('Restricted access'); 

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'ordering');

//onsubmit="return submitform();"

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

?>


<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" >
<div id="editcell">




	<table class="adminlist" border="1">
	<thead>
		<tr>
			
			<th width="5%" nowrap="nowrap" class="title">
				id
				
			</th>
			<th width="10%" nowrap="nowrap" class="title">
				idgroup
				
			</th>
			<th width="10%" nowrap="nowrap" class="title">
				iduser 	
				
			</th>
			<th width="30%" nowrap="nowrap" class="title">
				typology
				
			</th>
			
		</tr>
	</thead>	
	
	
	<?php 
	
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];


		?>
		
		<tr >
			<td>
				<?php echo $row->id; ?>
			</td>
			
			<td>
				<?php echo $row->idgroup; ?>
			</td>
			<td>
				<?php echo $row->iduser; ?>
			</td>
			<td>
				<?php echo $row->typology; ?>
			</td>
		</tr>
		<?php
		
	}
	?>
<tfoot>
	
	</tfoot>
	</table>
</div>
<div>

</div>


<input type="hidden" name="controller" value="moduli" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />

</form>
