<?php
/**
*
* @package Icy Phoenix
* @version $Id: users_gender_module.php 64 2008-12-01 21:14:17Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// Gender SQL
$template->assign_vars(array(
	'L_RANK' => $lang['Rank'],
	'L_USERS' => $lang['Users'],
	'L_PERCENTAGE' => $lang['Percent'],
	'L_GENDER' => $lang['Gender'],
	'L_GRAPH' => $lang['Graph'],
	'MODULE_NAME' => $lang['module_name_users_gender']
	)
);

define('NO_GENDER', 0);
define('MALE', 1);
define('FEMALE', 2);

$rank = 0;

$sql = 'SELECT COUNT(user_gender) used_counter, user_gender
	FROM ' . USERS_TABLE . '
	WHERE user_id != -1
	GROUP BY user_gender ORDER BY used_counter DESC';
if (!$result = $stat_db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Couldn't retrieve users data", '', __LINE__, __FILE__, $sql);
}

$user_count = $stat_db->sql_numrows($result);
$user_data = $stat_db->sql_fetchrowset($result);
$percentage = 0;
$bar_percent = 0;
$usercount = $board_config['max_users'];

$firstcount = $user_data[0]['used_counter'];
$cst = ($firstcount > 0) ? 90 / $firstcount : 90;

$template->_tpldata['stats_row.'] = array();
//reset($template->_tpldata['stats_row.']);

for ($i = 0; $i < $user_count; $i++)
{

	$class = ($i % 2) ? $theme['td_class2'] : $theme['td_class1'];

	$percentage = 0;
	$bar_percent = 0;
	if ($user_data[$i]['used_counter'] != 0)
	{
		$percentage = round(min(100, ($user_data[$i]['used_counter'] /$usercount) * 100));
		$bar_percent = round($user_data[$i]['used_counter'] * $cst);
	}

	switch ($user_data[$i]['user_gender'])
	{
		case NO_GENDER: $gender = $lang['No_gender_specify']; $gender_image =''; break;
		case MALE: $gender = '<img src="' . $images['icon_minigender_male'] . '" border="0" alt="' . $lang['Male'] . '" />'; break;
		case FEMALE: $gender = '<img src="' . $images['icon_minigender_female'] . '" border="0" alt="' . $lang['Female'] . '" />'; break;
	}

	$template->assign_block_vars('stats_row', array(
		'RANK' => $i + 1,
		'CLASS' => $class,
		'GENDER' => $gender,
		'USERS' => $user_data[$i]['used_counter'],
		'PERCENTAGE' => $percentage,
		'BAR' => $bar_percent
		)
	);
}

?>