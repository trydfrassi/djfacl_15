<?php defined('_JEXEC') or die('Restricted access');?> 
truncate table #__djfacl_contenuti;
<?php for ($i=0, $n=count( $this->items ); $i < $n; $i++){ $row = &$this->items[$i]; ?>insert into #__djfacl_contenuti values (<?php echo $row->id; ?>,<?php echo $row->id_user; ?>,<?php echo $row->id_group; ?>,<?php echo $row->id_components; ?>,<?php echo $row->id_modules; ?>,<?php echo $row->id_section; ?>,<?php echo $row->id_category; ?>,<?php echo $row->id_item; ?>,<?php echo $row->id_article; ?>,<?php echo $row->site_admin; ?>,<?php echo $row->jtask; ?>,<?php echo $row->css_block; ?>);
<?php }?>