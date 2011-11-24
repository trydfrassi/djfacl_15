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
				id_user
				
			</th>
			<th width="10%" nowrap="nowrap" class="title">
				id_group 	
				
			</th>
			<th width="30%" nowrap="nowrap" class="title">
				id_components
				
			</th>
				<th width="30%" nowrap="nowrap" class="title">
				id_modules
				
			</th>
		<th width="30%" nowrap="nowrap" class="title">
				id_section
				
			</th>
							<th width="30%" nowrap="nowrap" class="title">
				id_category
				
			</th>
							<th width="30%" nowrap="nowrap" class="title">
				id_item
				
			</th>
							<th width="30%" nowrap="nowrap" class="title">
				id_article
				
			</th>
							<th width="30%" nowrap="nowrap" class="title">
				site_admin
				
			</th>
							<th width="30%" nowrap="nowrap" class="title">
				jtask
				
			</th>
							<th width="30%" nowrap="nowrap" class="title">
				css_block
				
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
				<?php echo $row->id_user; ?>
			</td>
			<td>
				<?php echo $row->id_group; ?>
			</td>
			<td>
				<?php echo $row->id_components; ?>
			</td>
				<td>
				<?php echo $row->id_modules; ?>
			</td>
				<td>
				<?php echo $row->id_section; ?>
			</td>
				<td>
				<?php echo $row->id_category; ?>
			</td>
				<td>
				<?php echo $row->id_item; ?>
			</td>
				<td>
				<?php echo $row->id_article; ?>
			</td>
				<td>
				<?php echo $row->site_admin; ?>
			</td>
				<td>
				<?php echo $row->jtask; ?>
			</td>
					<td>
				<?php echo $row->css_block; ?>
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
