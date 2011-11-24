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
			<?php echo(utility::getJSCheckForm('idgroup#$#typology', '<=30#$#=="djfacl"', JText::_("CHECK_MSG_ADM2"))); ?>
				submitform( pressbutton );
				

		}


	}
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
		<td valign="top" align="right" class="key"><label for="id_users">
					<?php
					echo JText::_ ( 'Utente' );
					?>:
				</label></td>
		<td>

		<?php
				$valoreIuser="";
				echo $this->lists ['utenti_associati'];
				if (!$this->isNew){
					$valoreIuser = $this->detail->iduser;
					echo ('<input type="hidden" value="'.$this->detail->iduser.'" name = "iduser"/>');
					
				}else{
					$matricola = utility::getDjfVar('matricola','0');
					if ($matricola!=""&&$matricola!=null&&$matricola!="0"){
						echo ('<input type="hidden" value="'.$matricola.'" name = "iduser"/>');
						$valoreIuser = $matricola;
					}
				}
				?>

			</td>
	</tr>

	<tr>
		<td valign="top" align="right" class="key" id="gruppi"><label for="id_users">
					<?php
					echo JText::_ ( 'Gruppo' );
					?>:
				</label></td>
	<td>
					<?php echo $this->lists['gid']; ?>
					</td>
	</tr>
	<tr>
		<td valign="top" align="right" class="key"><label
			for="id_modules">
					<?php
					echo JText::_ ( 'Tipologia' );
					?>:
				</label></td>
		<td>
		
<input type="text" id="typology" name="typology" value="<?php echo $this->detail->typology; ?>" readonly="readonly"/>
				</td>
	</tr>


</table>
</fieldset>


</div>
<div class="col50"></div>
<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->id;	?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="gruppi_utenti_detail" />
<input type="hidden" name="matricola" value="<?php echo $valoreIuser;	?>" />


</form>




