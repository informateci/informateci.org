<?php
/**
*
* @package Icy Phoenix
* @version $Id: admin_rate.php 49 2008-09-14 20:36:03Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Nivisec.com (support@nivisec.com)
*
*/

define('IN_ICYPHOENIX', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['1700_Topic_Rating']['Configuration'] = $filename . '?mode=config';
	$module['1700_Topic_Rating']['Permissions'] = $filename . '?mode=auth';
	return;
}

if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
require('./pagestart.' . PHP_EXT);
include(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/lang_rate.' . PHP_EXT);
require(IP_ROOT_PATH . 'includes/functions_rate.php');

if( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
}
else
{
	$mode = "";
}

if( isset($_POST['admin_message']) || isset($_GET['admin_message']) )
{
	$admin_message = ( isset($_POST['admin_message']) ) ? $_POST['admin_message'] : $_GET['admin_message'];
}
else
{
	$admin_message = "";
}

//Begin Config Mode
if ($mode == 'config')
{
	$configs_name = array(
	allow_ext_rating,
	rating_max,
	allow_rerate,
	check_anon_ip_when_rating,
	min_rates_number,
	index_rating_return,
	large_rating_return_limit,
	header_rating_return_limit);

	$configs_desc = array(
	$lang['Allow_Detailed_Ratings_Page'],
	$lang['Max_Rating'],
	$lang['Allow_Users_To_ReRate'],
	$lang['Check_Anon_IP'],
	$lang['Min_Rates'],
	$lang['Main_Page_Number'],
	$lang['Big_Page_Number'],
	$lang['Header_Page_Number']);

	//Update config values if needed
	for($i = 0; $i < count($configs_name); $i++)
	{
		if( isset($_POST[$configs_name[$i]]) || isset($_GET[$configs_name[$i]]) )
		{
			$var_value = ( isset($_POST[$configs_name[$i]]) ) ? $_POST[$configs_name[$i]] : $_GET[$configs_name[$i]];
			if ($var_value != $board_config[$configs_name[$i]])
			{
				$sql = "UPDATE " . CONFIG_TABLE . "
				SET config_value =" . $var_value . "
				WHERE config_name ='" . $configs_name[$i] . "'";
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Error updating rating config data.", "", __LINE__, __FILE__, $sql);
				}
				$admin_message .= "<br />" . $lang['Update'] . ":&nbsp;&nbsp;&nbsp;" . $configs_desc[$i];
				$board_config[$configs_name[$i]] = $var_value;
			}
		}
	}

	//$allow_disable_yes = ( $board_config['allow_disable'] != '0' ) ? 'checked="checked"' : '';
	//$allow_disable_no  = ( $board_config['allow_disable'] == '0' ) ? 'checked="checked"' : '';
	//$allow_disable = '<input type="radio" name="allow_disable" value="1" '. $allow_disable_yes . ' /> ' . $lang['Yes'] . '&nbsp;&nbsp;<input type="radio" name="allow_disable" value="0" '. $allow_disable_no . ' /> ' . $lang['No'];
	$allow_ext_rating_yes = ( $board_config['allow_ext_rating'] != '0' ) ? 'checked="checked"' : '';
	$allow_ext_rating_no  = ( $board_config['allow_ext_rating'] == '0' ) ? 'checked="checked"' : '';
	$allow_ext_rating = '<input type="radio" name="allow_ext_rating" value="1" '. $allow_ext_rating_yes . ' /> ' . $lang['Yes'] . '&nbsp;&nbsp;<input type="radio" name="allow_ext_rating" value="0" '. $allow_ext_rating_no . ' /> ' . $lang['No'];
	$allow_rerate_yes = ( $board_config['allow_rerate'] != '0' ) ? 'checked="checked"' : '';
	$allow_rerate_no  = ( $board_config['allow_rerate'] == '0' ) ? 'checked="checked"' : '';
	$allow_rerate = '<input type="radio" name="allow_rerate" value="1" '. $allow_rerate_yes . ' /> ' . $lang['Yes'] . '&nbsp;&nbsp;<input type="radio" name="allow_rerate" value="0" '. $allow_rerate_no . ' /> ' . $lang['No'];
	$max_rating = '<input class="post" type="text" size="3" maxlength="3" name="rating_max" value="'. $board_config['rating_max'] . '" />';
	$hidden_submits = '<input type="hidden" name="mode" value="config" />';
	$check_anon_ip_yes = ( $board_config['check_anon_ip_when_rating'] != '0' ) ? 'checked="checked"' : '';
	$check_anon_ip_no  = ( $board_config['check_anon_ip_when_rating'] == '0' ) ? 'checked="checked"' : '';
	$check_anon_ip = '<input type="radio" name="check_anon_ip_when_rating" value="1" '. $check_anon_ip_yes . ' /> ' . $lang['Yes'] . '&nbsp;&nbsp;<input type="radio" name="check_anon_ip_when_rating" value="0" '. $check_anon_ip_no . ' /> ' . $lang['No'];
	$main_page_number = '<input class="post" type="text" size="5" maxlength="10" name="index_rating_return" value="'. $board_config['index_rating_return'] . '" />';
	$header_page_number = '<input class="post" type="text" size="5" maxlength="10" name="header_rating_return_limit" value="'. $board_config['header_rating_return_limit'] . '" />';
	$big_page_number = '<input class="post" type="text" size="5" maxlength="10" name="large_rating_return_limit" value="'. $board_config['large_rating_return_limit'] . '" />';
	$min_rates_number = '<input class="post" type="text" size="5" maxlength="10" name="min_rates_number" value="'. $board_config['min_rates_number'] . '" />';

	$configs_sumbits = array(
	$allow_ext_rating,
	$max_rating,
	$allow_rerate,
	$check_anon_ip,
	$min_rates_number,
	$main_page_number,
	$big_page_number,
	$header_page_number);

	//Set Configs for template
	for($i = 0; $i < count($configs_sumbits); $i++)
	{
		$template->assign_block_vars('config_row', array(
			'S_CONFIG' => $configs_sumbits[$i],
			'L_CONFIG' => $configs_desc[$i]
			)
		);
	}

	$template->set_filenames(array('body' => ADM_TPL . 'rate_config_body.tpl'));
}
//End Config Mode
//Begin Auth Mode
elseif ($mode == 'auth')
{
	$forum_auth_levels = array('NONE', 'ALL', 'REG', 'PRIVATE', 'MOD', 'ADMIN');
	$forum_auth_const = array(-1, AUTH_ALL, AUTH_REG, AUTH_ACL, AUTH_MOD, AUTH_ADMIN);
	$forum_auth_desc = array($lang['NONE'], $lang['ALL'], $lang['REG'], $lang['PRIVATE'], $lang['MOD'], $lang['ADMIN']);

	$page_title = $lang['Forum'] . ' ' . $lang['Authorization'];

	$sql = "SELECT forum_id, forum_name, auth_rate
	FROM " . FORUMS_TABLE;
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "Error getting forum data.", "", __LINE__, __FILE__, $sql);
	}
	$forum_row = $db->sql_fetchrowset($result);

	//Get a list of forums (can't use function here, need ALL for admin)
	$sql = "SELECT topic_id
	FROM " . RATINGS_TABLE;
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "Error getting forum data.", "", __LINE__, __FILE__, $sql);
	}
	$topics_row = $db->sql_fetchrowset($result);

	$hidden_submits = '<input type="hidden" name="mode" value="auth" />';

	//Purge if option selected
	if( isset($_POST['forum_purge']) || isset($_GET['forum_purge']) )
	{
		//Compare each topic to see if it exists in DB
		for($i = 0; $i < count($topics_row); $i++)
		{
			$sql = "SELECT *
			FROM " . TOPICS_TABLE . "
			WHERE topic_id = " . $topics_row[$i]['topic_id'];
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Error getting topic data.", "", __LINE__, __FILE__, $sql);
			}
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			//If a blank title was returned, we know the topic doesn't exist anymore
			if ( !isset($row['topic_title']) )
			{
				$sql = "DELETE
				FROM " . RATINGS_TABLE . "
				WHERE topic_id = " . $topics_row[$i]['topic_id'];
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Error deleting rating data.", "", __LINE__, __FILE__, $sql);
				}
				$admin_message .= '<br />' . $lang['Purged'] . ':&nbsp;&nbsp;&nbsp;' . $lang['Topic'] . '&nbsp;#&nbsp;&nbsp;' . $topics_row[$i]['topic_id'];
			}
		}
		$admin_message .= '<br />' . $lang['Purge'] . ':&nbsp;&nbsp;&nbsp;' . $lang['Complete'];
	}

	//Clear all the data if option selected
	if( isset($_POST['ratings_clear']) || isset($_GET['ratings_clear']) )
	{
		if( isset($_POST['ratings_clear_confirm']) || isset($_GET['ratings_clear_confirm']) )
		{
			$clear_confirm = ( isset($_POST['ratings_clear_confirm']) ) ? $_POST['ratings_clear_confirm'] : $_GET['ratings_clear_confirm'];
			if ( strtoupper($clear_confirm) == 'YES' )
			{
				$sql = "DELETE
				FROM " . RATINGS_TABLE;
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Error deleting rating data.", "", __LINE__, __FILE__, $sql);
				}
				$sql = "UPDATE " . TOPICS_TABLE . " SET topic_rating = '0'";
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Error deleting rating data.", "", __LINE__, __FILE__, $sql);
				}
				$admin_message .= "<br />" . $lang['Clear'] . ':&nbsp;&nbsp;&nbsp;' . $lang['Complete'];
			}
		}
	}

	for ($x = 0; $x < count($forum_row); $x++)
	{
		$current_auth = $forum_row[$x]['auth_rate'];

		if( isset($_POST['forum_update_id_'. $forum_row[$x]['forum_id']]) || isset($_GET['forum_update_id_'. $forum_row[$x]['forum_id']]) )
		{
			$id_value = ( isset($_POST['forum_update_id_'. $forum_row[$x]['forum_id']]) ) ? $_POST['forum_update_id_'. $forum_row[$x]['forum_id']] : $_GET['forum_update_id_'. $forum_row[$x]['forum_id']];
			$name_value = ( isset($_POST['forum_update_name_'. $forum_row[$x]['forum_id']]) ) ? $_POST['forum_update_name_'. $forum_row[$x]['forum_id']] : $_GET['forum_update_name_'. $forum_row[$x]['forum_id']];
			$update_value = ( isset($_POST['forum_update_value_'. $forum_row[$x]['forum_id']]) ) ? $_POST['forum_update_value_'. $forum_row[$x]['forum_id']] : $_GET['forum_update_value_'. $forum_row[$x]['forum_id']];
			if ($update_value != $current_auth)
			{
				$sql = "UPDATE " . FORUMS_TABLE . "
				SET auth_rate = " . $update_value . "
				WHERE forum_id = " . $id_value;
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Error updating rating auth data.", "", __LINE__, __FILE__, $sql);
				}
				$admin_message .= '<br />' . $lang['Update'] . ':&nbsp;&nbsp;&nbsp;' . $lang['Forum'] . '&nbsp;' . $name_value;
				$current_auth = $update_value;
			}
		}

		$hidden_submits .= '<input type="hidden" name="forum_update_id_'. $forum_row[$x]['forum_id'] . '" value="' . $forum_row[$x]['forum_id'] . '" /><input type="hidden" name="forum_update_name_'. $forum_row[$x]['forum_id'] . '" value="' . strip_tags($forum_row[$x]['forum_name']) . '" />';

		$select_auth_mode = '<select name="forum_update_value_' . $forum_row[$x]['forum_id'] . '">';
		for($i = 0; $i < count($forum_auth_levels); $i++)
		{
			$selected = ($current_auth == $forum_auth_const[$i]) ? ' selected="selected"' : '';
			$select_auth_mode .= '<option value="' . $forum_auth_const[$i] . '"' . $selected . '>' . $forum_auth_levels[$i] . '</option>';
		}
		$select_auth_mode .= '</select>';

		$template->assign_block_vars('forums_row', array(
			'FORUM_NAME' => $forum_row[$x]['forum_name'],
			'S_FORUM_AUTH' => $select_auth_mode
			)
		);
	}

	$template->set_filenames(array('body' => ADM_TPL . 'rate_auth_body.tpl'));

	//Set Description Part
	for($i = 0; $i < count($forum_auth_levels); $i++)
	{
		$template->assign_block_vars('descrow', array(
			'L_AUTH_TYPE' => $forum_auth_levels[$i],
			'L_AUTH_DESC' => $forum_auth_desc[$i]
			)
		);
	}

	//Set Options Part
	$options_types = array($lang['Purge'], $lang['Clear']);
	$options_sumbits = array('<input type="checkbox" name="forum_purge" />', '<input type="checkbox" name="ratings_clear" />&nbsp;&nbsp;<input class="post" type="text" size="3" maxlength="3" name="ratings_clear_confirm" value="NO" />');
	$options_desc = array($lang['Purge_Desc'], $lang['Clear_Desc']);

	for($i = 0; $i < count($options_types); $i++)
	{
		$template->assign_block_vars('optionrow', array(
			'L_OPT_TYPE' => $options_types[$i],
			'S_OPT_PART' => $options_sumbits[$i],
			'L_OPT_DESC' => $options_desc[$i]
			)
		);
	}
}
//End Auth Mode
else
{
	print 'No mode specified';
}

//Assign page wide vars
$template->assign_vars(array(
	'ADMIN_MESSAGE' => $admin_message . '<br />' . create_date($board_config['default_dateformat'], time(), $board_config['board_timezone']),
	'CLASS_1' => $theme['td_class1'],
	'CLASS_2' => $theme['td_class2'],

	'S_MODE_ACTION' => append_sid($filename),
	'S_HIDDEN_FIELDS' => $hidden_submits,
	'S_MASS_UPDATE' => $mass_auth_mode,

	'L_SUBMIT' => $lang['Update'],
	'L_RESET' => $lang['Reset'],
	'L_MASS_UPDATE' => $lang['Purge'],
	'L_STATUS' => $lang['Status'],

	'L_AUTH_DESCRIPTION' => $lang['Auth_Description'],
	'L_PERMISSIONS' => $lang['Permissions'],
	'L_FORUM' => $lang['Forum'],
	'L_OPTIONS' => $lang['Options'],
	'L_PAGE_NAME' => $page_title
	)
);

$template->pparse('body');
include('page_footer_admin.' . PHP_EXT);

?>