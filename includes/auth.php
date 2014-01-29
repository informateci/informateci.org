<?php
/**
*
* @package Icy Phoenix
* @version $Id: auth.php 80 2009-02-19 13:45:54Z Mighty Gorgon $
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

// CTracker_Ignore: File checked by human
/*
	$type's accepted (pre-pend with AUTH_):

	01 View,
	02 Read,
	03 Post,
	04 Reply,
	05 Edit,
	06 Delete,
	07 Sticky,
	08 Announce,
	09 Global Ann,
	10 News,
	11 Calendar,
	12 Vote,
	13 Poll,
	14 Attach,
	15 Download,
	16 Warn,
	17 Unban,
	18 Report,
	19 Rate

	Possible options ($type/forum_id combinations):

	* If you include a type and forum_id then a specific lookup will be done and
	the single result returned

	* If you set type to AUTH_ALL and specify a forum_id an array of all auth types
	will be returned

	* If you provide a forum_id a specific lookup on that forum will be done

	* If you set forum_id to AUTH_LIST_ALL and specify a type an array listing the
	results for all forums will be returned

	* If you set forum_id to AUTH_LIST_ALL and type to AUTH_ALL a multidimensional
	array containing the auth permissions for all types and all forums for that
	user is returned

	All results are returned as associative arrays, even when a single auth type is
	specified.

	If available you can send an array (either one or two dimensional) containing the
	forum auth levels, this will prevent the auth function having to do its own
	lookup
*/
function auth($type, $forum_id, $userdata, $f_access = '')
{
	global $db, $lang, $tree, $board_config;

	if (!empty($tree['data']))
	{
		$f_access = array();
		if (!empty($forum_id))
		{
			$idx = $tree['keys'][POST_FORUM_URL . $forum_id];
			$f_access = $tree['data'][$idx];
		}
		else
		{
			for ($i = 0; $i < count($tree['data']); $i++)
			{
				if ($tree['type'][$i] == POST_FORUM_URL)
				{
					$f_access[] = $tree['data'][$i];
				}
			}
		}
	}

	switch($type)
	{
		case AUTH_ALL:
			$a_sql = 'a.auth_view, a.auth_read, a.auth_post, a.auth_reply, a.auth_edit, a.auth_delete, a.auth_sticky, a.auth_announce, a.auth_globalannounce, a.auth_news, a.auth_cal, a.auth_vote, a.auth_pollcreate, a.auth_attachments, a.auth_download, a.auth_ban, a.auth_greencard, a.auth_bluecard, a.auth_rate';
			$auth_fields = array('auth_view', 'auth_read', 'auth_post', 'auth_reply', 'auth_edit', 'auth_delete', 'auth_sticky', 'auth_announce', 'auth_globalannounce', 'auth_news', 'auth_cal', 'auth_vote', 'auth_pollcreate', 'auth_attachments', 'auth_download', 'auth_ban', 'auth_greencard', 'auth_bluecard', 'auth_rate');
			break;
		case AUTH_VIEW:
			$a_sql = 'a.auth_view';
			$auth_fields = array('auth_view');
			break;
		case AUTH_READ:
			$a_sql = 'a.auth_read';
			$auth_fields = array('auth_read');
			break;
		case AUTH_POST:
			$a_sql = 'a.auth_post';
			$auth_fields = array('auth_post');
			break;
		case AUTH_REPLY:
			$a_sql = 'a.auth_reply';
			$auth_fields = array('auth_reply');
			break;
		case AUTH_EDIT:
			$a_sql = 'a.auth_edit';
			$auth_fields = array('auth_edit');
			break;
		case AUTH_DELETE:
			$a_sql = 'a.auth_delete';
			$auth_fields = array('auth_delete');
			break;
		case AUTH_STICKY:
			$a_sql = 'a.auth_sticky';
			$auth_fields = array('auth_sticky');
			break;
		case AUTH_ANNOUNCE:
			$a_sql = 'a.auth_announce';
			$auth_fields = array('auth_announce');
			break;
		case AUTH_GLOBALANNOUNCE:
			$a_sql = 'a.auth_globalannounce';
			$auth_fields = array('auth_globalannounce');
			break;
		case AUTH_NEWS:
			$a_sql = 'a.auth_news';
			$auth_fields = array('auth_news');
			break;
		case AUTH_CAL:
			$a_sql = 'a.auth_cal';
			$auth_fields = array('auth_cal');
			break;
		case AUTH_VOTE:
			$a_sql = 'a.auth_vote';
			$auth_fields = array('auth_vote');
			break;
		case AUTH_POLLCREATE:
			$a_sql = 'a.auth_pollcreate';
			$auth_fields = array('auth_pollcreate');
			break;
		case AUTH_ATTACHMENTS:
			$a_sql = 'a.auth_attachments';
			$auth_fields = array('auth_attachments');
			break;
		case AUTH_DOWNLOAD:
			$a_sql = 'a.auth_download';
			$auth_fields = array('auth_download');
			break;
		case AUTH_BAN:
			$a_sql = 'a.auth_ban';
			$auth_fields = array('auth_ban');
			break;
		case AUTH_GREENCARD:
			$a_sql = 'a.auth_greencard';
			$auth_fields = array('auth_greencard');
			break;
		case AUTH_BLUECARD:
			$a_sql = 'a.auth_bluecard';
			$auth_fields = array('auth_bluecard');
			break;
		case AUTH_RATE:
			$a_sql = 'a.auth_rate';
			$auth_fields = array('auth_rate');
			break;
		default:
			break;
	}

	//
	// If f_access has been passed, or auth is needed to return an array of forums
	// then we need to pull the auth information on the given forum (or all forums)
	//
	if (empty($f_access))
	{
		$forum_match_sql = ($forum_id != AUTH_LIST_ALL) ? "WHERE a.forum_id = $forum_id" : '';

		$sql = "SELECT a.forum_id, $a_sql
			FROM " . FORUMS_TABLE . " a
			$forum_match_sql";
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Failed obtaining forum access control lists', '', __LINE__, __FILE__, $sql);
		}

		$sql_fetchrow = ($forum_id != AUTH_LIST_ALL) ? 'sql_fetchrow' : 'sql_fetchrowset';

		if (!($f_access = $db->$sql_fetchrow($result)))
		{
			$db->sql_freeresult($result);
			return array();
		}
		$db->sql_freeresult($result);
	}

	//
	// If the user isn't logged on then all we need do is check if the forum
	// has the type set to ALL, if yes they are good to go, if not then they are denied access
	//
	$u_access = array();
	if ($userdata['session_logged_in'])
	{
		$forum_match_sql = ($forum_id != AUTH_LIST_ALL) ? "AND a.forum_id = $forum_id" : '';

		$sql = "SELECT a.forum_id, $a_sql, a.auth_mod
			FROM " . AUTH_ACCESS_TABLE . " a, " . USER_GROUP_TABLE . " ug
			WHERE ug.user_id = " . $userdata['user_id'] . "
				AND ug.user_pending = 0
				AND a.group_id = ug.group_id
				$forum_match_sql";
		if (!($result = $db->sql_query($sql, false, 'auth_')))
		{
			message_die(GENERAL_ERROR, 'Failed obtaining forum access control lists', '', __LINE__, __FILE__, $sql);
		}

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if ($forum_id != AUTH_LIST_ALL)
				{
					$u_access[] = $row;
				}
				else
				{
					$u_access[$row['forum_id']][] = $row;
				}
			}
			while($row = $db->sql_fetchrow($result));
		}
		$db->sql_freeresult($result);
	}

	$is_admin = (($userdata['user_level'] == ADMIN) && $userdata['session_logged_in']) ? true : 0;
	//$is_admin = ((($userdata['user_level'] == ADMIN) || ($userdata['user_level'] == JUNIOR_ADMIN)) && $userdata['session_logged_in']) ? true : 0;

	$auth_user = array();
	for($i = 0; $i < count($auth_fields); $i++)
	{
		$key = $auth_fields[$i];

		//
		// If the user is logged on and the forum type is either ALL or REG then the user has access
		//
		// If the type if ACL, MOD or ADMIN then we need to see if the user has specific permissions
		// to do whatever it is they want to do ... to do this we pull relevant information for the
		// user (and any groups they belong to)
		//
		// Now we compare the users access level against the forums. We assume here that a moderator
		// and admin automatically have access to an ACL forum, similarly we assume admins meet an
		// auth requirement of MOD
		//
		if ($forum_id != AUTH_LIST_ALL)
		{
			$value = $f_access[$key];

			switch($value)
			{
				case AUTH_ALL:
					$auth_user[$key] = true;
					$auth_user[$key . '_type'] = $lang['Auth_Anonymous_Users'];
					break;

				case AUTH_REG:
					$auth_user[$key] = ($userdata['session_logged_in']) ? true : 0;
					// Check if the user is a BOT
					$auth_user[$key] = (($board_config['bots_reg_auth'] == true) && ($userdata['bot_id'] !== false)) ? true : $auth_user[$key];
					$auth_user[$key . '_type'] = $lang['Auth_Registered_Users'];
					break;

				// Self AUTH - BEGIN
				case AUTH_SELF:
					$auth_user[$key] = ($userdata['session_logged_in']) ? ((auth_check_user(AUTH_MOD, 'auth_mod', $u_access, $is_admin)) ? true : AUTH_SELF) : 0;
					$auth_user[$key . '_type'] = $lang['Auth_Self_Users'];
					break;
				// Self AUTH - END

				case AUTH_ACL:
					$auth_user[$key] = ($userdata['session_logged_in']) ? auth_check_user(AUTH_ACL, $key, $u_access, $is_admin) : 0;
					$auth_user[$key . '_type'] = $lang['Auth_Users_granted_access'];
					break;

				case AUTH_MOD:
					$auth_user[$key] = ($userdata['session_logged_in']) ? auth_check_user(AUTH_MOD, 'auth_mod', $u_access, $is_admin) : 0;
					$auth_user[$key . '_type'] = $lang['Auth_Moderators'];
					break;

				case AUTH_ADMIN:
					$auth_user[$key] = $is_admin;
					$auth_user[$key . '_type'] = $lang['Auth_Administrators'];
					break;

				case AUTH_NONE:
					$auth_user[$key] = 0;
					break;

				default:
					$auth_user[$key] = 0;
					break;
			}
		}
		else
		{
			for($k = 0; $k < count($f_access); $k++)
			{
				$value = $f_access[$k][$key];
				$f_forum_id = $f_access[$k]['forum_id'];
				$u_access[$f_forum_id] = isset($u_access[$f_forum_id]) ? $u_access[$f_forum_id] : array();

				switch($value)
				{
					case AUTH_ALL:
						$auth_user[$f_forum_id][$key] = true;
						$auth_user[$f_forum_id][$key . '_type'] = $lang['Auth_Anonymous_Users'];
						break;

					case AUTH_REG:
						$auth_user[$f_forum_id][$key] = ($userdata['session_logged_in']) ? true : 0;
						// Check if the user is a BOT
						$auth_user[$f_forum_id][$key] = (($board_config['bots_reg_auth'] == true) && ($userdata['bot_id'] !== false)) ? true : $auth_user[$f_forum_id][$key];
						$auth_user[$f_forum_id][$key . '_type'] = $lang['Auth_Registered_Users'];
						break;

					// Self AUTH - BEGIN
					case AUTH_SELF:
						$auth_user[$f_forum_id][$key] = ($userdata['session_logged_in']) ? ((auth_check_user(AUTH_MOD, 'auth_mod', $u_access[$f_forum_id], $is_admin)) ? true : AUTH_SELF) : 0;
						$auth_user[$f_forum_id][$key . '_type'] = $lang['Auth_Self_Users'];
						break;
					// Self AUTH - END

					case AUTH_ACL:
						$auth_user[$f_forum_id][$key] = ($userdata['session_logged_in']) ? auth_check_user(AUTH_ACL, $key, $u_access[$f_forum_id], $is_admin) : 0;
						$auth_user[$f_forum_id][$key . '_type'] = $lang['Auth_Users_granted_access'];
						break;

					case AUTH_MOD:
						$auth_user[$f_forum_id][$key] = ($userdata['session_logged_in']) ? auth_check_user(AUTH_MOD, 'auth_mod', $u_access[$f_forum_id], $is_admin) : 0;
						$auth_user[$f_forum_id][$key . '_type'] = $lang['Auth_Moderators'];
						break;

					case AUTH_ADMIN:
						$auth_user[$f_forum_id][$key] = $is_admin;
						$auth_user[$f_forum_id][$key . '_type'] = $lang['Auth_Administrators'];
						break;

					case AUTH_NONE:
						$auth_user[$f_forum_id][$key] = 0;
						break;

					default:
						$auth_user[$f_forum_id][$key] = 0;
						break;
				}
			}
		}
	}

	// Is user a moderator?
	if ($forum_id != AUTH_LIST_ALL)
	{
		$auth_user['auth_mod'] = ($userdata['session_logged_in']) ? auth_check_user(AUTH_MOD, 'auth_mod', $u_access, $is_admin) : 0;
	}
	else
	{
		for($k = 0; $k < count($f_access); $k++)
		{
			$f_forum_id = $f_access[$k]['forum_id'];
			$u_access[$f_forum_id] = isset($u_access[$f_forum_id]) ? $u_access[$f_forum_id] : array();

			$auth_user[$f_forum_id]['auth_mod'] = ($userdata['session_logged_in']) ? auth_check_user(AUTH_MOD, 'auth_mod', $u_access[$f_forum_id], $is_admin) : 0;
		}
	}

	return $auth_user;
}

function auth_check_user($type, $key, $u_access, $is_admin)
{
	$auth_user = 0;

	if (count($u_access))
	{
		for($j = 0; $j < count($u_access); $j++)
		{
			$result = 0;
			switch($type)
			{
				case AUTH_ACL:
					$result = $u_access[$j][$key];

				case AUTH_MOD:
					$result = $result || $u_access[$j]['auth_mod'];

				case AUTH_ADMIN:
					$result = $result || $is_admin;
					break;
			}

			$auth_user = $auth_user || $result;
		}
	}
	else
	{
		$auth_user = $is_admin;
	}

	return $auth_user;
}

// build_exclusion_forums_list
// function needed to build a list of forum with no read access.
function build_exclusion_forums_list($only_auth_view = true)
{
	global $db, $lang, $tree, $board_config;
	global $userdata;

	$sql_auth = "SELECT forum_id, cat_id, forum_name, auth_view, auth_read, auth_post FROM " . FORUMS_TABLE;
	if(!$result_auth = $db->sql_query($sql_auth, false, 'forums_excluded_list_', FORUMS_CACHE_FOLDER))
	{
		message_die(GENERAL_ERROR, 'could not query forums information.', '', __LINE__, __FILE__, $sql_auth);
	}
	$forum_data = array();
	while($row_auth = $db->sql_fetchrow($result_auth))
	{
		$forum_data[] = $row_auth;
	}
	$db->sql_freeresult($result_auth);

	$is_auth_ary = array();
	$is_auth_ary = auth(AUTH_ALL, AUTH_LIST_ALL, $userdata);

	$except_forums = '\'start\'';
	for($f = 0; $f < count($forum_data); $f++)
	{
		$exclude_this = false;

		if((!$is_auth_ary[$forum_data[$f]['forum_id']]['auth_read']) || ((!$is_auth_ary[$forum_data[$f]['forum_id']]['auth_view']) && $only_auth_view))
		{
			$exclude_this = true;
		}

		// SELF AUTH - BEGIN
		// Comment the lines below if you wish to show RESERVED topics for AUTH_SELF.
		if(((($userdata['user_level'] != ADMIN) && ($userdata['user_level'] != MOD)) || (($userdata['user_level'] == MOD) && ($board_config['allow_mods_view_self'] == false))) && (intval($is_auth_ary[$forum_data[$f]['forum_id']]['auth_read']) == AUTH_SELF))
		{
			$exclude_this = true;
		}
		// SELF AUTH - END

		if ($exclude_this == true)
		{
			if($except_forums == '\'start\'')
			{
				$except_forums = $forum_data[$f]['forum_id'];
			}
			else
			{
				$except_forums .= ',' . $forum_data[$f]['forum_id'];
			}
		}
	}
	return $except_forums;
}

// build_allowed_forums_list
// function needed to build a list of forum with read access.
function build_allowed_forums_list()
{
	global $db, $lang, $tree, $board_config;
	global $userdata;

	$sql_auth = "SELECT forum_id, cat_id, forum_name, auth_view, auth_read, auth_post FROM " . FORUMS_TABLE;
	if(!$result_auth = $db->sql_query($sql_auth, false, 'forums_allowed_list_', FORUMS_CACHE_FOLDER))
	{
		message_die(GENERAL_ERROR, 'could not query forums information.', '', __LINE__, __FILE__, $sql_auth);
	}
	$forum_data = array();
	while($row_auth = $db->sql_fetchrow($result_auth))
	{
		$forum_data[] = $row_auth;
	}
	$db->sql_freeresult($result_auth);

	$is_auth_ary = array();
	$is_auth_ary = auth(AUTH_ALL, AUTH_LIST_ALL, $userdata);

	$allowed_forums = '0';
	for($f = 0; $f < count($forum_data); $f++)
	{
		$include_this = false;

		if($is_auth_ary[$forum_data[$f]['forum_id']]['auth_read'])
		{
			$include_this = true;
		}

		// SELF AUTH - BEGIN
		// Comment the lines below if you wish to show RESERVED topics for AUTH_SELF.
		if(((($userdata['user_level'] != ADMIN) && ($userdata['user_level'] != MOD)) || (($userdata['user_level'] == MOD) && ($board_config['allow_mods_view_self'] == false))) && (intval($is_auth_ary[$forum_data[$f]['forum_id']]['auth_read']) == AUTH_SELF))
		{
			$include_this = false;
		}
		// SELF AUTH - END

		if ($include_this == true)
		{
			$allowed_forums .= ', ' . $forum_data[$f]['forum_id'];
		}
	}
	return $allowed_forums;
}

?>