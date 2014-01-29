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
* Smartor: EzPortal, phpBB Fetch Posts
* Ca5ey and Mouse Hover Topic Preview MOD by Shannado
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

include_once(IP_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);

function phpbb_fetch_posts($forum_sql, $number_of_posts, $text_length)
{
	global $db, $board_config, $bbcode, $userdata;

	$sql = 'SELECT t.topic_id, t.topic_time, t.topic_title, t.topic_desc, t.forum_id, t.topic_poster, t.topic_first_post_id, t.topic_status, t.topic_replies, p.post_id, p.enable_smilies, p.post_text, p.post_text_compiled, u.username, u.user_id, u.user_active, u.user_color
			FROM ' . TOPICS_TABLE . ' AS t, ' . USERS_TABLE . ' AS u, ' . POSTS_TABLE . ' AS p
			WHERE t.forum_id IN (' . $forum_sql . ')
				AND t.topic_time <= ' . time() . '
				AND t.topic_poster = u.user_id
				AND t.topic_first_post_id = p.post_id
				AND t.topic_status <> 2
			ORDER BY t.topic_time DESC';
	if ($number_of_posts != 0)
	{
		$sql .= ' LIMIT 0,' . $number_of_posts;
	}

	// query the database
	if(!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not query announcements information', '', __LINE__, __FILE__, $sql);
	}

	// fetch all postings
	$posts = array();
	if ($row = $db->sql_fetchrow($result))
	{
		$i = 0;
		do
		{
			$posts[$i]['enable_smilies'] = $row['enable_smilies'];
			$posts[$i]['post_text'] = $row['post_text'];
			$posts[$i]['forum_id'] = $row['forum_id'];
			$posts[$i]['topic_id'] = $row['topic_id'];
			$posts[$i]['topic_replies'] = $row['topic_replies'];
			$posts[$i]['topic_time'] = create_date_ip($board_config['default_dateformat'], $row['topic_time'], $board_config['board_timezone']);
			$posts[$i]['topic_title'] = $row['topic_title'];
			$posts[$i]['topic_desc'] = $row['topic_desc'];
			$posts[$i]['user_id'] = $row['user_id'];
			$posts[$i]['username'] = $row['username'];
			$posts[$i]['user_active'] = $row['user_active'];
			$posts[$i]['user_color'] = $row['user_color'];

			$message_compiled = empty($posts[$i]['post_text_compiled']) ? false : $posts[$i]['post_text_compiled'];

			$bbcode->allow_bbcode = $board_config['allow_bbcode'];
			$bbcode->allow_html = $board_config['allow_html'];
			if ($board_config['allow_smilies'] && !$lofi)
			{
				$bbcode->allow_smilies = $board_config['allow_smilies'];
			}
			else
			{
				$bbcode->allow_smilies = false;
			}

			$clean_tags = false;
			if ((strlen($posts[$i]['post_text']) > $text_length) && ($text_length > 0))
			{
				$clean_tags = true;
				$posts[$i]['striped'] = 1;
			}

			if($message_compiled === false)
			{
				$bbcode->allow_smilies = ($board_config['allow_smilies'] && $posts[$i]['enable_smilies']) ? true : false;
				$posts[$i]['post_text'] = $bbcode->parse($posts[$i]['post_text'], '', false, $clean_tags);
			}
			else
			{
				$posts[$i]['post_text'] = $message_compiled;
			}

			if ($clean_tags == true)
			{
				$posts[$i]['post_text'] = (strlen($posts[$i]['post_text']) > $text_length) ? substr($posts[$i]['post_text'], 0, $text_length) . ' ...' : $posts[$i]['post_text'];
			}

			// define censored word matches
			$orig_word = array();
			$replacement_word = array();
			obtain_word_list($orig_word, $replacement_word);
			// censor text and title
			if (!empty($orig_word) && count($orig_word) && !$userdata['user_allowswearywords'])
			{
				$posts[$i]['topic_title'] = preg_replace($orig_word, $replacement_word, $posts[$i]['topic_title']);
				$posts[$i]['post_text'] = preg_replace($orig_word, $replacement_word, $posts[$i]['post_text']);
			}
			$posts[$i]['post_text'] = nl2br($posts[$i]['post_text']);
			$i++;
		}
		while ($row = $db->sql_fetchrow($result));
	}
	// return the result
	return $posts;
} // phpbb_fetch_posts

function phpbb_fetch_posts_attach($forum_sql, $number_of_posts, $text_length, $show_portal = true, $random_mode = false, $single_post = false, $only_auth_view = true)
{
	global $db, $board_config, $bbcode, $userdata;

	$except_forums = build_exclusion_forums_list($only_auth_view);

	$add_to_sql = '';
	if (($single_post == false) && !empty($forum_sql))
	{
		$except_forums_exp = explode(',', str_replace(' ', '', $except_forums));
		$allowed_forums_exp = explode(',', str_replace(' ', '', $forum_sql));
		$except_forums = '';
		for ($e = 0; $e < count($except_forums_exp); $e++)
		{
			if (!in_array($except_forums_exp[$e], $allowed_forums_exp))
			{
				$except_forums .= ($except_forums == '') ? $except_forums_exp[$e] : (', ' . $except_forums_exp[$e]);
			}
		}
		$add_to_sql .= ' AND t.forum_id IN (' . $forum_sql . ')';
		$add_to_sql .= ' AND t.forum_id NOT IN (' . $except_forums . ')';
	}
	else
	{
		$add_to_sql .= ' AND t.forum_id NOT IN (' . $except_forums . ')';
	}

	if ($show_portal == true)
	{
		$add_to_sql .= ' AND t.topic_show_portal = 1';
	}

	if ($random_mode == false)
	{
		$order_sql = 't.topic_time DESC';
	}
	else
	{
		$order_sql = 'RAND()';
	}

	if ($number_of_posts != 0)
	{
		$limit_sql = ' LIMIT 0,' . $number_of_posts;
	}
	else
	{
		$limit_sql = '';
	}

	if ($single_post == true)
	{
		$single_post_id = $forum_sql;
		$sql = "SELECT p.post_id, p.topic_id, p.forum_id, p.enable_smilies, p.post_attachment, p.enable_autolinks_acronyms, p.post_text, p.post_text_compiled, t.forum_id, t.topic_time, t.topic_title, t.topic_attachment, t.topic_replies, u.username, u.user_id, u.user_active, u.user_color
				FROM " . POSTS_TABLE . " AS p, " . TOPICS_TABLE . " AS t, " . USERS_TABLE . " AS u
				WHERE p.post_id = '" . $single_post_id . "'
					" . $add_to_sql . "
					AND t.topic_id = p.topic_id
					AND p.poster_id = u.user_id";
	}
	else
	{
		$sql = "SELECT t.topic_id, t.topic_time, t.topic_title, t.forum_id, t.topic_poster, t.topic_first_post_id, t.topic_status, t.topic_show_portal, t.topic_attachment, t.topic_replies, u.username, u.user_id, u.user_active, u.user_color, p.post_id, p.enable_smilies, p.post_attachment, p.enable_autolinks_acronyms, p.post_text, p.post_text_compiled
				FROM " . TOPICS_TABLE . " AS t, " . USERS_TABLE . " AS u, " . POSTS_TABLE . " AS p
				WHERE t.topic_time <= " . time() . "
					" . $add_to_sql . "
					AND t.topic_poster = u.user_id
					AND t.topic_first_post_id = p.post_id
					AND t.topic_status <> 2
				ORDER BY " . $order_sql . $limit_sql;
	}
	// query the database
	if(!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not query announcements information', '', __LINE__, __FILE__, $sql);
	}

	$orig_autolink = array();
	$replacement_autolink = array();
	obtain_autolink_list($orig_autolink, $replacement_autolink, $forum_sql);

	// fetch all postings
	$posts = array();
	if ($row = $db->sql_fetchrow($result))
	{
		$i = 0;
		do
		{
			$posts[$i]['enable_smilies'] = $row['enable_smilies'];
			$posts[$i]['enable_autolinks_acronyms'] = $row['enable_autolinks_acronyms'];
			$posts[$i]['post_text'] = $row['post_text'];
			$message = $posts[$i]['post_text'];
			$posts[$i]['forum_id'] = $row['forum_id'];
			$posts[$i]['topic_id'] = $row['topic_id'];
			$posts[$i]['topic_replies'] = $row['topic_replies'];
			$posts[$i]['topic_time'] = create_date_ip($board_config['default_dateformat'], $row['topic_time'], $board_config['board_timezone']);
			$posts[$i]['topic_title'] = $row['topic_title'];
			$posts[$i]['user_id'] = $row['user_id'];
			$posts[$i]['username'] = $row['username'];
			$posts[$i]['user_active'] = $row['user_active'];
			$posts[$i]['user_color'] = $row['user_color'];
			$posts[$i]['topic_attachment'] = $row['topic_attachment'];
			$posts[$i]['post_id'] = $row['post_id'];
			$posts[$i]['post_attachment'] = $row['post_attachment'];

			$message_compiled = empty($posts[$i]['post_text_compiled']) ? false : $posts[$i]['post_text_compiled'];

			$bbcode->allow_bbcode = $board_config['allow_bbcode'];
			$bbcode->allow_html = $board_config['allow_html'];
			if ($board_config['allow_smilies'] && !$lofi)
			{
				$bbcode->allow_smilies = $board_config['allow_smilies'];
			}
			else
			{
				$bbcode->allow_smilies = false;
			}

			$clean_tags = false;
			if ((strlen($posts[$i]['post_text']) > $text_length) && ($text_length > 0))
			{
				$clean_tags = true;
				$posts[$i]['striped'] = 1;
			}

			if($message_compiled === false)
			{
				$bbcode->allow_smilies = ($board_config['allow_smilies'] && $posts[$i]['enable_smilies']) ? true : false;
				$posts[$i]['post_text'] = $bbcode->parse($posts[$i]['post_text'], '', false, $clean_tags);
			}
			else
			{
				$posts[$i]['post_text'] = $message_compiled;
			}

			if ($clean_tags == true)
			{
				$posts[$i]['post_text'] = (strlen($posts[$i]['post_text']) > $text_length) ? substr($posts[$i]['post_text'], 0, $text_length) . ' ...' : $posts[$i]['post_text'];
			}

			// define censored word matches
			$orig_word = array();
			$replacement_word = array();
			obtain_word_list($orig_word, $replacement_word);
			// censor text and title
			if (!empty($orig_word) && count($orig_word) && !$userdata['user_allowswearywords'])
			{
				$posts[$i]['topic_title'] = preg_replace($orig_word, $replacement_word, $posts[$i]['topic_title']);
				$posts[$i]['post_text'] = preg_replace($orig_word, $replacement_word, $posts[$i]['post_text']);
			}
			//Acronyms, AutoLinks, Wrap - BEGIN
			if ($posts[$i]['enable_autolinks_acronyms'] == 1)
			{
				$posts[$i]['post_text'] = $bbcode->acronym_pass($posts[$i]['post_text']);
				if(count($orig_autolink))
				{
					$posts[$i]['post_text'] = autolink_transform($posts[$i]['post_text'], $orig_autolink, $replacement_autolink);
				}
				//$posts[$i]['post_text'] = kb_word_wrap_pass($posts[$i]['post_text']);
			}
			//Acronyms, AutoLinks, Wrap -END
			$i++;
		}
		while ($row = $db->sql_fetchrow($result));
	}
	$db->sql_freeresult($result);
	// return the result
	return $posts;
}


function phpbb_fetch_poll($forum_sql)
{
	global $db;

	$sql = 'SELECT t.*, vd.*
					FROM ' . TOPICS_TABLE . ' AS t, ' . VOTE_DESC_TABLE  . ' AS vd
			WHERE t.forum_id IN (' . $forum_sql . ')
				AND t.topic_status <> 1
				AND t.topic_status <> 2
				AND t.topic_vote = 1
				AND t.topic_id = vd.topic_id
			ORDER BY t.topic_time DESC
			LIMIT 0,1';

	if (!$query = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not query poll information', '', __LINE__, __FILE__, $sql);
	}

	$result = $db->sql_fetchrow($query);

	if ($result)
	{
		$sql = 'SELECT * FROM ' . VOTE_RESULTS_TABLE . '
				WHERE vote_id = ' . $result['vote_id'] . '
				ORDER BY vote_option_id';

		if (!$query = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not query vote result information', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($query))
		{
			$result['options'][] = $row;
		}
	}

	return $result;
} // end func phpbb_fetch_poll

?>