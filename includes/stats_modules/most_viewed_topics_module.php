<?php
/**
*
* @package Icy Phoenix
* @version $Id: most_viewed_topics_module.php 64 2008-12-01 21:14:17Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// Most Viewed Topics
$template->assign_vars(array(
	'L_RANK' => $lang['Rank'],
	'L_VIEWS' => $lang['Views'],
	'L_TOPIC' => $lang['Topic'],
	'MODULE_NAME' => $lang['module_name_most_viewed_topics'])
);

$auth_data_sql = $statistics->forum_auth($userdata);

if ($auth_data_sql == '')
{
	// No authed Forum
	return;
}

$sql = 'SELECT topic_id, topic_title, topic_views
	FROM ' . TOPICS_TABLE .	'
	WHERE forum_id IN (' . $auth_data_sql . ') AND (topic_status <> 2) AND (topic_views > 0)
	ORDER BY topic_views DESC
	LIMIT ' . $return_limit;
if (!($result = $stat_db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Couldn\'t retrieve topic data', '', __LINE__, __FILE__, $sql);
}

$topic_count = $stat_db->sql_numrows($result);
$topic_data = $stat_db->sql_fetchrowset($result);

$template->_tpldata['stats_row.'] = array();
//reset($template->_tpldata['stats_row.']);

for ($i = 0; $i < $topic_count; $i++)
{
	$class = ($i % 2) ? $theme['td_class2'] : $theme['td_class1'];

	$template->assign_block_vars('stats_row', array(
		'RANK' => $i + 1,
		'CLASS' => $class,
		'TITLE' => $topic_data[$i]['topic_title'],
		'VIEWS' => $topic_data[$i]['topic_views'],
		'URL' => append_sid(IP_ROOT_PATH . VIEWTOPIC_MG . '?' . POST_TOPIC_URL . '=' . $topic_data[$i]['topic_id']))
	);
}

?>