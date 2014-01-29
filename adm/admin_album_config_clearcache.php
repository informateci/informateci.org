<?php
/**
*
* @package Icy Phoenix
* @version $Id: admin_album_config_clearcache.php 49 2008-09-14 20:36:03Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

$album_config_tabs[] =  array(
	'order' => 8,
	'selection' => 'clearcache',
	'title' => $lang['Clear_Cache_Tab'],
	'detail' => '',
	'sub_config' => array(
		/*
		0 => array(
			'order' => 0,
			'selection' => '',
			'title' => '',
			'detail' => ''
		)
		*/
	),
	'config_table_name' => ALBUM_CONFIG_TABLE,
	'generate_function' => 'album_generate_config_clearcache',
	'template_file' => ADM_TPL . 'album_config_clearcache_body.tpl'
);

function album_generate_config_clearcache($config_data)
{
	global $template, $lang, $new;

	$template->assign_vars(array(
		'CLEARCACHE_TEXT' => $lang['Album_clear_cache_confirm'],
		'L_YES' => $lang['Yes'],
		'L_NO' => $lang['No']
		)
	);
}
?>