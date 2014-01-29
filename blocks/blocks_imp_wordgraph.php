<?php
/**
*
* @package Icy Phoenix
* @version $Id: blocks_imp_wordgraph.php 61 2008-10-30 09:25:26Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* masterdavid - Ronald John David
* Bicet
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

if(!function_exists('imp_wordgraph_func'))
{
	function imp_wordgraph_func()
	{
		global $lang, $template, $board_config, $db, $cms_config_vars, $block_id;

		$template->_tpldata['wordgraph_loop.'] = array();

		$words_array = array();

		$sql = 'SELECT w.word_text, COUNT(*) AS word_count
			FROM ' . SEARCH_WORD_TABLE . ' AS w, ' . SEARCH_MATCH_TABLE . ' AS m
			WHERE m.word_id = w.word_id
			GROUP BY m.word_id
			ORDER BY word_count DESC LIMIT ' . intval($cms_config_vars['md_wordgraph_words'][$block_id]);
		if (!($result = $db->sql_query($sql, false, 'wordgraph_')))
		{
			message_die(GENERAL_ERROR, 'Could not obtain word list', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result))
		{
			$word = strtolower($row['word_text']);
			$word_count = $row['word_count'];
			$words_array[$word] = $word_count;
		}

		$minimum = 1000000;
		$maximum = -1000000;

		foreach (array_keys($words_array) as $word)
		{
			if ($words_array[$word] > $maximum)
			{
				$maximum = $words_array[$word];
			}

			if ($words_array[$word] < $minimum)
			{
				$minimum = $words_array[$word];
			}
		}

		$words = array_keys($words_array);
		sort($words);

		foreach ($words as $word)
		{
			$ratio = intval(mt_rand(8, 14));
			$template->assign_block_vars('wordgraph_loop', array(
				'WORD' => ($cms_config_vars['md_wordgraph_count'][$block_id]) ? $word . ' (' . $words_array[$word] . ')' : $word,
				'WORD_FONT_SIZE' => $ratio,
				'WORD_SEARCH_URL' => append_sid(SEARCH_MG . '?search_keywords=' . urlencode($word)),
				)
			);
		}

		$template->assign_vars(array(
			'L_WORDGRAPH' => $lang['Wordgraph'],
			)
		);
	}
}

imp_wordgraph_func();

?>