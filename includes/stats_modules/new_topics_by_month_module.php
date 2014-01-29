<?php
/**
*
* @package Icy Phoenix
* @version $Id: new_topics_by_month_module.php 64 2008-12-01 21:14:17Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// New topics by month
$template->assign_vars(array(
	'L_NEWTOPICSBYMONTH' => $lang['module_name_new_topics_by_month'],
	'L_YEAR' => $lang['Year'],
	'L_MONTH' => $lang['Month'],
	'L_NUMBER' => $lang['Number'],
	'L_JAN' => $lang['Month_jan'],
	'L_FEB' => $lang['Month_feb'],
	'L_MAR' => $lang['Month_mar'],
	'L_APR' => $lang['Month_apr'],
	'L_MAY' => $lang['Month_may'],
	'L_JUN' => $lang['Month_jun'],
	'L_JUL' => $lang['Month_jul'],
	'L_AUG' => $lang['Month_aug'],
	'L_SEP' => $lang['Month_sep'],
	'L_OCT' => $lang['Month_oct'],
	'L_NOV' => $lang['Month_nov'],
	'L_DEC' => $lang['Month_dec'])
);

$sql = 'SELECT YEAR(FROM_UNIXTIME(topic_time)) as aar, MONTH(FROM_UNIXTIME(topic_time)) as mnd, COUNT(*) AS ant
	FROM ' . TOPICS_TABLE . '
	GROUP BY YEAR(FROM_UNIXTIME(topic_time)),MONTH(FROM_UNIXTIME(topic_time))
	ORDER BY topic_time';
if (!($result = $stat_db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Couldn\'t retrieve users data', '', __LINE__, __FILE__, $sql);
}

$topics_count = $stat_db->sql_numrows($result);
$topics_data = $stat_db->sql_fetchrowset($result);

$template->_tpldata['stats_row.'] = array();
//reset($template->_tpldata['stats_row.']);

for ($i = 0; $i < $topics_count; $i= $i + $k)
{
	$class = ($i % 2) ? $theme['td_class2'] : $theme['td_class1'];

	$year = $topics_data[$i]['aar'];
	$k = 0;
	for ($j = 0; $j < 12; $j++)
	{
		$m[$j + 1] = 0;
	}
	for ($j = 0; $j < 12; $j++)
	{
		if ($year == $topics_data[$i + $j]['aar'])
		{
			$month = $topics_data[$i + $j]['mnd'];
			$m[$month] = $topics_data[$i + $j]['ant'];
			$k = $k + 1;
		}
	}
	$template->assign_block_vars('stats_row', array(
		'CLASS' => $class,
		'YEAR' => $year,
		'M01' => $m[1],
		'M02' => $m[2],
		'M03' => $m[3],
		'M04' => $m[4],
		'M05' => $m[5],
		'M06' => $m[6],
		'M07' => $m[7],
		'M08' => $m[8],
		'M09' => $m[9],
		'M10' => $m[10],
		'M11' => $m[11],
		'M12' => $m[12])
	);
}

?>