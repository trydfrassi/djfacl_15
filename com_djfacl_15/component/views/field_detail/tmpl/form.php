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

JHTML::_ ( 'behavior.tooltip' );
JHTML::_ ( 'behavior.modal');

global $mainframe;
$editor =& JFactory::getEditor();
$app = &JFactory::getApplication();
$nameeditor = $app->getCfg('editor');
$getContent = $editor->getContent('introtext');
$nome = $getContent;
$valore = JRequest::getVar('valore');
$tipologia = JRequest::getVar('tipologia');

?>
	
<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'save') {
				var content = <?php echo ($nome);?>
				<?php echo(utility::getJSCheckForm('title','==""','Titolo - Mancante')); ?>
				<?php if(!empty($tipologia)&&empty($valore)) echo(utility::getJSCheckForm('field_value','==""','Valore Attributo - Mancante')); ?>
				if (content == ""){
				 	alert ("Corpo - Mancante");
					return;
				}
				submitform( pressbutton );
			}
		}
</script>

<form action="<?php echo JRoute::_ ( $this->request_url ); ?>" method="post" name="adminForm" id="adminForm">
<div style="margin-top:20px;" >
<table class="admintable2" >
<tr>
		<td align="right" COLSPAN="2" ><?php 
			echo("<table align='RIGHT'><tr><td>");
					utility::getJoomlaButton('components/com_djfacl/assets/images/save.png','save');
					utility::getJoomlaButton('components/com_djfacl/assets/images/cancel.png','cancel');
					echo("</td></tr></table>");
					?></td>
			<td>
			</td>
	</tr>

	<?php 
	utility::getFormTextRow ( 'id', $this->detail->id, ' disabled="disabled" ' );
	utility::getFormTextRow ( 'title', $this->detail->title, ' ' ); 
	utility::getFormEditorRow ( 'introtext', $this->detail->introtext,null,'100%',300, true ); 
	utility::getFormCalendarRow ( 'created', $this->detail->created );
	utility::getFormCalendarRow ( 'publish_up', $this->detail->publish_up );
	utility::getFormCalendarRow ( 'publish_down', $this->detail->publish_down );
	utility::getFormRadioRow ( 'state', $this->detail->state, 'NOT_PUBLISHED', '0' );
	utility::getFormRadioRow ( 'state', $this->detail->state, 'PUBLISHED', '1' );

		
	$esito = utility::check_if_table_exists ( '#__djfappend_field' );
	$catid = Jrequest::getVar('catid');
	$sectionid = utility::getField('select section as value from #__categories where id = '.$catid);
	
	
	$query_categoria = 'select id as value, title as text from #__categories order by trim(2)';
	$query_sezione = 'select id as value, title as text from #__sections order by trim(2)';
	
	utility::getFormSelectRow ($paramName = 'catid',$paramValue = $catid,$select_custom = null,$query_select = $query_categoria,$inputTags = ' disabled="disabled" ');
	utility::getFormSelectRow ($paramName = 'sectionid',$paramValue = $sectionid,$select_custom = null,$query_select = $query_sezione,$inputTags = ' disabled="disabled" ');
	
	$field_name_id = "";
	$field_value_id = "";

	
	if(!empty($tipologia)){
	
	$queryPerIdValore = 'select id as value from #__djfappend_field_value where valore="'.$valore.'"';
	$queryPerIdTipo = 'select id as value from #__djfappend_field_type where name="'.$tipologia.'"';
	
	//echo($queryPerIdValore);
	//echo($queryPerIdTipo);
	
	$field_name_id = utility::getField($queryPerIdTipo);
	$field_value_id = utility::getField($queryPerIdValore);
	
	//echo($field_name_id);
	//echo($field_value_id);
	
	
	utility::getFormTextRowOnlyShow ( 'field_name', $tipologia,  ' disabled="disabled" ' );
	
	if (!empty($field_value_id)){
		utility::getFormTextRowOnlyShow ( 'field_value', $valore,  ' disabled="disabled" ' );
		echo('<input type="hidden" name="field_value" value="'.$field_value_id.'" />');
	}else{
		$query_valori = 'select id as value, valore as text from #__djfappend_field_value where id_field_type='.$field_name_id.' order by trim(2)';
		//echo($query_valori);
		$listaRisultati=utility::getQueryArray($query_valori);
		if (empty($listaRisultati)){
			utility::getFormTextRow ( 'field_value', $this->detail->field_value, '  ' );
		}else
		utility::getFormSelectRow ($paramName = 'field_value',$paramValue = $valore,$select_custom = null,$query_select = $query_valori,$inputTags = '  ');
	}
	
	echo('<input type="hidden" name="id_field_type" value="'.$field_name_id.'" />');
	
	utility::getFormCalendarRow ( 'event_date', $this->detail->event_date );
	}
	
	?>
	<tr>
		<td align="right" COLSPAN="2" ><?php 
			echo("<table align='RIGHT'><tr><td>");
					utility::getJoomlaButton('components/com_djfacl/assets/images/save.png','save');
					utility::getJoomlaButton('components/com_djfacl/assets/images/cancel.png','cancel');
					echo("</td></tr></table>");
					?></td>
			<td>
			</td>
	</tr>
</table>
</div>

<div class="col50"></div>
<div class="clr"></div>
<input type="hidden" name="cid[]" value="<?php echo $this->detail->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" value="<?php echo($catid);?>" name="catid" />
<input type="hidden" value="<?php echo($sectionid);?>" name="sectionid" />
	

</form>