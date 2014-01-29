<?php
/**
*
* @package Icy Phoenix
* @version $Id: users_zebra_block.php 76 2009-01-31 21:11:24Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

include_once(IP_ROOT_PATH . 'includes/functions_zebra.' . PHP_EXT);

$friends_list = user_get_zebra_list();
$friends_online_list = user_get_friends_online_list();
$template->set_filenames(array('friends_online' => 'profile_friends_body.tpl'));
if ($friends_list == false)
{
	$template->assign_block_vars('no_friends_online', array());
	$template->assign_block_vars('no_friends_offline', array());
}
else
{
	if ($friends_online_list == false)
	{
		$template->assign_block_vars('no_friends_online', array());
		for ($i = 0; $i < count($friends_list); $i++)
		{
			$template->assign_block_vars('friends_offline', array(
				'USERNAME_FULL' => colorize_username($friends_list[$i]['zebra_id'], $friends_list[$i]['username'], $friends_list[$i]['user_color'], $friends_list[$i]['user_active']),
				)
			);
		}
	}
	else
	{
		$uon = 0;
		$uoff = 0;
		for ($i = 0; $i < count($friends_list); $i++)
		{
			// array_key_exists($friends_list[$i]['zebra_id'], $friends_online_list)
			if (isset($friends_online_list[$friends_list[$i]['zebra_id']]['user_allow_viewonline']))
			{
				if (($friends_online_list[$friends_list[$i]['zebra_id']]['user_allow_viewonline'] == true) || ($userdata['user_level'] == ADMIN))
				{
					$template->assign_block_vars('friends_online', array(
						'USERNAME_FULL' => colorize_username($friends_list[$i]['zebra_id'], $friends_list[$i]['username'], $friends_list[$i]['user_color'], $friends_list[$i]['user_active']),
						)
					);
					$uon++;
				}
				else
				{
					$template->assign_block_vars('friends_offline', array(
						'USERNAME_FULL' => colorize_username($friends_list[$i]['zebra_id'], $friends_list[$i]['username'], $friends_list[$i]['user_color'], $friends_list[$i]['user_active']),
						)
					);
					$uoff++;
				}
			}
			else
			{
				$template->assign_block_vars('friends_offline', array(
					'USERNAME_FULL' => colorize_username($friends_list[$i]['zebra_id'], $friends_list[$i]['username'], $friends_list[$i]['user_color'], $friends_list[$i]['user_active']),
					)
				);
				$uoff++;
			}
		}

		if ($uon == 0)
		{
			$template->assign_block_vars('no_friends_online', array());
		}

		if ($uoff == 0)
		{
			$template->assign_block_vars('no_friends_offline', array());
		}
	}
}
$template->assign_var_from_handle('FRIENDS_ONLINE', 'friends_online');

?>