<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

if (isset($meta_post_id) && ($meta_post_id > 0))
{
	$sql = "SELECT c.cat_title, f.forum_name, t.topic_title, p.topic_id
					FROM " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f, " . CATEGORIES_TABLE . " c
					WHERE p.post_id = '" . $meta_post_id . "'
						AND t.topic_id = p.topic_id
						AND f.forum_id = p.forum_id
						AND c.cat_id = f.cat_id
					LIMIT 1";
	// Mighty Gorgon: shall we cache this as well? Maybe too many files... better avoid...
	//if($result = $db->sql_query($sql, false, 'posts_meta_', TOPICS_CACHE_FOLDER))
	if($result = $db->sql_query($sql))
	{
		while ($meta_row = $db->sql_fetchrow($result))
		{
			$meta_topic_id = $meta_row['topic_id'];
			$meta_row['cat_title'] = strip_tags(ip_stripslashes($meta_row['cat_title']));
			$meta_row['forum_name'] = strip_tags(ip_stripslashes($meta_row['forum_name']));
			$meta_row['topic_title'] = strip_tags(ip_stripslashes($meta_row['topic_title']));
			/*
			$meta_description = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $meta_row['forum_name'] . ' :: ' . $meta_row['topic_title'];
			$page_title = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $meta_row['forum_name'] . ' :: ' . $page_title;
			*/
			$meta_description = $meta_row['forum_name'] . ' :: ' . $meta_row['topic_title'];
			$page_title = $meta_row['forum_name'] . ' :: ' . $page_title;
		}
		$db->sql_freeresult($result);
	}
	$sql = "SELECT w.word_text
					FROM " . TOPICS_TABLE . " t, " . SEARCH_MATCH_TABLE . " m, " . SEARCH_WORD_TABLE . " w
					WHERE t.topic_id = '" . $meta_topic_id . "'
						AND t.topic_first_post_id = m.post_id
						AND m.word_id = w.word_id
					LIMIT 20";
	$result = CACHE_TOPICS_META ? $db->sql_query($sql, false, 'topics_kw_', TOPICS_CACHE_FOLDER) : $db->sql_query($sql);
	if($result)
	{
		$meta_keywords = '';
		while ($meta_row = $db->sql_fetchrow($result))
		{
			$meta_keywords .= $meta_row['word_text'] . ', ';
		}
		$db->sql_freeresult($result);
	}
}
elseif (isset($meta_topic_id) && ($meta_topic_id > 0))
{
	$sql = "SELECT c.cat_title, f.forum_name, t.topic_title
					FROM " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f, " . CATEGORIES_TABLE . " c
					WHERE t.topic_id = '" . $meta_topic_id . "'
						AND f.forum_id = t.forum_id
						AND c.cat_id = f.cat_id
					LIMIT 1";
	$result = CACHE_TOPICS_META ? $db->sql_query($sql, false, 'topics_meta_', TOPICS_CACHE_FOLDER) : $db->sql_query($sql);
	if($result)
	{
		while ($meta_row = $db->sql_fetchrow($result))
		{
			$meta_row['cat_title'] = strip_tags(ip_stripslashes($meta_row['cat_title']));
			$meta_row['forum_name'] = strip_tags(ip_stripslashes($meta_row['forum_name']));
			$meta_row['topic_title'] = strip_tags(ip_stripslashes($meta_row['topic_title']));
			/*
			$meta_description = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $meta_row['forum_name'] . ' :: ' . $meta_row['topic_title'];
			$page_title = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $meta_row['forum_name'] . ' :: ' . $page_title;
			*/
			$meta_description = $meta_row['forum_name'] . ' :: ' . $meta_row['topic_title'];
			$page_title = $meta_row['forum_name'] . ' :: ' . $page_title;
		}
		$db->sql_freeresult($result);
	}
	$sql = "SELECT w.word_text
					FROM " . TOPICS_TABLE . " t, " . SEARCH_MATCH_TABLE . " m, " . SEARCH_WORD_TABLE . " w
					WHERE t.topic_id = '" . $meta_topic_id . "'
						AND t.topic_first_post_id = m.post_id
						AND m.word_id = w.word_id
					LIMIT 20";
	$result = CACHE_TOPICS_META ? $db->sql_query($sql, false, 'topics_kw_', TOPICS_CACHE_FOLDER) : $db->sql_query($sql);
	if($result)
	{
		$meta_keywords = '';
		while ($meta_row = $db->sql_fetchrow($result))
		{
			$meta_keywords .= $meta_row['word_text'] . ', ';
		}
		$db->sql_freeresult($result);
	}
}
elseif (isset($meta_forum_id) && ($meta_forum_id > 0))
{
	$sql = "SELECT c.cat_title, f.forum_name
					FROM " . FORUMS_TABLE . " f, " . CATEGORIES_TABLE . " c
					WHERE f.forum_id = '" . $meta_forum_id . "'
						AND c.cat_id = f.cat_id
					LIMIT 1";
	if($result = $db->sql_query($sql, false, 'forums_meta_', FORUMS_CACHE_FOLDER))
	{
		while ($meta_row = $db->sql_fetchrow($result))
		{
			$meta_row['cat_title'] = strip_tags(ip_stripslashes($meta_row['cat_title']));
			$meta_row['forum_name'] = strip_tags(ip_stripslashes($meta_row['forum_name']));
			/*
			$meta_description = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $meta_row['forum_name'];
			$meta_keywords = ip_stripslashes($board_config['sitename']) . ', ' . $meta_row['cat_title'] . ', ' . $meta_row['forum_name'] . ', ';
			$page_title = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $page_title;
			*/
			$meta_description = $meta_row['forum_name'];
			$meta_keywords = $meta_row['cat_title'] . ', ' . $meta_row['forum_name'] . ', ';
			//$page_title = $meta_row['cat_title'] . ' :: ' . $page_title;
		}
		$db->sql_freeresult($result);
	}
}
elseif (isset($meta_cat_id) && ($meta_cat_id > 0))
{
	$sql = "SELECT cat_title
					FROM " . CATEGORIES_TABLE . "
					WHERE cat_id = '" . $meta_cat_id . "'
					LIMIT 1";
	if($result = $db->sql_query($sql, false, 'forums_cats_meta_', FORUMS_CACHE_FOLDER))
	{
		while ($meta_row = $db->sql_fetchrow($result))
		{
			$meta_row['cat_title'] = strip_tags(ip_stripslashes($meta_row['cat_title']));
			/*
			$meta_description = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'];
			$meta_keywords = ip_stripslashes($board_config['sitename']) . ', ' . $meta_row['cat_title'] . ', ';
			$page_title = ip_stripslashes($board_config['sitename']) . ' :: ' . $meta_row['cat_title'] . ' :: ' . $page_title;
			*/
			$meta_description = $meta_row['cat_title'];
			$meta_keywords = $meta_row['cat_title'] . ', ';
			//$page_title = $meta_row['cat_title'] . ' :: ' . $page_title;
		}
		$db->sql_freeresult($result);
	}
}
else
{
	/*
	$meta_description = '';
	$meta_keywords = '';
	*/
}

// Mighty Gorgon: shall we UTF8 decode also page_title and metas?
/*
$page_title = ip_utf8_decode($page_title);
$meta_description = ip_utf8_decode($meta_description);
$meta_keywords = ip_utf8_decode($meta_keywords);
*/

?>