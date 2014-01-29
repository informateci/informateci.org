<?php
/**
*
* @package Icy Phoenix
* @version $Id: blocks_imp_welcome.php 61 2008-10-30 09:25:26Z Mighty Gorgon $
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

if(!function_exists('imp_welcome_block_func'))
{
	function imp_welcome_block_func()
	{
		global $template, $lang, $board_config;
		$template->assign_vars(array(
			'L_WELCOME_MESSAGE' => $lang['Welcome_Message'],
			)
		);
	}
}

imp_welcome_block_func();

?>