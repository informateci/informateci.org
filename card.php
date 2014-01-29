<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Niels Chr. Rød (ncr@db9.dk) - (http://mods.db9.dk)
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);

// Find what we are to do
$mode = (isset($_POST['report_x'])) ? 'report' :
		((isset($_POST['report_reset_x'])) ? 'report_reset' :
			((isset($_POST['ban_x'])) ? 'ban' :
				((isset($_POST['unban_x'])) ? 'unban' :
					((isset($_POST['warn_x'])) ? 'warn' :
						((isset($_POST['block_x'])) ? 'block' :
							((isset($_GET['mode'])) ? $_GET['mode'] : ''
							)
						)
					)
				)
			)
		);

$forum_id = !empty($_GET[POST_FORUM_URL]) ? intval($_GET[POST_FORUM_URL]) : (!empty($_POST[POST_FORUM_URL]) ? intval($_POST[POST_FORUM_URL]) : '0');
$topic_id = !empty($_GET[POST_TOPIC_URL]) ? intval($_GET[POST_TOPIC_URL]) : (!empty($_POST[POST_TOPIC_URL]) ? intval($_POST[POST_TOPIC_URL]) : '0');
$post_id = !empty($_GET[POST_POST_URL]) ? intval($_GET[POST_POST_URL]) : (!empty($_POST[POST_POST_URL]) ? intval($_POST[POST_POST_URL]) : '0');
$post_id = empty($post_id) ? ((isset($_POST['post_id'])) ? intval ($_POST['post_id']) : ((isset($_GET['post_id'])) ? intval($_GET['post_id']) : '')) : $post_id;
$user_id = (isset($_POST[POST_USERS_URL])) ? intval ($_POST[POST_USERS_URL]) : ((isset($_GET[POST_USERS_URL])) ? intval($_GET[POST_USERS_URL]) : '');

$forum_id_append = (!empty($forum_id) ? (POST_FORUM_URL . '=' . $forum_id . '&amp;') : '');
$topic_id_append = (!empty($topic_id) ? (POST_TOPIC_URL . '=' . $topic_id . '&amp;') : '');
$post_id_append = (!empty($post_id) ? (POST_POST_URL . '=' . $post_id) : '');

// check that we have all what is needed to know
if (!($post_id + $user_id))
{
	message_die(GENERAL_ERROR, "No user/post specified", "", __LINE__, __FILE__,'post_id="'.$post_id.'", user_id="'.$user_id.'"');
}
if (empty($mode))
{
	message_die(GENERAL_ERROR, "No action specified", "", __LINE__, __FILE__,'mode="'.$mode.'"');
}

$sql = 'SELECT DISTINCT forum_id, poster_id, post_bluecard FROM ' . POSTS_TABLE . ' WHERE post_id = "'.$post_id.'"';
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Couldn't obtain forums information.", "", __LINE__, __FILE__, $sql);
}

$result = $db->sql_fetchrow($result);
$blue_card = $result['post_bluecard'];
if ($post_id)
{
	// post mode
	$forum_id = $result['forum_id'];
	$poster_id = $result['poster_id'];
}
elseif ($user_id)
{
	//user mode
	//forum_id will control witch permission, when no post_id is given, and a user_id is given instead
	//disable the frum_id line will give no default access when no post_id is given
	// installe extra permission mod, in order to enable this feature
//	$forum_id = PAGE_CARD;
	$poster_id = $user_id;
}

// Start session management
$userdata = session_pagestart($user_ip);
init_userprefs($userdata);
// End session management

//
// Start auth check
//
$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata);

if ($mode == 'report_reset')
{
	if (! $is_auth['auth_mod'])
		message_die(GENERAL_ERROR, $lang['Not_Authorized']);

	$sql = 'SELECT p.post_subject, f.forum_name FROM ' . POSTS_TABLE . ' p, ' . FORUMS_TABLE . ' f WHERE p.post_id="' . $post_id . '" AND p.forum_id = f.forum_id';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't get post subject information".$sql);
	}
	$subject = $db->sql_fetchrow($result);
	$post_subject = $subject['post_subject'];
	$forum_name = $subject['forum_name'];

	$sql = 'UPDATE ' . POSTS_TABLE . ' SET post_bluecard="0" WHERE post_id="' . $post_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't update blue card information");
	}
	message_die(GENERAL_MESSAGE, $lang['Post_reset'].'<br /><br />'.
	sprintf($lang['Click_return_viewtopic'], '<a href="' . append_sid(VIEWTOPIC_MG . '?' . $forum_id_append . $topic_id_append . POST_POST_URL . '=' . $post_id . '#p' . $post_id). '">', '</a>'));

}
elseif ($mode == 'report')
{
	if (!$is_auth['auth_bluecard'])
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorized']);
	}

	$sql = 'SELECT f.forum_name, p.topic_id FROM ' . POSTS_TABLE . ' p, ' . FORUMS_TABLE . ' f WHERE p.post_id = "' . $post_id . '" AND  p.forum_id = f.forum_id';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't get post subject information");
	}

	$post_details = $db->sql_fetchrow($result);
	$forum_name = $post_details['forum_name'];
	$topic_id = $post_details['topic_id'];
	$sql = 'SELECT p.post_subject FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t WHERE t.topic_id = "' . $topic_id . '" AND p.post_id = t.topic_first_post_id';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't get topic subject information" . $sql);
	}
	$post_details = $db->sql_fetchrow($result);
	$post_subject = $post_details['post_subject'];

	$sql = 'SELECT p.topic_id FROM ' . POSTS_TABLE . ' p WHERE p.post_subject = "(' . $post_id . ')' . $post_subject . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't get topic subject information" . $sql);
	}
	$post_details = $db->sql_fetchrow($result);
	$allready_reported= ($blue_card) ? $post_details['topic_id'] : '';

	$blue_card++;
	$sql = 'UPDATE ' . POSTS_TABLE . ' SET post_bluecard = "' . $blue_card . '" WHERE post_id = "' . $post_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't update blue card information");
	}
	//
	// Obtain list of moderators of this forum
	$sql = "SELECT g.group_name, u.username, u.user_email, u.user_lang
		FROM " . AUTH_ACCESS_TABLE . " aa,  " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g, " . USERS_TABLE . " u
		WHERE aa.forum_id = $forum_id AND aa.auth_mod = " . TRUE . "
		AND ug.group_id = aa.group_id AND g.group_id = aa.group_id AND u.user_id = ug.user_id";
	if(!$result_mods = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't obtain moderators information.", "", __LINE__, __FILE__, $sql);
	}
	$total_mods = $db->sql_numrows($result_mods);
	$i = 0;
	if (!$total_mods)
	{
		message_die(GENERAL_MESSAGE, $lang['No_moderators'].'<br /><br />');
	}
	(($board_config['report_forum'])? sprintf($lang['Send_message'], '<a href="' . append_sid('posting.' . PHP_EXT . '?mode=' . (($allready_reported) ? 'reply&amp;' . POST_TOPIC_URL .'=' . $allready_reported : 'newtopic&amp' . POST_FORUM_URL . '=' . $board_config['report_forum']) . '&amp;postreport=' . $post_id). '">', '</a>') : '') . sprintf($lang['Click_return_viewtopic'], '<a href="' . append_sid(VIEWTOPIC_MG . '?' . $forum_id_append . $topic_id_append . POST_POST_URL . '=' . $post_id . '#p' . $post_id). '">', '</a>');
	if (($blue_card >= $board_config['bluecard_limit_2'] && (!(($blue_card-$board_config['bluecard_limit_2']) % $board_config['bluecard_limit']))) || ($blue_card == $board_config['bluecard_limit_2']))
	{
		$mods_rowset = $db->sql_fetchrowset($result_mods);
		include(IP_ROOT_PATH . 'includes/emailer.' . PHP_EXT);
		$server_url = create_server_url();
		$viewtopic_server_url = $server_url . VIEWTOPIC_MG;
		while ($i < $total_mods)
		{
			$emailer = new emailer($board_config['smtp_delivery']);

			$email_headers = 'X-AntiAbuse: Board servername - ' . trim($board_config['server_name']) . "\n";
			$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
			$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
			$email_headers .= 'X-AntiAbuse: User IP - ' . decode_ip($user_ip) . "\n";

			$emailer->use_template('repport_post', (file_exists(IP_ROOT_PATH . 'language/lang_' . $mods_rowset[$i]['user_lang'] . '/email/html/repport_post.tpl')) ? $mods_rowset[$i]['user_lang'] : 'english');
			$emailer->email_address($mods_rowset[$i]['user_email']);
			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);
			$emailer->extra_headers($email_headers);
			//$emailer->set_subject($subject);

			$emailer->assign_vars(array(
				'POST_URL' => $viewtopic_server_url . '?' . $forum_id_append . $topic_id_append . POST_POST_URL . '=' . $post_id . '#p' . $post_id,
				'POST_SUBJECT' => $post_subject,
				'FORUM_NAME' => $forum_name,
				'USER' => '"' . $userdata['username'] . '"',
				'NUMBER_OF_REPPORTS' => $blue_card,
				'SITENAME' => ip_stripslashes($board_config['sitename']),
				'BOARD_EMAIL' => $board_config['board_email']));
			$emailer->send();
			$emailer->reset();
			$i++;
		}
	}
	message_die(GENERAL_MESSAGE, (($total_mods) ? sprintf($lang['Post_repported'], $total_mods) : $lang['Post_repported_1']) . '<br /><br />' . (($board_config['report_forum']) ? sprintf($lang['Send_message'], '<a href="' . append_sid('posting.' . PHP_EXT . '?mode=' . (($allready_reported) ? 'reply&amp;' . POST_TOPIC_URL . '=' . $allready_reported : 'newtopic&amp;' . POST_FORUM_URL . '=' . $board_config['report_forum']) . '&amp;postreport=' . $post_id) . '">', '</a>') : '') . sprintf($lang['Click_return_viewtopic'], '<a href="' . append_sid(VIEWTOPIC_MG . '?' . $forum_id_append . $topic_id_append . POST_POST_URL . '=' . $post_id . '#p' . $post_id). '">', '</a>'));
}
elseif ($mode == 'unban')
{
	$no_error_ban = false;
	if (! $is_auth['auth_greencard'])
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorized']);
	}
	// look up the user
	$sql = 'SELECT user_active, user_warnings FROM ' . USERS_TABLE . ' WHERE user_id="' . $poster_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't obtain judge information.", "", __LINE__, __FILE__, $sql);
	}
	$the_user = $db->sql_fetchrow($result);
	// remove the user from ban list
	$sql = 'DELETE FROM ' . BANLIST_TABLE . ' WHERE ban_userid="' . $poster_id . '"';
	if (! $result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't remove ban_userid info into database", "", __LINE__, __FILE__, $sql);
	}
	// update the user table with new status
	$sql = 'UPDATE ' . USERS_TABLE . ' SET user_warnings="0" WHERE user_id="' . $poster_id . '"';
	if(! $result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't update user status information", "", __LINE__, __FILE__, $sql);
	}
	$message = $lang['Ban_update_green'] . '<br /><br />' . sprintf($lang['Send_PM_user'], '<a href="' . append_sid('privmsg.' . PHP_EXT . '?mode=post&u=' . $poster_id) . '">', '</a>');
	$e_temp = 'ban_reactivated';
	//$e_subj = $lang['Ban_reactivate'];
	$no_error_ban = true;
}
elseif ($mode == 'ban')
{
	$no_error_ban = false;
	if (!$is_auth['auth_ban'])
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorized']);
	}
	// look up the user
	$sql = 'SELECT user_active, user_level FROM ' . USERS_TABLE . ' WHERE user_id="' . $poster_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't obtain judge information.", "", __LINE__, __FILE__, $sql);
	}
	$the_user = $db->sql_fetchrow($result);
	if (($the_user['user_level'] == ADMIN) || ($the_user['user_level'] == JUNIOR_ADMIN))
	{
		message_die(GENERAL_ERROR, $lang['Ban_no_admin']);
	}

	// insert the user in the ban list
	$sql = 'SELECT ban_userid FROM ' . BANLIST_TABLE . ' WHERE ban_userid = "' . $poster_id . '"';
	if($result = $db->sql_query($sql))
	{
		if ((!$db->sql_fetchrowset($result)) && ($poster_id != ANONYMOUS))
		{
			// insert the user in the ban list
			$sql = "INSERT INTO " . BANLIST_TABLE . " (ban_userid) VALUES ($poster_id)";
			if (!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Couldn't insert ban_userid info into database", "", __LINE__, __FILE__, $sql);
			}
			// update the user table with new status
			$sql = 'UPDATE ' . USERS_TABLE . ' SET user_warnings="' . $board_config['max_user_bancard'] . '" WHERE user_id="' . $poster_id . '"';
			if(! $result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Couldn't update user status information", "", __LINE__, __FILE__, $sql);
			}
			$sql = 'UPDATE ' . SESSIONS_TABLE . ' SET session_logged_in="0" WHERE session_user_id="' . $poster_id . '"';
			if (!$db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Couldn't update banned sessions from database", "", __LINE__, __FILE__, $sql);
			}
			$no_error_ban=true;
			$message = $lang['Ban_update_red'];
			$e_temp = 'ban_block';
			//$e_subj = $lang['Card_banned'];
		}
		else
		{
			$no_error_ban = true;
			$message = $lang['user_already_banned'];
		}
	}
	else
	{
		message_die(GENERAL_ERROR, "Couldn't obtain banlist information", "", __LINE__, __FILE__, $sql);
	}
}
elseif ($mode == 'block')
{
	if (empty($board_config['block_time']))
	{
		message_die(GENERAL_ERROR, "Protect user account mod not installed, this is required for this operation");
	}
	$no_error_ban = false;
	if (! $is_auth['auth_ban'])
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorized']);
	}
	// look up the user
	$sql = 'SELECT user_active, user_level FROM ' . USERS_TABLE . ' WHERE user_id="' . $poster_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't obtain judge information.", "", __LINE__, __FILE__, $sql);
	}
	$the_user = $db->sql_fetchrow($result);
	if (($the_user['user_level'] == ADMIN) || ($the_user['user_level'] == JUNIOR_ADMIN))
	{
		message_die(GENERAL_ERROR, $lang['Block_no_admin']);
	}
	// update the user table with new status
	$sql = 'UPDATE ' . USERS_TABLE . ' SET user_block_by="' . $user_ip . '", user_blocktime="' . (time() + $board_config['RY_block_time'] * 60).'" WHERE user_id="' . $poster_id . '"';
	if(! $result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't update user status information", "", __LINE__, __FILE__, $sql);
	}
	$sql = 'UPDATE ' . SESSIONS_TABLE . ' SET session_logged_in="0", session_user_id=".ANONYMOUS." WHERE session_user_id="' . $poster_id . '"';
	if (!$db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't update blocked sessions from database", "", __LINE__, __FILE__, $sql);
	}

	$no_error_ban=true;
	$block_time = make_time_text ($board_config['RY_block_time']);
	$message = sprintf($lang['Block_update'],$block_time) . '<br /><br />' . sprintf($lang['Send_PM_user'], '<a href="' . append_sid('privmsg.' . PHP_EXT . '?mode=post&amp;' . POST_USERS_URL .'=' . $poster_id) . '">', '</a>');
	$e_temp = 'card_block';
	//$e_subj = sprintf($lang['Card_blocked'], $block_time);
}
elseif ($mode == 'warn')
{
	$no_error_ban = false;
	if (!$is_auth['auth_ban'])
	{
		message_die(GENERAL_ERROR, $lang['Not_Authorized']);
	}
	// look up the user
	$sql = 'SELECT user_active, user_warnings, user_level FROM ' . USERS_TABLE . ' WHERE user_id="' . $poster_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't obtain judge information.", "", __LINE__, __FILE__, $sql);
	}
	$the_user = $db->sql_fetchrow($result);
	if (($the_user['user_level'] == ADMIN) || ($the_user['user_level'] == JUNIOR_ADMIN))
	{
		message_die(GENERAL_ERROR, $lang['Ban_no_admin']);
	}

	//update the warning counter
	$sql = 'UPDATE ' . USERS_TABLE . ' SET user_warnings = user_warnings + 1 WHERE user_id = "' . $poster_id . '"';
	if(! $result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't update user status information", "", __LINE__, __FILE__, $sql);
	}

	// se if the user are to be banned, if so do it ...
	if (($the_user['user_warnings'] + 1) >= $board_config['max_user_bancard'])
	{
		$sql = 'SELECT ban_userid FROM ' . BANLIST_TABLE . ' WHERE ban_userid = "' . $poster_id . '"';
		if($result = $db->sql_query($sql))
		{
			if ((!$db->sql_fetchrowset($result)) && ($poster_id != ANONYMOUS))
			{
				// insert the user in the ban list
				$sql = "INSERT INTO " . BANLIST_TABLE . " (ban_userid) VALUES ($poster_id)";
				if (!$result = $db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, "Couldn't insert ban_userid info into database", "", __LINE__, __FILE__, $sql);
				}
				// update the user table with new status
				$sql = 'UPDATE ' . SESSIONS_TABLE . ' SET session_logged_in = "0" WHERE session_user_id = "' . $poster_id . '"';
				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, "Couldn't update banned sessions from database", "", __LINE__, __FILE__, $sql);
				}
				$no_error_ban = true;
				$message = $lang['Ban_update_red'];
				$e_temp = 'ban_block';
				// $e_subj = $lang['Ban_blocked'];
			}
			else
			{
				$no_error_ban = true;
				$message = $lang['user_already_banned'];
			}
		}
		else
		{
			message_die(GENERAL_ERROR, "Couldn't obtain banlist information", "", __LINE__, __FILE__, $sql);
		}
	}
	else
	{
		// the user shall not be baned this time, update the counter
		$message = sprintf($lang['Ban_update_yellow'], ($the_user['user_warnings'] + 1), $board_config['max_user_bancard']) . '<br /><br />' . sprintf($lang['Send_PM_user'], '<a href="' . append_sid('privmsg.' . PHP_EXT . '?mode=post&u=' . $poster_id) . '">', '</a>');
		$no_error_ban = true;
		$e_temp = 'ban_warning';
		// $e_subj = $lang['Ban_warning'];
	}
}

if ($no_error_ban)
{
	$sql = 'SELECT username, user_warnings, user_email, user_lang FROM ' . USERS_TABLE . ' WHERE user_id="' . $poster_id . '"';
	if(!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't find the users personal information", "", __LINE__, __FILE__, $sql);
	}
	$warning_data = $db->sql_fetchrow($result);
	if (!empty($warning_data['user_email']))
	{
		$server_url = create_server_url();
		$viewtopic_server_url = $server_url . VIEWTOPIC_MG;
		$from_email = ($userdata['user_email'] && $userdata['user_viewemail']) ? $userdata['user_email'] : $board_config['board_email'];

		include_once(IP_ROOT_PATH . 'includes/emailer.' . PHP_EXT);
		$emailer = new emailer($board_config['smtp_delivery']);

		$email_headers = 'X-AntiAbuse: Board servername - ' . trim($board_config['server_name']) . "\n";
		$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
		$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
		$email_headers .= 'X-AntiAbuse: User IP - ' . decode_ip($user_ip) . "\n";

		$emailer->use_template($e_temp, $warning_data['user_lang']);
		$emailer->email_address($warning_data['user_email']);
		$emailer->from($from_email);
		$emailer->replyto($from_email);
		$emailer->extra_headers($email_headers);
		//$emailer->set_subject($e_subj);

		$emailer->assign_vars(array(
			'SITENAME' => ip_stripslashes($board_config['sitename']),
			'WARNINGS' => $warning_data['user_warnings'],
			'TOTAL_WARN' => $board_config['max_user_bancard'],
			'POST_URL' => $viewtopic_server_url . '?' . $forum_id_append . $topic_id_append . POST_POST_URL . '=' . $post_id . '#p' . $post_id,
			'EMAIL_SIG' => str_replace("<br />", "\n", "-- \n" . ip_stripslashes($board_config['board_email_sig'])),
			'WARNER' => $userdata['username'],
			'BLOCK_TIME' => $block_time,
			'WARNED_POSTER' => $warning_data['username'])
		);
		$emailer->send();
		$emailer->reset();
	}
	else
	{
		$message .= '<br /><br />' . $lang['user_no_email'];
	}
}
else
{
	$message = 'Error in card.php file';
}

$db->clear_cache('ban_', USERS_CACHE_FOLDER);

$message .= ($post_id != '-1') ? '<br /><br />' . sprintf($lang['Click_return_viewtopic'], '<a href="' . append_sid(VIEWTOPIC_MG . '?' . $forum_id_append . $topic_id_append . POST_POST_URL . '=' . $post_id . '#p' . $post_id) . '">', '</a>') : '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid(FORUM_MG). '">', '</a>');
message_die(GENERAL_MESSAGE, $message);
include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);

?>