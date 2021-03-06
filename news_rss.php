<?php
/**
*
* @package Icy Phoenix
* @version $Id: news_rss.php 49 2008-09-14 20:36:03Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* CodeMonkeyX.net (webmaster@codemonkeyx.net)
*
*/

// CTracker_Ignore: File Checked By Human
// Added to optimize memory for attachments
define('ATTACH_DISPLAY', true);
define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);

if( $board_config['allow_rss'] != 1 )
{
	echo 'RSS has been disabled for this site';
	return;
}

// Start session management
$userdata = session_pagestart($user_ip);
init_userprefs($userdata);
// End session management

include_once(IP_ROOT_PATH . 'includes/news.' . PHP_EXT);

header("Content-type: text/xml");

// Tell the template class which template to use.
$template->set_filenames(array('news' => 'news_200_rss_body.tpl'));

$content =& new NewsModule(IP_ROOT_PATH);

$content->setVariables( array(
	'L_INDEX' => $lang['Index'],
	'L_CATEGORIES' => $lang['Categories'],
	'L_ARCHIVES' => $lang['Archives']
	)
);

$content->renderSyndication();

$content->display();
$content->clear();

?>