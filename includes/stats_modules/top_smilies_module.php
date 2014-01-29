<?php
/**
*
* @package Icy Phoenix
* @version $Id: top_smilies_module.php 64 2008-12-01 21:14:17Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// Top Smilies
$template->assign_vars(array(
	'L_TOP_SMILIES' => $lang['module_name_top_smilies'],
	'L_HOWMANY' => $lang['How_many'],
	'L_RANK' => $lang['Rank'],
	'L_PERCENTAGE' => $lang['Percent'],
	'L_GRAPH' => $lang['Graph'],
	'L_IMAGE' => $lang['smiley_url'],
	'L_CODE' => $lang['smiley_code']
	)
);

//
// Set smile_pref to 0, if you want that smilies are only counted once per post.
// This means that, if the same smilie is entered ten times in a message, only one is counted in that message.
//
$smile_pref = 1;

// Determine if Caching is used
if (!$statistics->result_cache_used)
{
	@set_time_limit(0);

	// Init Cache -- tells the Stats Mod that we want to use the result cache
	$result_cache->init_result_cache();

	// With every new sql_query insult, the Statistics Mod will end the previous Control. ;)
	$sql = "SELECT code, smile_url FROM " . SMILIES_TABLE;
	if (!($result = $stat_db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Couldn\'t retrieve smilies data', '', __LINE__, __FILE__, $sql);
	}

	$rows = $stat_db->sql_fetchrowset($result);
	$num_rows = $stat_db->sql_numrows($result);

	$all_smilies = array();
	$total_smilies = 0;
	$where_query = '';
	$smile_group = array();
	$smile_urls = array();
	$smile_urls['url'] = array();
	$count = 0;

	for ($i = 0; $i < $num_rows; $i++)
	{
		$where_query .= ($where_query == '') ? ' (post_text LIKE \'%' . str_replace("'", "\'", $rows[$i]['code']) . '%\')' : ' OR (post_text LIKE \'%' . str_replace("'", "\'", $rows[$i]['code']) . '%\')';

		if (!in_array($rows[$i]['smile_url'], $smile_urls['url']))
		{
			$smile_urls['url'][] = $rows[$i]['smile_url'];
			$smile_urls[$rows[$i]['smile_url']] = $count;
			$count++;
			$all_smilies[$smile_urls[$rows[$i]['smile_url']]]['code'] = str_replace("'", "\'", $rows[$i]['code']);
			$all_smilies[$smile_urls[$rows[$i]['smile_url']]]['smile_url'] = $rows[$i]['smile_url'];
		}

		$smile_group[$smile_urls[$rows[$i]['smile_url']]]['code'][] = str_replace("'", "\'", $rows[$i]['code']);
		$smile_group[$smile_urls[$rows[$i]['smile_url']]]['url'][] = $rows[$i]['smile_url'];

		$all_smilies[$smile_urls[$rows[$i]['smile_url']]]['count'] = 0;
	}

	$sql = "SELECT post_text
	FROM " . POSTS_TABLE . "
	WHERE " . $where_query . "
	GROUP BY post_text";

	if (!($result = $stat_db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Couldn\'t retrieve smilies data', '', __LINE__, __FILE__, $sql);
	}

	$rows = $stat_db->sql_fetchrowset($result);
	$message = '';
	for ($i = 0; $i < count($rows); $i++)
	{
		$message .= $rows[$i]['post_text'];
	}

	//echo(';' . $message . ';');

	for ($i = 0; $i < count($smile_group); $i++)
	{
		$found = false;
		$match_regexp = '';
		for ($j = 0; $j < count($smile_group[$i]['code']) && ($found == false); $j++)
		{
			if ($smile_pref == 0)
			{
				if (strstr($message, $smile_group[$i]['code'][$j]))
				{
					$all_smilies[$i]['count'] = $all_smilies[$i]['count'] + 1;
					$found = true;
				}
			}
			else
			{
				$match_regexp .= ($match_regexp == '') ? '/(?<=.\W|\W.|^\W)' . preg_quote($smile_group[$i]['code'][$j], "/") . '(?=.\W|\W.|\W$)' : '|(?<=.\W|\W.|^\W)' . preg_quote($smile_group[$i]['code'][$j], "/") . '(?=.\W|\W.|\W$)';
			}
		}

		if (!$found)
		{
			if ($match_regexp != '')
			{
				$match_regexp .= '/';
	//			echo '<br /><br />' . $match_regexp . "<br />";
	//			echo "#".$all_smilies[$i]['smile_url']."#";
				preg_match_all($match_regexp, ' ' . $message . ' ', $matches);
	//			echo "<br />-" . count($matches[0]) . "-<br />";
				$all_smilies[$i]['count'] = $all_smilies[$i]['count'] + count($matches[0]);
			}
		}
	}

	for ($i = 0; $i < count($all_smilies); $i++)
	{
		$total_smilies = $total_smilies + $all_smilies[$i]['count'];
	}

	// Sort array
	$all_smilies = smilies_sort_multi_array_attachment($all_smilies, 'count', 'DESC');

	$limit = ($return_limit > count($all_smilies)) ? count($all_smilies) : $return_limit;

	$firstcount = $all_smilies[0]['count'];

	$template->_tpldata['stats_row.'] = array();
	//reset($template->_tpldata['stats_row.']);

	for ($i = 0; $i < $limit; $i++)
	{
		$class = ($i % 2) ? $theme['td_class2'] : $theme['td_class1'];

		$statistics->do_math($firstcount, $all_smilies[$i]['count'], $total_smilies);

		if ($all_smilies[$i]['count'] != 0)
		{
			$template->assign_block_vars('stats_row', array(
				'RANK' => $i + 1,
				'CLASS' => $class,
				'CODE' => $all_smilies[$i]['code'],
				'USES' => $all_smilies[$i]['count'],
				'PERCENTAGE' => $statistics->percentage,
				'BAR' => $statistics->bar_percent,
				'URL' => '<img src="http://' . $_SERVER['HTTP_HOST'] . $board_config['script_path'] . $board_config['smilies_path'] . '/' . $all_smilies[$i]['smile_url'] . '" alt="' . $all_smilies[$i]['smile_url'] . '" />'
				)
			);
		}

		$result_cache->assign_template_block_vars('topsmilies');
	}
}
else
{
	// Now use the result cache, with block_num_vars we are getting the number of variables within the block
	for ($i = 0; $i < $result_cache->block_num_vars('topsmilies'); $i++)
	{
		$template->assign_block_vars('stats_row', $result_cache->get_block_array('topsmilies', $i));
	}
}

?>