<?php
/**
*
* @package Icy Phoenix
* @version $Id: blocks_imp_visit_counter.php 61 2008-10-30 09:25:26Z Mighty Gorgon $
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

if(!function_exists('imp_visit_counter_block_func'))
{
	function imp_visit_counter_block_func()
	{
		global $template, $lang, $board_config;
		$template->assign_vars(array(
			'VISIT_COUNTER' => sprintf($lang['Visit_counter_statement'], $board_config['visit_counter'] + 1, create_date($board_config['default_dateformat'], $board_config['board_startdate'], $board_config['board_timezone'])),
			'L_VISIT_COUNTER' => $lang['Visit_counter']
			)
		);
	}
}

imp_visit_counter_block_func();

?>