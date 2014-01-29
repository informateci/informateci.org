<?php
/**
*
* @package Icy Phoenix
* @version $Id: birthday_popup.php 61 2008-10-30 09:25:26Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);

// Start session management
$userdata = session_pagestart($user_ip);
init_userprefs($userdata);
// End session management

$gen_simple_header = true;
$page_title = $lang['Greeting_Messaging'];
$meta_description = '';
$meta_keywords = '';
include(IP_ROOT_PATH . 'includes/page_header.' . PHP_EXT);

$year = create_date('Y', time(), $board_config['board_timezone']);
$date_today = create_date('Ymd', time(), $board_config['board_timezone']);
$user_birthday = realdate('md', $userdata['user_birthday']);
$user_birthday2 = (($year . $user_birthday < $date_today) ? ($year + 1) : $year) . $user_birthday;
$l_greeting = ($user_birthday2 == $date_today) ? sprintf($lang['Birthday_greeting_today'], date('Y') - realdate('Y', $userdata['user_birthday'])) : sprintf($lang['Birthday_greeting_prev'], date('Y') - realdate('Y', $userdata['user_birthday']), realdate(str_replace('Y', '', $lang['DATE_FORMAT_BIRTHDAY']), $userdata['user_birthday']));

$template->set_filenames(array('body' => 'greeting_popup.tpl'));

$template->assign_vars(array(
	'L_CLOSE_WINDOW' => $lang['Close_window'],
	'L_MESSAGE' => $l_greeting
	)
);

$template->pparse('body');

include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);

?>