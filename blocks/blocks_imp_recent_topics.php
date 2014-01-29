<?php

/**

*

* @package Icy Phoenix

* @version $Id: blocks_imp_recent_topics.php 110 2009-07-14 08:09:47Z Mighty Gorgon $

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



if(!function_exists('imp_recent_topics_block_func'))

{

	function imp_recent_topics_block_func()

	{

		global $template, $cms_config_vars, $block_id, $userdata, $board_config, $db, $var_cache, $lang, $bbcode;

		@include_once(IP_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);



		$template->_tpldata['recent_topic_row.'] = array();



		$bbcode->allow_html = $html_on;

		$bbcode->allow_bbcode = $bbcode_on;

		$bbcode->allow_smilies = $smilies_on;



		$except_forums = build_exclusion_forums_list();



		$current_time = time();

		$extra = "AND t.topic_time <= $current_time";


//	FOR INFUSION ADD p.post_username, p.ext_site_id
		$sql = "SELECT t.topic_id, t.topic_title, t.topic_last_post_id, t.forum_id, p.post_id, p.post_username, p.ext_site_id, p.poster_id, p.post_time, u.user_id, u.username, u.user_active, u.user_color

			FROM " . TOPICS_TABLE . " AS t, " . POSTS_TABLE . " AS p, " . USERS_TABLE . " AS u

			WHERE t.forum_id NOT IN (" . $except_forums . ")

				AND t.topic_status <> 2

				AND p.post_id = t.topic_last_post_id

				AND p.poster_id = u.user_id

				$extra

			ORDER BY p.post_time DESC

			LIMIT " . $cms_config_vars['md_num_recent_topics'][$block_id];


		if (!$result1 = $db->sql_query($sql))

		{

			message_die(GENERAL_ERROR, 'Could not query recent topics information', '', __LINE__, __FILE__, $sql);

		}

		$number_recent_topics = $db->sql_numrows($result1);

		$recent_topic_row = array();

		while ($row1 = $db->sql_fetchrow($result1))

		{

			$recent_topic_row[] = $row1;

		}



		if($cms_config_vars['md_recent_topics_style'][$block_id])

		{

			$style_row = 'scroll';

		}

		else

		{

			$style_row = 'static';

		}



		$template->assign_block_vars($style_row, '');



		for ($i = 0; $i < $number_recent_topics; $i++)

		{

			$orig_word = array();

			$replacement_word = array();

			obtain_word_list($orig_word, $replacement_word);



			if (!empty($orig_word))

			{

				$recent_topic_row[$i]['topic_title'] = (!empty($recent_topic_row[$i]['topic_title'])) ? preg_replace($orig_word, $replacement_word, $recent_topic_row[$i]['topic_title']) : '';

			}



			// Convert and clean special chars!

			$topic_title = htmlspecialchars_clean($recent_topic_row[$i]['topic_title']);
			
			$postername = colorize_username($recent_topic_row[$i]['user_id'], $recent_topic_row[$i]['username'], $recent_topic_row[$i]['user_color'], $recent_topic_row[$i]['user_active']);
			
	if( ( $recent_topic_row[$i]['user_id'] == ANONYMOUS ) && ( $recent_topic_row[$i]['forum_id'] = "19" ) && ( $recent_topic_row[$i]['ext_site_id'] > "0" ) )
	{
			$postername = $recent_topic_row[$i]['post_username'];
	}
			
			
			
			$template->assign_block_vars($style_row . '.recent_topic_row', array(

				'U_TITLE' => append_sid(VIEWTOPIC_MG . '?' . POST_FORUM_URL . '=' . $recent_topic_row[$i]['forum_id'] . '&amp;' . POST_TOPIC_URL . '=' . $recent_topic_row[$i]['topic_id'] . '&amp;' . POST_POST_URL . '=' . $recent_topic_row[$i]['post_id']) . '#p' . $recent_topic_row[$i]['post_id'],

				'L_TITLE' => $topic_title,

				'L_BY' => $lang['By'],

				'L_ON' => $lang['POSTED_ON'],

				'S_POSTER' => $postername,

				'S_POSTTIME' => create_date_ip($board_config['default_dateformat'], $recent_topic_row[$i]['post_time'], $board_config['board_timezone'])

				)

			);

		}

	}

}



imp_recent_topics_block_func();



?>