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
JHTML::_ ( 'behavior.tooltip' );
JHTML::_ ('behavior.modal');
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (pressbutton == 'save') {
			<?php echo(utility::getJSCheckForm('css_block', '==""', JText::_('CHECK_MSG_CSS'))); ?>
			
				submitform( pressbutton );


		}


	}
</script>
<script type="text/javascript">
function checkDisabled() {
	
	var form = document.adminForm;
	//alert(form.id_section.value + " " + form.id_category.value +" "+form.id_item.value);
	alert(form.id_group.value + " " + form.id_users.value );
	
	/*if (form.id_section.value != 0 && form.id_category.value == 0 && form.id_item.value == 0){
		form.id_section.disabled = false;
		form.id_category.disabled = true;
		form.id_item.disabled = true;
	}

	else if (form.id_section.value == 0 && form.id_category.value != 0 && form.id_item.value == 0 ){
		form.id_section.disabled = true;
		form.id_category.disabled = false;
		form.id_item.disabled = true;
	}

	else if (form.id_section.value == 0 && form.id_category.value == 0 && form.id_item.value != 0 ){
		form.id_section.disabled = true;
		form.id_category.disabled = true;
		form.id_item.disabled = false;
	}
	else if (form.id_section.value == 0 && form.id_category.value == 0 && form.id_item.value == 0 ){
		form.id_category.disabled = false;
		form.id_section.disabled = false;
		form.id_item.disabled = false;
	}
*/


	
	
       
}
</script>
<script type="text/javascript">
function jSelectArticle(id, title) {
	
      document.getElementById('id_article').value = id;
      document.getElementById('article_title').value = title;
      document.getElementById('sbox-window').close();
                  
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
  </script>


<style type="text/css">
table.paramlist td.paramlist_key {
	width: 92px;
	text-align: left;
	height: 30px;
}
</style>

<form action="<?php echo JRoute::_ ( $this->request_url )?>" method="post" name="adminForm" id="adminForm">
<div class="col50">
<fieldset class="adminform"><legend><?php echo JText::_ ( 'DETAIL' ); ?></legend>
<table class="admintable">
	<tr>
		<td width="100" align="right" class="key"><label for="title">
					<?php
					echo JText::_ ( 'Id' );
					?>:
				</label></td>
		<td>
		<input class="text_area" disabled="disabled" type="text" name="id"
			id="id" size="32" maxlength="250"
			value="<?php
			echo $this->detail->id;
			?>" /></td>
	</tr>
 	

	<tr>
		<td valign="top" align="right" class="key"><label
			for="css_block">
					<?php
					echo JText::_ ( 'Css Block' );
					?>:
				</label></td>
		<td>
		<input class="text_area" type="text" name="css_block"
			id="css_block" size="32" maxlength="250"
			value="<?php echo $this->detail->css_block; ?>" /></td>
	</tr>


</table>
</fieldset>


</div>
<div class="col50"></div>
<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->id;	?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="cssblock_detail" />

</form>


