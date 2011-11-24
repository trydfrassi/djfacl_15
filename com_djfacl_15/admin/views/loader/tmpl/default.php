<?php defined('_JEXEC') or die('Restricted access'); 

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'ordering');

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');


?>


<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm"   enctype="multipart/form-data">
<div class="col50">
<fieldset class="adminform"><legend><?php echo JText::_ ( 'Import/Export' ); ?></legend>
	<table class="admintable" style="text-align:left;">
	<thead>
	
	<tr>
		 <td class="key">
			<label for="title"><?php echo JText::_ ( 'AGGIORNA_FILE_BACKUP' ); ?>:</label>
		</td>
		  <td>
		  <a href="index.php?option=com_djfacl&controller=loader&task=backup" title="<?php echo JText::_ ( 'AGGIORNA_FILE_BACKUP' ); ?>">
		  <img src="components/com_djfacl/assets/images/toolbar/icon-32-export.png"/>
		   </a>

		</td>
		
	</tr>
	<tr>
		 <td class="key" style="width:190px;">
			<label for="title"><?php echo JText::_ ( 'SCARICA_FILE_BACKUP' ); ?>:</label>
		</td>
		  <td>
		  
		  <?php 
		  jimport( 'joomla.filesystem.file' );
		  $filename="";
		   if (! file_exists ( JPATH_BASE . DS . "components".DS."com_djfacl".DS."backup" )) {
			mkdir ( JPATH_BASE . DS . "components".DS."com_djfacl".DS."backup", 0777 );
		}
		  if (! file_exists ( JPATH_BASE . DS . "components".DS."com_djfacl".DS."backup".DS."backup" )) {
			mkdir ( JPATH_BASE . DS . "components".DS."com_djfacl".DS."backup".DS."backup", 0777 );
		}
		  $files = JFolder::files( JPATH_BASE . DS . "components".DS."com_djfacl".DS."backup" ,'.', false, false );
             
		       $filesArray = array();
               foreach($files as $file){
                        //In zip file we do not want to include folder
                        $filename = $file;
               }
		  
		  ?>
			<a href="components/com_djfacl/backup/<?php echo $filename; ?>" TYPE="application/zip"><?php echo $filename; ?></a>
		</td>
	</tr>
	<tr>
		 <td class="key">
			<label for="title"><?php echo JText::_ ( 'RIPRISTINO' ); ?>:</label>
		</td>
		  <td>
			<input type="file" name="upload" id="upload" size="30" maxlength="512" /> 
			<input type="hidden" name="update_file" value="TRUE" />
			<input type="hidden" name="filename" value="" />
			<input type="hidden" name="filename_sys" value="" />
			<input type="submit" value="<?php echo JText::_ ( 'INVIO' ); ?>"/>
		</td>
		
	</tr>
	
		
	</thead>	
	

	</table> 
	 </fieldset>
</div>

<input type="hidden" name="controller" value="loader" />
<input type="hidden" name="task" value="restore" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
