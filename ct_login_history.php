<?php
/**
*
* @package Icy Phoenix
* @version $Id: ct_login_history.php 49 2008-09-14 20:36:03Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* This file is for the Login History each User can see for his Account.
*
* @author Christian Knerr (cback)
* @package ctracker
* @version 5.0.0
* @since 17.08.2006 - 02:42:16
* @copyright (c) 2006 www.cback.de
*
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);


// Start session management
$userdata = session_pagestart($user_ip);
init_userprefs($userdata);
// End session management


// session id check
if (!empty($_POST['sid']) || !empty($_GET['sid']))
{
	$sid = (!empty($_POST['sid'])) ? $_POST['sid'] : $_GET['sid'];
}
else
{
	$sid = '';
}

// Ensure that a user is logged in and the feature is available
if ( !$userdata['session_logged_in'] )
{
	message_die(GENERAL_MESSAGE, $lang['ctracker_lhistory_err']);
}


// Include the page_header
$page_title = $lang['ctracker_lhistory_nav'];
$meta_description = '';
$meta_keywords = '';
include(IP_ROOT_PATH . 'includes/page_header.' . PHP_EXT);


// Define the Template for this file
$template->set_filenames(array('body' => 'ctracker_login_history.tpl'));


// Output Login History
if ( $ctracker_config->settings['login_history'] )
{
	$sql = 'SELECT * FROM ' . CTRACKER_LOGINHISTORY . ' WHERE ct_user_id=' . $userdata['user_id'] . ' ORDER BY ct_login_time DESC';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, $lang['ctracker_error_database_op'], '', __LINE__, __FILE__, $sql);
	}

	$count = 0;

	while ( $row = $db->sql_fetchrow($result) )
	{
		$count++;

		$template->assign_block_vars('login_output', array(
				'ROW_CLASS'	=> ($count % 2 == 0)? $theme['td_class1'] : $theme['td_class2'],
				'VALUE_1'		=> $count,
				'VALUE_2'		=> date($userdata['user_dateformat'], $row['ct_login_time']),
				'VALUE_3'		=> $row['ct_login_ip'])
		);
	}
}


// Output settings for Login Checker
if ( $ctracker_config->settings['login_ip_check'] == 1 )
{

	$sel1 = '';
	$sel2 = '';

	if ( $_POST['submit'] )
	{
		$newsetting = intval($_POST['ct_enable_ip_warn']);
		$sql = 'UPDATE ' . USERS_TABLE . ' SET ct_enable_ip_warn=' . $newsetting . ' WHERE user_id=' . $userdata['user_id'];
		$userdata['ct_enable_ip_warn'] = $newsetting;
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, $lang['ctracker_error_database_op'], '', __LINE__, __FILE__, $sql);
		}
	}

	($userdata['ct_enable_ip_warn'] == 1)? $sel1 = ' checked="checked"' : $sel2 = ' checked';

	$template->assign_block_vars('log_set', array(
			'S_FORM_ACTION'	=> append_sid('ct_login_history.' . PHP_EXT),
			'L_HEADER_TEXT'	=> $lang['ctracker_ipwarn_prof'],
			'L_DESC'				=> $lang['ctracker_ipwarn_pdes'],
			'L_ON'					=> $lang['ctracker_settings_on'],
			'L_OFF'					=> $lang['ctracker_settings_off'],
			'L_SEND'				=> $lang['ctracker_ipwarn_send'],
			'S_SELECT_ON'		=> $sel1,
			'S_SELECT_OFF'	=> $sel2,
			'IMG_ICON'			=> $images['ctracker_log_manager'])
	);
}


// Send some vars to the template
$template->assign_vars(array(
	'L_HEADER_TEXT' => $lang['ctracker_lhistory_h'],
	'L_DESCRIPTION' => ($ctracker_config->settings['login_history'] == 1) ? sprintf($lang['ctracker_lhistory_i'], $ctracker_config->settings['login_history_count']) : $lang['ctracker_lhistory_off'],
	'L_TABLEHEAD_1' => $lang['ctracker_lhistory_h1'],
	'L_TABLEHEAD_2' => $lang['ctracker_lhistory_h2'])
);


// Generate the page
$template->pparse('body');


// Include the page_tail.php file
include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);


?>