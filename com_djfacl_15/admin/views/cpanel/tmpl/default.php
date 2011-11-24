<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
	
	
	JToolBarHelper::title ( JText::_ ( 'Djf Acl - ').JText::_ ( 'GESTIONE_PANNELLO' ), 'djfacl' );
	//jceToolbarHelper::help( 'cpanel' );	
	
	
	//$updater =& JCEUpdater::getInstance();	
?>
<table class="admintable">
    <tr>
        <td width="55%" valign="top" colspan="2">
		<div id="cpanel">
		
		<div style="float: left;">
			
		
			<div class="icon">
				<a href="index.php?option=com_djfacl&controller=gruppi">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-tree.png">
					<span><?php echo JText::_( 'Groups' );?></span>
				</a>
			</div>
						<div class="icon">
				<a href="index.php?option=com_djfacl&controller=gruppi_utenti">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-groups.png">
					<span><?php echo JText::_( 'USERGROUPS' );?></span>
				</a>
			</div>
	<div class="icon">
				<a href="index.php?option=com_djfacl&controller=gruppi_icone">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-icon-groups.png">
					<span><?php echo JText::_( 'ICON-GROUPS' );?></span>
				</a>
			</div>		
						<div class="icon">
				<a href="index.php?option=com_djfacl&controller=contenuti">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-content.png">
					<span><?php echo JText::_( 'Content' );?></span>
				</a>
			</div>
			
						<div class="icon">
				<a href="index.php?option=com_djfacl&controller=cssblock">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-css.png">
					<span><?php echo JText::_( 'CSSBLOCK' );?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_djfacl&controller=jtask">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-application.png">
					<span>Task</span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_djfacl&controller=icon">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-icon.png">
					<span><?php echo JText::_( 'ICONE' );?></span>
				</a>
			</div>
			
				<div class="icon">
				<a href="index.php?option=com_djfacl&controller=loader">
					<img alt="Configurazione globale" src="components/com_djfacl/assets/images/header/icon-48-loader.png">
					<span>Impor/Export</span>
				</a>
			</div>
			
			
		</div>
		
</div>
        <div class="clr"></div>
        </td>
    </tr>
	<tr>
    	<td>
    	<?php 
    	
    	$params = JComponentHelper::getParams('com_djfacl');
    	$credits=$params->get('credits');  
    	?>
        	<table class="admintable">
        	<?php if ($credits=="yes") {?>
            	<tr>
                    <td class="key">
                        <?php echo JText::_( 'Author' );?>
                    </td>
                    <td>
                        <a target="new" href="http://www.davidfrassi.it">David Frassi</a>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'Email' );?>
                    </td>
                    <td>
                        <a target="new" href="mailto:info@davidfrassi.it">info@davidfrassi.it</a>
                    </td>
                </tr>
            	<tr>
                    <td class="key">
                        <?php echo JText::_( 'Site' );?>
                    </td>
                    <td>
                        <a target="new" href="http://www.davidfrassi.it/index.php/joomla-components-download" target="_new">www.davidfrassi.it/index.php/joomla-components-download</a>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'License' );?>
                    </td>
                    <td>GNU/GPL</td>
                </tr>
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'Component Version' );?>
                    </td>
                    <td>
                       <b> <?php echo $this->com_info['version'];?></b>
                    </td>
                </tr>
                
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'MOD_djfacl_quickicon' );?>
                    </td>
                    <td>
                      <b><?php echo $this->mod_djfacl_quickicon['version'];?></b>
                    </td>
                </tr>
                
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'MOD_djfacl' );?>
                    </td>
                    <td>
                      <b><?php echo $this->mod_djfacl['version'];?></b>
                    </td>
                </tr>
                
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'PLG_djfacl' );?>
                    </td>
                    <td>
                    <b><?php echo $this->plg_djfacl['version'];?></b>
                    </td>
                </tr>
                
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'PLG_djfcontent' );?>
                    </td>
                    <td>
                       <b> 
                       <?php echo $this->plg_djfcontent['version'];?>
                       </b>
                    </td>
                </tr>
                
                
                
                                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'DESCRIPTION' );?>
                    </td>
                    <td>
                    
                        <?php echo(JText::_ ( 'djfacl_DESCRIZIONE' )); ?>       </td>
                </tr>
                
            </table>
        </td>
    </tr>
</table>
<?php if ($credits=="yes") {?>
<div style="text-align:center;">  <a href="http://www.davidfrassi.it/index.php/joomla-components-download" target="new"> <img src="components/com_djfacl/assets/images/header/djf-.jpg"/></a></div>
<?php } ?>