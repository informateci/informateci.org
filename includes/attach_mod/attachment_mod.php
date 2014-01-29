<?php
/**
*
* @package Icy Phoenix
* @version $Id: attachment_mod.php 88 2009-02-22 19:54:45Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* (c) 2002 Meik Sievertsen (Acyd Burn)
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
	exit;
}

// Mighty Gorgon: new attachments cache.
$sql = "SELECT * FROM " . ATTACH_CONFIG_TABLE;
if (!($result = $db->sql_query($sql, false, 'attach_')))
{
	message_die(GENERAL_ERROR, 'Could not query attachment information', '', __LINE__, __FILE__, $sql);
}

while ($row = $db->sql_fetchrow($result))
{
	$attach_config[$row['config_name']] = trim($row['config_value']);
}

// We assign the original default board language here, because it gets overwritten later with the users default language
$attach_config['board_lang'] = trim($board_config['default_lang']);

// Needed to correctly process attachments!
define('PAGE_PRIVMSGS', -10);

include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/constants.' . PHP_EXT);
include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_attach.' . PHP_EXT);
include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_filetypes.' . PHP_EXT);
if(defined('IN_DOWNLOAD') || defined('IN_ADMIN') || defined('ATTACH_DISPLAY') || defined('ATTACH_PM') || defined('ATTACH_POSTING'))
{
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_includes.' . PHP_EXT);
}
if(defined('IN_DOWNLOAD') || defined('IN_ADMIN') || defined('ATTACH_PM') || defined('ATTACH_POSTING'))
{
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_posting.' . PHP_EXT);
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_delete.' . PHP_EXT);
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_thumbs.' . PHP_EXT);
}
if(defined('IN_ADMIN'))
{
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_selects.' . PHP_EXT);
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_admin.' . PHP_EXT);
}
if(defined('ATTACH_PROFILE'))
{
	include_once(IP_ROOT_PATH . ATTACH_MOD_PATH . 'includes/functions_profile.' . PHP_EXT);
}

// Please do not change the include-order, it is valuable for proper execution.
// Functions for displaying Attachment Things
if(defined('IN_DOWNLOAD') || defined('ATTACH_DISPLAY') || defined('ATTACH_PM') || defined('ATTACH_POSTING'))
{
	include(IP_ROOT_PATH . ATTACH_MOD_PATH . 'displaying.' . PHP_EXT);
}

// Posting Attachments Class (HAS TO BE BEFORE PM)
if(defined('ATTACH_PM') || defined('ATTACH_POSTING'))
{
	include(IP_ROOT_PATH . ATTACH_MOD_PATH . 'posting_attachments.' . PHP_EXT);
}

if(defined('ATTACH_PM'))
{
	// PM Attachments Class
	include(IP_ROOT_PATH . ATTACH_MOD_PATH . 'pm_attachments.' . PHP_EXT);
}
/*
*/

if (!intval($attach_config['allow_ftp_upload']))
{
	$upload_dir = $attach_config['upload_dir'];
}
else
{
	$upload_dir = $attach_config['download_path'];
}

?>