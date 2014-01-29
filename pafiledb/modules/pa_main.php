<?php
/*
  paFileDB 3.0
  ©2001/2002 PHP Arena
  Written by Todd
  todd@phparena.net
  http://www.phparena.net
  Keep all copyright links on the script visible
  Sub category counting bug fix by Kron
  Please read the license included with this script for more information.
*/

class pafiledb_main extends pafiledb_public
{
	function main($action)
	{
		global $pafiledb_template, $pafiledb_config, $debug, $lang, $board_config, $phpEx, $phpbb_root_path, $theme, $images;
		$pafiledb_template->assign_vars(array(
			'L_HOME' => $lang['Home'],
			'CURRENT_TIME' => sprintf($lang['Current_time'], create_date($board_config['default_dateformat'], time(), $board_config['board_timezone'])),
			'TPL_COLOR' => $theme['body_background'],
			'U_INDEX' => append_sid(PORTAL_MG),
			'U_DOWNLOAD' => append_sid('dload.' . $phpEx),

			'CAT_BLOCK_IMG' => $images['category_block'],
			'SPACER' => $images['spacer'],

			'DOWNLOAD' => $pafiledb_config['settings_dbname'],
			'TREE' => $menu_output
			)
		); 

		//===================================================
		// Show the Category for the download database index
		//===================================================
		$this->category_display();

		$this->display($lang['Download'], 'pa_main_body.tpl');
	}
}

?>