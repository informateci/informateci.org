<?php
/**
*
* @package Icy Phoenix
* @version $Id: album_common.php 101 2009-05-16 16:03:40Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Smartor (smartor_xp@hotmail.com)
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

define('IN_ALBUM', true);
define('ALBUM_NAV_ARROW', $lang['Nav_Separator']);

if (!defined('IMG_THUMB'))
{
	$cms_page_id = 'album';
	$cms_page_nav = (!empty($cms_config_layouts[$cms_page_id]['page_nav']) ? true : false);
	$cms_global_blocks = (!empty($cms_config_layouts[$cms_page_id]['global_blocks']) ? true : false);
	$cms_auth_level = (isset($cms_config_layouts[$cms_page_id]['view']) ? $cms_config_layouts[$cms_page_id]['view'] : AUTH_ALL);
	check_page_auth($cms_page_id, $cms_auth_level);
}

// Include Language
$language = $board_config['default_lang'];

if (!file_exists(IP_ROOT_PATH . 'language/lang_' . $language . '/lang_album_main.' . PHP_EXT))
{
	$language = 'english';
}

include(IP_ROOT_PATH . 'language/lang_' . $language . '/lang_album_main.' . PHP_EXT);

// Get Album Config
$album_config = array();
$sql = "SELECT * FROM " . ALBUM_CONFIG_TABLE;
if(!$result = $db->sql_query($sql, false, 'album_config_'))
{
	message_die(GENERAL_ERROR, 'Could not query Album config information', '', __LINE__, __FILE__, $sql);
}
while($row = $db->sql_fetchrow($result))
{
	$album_config[$row['config_name']] = $row['config_value'];
}
$db->sql_freeresult($result);

if($album_config['album_debug_mode'] == 1)
{
	$GLOBALS['album_debug_enabled'] = true;
}
else
{
	$GLOBALS['album_debug_enabled'] = false;
}

if ($album_config['show_img_no_gd'] == 1)
{
	//$thumb_size = 'width="' . $album_config['thumbnail_size'] . '" height="' . $album_config['thumbnail_size'] . '"';
	$thumb_size = 'width="' . $album_config['thumbnail_size'] . '"';
}
else
{
	$thumb_size = '';
}

if ((intval($album_config['set_memory']) > '0') && (intval($album_config['set_memory']) < '33'))
{
	@ini_set('memory_limit', intval($album_config['set_memory']) . 'M');
}

if ($album_config['show_inline_copyright'] == 0)
{
	/*
	$album_copyright = '<div align="center" class="gensmall" style="font-family: Verdana, Arial, Helvetica, sans-serif; letter-spacing: -1px"><b>Photo Album Powered by</b><br />';
	$album_copyright .= 'Photo Album 2' . $album_config['album_version'] . '&nbsp;&copy;&nbsp;2002-2003&nbsp;<a href="http://smartor.is-root.com" target="_blank">Smartor</a><br />';
	$album_copyright .= 'Volodymyr (CLowN) Skoryk\'s SP1 Addon 1.5.1<br />';
	$album_copyright .= 'IdleVoid\'s Album Category Hierarchy 1.3.0<br />';
	$album_copyright .= '<a href="http://www.mightygorgon.com" target="_blank">Mighty Gorgon</a> Full Album Pack ' . $album_config['fap_version'];
	$album_copyright .= '</div>';
	*/
	$album_copyright = '<div align="center" class="gensmall" style="font-family: Verdana, Arial, Helvetica, sans-serif; letter-spacing: -1px">';
	$album_copyright .= 'Photo Album Powered by:&nbsp;<a href="http://www.icyphoenix.com" target="_blank">Mighty Gorgon</a> Full Album Pack ' . $album_config['fap_version'] . '&nbsp;&copy;&nbsp;2007<br />';
	$album_copyright .= '[based on <a href="http://smartor.is-root.com" target="_blank">Smartor</a> Photo Album plus IdleVoid\'s Album CH &amp; CLowN SP1]';
	$album_copyright .= '</div>';
}
else
{
	/*
	$album_copyright = '<div align="center" class="gensmall" style="font-family: Verdana, Arial, Helvetica, sans-serif; letter-spacing: -1px"><b>Photo Album Powered by:</b>&nbsp;';
	$album_copyright .= 'Photo Album 2' . $album_config['album_version'] . '&nbsp;<a href="http://smartor.is-root.com" target="_blank">Smartor</a>&nbsp;-&nbsp;';
	$album_copyright .= 'CLowN SP1 Addon 1.5.1&nbsp;-&nbsp;';
	$album_copyright .= 'IdleVoid\'s Album CH 1.3.0&nbsp;-&nbsp;';
	$album_copyright .= '<a href="http://www.mightygorgon.com" target="_blank">Mighty Gorgon</a> Full Album Pack ' . $album_config['fap_version'];
	$album_copyright .= '</div>';
	*/
	$album_copyright = '<div align="center" class="gensmall" style="font-family: Verdana, Arial, Helvetica, sans-serif; letter-spacing: -1px">';
	$album_copyright .= 'Photo Album Powered by:&nbsp;<a href="http://www.icyphoenix.com" target="_blank">Mighty Gorgon</a> Full Album Pack ' . $album_config['fap_version'] . '&nbsp;&copy;&nbsp;2007';
	$album_copyright .= '&nbsp;[based on <a href="http://smartor.is-root.com" target="_blank">Smartor</a> Photo Album plus IdleVoid\'s Album CH &amp; CLowN SP1]';
	$album_copyright .= '</div>';
}

$preview_lb_div = '';
if ($album_config['lb_preview'])
{
	// Mighty Gorgon: currently disabled...
	/*
	$preview_lb_div = '<script type="text/javascript" src="templates/common/album/fap_loader.js"></script>';
	$preview_lb_div .= '<div id="preview_div" style="display: none; position: absolute; z-index: 110; left: -600px; top: -600px;">';
	$preview_lb_div .= '	<div class="border_preview" style="width: ' . $album_config['midthumb_width'] . 'px; height: ' . $album_config['midthumb_height'] . 'px;">';
	$preview_lb_div .= '		<div id="loader_container" style="display: none; visibility: hidden;">';
	$preview_lb_div .= '			<div id="loader">';
	$preview_lb_div .= '				<div align="center">Loading preview...</div>';
	$preview_lb_div .= '				<div id="loader_bg">';
	$preview_lb_div .= '					<div id="progress" style="left: 96px; width: 16px;"></div>';
	$preview_lb_div .= '				</div>';
	$preview_lb_div .= '			</div>';
	$preview_lb_div .= '		</div>';
	$preview_lb_div .= '		Preview';
	$preview_lb_div .= '		<div class="preview_temp_load">';
	$preview_lb_div .= '			<img onload="javascript:remove_loading();" src="" alt="" />';
	$preview_lb_div .= '		</div>';
	$preview_lb_div .= '	</div>';
	$preview_lb_div .= '</div>';
	$preview_lb_div .= '<br /><br />';
	*/
}

include_once(ALBUM_MOD_PATH . 'album_functions.' . PHP_EXT);
include_once(ALBUM_MOD_PATH . 'album_hierarchy_functions.' . PHP_EXT);

$album_search_box = '<form name="search" action="' . append_sid(album_append_uid('album_search.' . PHP_EXT)) . '">';
$album_search_box .= '	<span class="gensmall">' . $lang['Search'] . ':&nbsp;</span>';
$album_search_box .= '	<select name="mode">';
$album_search_box .= '		<option value="user">' . $lang['Username'] . '</option>';
$album_search_box .= '		<option value="name">' . $lang['Pic_Name'] . '</option>';
$album_search_box .= '		<option value="desc">' . $lang['Description'] . '</option>';
$album_search_box .= '		<option value="name_desc">' . $lang['Title_Description'] . '</option>';
$album_search_box .= '	</select>';
$album_search_box .= '	' . $lang['Search_Contents'];
$album_search_box .= '	<input class="post" type="text" name="search" maxlength="30" />&nbsp;&nbsp;';
$album_search_box .= '	<input class="liteoption" type="submit" value="' . $lang['Go'] . '" />';
$album_search_box .= '</form>';

$template->assign_vars(array(
	'IMG_ALBUM_FOLDER' => $images['pm_outbox'],
	'IMG_ALBUM_SUBFOLDER' => $images['pm_inbox'],
	'IMG_ALBUM_FOLDER_SMALL' => $images['topic_nor_read'],
	'IMG_ALBUM_FOLDER_SMALL_NEW' => $images['topic_nor_unread'],
	'IMG_ALBUM_SUBFOLDER_SMALL' => $images['icon_minipost'],
	'IMG_ALBUM_SUBFOLDER_SMALL_NEW' => $images['icon_minipost_new'],
	'IMG_ALBUM_FOLDER_NEW' => $images['pm_savebox'],
	'IMG_ALBUM_FOLDER_SS' => $images['pm_sentbox'],
	'IMG_SLIDESHOW' => $images['icon_latest_reply'],
	'IMG_SLIDESHOW_NEW' => $images['icon_newest_reply'],

	'ALBUM_SEARCH_BOX' => $album_search_box,

	'THUMB_SIZE' => $thumb_size,
	'MIDTHUMB_W' => $album_config['midthumb_width'],
	'MIDTHUMB_H' => $album_config['midthumb_height'],
	'PREVIEW_LB_DIV' => $preview_lb_div,

	'U_ALBUM_SEARCH' => append_sid(album_append_uid('album_search.' . PHP_EXT)),
	'U_ALBUM_UPLOAD' => append_sid(album_append_uid('album_upload.' . PHP_EXT)),

	'ALBUM_VERSION' => '2' . $album_config['album_version'],
	'ALBUM_COPYRIGHT' => $preview_lb_div . $album_copyright
	)
);

?>