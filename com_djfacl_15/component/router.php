<?php
/**
 * @version		$Id: router.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Djfacl
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * @param	array	A named array
 * @return	array
 */
function DjfaclBuildRoute( &$query )
{
	$segments = array();

	
if (isset ( $query ['view'] )) {
		$segments [] = $query ['view'];
		unset ( $query ['view'] );
	}
	if (isset ( $query ['catid'] )) {
		$segments [] = $query ['catid'];
		unset ( $query ['catid'] );
	}
	
	if (isset ( $query ['sectionid'] )) {
		$segments [] = $query ['sectionid'];
		unset ( $query ['sectionid'] );
	}
	if (isset ( $query ['id'] )) {
		$segments [] = $query ['id'];
		unset ( $query ['id'] );
	}
	if (isset ( $query ['task'] )) {
		$segments [] = $query ['task'];
		unset ( $query ['task'] );
	}
	if (isset ( $query ['controller'] )) {
		$segments [] = $query ['controller'];
		unset ( $query ['controller'] );
	}
	if (isset ( $query ['id_field'] )) {
		$segments [] = $query ['id_field'];
		unset ( $query ['id_field'] );
	}
	if (isset ( $query ['tmpl'] )) {
		$segments [] = $query ['tmpl'];
		unset ( $query ['tmpl'] );
	}

	if (isset ( $query ['ret'] )) {
		$segments [] = $query ['ret'];
		unset ( $query ['ret'] );
	}
	
	
	//echo(sizeOf($segments));
	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/Djfacl/task/bid/Itemid
 *
 * index.php?/Djfacl/bid/Itemid
 */
function _DjfaclParseRoute( $segments )
{
$count = count ( $segments );
	$vars = array ();
	$menu = &JSite::getMenu ();
	$item = &$menu->getActive ();
		
	for ($i=0;$i<$count;$i++){
	echo("<br>antani = ".$segments [$i]);
	}
 
	return $vars;
}