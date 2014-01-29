<?php
/**
*
* @package Icy Phoenix
* @version $Id: admin_groups.php 76 2009-01-31 21:11:24Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Icy Phoenix is based on phpBB
* @copyright (c) 2008 phpBB Group
*
*/

define('IN_ICYPHOENIX', true);

if (!empty($setmodules))
{
	$filename = basename(__FILE__);
	$module['1620_Groups']['110_Manage_Groups'] = $filename;
	return;
}

// Load default header
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include_once(IP_ROOT_PATH . 'includes/functions_groups.' . PHP_EXT);
require('./pagestart.' . PHP_EXT);

if (isset($_POST[POST_GROUPS_URL]) || isset($_GET[POST_GROUPS_URL]))
{
	$group_id = (isset($_POST[POST_GROUPS_URL])) ? intval($_POST[POST_GROUPS_URL]) : intval($_GET[POST_GROUPS_URL]);
}
else
{
	$group_id = 0;
}

if (isset($_POST['mode']) || isset($_GET['mode']))
{
	$mode = (isset($_POST['mode'])) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = '';
}

attachment_quota_settings('group', $_POST['group_update'], $mode);

if (isset($_POST['edit']) || isset($_GET['edit']) || isset($_POST['new']))
{
	//
	// Ok they are editing a group or creating a new group
	//
	$template->set_filenames(array('body' => ADM_TPL . 'group_edit_body.tpl'));

	if (isset($_POST['edit']) || isset($_GET['edit']))
	{
		//
		// They're editing. Grab the vars.
		//
		$sql = "SELECT *
			FROM " . GROUPS_TABLE . "
			WHERE group_single_user <> " . TRUE . "
			AND group_id = $group_id";
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Error getting group information', '', __LINE__, __FILE__, $sql);
		}

		if (!($group_info = $db->sql_fetchrow($result)))
		{
			message_die(GENERAL_MESSAGE, $lang['Group_not_exist']);
		}

		$mode = 'editgroup';
		$template->assign_block_vars('group_edit', array());

	}
	elseif (isset($_POST['new']))
	{
		$group_info = array (
			'group_name' => '',
			'group_description' => '',
			'group_moderator' => '',
			'group_rank' => '0',
			'group_color' => '',
			'group_legend' => '1',
			'group_count' => '99999999',
			'group_count_max' => '99999999',
			'group_count_enable' => '0',
			'group_type' => GROUP_OPEN
		);
		$group_open = ' checked="checked"';

		$mode = 'newgroup';
	}

	//
	// Ok, now we know everything about them, let's show the page.
	//
	if ($group_info['group_moderator'] != '')
	{
		$sql = "SELECT user_id, username
			FROM " . USERS_TABLE . "
			WHERE user_id = " . $group_info['group_moderator'];
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not obtain user info for moderator list', '', __LINE__, __FILE__, $sql);
		}

		if (!($row = $db->sql_fetchrow($result)))
		{
			message_die(GENERAL_ERROR, 'Could not obtain user info for moderator list', '', __LINE__, __FILE__, $sql);
		}

		$group_moderator = $row['username'];
	}
	else
	{
		$group_moderator = '';
	}

	$sql = "SELECT * FROM " . RANKS_TABLE . "
		WHERE rank_special = 1
		ORDER BY rank_title";
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not obtain ranks data', '', __LINE__, __FILE__, $sql);
	}

	$rank_select_box = '<option value="0">' . $lang['No_Rank_Special'] . '</option>';
	while($row = $db->sql_fetchrow($result))
	{
		$rank = $row['rank_title'];
		$rank_id = $row['rank_id'];
		$selected = ($group_info['group_rank'] == $rank_id) ? ' selected="selected"' : '';
		$rank_select_box .= '<option value="' . $rank_id . '"' . $selected . '>' . $rank . '</option>';
	}

	$group_info['group_color'] = check_valid_color($group_info['group_color']);
	$group_open = ($group_info['group_type'] == GROUP_OPEN) ? ' checked="checked"' : '';
	$group_closed = ($group_info['group_type'] == GROUP_CLOSED) ? ' checked="checked"' : '';
	$group_hidden = ($group_info['group_type'] == GROUP_HIDDEN) ? ' checked="checked"' : '';
	$group_count_enable_checked = ($group_info['group_count_enable']) ? ' checked="checked"' : '';
	$group_legend_checked = ($group_info['group_legend'] == 1) ? ' checked="checked"' : '';

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" />';

	$template->assign_vars(array(
		'GROUP_NAME' => $group_info['group_name'],
		'GROUP_DESCRIPTION' => $group_info['group_description'],
		'GROUP_MODERATOR' => $group_moderator,
		'RANK_SELECT_BOX' => $rank_select_box,
		'GROUP_RANK' => $group_info['group_rank'],
		'GROUP_COLOR' => ($group_info['group_color'] ? str_replace('#', '', $group_info['group_color']) : ''),
		'GROUP_COLOR_STYLE' => ($group_info['group_color'] ? ' style="color:' . $group_info['group_color'] . ';font-weight:bold;"' : ' style="font-weight:bold;"'),
		'GROUP_LEGEND' => $group_info['group_legend'],
		'GROUP_LEGEND_CHECKED' => $group_legend_checked,
		'GROUP_COUNT' => $group_info['group_count'],
		'GROUP_COUNT_MAX' => $group_info['group_count_max'],
		'GROUP_COUNT_ENABLE_CHECKED' => $group_count_enable_checked,

		'L_GROUP_COUNT' => $lang['group_count'],
		'L_GROUP_COUNT_MAX' => $lang['group_count_max'],
		'L_GROUP_COUNT_EXPLAIN' => $lang['group_count_explain'],
		'L_GROUP_COUNT_ENABLE' => $lang['Group_count_enable'],
		'L_GROUP_COUNT_UPDATE' => $lang['Group_count_update'],
		'L_GROUP_COUNT_DELETE' => $lang['Group_count_delete'],
		'L_GROUP_TITLE' => $lang['Group_administration'],
		'L_GROUP_EDIT_DELETE' => (isset($_POST['new'])) ? $lang['New_group'] : $lang['Edit_group'],
		'L_GROUP_NAME' => $lang['group_name'],
		'L_GROUP_DESCRIPTION' => $lang['group_description'],
		'L_GROUP_MODERATOR' => $lang['group_moderator'],
		'L_GROUP_RANK' => $lang['group_rank'],
		'L_GROUP_COLOR' => $lang['group_color'],
		'L_GROUP_LEGEND' => $lang['group_legend'],
		'L_FIND_USERNAME' => $lang['Find_username'],
		'L_GROUP_STATUS' => $lang['group_status'],
		'L_GROUP_OPEN' => $lang['group_open'],
		'L_GROUP_CLOSED' => $lang['group_closed'],
		'L_GROUP_HIDDEN' => $lang['group_hidden'],
		'L_GROUP_DELETE' => $lang['group_delete'],
		'L_GROUP_DELETE_CHECK' => $lang['group_delete_check'],
		'L_EXAMPLE' => $lang['Example'],
		'L_SUBMIT' => $lang['Submit'],
		'L_RESET' => $lang['Reset'],
		'L_DELETE_MODERATOR' => $lang['delete_group_moderator'],
		'L_DELETE_MODERATOR_EXPLAIN' => $lang['delete_moderator_explain'],
		'L_YES' => $lang['Yes'],

		'U_SEARCH_USER' => append_sid('../' . SEARCH_MG . '?mode=searchuser'),

		'S_GROUP_OPEN_TYPE' => GROUP_OPEN,
		'S_GROUP_CLOSED_TYPE' => GROUP_CLOSED,
		'S_GROUP_HIDDEN_TYPE' => GROUP_HIDDEN,
		'S_GROUP_OPEN_CHECKED' => $group_open,
		'S_GROUP_CLOSED_CHECKED' => $group_closed,
		'S_GROUP_HIDDEN_CHECKED' => $group_hidden,
		'S_GROUP_ACTION' => append_sid('admin_groups.' . PHP_EXT),
		'S_HIDDEN_FIELDS' => $s_hidden_fields
		)
	);

	$template->pparse('body');

}
elseif (isset($_POST['group_update']))
{
	// Ok, they are submitting a group, let's save the data based on if it's new or editing
	if (isset($_POST['group_delete']))
	{
		// Reset User Moderator Level

		// Is Group moderating a forum ?
		$sql = "SELECT auth_mod FROM " . AUTH_ACCESS_TABLE . "
			WHERE group_id = " . $group_id;
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not select auth_access', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		if (intval($row['auth_mod']) == 1)
		{
			// Yes, get the assigned users and update their Permission if they are no longer moderator of one of the forums
			$sql = "SELECT user_id FROM " . USER_GROUP_TABLE . "
				WHERE group_id = " . $group_id;
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not select user_group', '', __LINE__, __FILE__, $sql);
			}

			$rows = $db->sql_fetchrowset($result);
			for ($i = 0; $i < count($rows); $i++)
			{
				$sql = "SELECT g.group_id FROM " . AUTH_ACCESS_TABLE . " a, " . GROUPS_TABLE . " g, " . USER_GROUP_TABLE . " ug
				WHERE (a.auth_mod = 1) AND (g.group_id = a.group_id) AND (a.group_id = ug.group_id) AND (g.group_id = ug.group_id)
					AND (ug.user_id = " . intval($rows[$i]['user_id']) . ") AND (ug.group_id <> " . $group_id . ")";
				if (!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Could not obtain moderator permissions', '', __LINE__, __FILE__, $sql);
				}

				if ($db->sql_numrows($result) == 0)
				{
					$sql = "UPDATE " . USERS_TABLE . " SET user_level = " . USER . "
					WHERE user_level = " . MOD . " AND user_id = " . intval($rows[$i]['user_id']);

					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not update moderator permissions', '', __LINE__, __FILE__, $sql);
					}
				}
			}
		}

		// Delete Group
		$sql = "DELETE FROM " . GROUPS_TABLE . "
			WHERE group_id = " . $group_id;
		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update group', '', __LINE__, __FILE__, $sql);
		}

		$sql = "DELETE FROM " . USER_GROUP_TABLE . "
			WHERE group_id = " . $group_id;
		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update user_group', '', __LINE__, __FILE__, $sql);
		}

		$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
			WHERE group_id = " . $group_id;
		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update auth_access', '', __LINE__, __FILE__, $sql);
		}

		$sql_users = "UPDATE " . USERS_TABLE . "
			SET user_color = '" . $board_config['active_users_color'] . "', user_color_group = '0'
			WHERE user_color_group = " . $group_id;
		if (!$db->sql_query($sql_users))
		{
			message_die(GENERAL_ERROR, 'Could not update users in groups', '', __LINE__, __FILE__, $sql);
		}

		empty_cache_folders(USERS_CACHE_FOLDER);

		$message = $lang['Deleted_group'] . '<br /><br />' . sprintf($lang['Click_return_groupsadmin'], '<a href="' . append_sid('admin_groups.' . PHP_EXT) . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid('index.' . PHP_EXT . '?pane=right') . '">', '</a>');

		message_die(GENERAL_MESSAGE, $message);
	}
	else
	{
		$group_type = isset($_POST['group_type']) ? intval($_POST['group_type']) : GROUP_OPEN;
		$group_name = isset($_POST['group_name']) ? htmlspecialchars(trim($_POST['group_name'])) : '';
		$group_description = isset($_POST['group_description']) ? trim($_POST['group_description']) : '';
		$group_moderator = isset($_POST['username']) ? $_POST['username'] : '';
		$delete_old_moderator = isset($_POST['delete_old_moderator']) ? true : false;
		$group_rank = isset($_POST['group_rank']) ? $_POST['group_rank'] : '0';
		$group_color = isset($_POST['group_color']) ? check_valid_color($_POST['group_color']) : false;
		$group_color = ($group_color != false) ? $group_color : '';
		$group_legend = isset($_POST['group_legend']) ? $_POST['group_legend'] : '0';
		$group_count = isset($_POST['group_count']) ? intval($_POST['group_count']) : 0;
		$group_count_max = isset($_POST['group_count_max']) ? intval($_POST['group_count_max']) : 0;
		$group_count_enable = isset($_POST['group_count_enable']) ? true : false;
		$group_count_update = isset($_POST['group_count_update']) ? true : false;
		$group_count_delete = isset($_POST['group_count_delete']) ? true : false;

		if ($group_name == '')
		{
			message_die(GENERAL_MESSAGE, $lang['No_group_name']);
		}
		elseif ($group_moderator == '')
		{
			message_die(GENERAL_MESSAGE, $lang['No_group_moderator']);
		}

		$this_userdata = get_userdata($group_moderator, true);
		$group_moderator = $this_userdata['user_id'];

		if (!$group_moderator)
		{
			message_die(GENERAL_MESSAGE, $lang['No_group_moderator']);
		}

		if($mode == 'editgroup')
		{
			$sql = "SELECT *
				FROM " . GROUPS_TABLE . "
				WHERE group_single_user <> " . TRUE . "
				AND group_id = " . $group_id;
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Error getting group information', '', __LINE__, __FILE__, $sql);
			}

			if(!($group_info = $db->sql_fetchrow($result)))
			{
				message_die(GENERAL_MESSAGE, $lang['Group_not_exist']);
			}

			if ($group_info['group_moderator'] != $group_moderator)
			{
				if ($delete_old_moderator)
				{
					$sql = "DELETE FROM " . USER_GROUP_TABLE . "
						WHERE user_id = " . $group_info['group_moderator'] . "
							AND group_id = " . $group_id;
					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not update group moderator', '', __LINE__, __FILE__, $sql);
					}

					$sql_users = "UPDATE " . USERS_TABLE . "
						SET user_color = '" . $board_config['active_users_color'] . "', user_color_group = '0'
						WHERE user_id = " . $group_info['group_moderator'] . "
							AND user_color_group = " . $group_id;
					if (!$db->sql_query($sql_users))
					{
						message_die(GENERAL_ERROR, 'Could not update users in groups', '', __LINE__, __FILE__, $sql);
					}
				}

				$sql = "SELECT user_id
					FROM " . USER_GROUP_TABLE . "
					WHERE user_id = $group_moderator
						AND group_id = $group_id";
				if (!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Failed to obtain current group moderator info', '', __LINE__, __FILE__, $sql);
				}

				if (!($row = $db->sql_fetchrow($result)))
				{
					$sql = "INSERT INTO " . USER_GROUP_TABLE . " (group_id, user_id, user_pending)
						VALUES (" . $group_id . ", " . $group_moderator . ", 0)";
					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not update group moderator', '', __LINE__, __FILE__, $sql);
					}
				}
			}

			$group_color = (check_valid_color($group_color) ? check_valid_color($group_color) : '');
			$sql = "UPDATE " . GROUPS_TABLE . "
				SET group_type = $group_type, group_name = '" . str_replace("\'", "''", $group_name) . "', group_description = '" . str_replace("\'", "''", $group_description) . "', group_moderator = $group_moderator, group_rank='$group_rank', group_color='$group_color', group_legend='$group_legend', group_count='$group_count', group_count_max='$group_count_max', group_count_enable='$group_count_enable'
				WHERE group_id = $group_id";
			if (!$db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not update group', '', __LINE__, __FILE__, $sql);
			}
			if ($group_count_delete)
			{
				//removing old users
				$sql = "DELETE FROM " . USER_GROUP_TABLE . "
					WHERE group_id = '" . $group_id . "'
					AND user_id NOT IN ('" . $group_moderator . "','" . ANONYMOUS . "')";
				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Could not remove users, group count', '', __LINE__, __FILE__, $sql);
				}
				$group_count_remove=$db->sql_affectedrows();
			}
			if ($group_count_update)
			{
				//finding new users
				$sql = "SELECT u.user_id FROM " . USERS_TABLE . " u
					LEFT JOIN " . USER_GROUP_TABLE ." ug ON u.user_id=ug.user_id AND ug.group_id='$group_id'
					WHERE u.user_posts>='$group_count' AND u.user_posts<'$group_count_max'
					AND ug.group_id is NULL
					AND u.user_id NOT IN ('" . $group_moderator . "','" . ANONYMOUS . "')";
				if (!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, $sql.'Could not select new users, group count', '', __LINE__, __FILE__, $sql);
				}
				//inserting new users
				$group_count_added=0;
				while (($new_members = $db->sql_fetchrow($result)))
				{
					$sql = "INSERT INTO " . USER_GROUP_TABLE . " (group_id, user_id, user_pending)
						VALUES ('" . $group_id . "', " . $new_members['user_id'] . ", 0)";
					if (!($result2 = $db->sql_query($sql)))
					{
						message_die(GENERAL_ERROR, 'Error inserting user group, group count', '', __LINE__, __FILE__, $sql);
					}
					$group_count_added++;
				}
			}

			$sql_users = "UPDATE " . USERS_TABLE . "
				SET user_color = '" . $group_color . "', user_rank = '" . $group_rank . "'
				WHERE user_color_group = " . $group_id;
			if (!$db->sql_query($sql_users))
			{
				message_die(GENERAL_ERROR, 'Could not update users in groups', '', __LINE__, __FILE__, $sql);
			}

			empty_cache_folders(USERS_CACHE_FOLDER);

			$message = $lang['Updated_group'] . '<br />' . sprintf($lang['group_count_updated'], $group_count_remove, $group_count_added) . '<br /><br />' . sprintf($lang['Click_return_groupsadmin'], '<a href="' . append_sid('admin_groups.' . PHP_EXT) . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid('index.' . PHP_EXT . '?pane=right') . '">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		}
		elseif($mode == 'newgroup')
		{
			$sql = "SELECT max(group_legend_order) max_legend_order FROM " . GROUPS_TABLE;
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could not query from menu table", $lang['Error'], __LINE__, __FILE__, $sql);
			}
			$row = $db->sql_fetchrow($result);
			$group_legend_order = $row['max_legend_order'] + 1;

			$sql = "INSERT INTO " . GROUPS_TABLE . " (group_type, group_name, group_description, group_moderator, group_rank, group_color, group_legend, group_legend_order, group_count, group_count_max, group_count_enable, group_single_user)
				VALUES ($group_type, '" . str_replace("\'", "''", $group_name) . "', '" . str_replace("\'", "''", $group_description) . "', $group_moderator, '$group_rank', '$group_color', '$group_legend', '$group_legend_order', '$group_count', '$group_count_max', '$group_count_enable', '0')";
			if (!$db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not insert new group', '', __LINE__, __FILE__, $sql);
			}
			$new_group_id = $db->sql_nextid();

			adjust_legend_order();

			$sql = "INSERT INTO " . USER_GROUP_TABLE . " (group_id, user_id, user_pending)
				VALUES ($new_group_id, $group_moderator, 0)";
			if (!$db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not insert new user-group info', '', __LINE__, __FILE__, $sql);
			}
			if ($group_count_delete)
			{
				//removing old users
				$sql = "DELETE FROM " . USER_GROUP_TABLE . "
					WHERE group_id=$new_group_id
					AND user_id NOT IN ('$group_moderator','" . ANONYMOUS . "')";
				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Could not remove users, group count', '', __LINE__, __FILE__, $sql);
				}
				$group_count_remove=$db->sql_affectedrows();
			}
			if ($group_count_update)
			{
				//finding new users
				$sql = "SELECT u.user_id FROM " . USERS_TABLE . " u
					LEFT JOIN " . USER_GROUP_TABLE ." ug ON u.user_id=ug.user_id AND ug.group_id='$new_group_id'
					WHERE u.user_posts>='$group_count' AND u.user_posts<'$group_count_max'
					AND ug.group_id is NULL
					AND u.user_id NOT IN ('$group_moderator','" . ANONYMOUS . "')";
				if (!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, $sql.'Could not select new users, group count', '', __LINE__, __FILE__, $sql);
				}
				//inserting new users
				$group_count_added=0;
				while (($new_members = $db->sql_fetchrow($result)))
				{
					$sql = "INSERT INTO " . USER_GROUP_TABLE . " (group_id, user_id, user_pending)
						VALUES ($new_group_id, " . $new_members['user_id'] . ", 0)";
					if (!($result2 = $db->sql_query($sql)))
					{
						message_die(GENERAL_ERROR, 'Error inserting user group, group count', '', __LINE__, __FILE__, $sql);
					}
					$group_count_added++;
				}
			}

			empty_cache_folders(USERS_CACHE_FOLDER);

			$message = $lang['Added_new_group'] . '<br />' . sprintf($lang['group_count_updated'], $group_count_remove, $group_count_added). '<br /><br />' . sprintf($lang['Click_return_groupsadmin'], '<a href="' . append_sid('admin_groups.' . PHP_EXT) . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid('index.' . PHP_EXT . '?pane=right') . '">', '</a>');;

			message_die(GENERAL_MESSAGE, $message);

		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['No_group_action']);
		}
	}
}
elseif (isset($_POST['mass_update']))
{
	$sql = "SELECT group_id
		FROM " . GROUPS_TABLE . "
		WHERE group_single_user <> " . true . "
		ORDER BY group_name ASC";
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not obtain group list', '', __LINE__, __FILE__, $sql);
	}
	while ($row = $db->sql_fetchrow($result))
	{
		$group_id = $row['group_id'];
		$group_color = isset($_POST['group_color_' . $group_id]) ? check_valid_color($_POST['group_color_' . $group_id]) : false;
		$group_color = ($group_color != false) ? $group_color : '';
		$group_legend = isset($_POST['group_legend_' . $group_id]) ? $_POST['group_legend_' . $group_id] : '0';

		$sql_ug = "UPDATE " . GROUPS_TABLE . "
			SET group_color = '$group_color', group_legend = '$group_legend'
			WHERE group_id = $group_id";
		if (!$db->sql_query($sql_ug))
		{
			message_die(GENERAL_ERROR, 'Could not update groups', '', __LINE__, __FILE__, $sql);
		}

		$sql_users = "UPDATE " . USERS_TABLE . "
			SET user_color = '$group_color'
			WHERE user_color_group = $group_id";
		if (!$db->sql_query($sql_users))
		{
			message_die(GENERAL_ERROR, 'Could not update users in groups', '', __LINE__, __FILE__, $sql);
		}
	}

	$group_color = isset($_POST['active_users_color']) ? check_valid_color($_POST['active_users_color']) : false;
	$group_color = ($group_color != false) ? $group_color : '';
	set_config('active_users_color', $group_color);

	$sql_users = "UPDATE " . USERS_TABLE . "
		SET user_color = '" . $group_color . "'
		WHERE user_color_group = ''
			AND user_color = '" . $board_config['active_users_color'] . "'
			AND user_active = 1";
	if (!$db->sql_query($sql_users))
	{
		message_die(GENERAL_ERROR, 'Could not update users in groups', '', __LINE__, __FILE__, $sql);
	}

	$group_legend = isset($_POST['active_users_legend']) ? $_POST['active_users_legend'] : '0';
	set_config('active_users_legend', $group_legend);

	$group_color = isset($_POST['bots_color']) ? check_valid_color($_POST['bots_color']) : false;
	$group_color = ($group_color != false) ? $group_color : '';
	set_config('bots_color', $group_color);

	$group_legend = isset($_POST['bots_legend']) ? $_POST['bots_legend'] : '0';
	set_config('bots_legend', $group_legend);

	empty_cache_folders(USERS_CACHE_FOLDER);

	$message = $lang['Groups_Updated'] . '<br /><br />' . sprintf($lang['Click_return_groupsadmin'], '<a href="' . append_sid('admin_groups.' . PHP_EXT) . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid('index.' . PHP_EXT . '?pane=right') . '">', '</a>');;

	message_die(GENERAL_MESSAGE, $message);
}
else
{
	$move = (isset($_GET['move'])) ? intval($_GET['move']) : -1;
	if (($move == '0') || ($move == '1'))
	{
		$group_id = (isset($_GET['group_id'])) ? intval($_GET['group_id']) : 0;
		if ($group_id != 0)
		{
			change_legend_order($group_id, $move);
		}
	}

	$sql = "SELECT group_id, group_name, group_color, group_type, group_legend
		FROM " . GROUPS_TABLE . "
		WHERE group_single_user <> " . true . "
		ORDER BY group_legend_order ASC, group_name ASC";
	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not obtain group list', '', __LINE__, __FILE__, $sql);
	}

	$select_list = '';
	if ($row = $db->sql_fetchrow($result))
	{
		$select_list .= '<select name="' . POST_GROUPS_URL . '">';
		$row_counter = 0;
		do
		{
			$row_counter++;
			$select_list .= '<option value="' . $row['group_id'] . '">' . $row['group_name'] . '</option>';
			switch ($row['group_type'])
			{
				case GROUP_OPEN:
					$type_lang = $lang['group_open'];
					break;
				case GROUP_CLOSED:
					$type_lang = $lang['group_closed'];
					break;
				case GROUP_HIDDEN:
					$type_lang = $lang['group_hidden'];
					break;
			}
			$row['group_color'] = check_valid_color($row['group_color']);
			$counting_list = array();
			$counting_list = count_users_in_group($row['group_id']);

			$g_move = '&nbsp;<a href="' . append_sid('admin_groups.' . PHP_EXT . '?group_id=' . $row['group_id'] . '&amp;move=0') . '"><img src="../images/cms/arrow_up.png" alt="' . $lang['Move_Up'] . '" title="' . $lang['Move_Up'] . '" /></a>';
			$g_move .= '&nbsp;<a href="' . append_sid('admin_groups.' . PHP_EXT . '?group_id=' . $row['group_id'] . '&amp;move=1') . '"><img src="../images/cms/arrow_down.png" alt="' . $lang['Move_Down'] . '" title="' . $lang['Move_Down'] . '" /></a>';

			$class = ($row_counter % 2) ? $theme['td_class2'] : $theme['td_class1'];

			$template->assign_block_vars('group_row', array(
				'ROW_CLASS' => $class,
				'GROUP_ID' => $row['group_id'],
				'GROUP_NAME' => $row['group_name'],
				'GROUP_MEMBERS' => $counting_list['members'] . '/' . $counting_list['pending'],
				'GROUP_STATUS' => $type_lang,
				'GROUP_COLOR' => str_replace('#', '', $row['group_color']),
				'GROUP_COLOR_STYLE' => ' style="' . ($row['group_color'] ? 'color: ' . $row['group_color'] . '; ' : '') . 'font-weight:bold;"',
				'GROUP_LEGEND' => $row['group_legend'],
				'GROUP_LEGEND_CHECKED' => ($row['group_legend'] == '1') ? ' checked="checked"' : '',
				'GROUP_LEGEND_MOVE' => $g_move,
				'U_GROUP_EDIT' => append_sid('admin_groups.' . PHP_EXT . '?edit=true&amp;' . POST_GROUPS_URL . '=' . $row['group_id']),
				'U_GROUP_PERMISSIONS' => append_sid('admin_ug_auth.' . PHP_EXT . '?mode=group&amp;' . POST_GROUPS_URL . '=' . $row['group_id'])
				)
			);
		}
		while ($row = $db->sql_fetchrow($result));
		$select_list .= '</select>';
	}

	$counting_list = array();
	$counting_list = count_active_users();
	$template->set_filenames(array('body' => ADM_TPL . 'group_select_body.tpl'));

	$row_counter++;
	$class_active_users = ($row_counter % 2) ? $theme['td_class2'] : $theme['td_class1'];
	$row_counter++;
	$class_bots = ($row_counter % 2) ? $theme['td_class2'] : $theme['td_class1'];

	$template->assign_vars(array(
		'ROW_CLASS_ACTIVE_USERS' => $class_active_users,
		'ROW_CLASS_BOTS' => $class_bots,
		'L_GROUP_TITLE' => $lang['Group_administration'],
		'L_GROUP_EXPLAIN' => $lang['Group_admin_explain'],
		'L_GROUP_SELECT' => $lang['Select_group'],
		'L_GROUP_EDIT' => $lang['Edit_group'],
		'L_GROUP_NAME' => $lang['group_name'],
		'L_GROUP_DESCRIPTION' => $lang['group_description'],
		'L_GROUP_MODERATOR' => $lang['group_moderator'],
		'L_GROUP_RANK' => $lang['group_rank'],
		'L_GROUP_COLOR' => $lang['group_color'],
		'L_GROUP_LEGEND' => $lang['group_legend_short'],
		'L_GROUP_STATUS' => $lang['group_status'],
		'L_GROUP_OPEN' => $lang['group_open'],
		'L_GROUP_CLOSED' => $lang['group_closed'],
		'L_GROUP_HIDDEN' => $lang['group_hidden'],
		'L_GROUP_MEMBERS' => $lang['group_members'],
		'L_LOOK_UP' => $lang['Look_up_group'],
		'L_EDIT' => $lang['Edit'],
		'L_MANAGE' => $lang['Manage'],
		'L_PERMISSIONS' => $lang['Permissions'],
		'L_CREATE_NEW_GROUP' => $lang['New_group'],
		'L_MASS_UPDATE' => $lang['group_update'],
		'L_BOTS_GROUP' => $lang['Bots_Group'],
		'L_BOTS_COLOR' => $lang['Bots_Color'],
		'L_ACTIVE_USERS_GROUP' => $lang['Active_Users_Group'],
		'L_ACTIVE_USERS_COLOR' => $lang['Active_Users_Color'],

		'ACTIVE_USERS_COLOR' => str_replace('#', '', $board_config['active_users_color']),
		'ACTIVE_USERS_COLOR_STYLE' => ' style="' . ($board_config['active_users_color'] ? 'color: ' . $board_config['active_users_color'] . '; ' : '') . 'font-weight:bold;"',
		'ACTIVE_USERS_LEGEND_CHECKED' => ($board_config['active_users_legend'] == 1) ? ' checked="checked"' : '',
		'ACTIVE_MEMBERS' => $counting_list['active_members'],
		'BOTS_COLOR' => str_replace('#', '', $board_config['bots_color']),
		'BOTS_COLOR_STYLE' => ' style="' . ($board_config['bots_color'] ? 'color: ' . $board_config['bots_color'] . '; ' : '') . 'font-weight:bold;"',
		'BOTS_LEGEND_CHECKED' => ($board_config['bots_legend'] == 1) ? ' checked="checked"' : '',
		'S_GROUP_ACTION' => append_sid('admin_groups.' . PHP_EXT),
		'S_GROUP_SELECT' => $select_list
		)
	);

	if ($select_list != '')
	{
		$template->assign_block_vars('select_box', array());
	}

	$template->pparse('body');
}

include('./page_footer_admin.' . PHP_EXT);

?>