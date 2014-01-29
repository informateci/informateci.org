<?php
/**
*
* @package Icy Phoenix
* @version $Id: drafts.php 110 2009-07-14 08:09:47Z Mighty Gorgon $
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

if ($board_config['allow_drafts'] == false)
{
	message_die(GENERAL_MESSAGE, $lang['Not_Auth_View']);
}

$mode = (isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : ''));
$mode_array = array('loadr', 'loadn', 'loadp', 'delete');
$mode = (in_array($mode, $mode_array) ? $mode : '');

if (!empty($_POST['kill_drafts']))
{
	$mode = 'delete';
}

$start = (isset($_GET['start'])) ? intval($_GET['start']) : 0;
$start = ($start < 0) ? 0 : $start;

if (!$userdata['session_logged_in'])
{
	$redirect = (isset($start)) ? ('&start=' . $start) : '';
	redirect(append_sid(LOGIN_MG . '?redirect=drafts.' . PHP_EXT . $redirect, true));
}

$draft_id = (isset($_POST['d']) ? intval($_POST['d']) : (isset($_GET['d']) ? intval($_GET['d']) : 0));
$draft_id = ($draft_id < 0) ? 0 : $draft_id;

if (($draft_id > 0) || !empty($_POST['kill_drafts']))
{
	if ($mode == 'loadr')
	{
		redirect(append_sid(POSTING_MG . '?d=' . $draft_id . '&mode=reply' . '&draft_mode=draft_load', true));
	}
	elseif ($mode == 'loadn')
	{
		redirect(append_sid(POSTING_MG . '?d=' . $draft_id . '&mode=newtopic' . '&draft_mode=draft_load', true));
	}
	elseif ($mode == 'loadp')
	{
		redirect(append_sid('privmsg.' . PHP_EXT . '?d=' . $draft_id . '&mode=post' . '&draft_mode=draft_load', true));
	}
	elseif ($mode == 'delete')
	{
		if(!isset($_POST['confirm']))
		{
			$page_title = $lang['Drafts'];
			$meta_description = '';
			$meta_keywords = '';
			$nav_server_url = create_server_url();
			$breadcrumbs_address = $lang['Nav_Separator'] . '<a href="' . $nav_server_url . append_sid(PROFILE_MG) . '">' . $lang['Profile'] . '</a>' . $lang['Nav_Separator'] . '<a class="nav-current" href="' . $nav_server_url . append_sid('drafts.' . PHP_EXT) . '">' . $lang['Drafts'] . '</a>';
			include(IP_ROOT_PATH . 'includes/page_header.' . PHP_EXT);

			$ref_url = explode('/', $_SERVER['HTTP_REFERER']);

			$s_hidden_fields = '';

			if (is_array($_POST['drafts_list']))
			{
				for ($i = 0; $i < count($_POST['drafts_list']); $i++)
				{
					$s_hidden_fields .= '<input type="hidden" name="drafts_list[]" value="' . $_POST['drafts_list'][$i] . '" />';
				}
				$s_hidden_fields .= '<input type="hidden" name="kill_drafts" value="true" />';
			}
			$s_hidden_fields .= '<input type="hidden" name="ref_url" value="' . htmlspecialchars($ref_url[count($ref_url) - 1]) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="d" value="' . $draft_id . '" />';
			$s_hidden_fields .= '<input type="hidden" name="mode" value="' . $mode . '" />';

			// Set template files
			$template->set_filenames(array('confirm' => 'confirm_body.tpl'));

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $lang['Confirm'],
				'MESSAGE_TEXT' => $lang['Drafts_Delete_Question'],

				'L_YES' => $lang['Yes'],
				'L_NO' => $lang['No'],

				'S_CONFIRM_ACTION' => append_sid('drafts.' . PHP_EXT),
				'S_HIDDEN_FIELDS' => $s_hidden_fields
				)
			);
			$template->pparse('confirm');
			include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);
			exit();
		}
		else
		{
			if (is_array($_POST['drafts_list']))
			{
				$draft_ids = implode(',', $_POST['drafts_list']);
				$sql_del = "DELETE FROM " . DRAFTS_TABLE . " WHERE draft_id IN (" . $draft_ids . ")";
				if(!$result_del = $db->sql_query($sql_del))
				{
					message_die(GENERAL_ERROR, 'Could not delete drafts', $lang['Error'], __LINE__, __FILE__, $sql);
				}
			}
			else
			{
				$sql_del = "DELETE FROM " . DRAFTS_TABLE . " WHERE draft_id = '" . $draft_id . "'";
				if(!$result_del = $db->sql_query($sql_del))
				{
					message_die(GENERAL_ERROR, 'Could not delete drafts', $lang['Error'], __LINE__, __FILE__, $sql);
				}
			}
		}
	}
}

// Generate the page
$page_title = $lang['Drafts'];
$meta_description = '';
$meta_keywords = '';
$nav_server_url = create_server_url();
$breadcrumbs_address = $lang['Nav_Separator'] . '<a href="' . $nav_server_url . append_sid('profile_main.' . PHP_EXT) . '">' . $lang['Profile'] . '</a>' . $lang['Nav_Separator'] . '<a class="nav-current" href="' . $nav_server_url . append_sid('drafts.' . PHP_EXT) . '">' . $lang['Drafts'] . '</a>';
$breadcrumbs_links_right = '<a href="#" onclick="setCheckboxes(\'drafts_form\', \'drafts_list[]\', true); return false;">' . $lang['Mark_all'] . '</a>&nbsp;&bull;&nbsp;<a href="#" onclick="setCheckboxes(\'drafts_form\', \'drafts_list[]\', false); return false;">' . $lang['Unmark_all'] . '</a>';
include_once(IP_ROOT_PATH . 'includes/users_zebra_block.' . PHP_EXT);
include(IP_ROOT_PATH . 'includes/page_header.' . PHP_EXT);

$template->set_filenames(array('body' => 'drafts_body.tpl'));

$template->assign_vars(array(
	'S_FORM_ACTION' => append_sid('drafts.' . PHP_EXT),
	'L_NO_DRAFTS' => $lang['Drafts_No_Drafts'],
	'L_DRAFTS_CATEGORY' => $lang['Category'],
	'L_DRAFTS_TYPE' => $lang['Drafts_Type'],
	'L_DRAFTS_SUBJECT' => $lang['Drafts_Subject'],
	'L_DRAFTS_ACTION' => $lang['Drafts_Action'],
	'L_DRAFTS_DELETE_SEL' => $lang['Drafts_Delete_Sel'],
	'L_DRAFTS_LOAD' => $lang['Drafts_Load'],
	'L_DRAFTS_DELETE' => $lang['Delete'],
	'L_CHECK_ALL' => $lang['Check_All'],
	'L_UNCHECK_ALL' => $lang['UnCheck_All'],
	)
);

$sql = "SELECT COUNT(*) as drafts_count FROM " . DRAFTS_TABLE . " d WHERE d.user_id = " . $userdata['user_id'];
if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not obtain drafts information', '', __LINE__, __FILE__, $sql);
}
$row = $db->sql_fetchrow($result);
$drafts_count = ($row['drafts_count']) ? $row['drafts_count'] : 0;
$db->sql_freeresult($result);
$no_drafts = ($drafts_count == 0) ? true : false;

//die(($no_drafts == false) ? 'FALSE' : 'TRUE');

if ($no_drafts == false)
{
	$sql = "SELECT d.*
		FROM " . DRAFTS_TABLE . " d
		WHERE d.user_id = '" . $userdata['user_id'] . "'
		ORDER BY d.save_time DESC
		LIMIT $start, " . $board_config['topics_per_page'];

	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not obtain drafts', '', __LINE__, __FILE__, $sql);
	}
	$draft_row = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < count($draft_row); $i++)
	{
		if ($i == 0)
		{
			$template->assign_block_vars('switch_drafts', array());
		}
		$draft_row[$i]['draft_cat'] = '';
		$draft_row[$i]['draft_title'] = '';
		$draft_load = '';
		$draft_type = '';
		$draft_cat_link = '';
		$draft_title_link = '';
		if ($draft_row[$i]['topic_id'] != 0)
		{
			$sql_d = "SELECT t.*, f.*
				FROM " . TOPICS_TABLE . " t,
					" . FORUMS_TABLE . " f
				WHERE t.topic_id = '" . $draft_row[$i]['topic_id'] . "'
					AND f.forum_id = t.forum_id
				LIMIT 1";
			if (!($result_d = $db->sql_query($sql_d)))
			{
				message_die(GENERAL_ERROR, 'Could not obtain topic info', '', __LINE__, __FILE__, $sql_d);
			}
			$draft_row_data = $db->sql_fetchrow($result_d);
			$db->sql_freeresult($result_d);
			$draft_image = '<img src="' . $images['topic_nor_read'] . '" alt="" />';
			$draft_type = $lang['Drafts_NM'];
			$draft_load = 'loadr';
			$draft_cat_link = append_sid(IP_ROOT_PATH . VIEWFORUM_MG . '?' . POST_FORUM_URL . '=' . $draft_row_data['forum_id']);
			$draft_title_link = append_sid(IP_ROOT_PATH . VIEWTOPIC_MG . '?'  . POST_TOPIC_URL . '=' . $draft_row[$i]['topic_id']);
			$draft_row[$i]['draft_cat'] = '<a href="' . $draft_cat_link . '">' . $draft_row_data['forum_name'] . '</a>';
			$draft_row[$i]['draft_title'] = '<a href="' . $draft_title_link . '">' . $draft_row_data['topic_title'] . '</a>';
		}
		elseif ($draft_row[$i]['forum_id'] != 0)
		{
			$sql_d = "SELECT f.*
				FROM " . FORUMS_TABLE . " f
				WHERE f.forum_id = '" . $draft_row[$i]['forum_id'] . "'
				LIMIT 1";
			if (!($result_d = $db->sql_query($sql_d)))
			{
				message_die(GENERAL_ERROR, 'Could not obtain topic info', '', __LINE__, __FILE__, $sql_d);
			}
			$draft_row_data = $db->sql_fetchrow($result_d);
			$db->sql_freeresult($result_d);
			$draft_image = '<img src="' . $images['topic_nor_unread'] . '" alt="" />';
			$draft_type = $lang['Drafts_NT'];
			$draft_load = 'loadn';
			$draft_cat_link = append_sid(IP_ROOT_PATH . VIEWFORUM_MG . '?' . POST_FORUM_URL . '=' . $draft_row_data['forum_id']);
			$draft_title_link = append_sid(IP_ROOT_PATH . 'drafts.' . PHP_EXT . '?mode=' . $draft_load . '&amp;d=' . $draft_row[$i]['draft_id']);
			$draft_row[$i]['draft_cat'] = '<a href="' . $draft_cat_link . '">' . $draft_row_data['forum_name'] . '</a>';
			$draft_row[$i]['draft_title'] = '<a href="' . $draft_title_link . '">' . $draft_row[$i]['draft_subject'] . '</a>';
		}
		else
		{
			$draft_image = '<img src="' . $images['topic_nor_read'] . '" alt="" />';
			$draft_type = $lang['Drafts_NPM'];
			$draft_load = 'loadp';
			$draft_cat_link = append_sid(IP_ROOT_PATH . 'privmsg.' . PHP_EXT);
			$draft_title_link = append_sid(IP_ROOT_PATH . 'drafts.' . PHP_EXT . '?mode=' . $draft_load . '&amp;d=' . $draft_row[$i]['draft_id']);
			$draft_row[$i]['draft_cat'] = '<a href="' . $draft_cat_link . '">' . $lang['Drafts_NPM'] . '</a>';
			$draft_row[$i]['draft_title'] = '<a href="' . $draft_title_link . '">' . $draft_row[$i]['draft_subject'] . '</a>';
		}

		$row_class = (!($i % 2)) ? $theme['td_class1'] : $theme['td_class2'];
		$template->assign_block_vars('draft_row', array(
			'ROW_CLASS' => $row_class,
			'S_DRAFT_ID' => $draft_row[$i]['draft_id'],
			'DRAFT_IMG' => $draft_image,
			'DRAFT_TYPE' => $draft_type,
			'DRAFT_CAT_LINK' => $draft_cat_link,
			'DRAFT_CAT' => $draft_row[$i]['draft_cat'],
			'DRAFT_TITLE_LINK' => $draft_title_link,
			'DRAFT_TITLE' => ip_stripslashes($draft_row[$i]['draft_title']),
			'DRAFT_TIME' => create_date_ip($board_config['default_dateformat'], $draft_row[$i]['save_time'], $board_config['board_timezone']),
			'U_DRAFT_LOAD' => append_sid(IP_ROOT_PATH . 'drafts.' . PHP_EXT . '?mode=' . $draft_load . '&amp;d=' . $draft_row[$i]['draft_id']),
			'U_DRAFT_DELETE' => append_sid(IP_ROOT_PATH . 'drafts.' . PHP_EXT . '?mode=delete&amp;d=' . $draft_row[$i]['draft_id']),
			)
		);
	}
	$pagination = generate_pagination('drafts.' . PHP_EXT . '?mode=list', $drafts_count, $board_config['topics_per_page'], $start);

	$template->assign_vars(array(
		'PAGINATION' => $pagination,
		'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / $board_config['topics_per_page']) + 1), ceil($drafts_count / $board_config['topics_per_page'])),
		'L_GOTO_PAGE' => $lang['Goto_page']
		)
	);
}
else
{
	$template->assign_block_vars('switch_no_drafts', array());
}

$template->pparse('body');

include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);

?>