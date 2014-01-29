<?php
/**
*
* @package Icy Phoenix
* @version $Id: blocks_imp_statistics.php 76 2009-01-31 21:11:24Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* masterdavid - Ronald John David
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

if(!function_exists('imp_statistics_block_func'))
{
	function imp_statistics_block_func()
	{
		global $template, $lang, $board_config;
		$total_topics = $board_config['max_topics'];
		$total_posts = $board_config['max_posts'];
		$total_topics = $board_config['max_topics'];
		$total_posts = $board_config['max_posts'];
		$total_users = $board_config['max_users'];
		$newest_userdata['user_id'] = $board_config['last_user_id'];
		$newest_user = colorize_username($newest_userdata['user_id']);
		$newest_uid = $newest_userdata['user_id'];

		if($total_posts == 0)
		{
			$l_total_post_s = $lang['Posted_articles_zero_total'];
		}
		elseif($total_posts == 1)
		{
			$l_total_post_s = $lang['Posted_article_total'];
		}
		else
		{
			$l_total_post_s = $lang['Posted_articles_total'];
		}

		if($total_users == 0)
		{
			$l_total_user_s = $lang['Registered_users_zero_total'];
		}
		elseif($total_users == 1)
		{
			$l_total_user_s = $lang['Registered_user_total'];
		}
		else
		{
			$l_total_user_s = $lang['Registered_users_total'];
		}

		$template->assign_vars(array(
			'TOTAL_USERS' => sprintf($l_total_user_s, $total_users),
			'NEWEST_USER' => sprintf($lang['Newest_user'], '', $newest_user, ''),
			'TOTAL_POSTS' => sprintf($l_total_post_s, $total_posts),
			'TOTAL_TOPICS' => sprintf($lang['total_topics'], $total_topics)
			)
		);
	}
}

imp_statistics_block_func();

?>