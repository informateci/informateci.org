<?php
/**
*
* @package Icy Phoenix
* @version $Id: xs_download.php 49 2008-09-14 20:36:03Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Vjacheslav Trushkin (http://www.stsoftware.biz)
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
$no_page_header = true;
require('./pagestart.' . PHP_EXT);

// check if mod is installed
if(empty($template->xs_version) || $template->xs_version !== 8)
{
	message_die(GENERAL_ERROR, isset($lang['xs_error_not_installed']) ? $lang['xs_error_not_installed'] : 'eXtreme Styles mod is not installed. You forgot to upload includes/template.php');
}

define('IN_XS', true);
include_once('xs_include.' . PHP_EXT);

$template->assign_block_vars('nav_left',array('ITEM' => '&raquo; <a href="' . append_sid('xs_import.' . PHP_EXT) . '">' . $lang['xs_import_styles'] . '</a>'));
$template->assign_block_vars('nav_left',array('ITEM' => '&raquo; <a href="' . append_sid('xs_download.' . PHP_EXT) . '">' . $lang['xs_download_styles'] . '</a>'));

// submit url
if(isset($_GET['url']) && !defined('DEMO_MODE'))
{
	$id = intval($_GET['url']);
	$var = 'xs_downloads_' . $id;
	$import_data = array(
		'host'		=> $_SERVER['HTTP_HOST'],
		'port'		=> $_SERVER['SERVER_PORT'],
		'url'		=> str_replace('xs_download.', 'xs_frameset.', $_SERVER['PHP_SELF']),
		'session'	=> $userdata['session_id'],
		'xs'		=> $template->xs_versiontxt,
		'style'		=> STYLE_HEADER_VERSION,
	);
	$str = '<form action="' . $board_config[$var] . '" method="post" style="display: inline;" target="main"><input type="hidden" name="data" value="' . htmlspecialchars(serialize($import_data)) . '" /><input type="submit" value="' . $lang['xs_continue'] . '" class="post" /></form>';
	$message = $lang['xs_import_download_warning'] . '<br /><br />' . $str . '<br /><br />' . str_replace('{URL}', append_sid('xs_download.' . PHP_EXT), $lang['xs_download_back']);
	xs_message($lang['Information'], $message);
}


if(isset($_GET['edit']))
{
	$id = intval($_GET['edit']);
	$template->assign_block_vars('edit', array(
		'ID'		=> $id,
		'TITLE'		=> $board_config['xs_downloads_title_'.$id],
		'URL'		=> $board_config['xs_downloads_'.$id]
		));
}

if(isset($_POST['edit']) && !defined('DEMO_MODE'))
{
	$id = intval($_POST['edit']);
	$update = array();
	if(!empty($_POST['edit_delete']))
	{
		// delete link
		$total = $board_config['xs_downloads_count'];
		$update['xs_downloads_count'] = $total - 1;
		for($i=$id; $i<($total-1); $i++)
		{
			$update['xs_downloads_'.$i] = $update['xs_downloads_'.($i+1)];
			$update['xs_downloads_title_'.$i] = $update['xs_downloads_title_'.($i+1)];
		}
		$update['xs_downloads_'.($total-1)] = '';
		$update['xs_downloads_title_'.($total-1)] = '';
	}
	else
	{
		$update['xs_downloads_'.$id] = stripslashes($_POST['edit_url']);
		$update['xs_downloads_title_'.$id] = stripslashes($_POST['edit_title']);
	}
	foreach($update as $var => $value)
	{
		if(isset($board_config[$var]))
		{
			$sql = "UPDATE " . CONFIG_TABLE . " SET config_value='" . xs_sql($value) . "' WHERE config_name='" . $var . "'";
		}
		else
		{
			$sql = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('" . $var . "', '" . xs_sql($value) . "')";
		}
		$db->sql_query($sql);
		$board_config[$var] = $value;
	}
	// update config cache
	if(defined('XS_MODS_CATEGORY_HIERARCHY210'))
	{
		if(!empty($config))
		{
			$config->read(true);
		}
	}
}

if(!empty($_POST['add_url']) && !defined('DEMO_MODE'))
{
	$id = $board_config['xs_downloads_count'];
	$update = array();
	$update['xs_downloads_'.$id] = stripslashes($_POST['add_url']);
	$update['xs_downloads_title_'.$id] = stripslashes($_POST['add_title']);
	$update['xs_downloads_count'] = $board_config['xs_downloads_count'] + 1;
	foreach($update as $var => $value)
	{
		if(isset($board_config[$var]))
		{
			$sql = "UPDATE " . CONFIG_TABLE . " SET config_value='" . xs_sql($value) . "' WHERE config_name='" . $var . "'";
		}
		else
		{
			$sql = "INSERT INTO " . CONFIG_TABLE . " (config_name, config_value) VALUES ('" . $var . "', '" . xs_sql($value) . "')";
		}
		$db->sql_query($sql);
		$board_config[$var] = $value;
	}
	// update config cache
	if( defined('XS_MODS_CATEGORY_HIERARCHY210') && !empty($config) )
	{
		$config->read(true);
	}
}

for($i=0; $i<$board_config['xs_downloads_count']; $i++)
{
	$row_class = $xs_row_class[$i % 2];
	$template->assign_block_vars('url', array(
		'ROW_CLASS'		=> $row_class,
		'NUM'			=> $i,
		'NUM1'			=> $i + 1,
		'URL'			=> htmlspecialchars($board_config['xs_downloads_'.$i]),
		'TITLE'			=> htmlspecialchars($board_config['xs_downloads_title_'.$i]),
		'U_DOWNLOAD'	=> append_sid('xs_download.' . PHP_EXT . '?url='.$i),
		'U_EDIT'		=> append_sid('xs_download.' . PHP_EXT . '?edit='.$i),
		));
}

$template->assign_vars(array(
	'U_POST'		=> append_sid('xs_download.' . PHP_EXT)
	));

$template->set_filenames(array('body' => XS_TPL_PATH . 'downloads.tpl'));
$template->pparse('body');
xs_exit();

?>