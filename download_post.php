<?php
/**
*
* @package Icy Phoenix
* @version $Id: download_post.php 98 2009-05-03 09:57:48Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);
include_once(IP_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);

// Start session management
$userdata = session_pagestart($user_ip);
init_userprefs($userdata);
// End session management

$post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
$code_id = isset($_GET['item']) ? intval($_GET['item']) : 0;

if(!$post_id)
{
	message_die(GENERAL_MESSAGE, 'Topic_post_not_exist');
}

// get post
$sql = "SELECT * FROM " . POSTS_TABLE . " WHERE post_id='{$post_id}'";
if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_MESSAGE, 'Topic_post_not_exist');
}
if (($postrow = $db->sql_fetchrow($result)) === false)
{
	message_die(GENERAL_MESSAGE, 'Topic_post_not_exist');
}
$db->sql_freeresult($result);

// prepare variables
$code_filename = '';
$code_text = '';
define('EXTRACT_CODE', $code_id);

// compile post
$bbcode->allow_bbcode = true;
$bbcode->allow_smilies = $board_config['allow_smilies'] && $postrow['user_allowsmile'] ? true : false;
$GLOBALS['code_post_id'] = $postrow['post_id'];
$message = $bbcode->parse($postrow['post_text']);
$GLOBALS['code_post_id'] = 0;

if(!strlen($code_text))
{
	message_die(GENERAL_MESSAGE, 'Topic_post_not_exist');
}

$code_text = undo_htmlspecialchars($code_text, true);

if(empty($code_filename))
{
	$code_filename = 'code_' . $post_id . ($code_id ? '_' . $code_id : '') . '.txt';
}

header('Content-Type: application/zip');
header('Content-Length: ' . strlen($code_text));
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Disposition: attachment; filename="' . $code_filename . '"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo $code_text;

?>