<?php
/**
*
* @package Icy Phoenix
* @version $Id: functions_jr_admin.php 80 2009-02-19 13:45:54Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

define('EXPLODE_SEPERATOR_CHAR', '|');
//define('JR_ADMIN_DIR', ADM .'/');
define('COPYRIGHT_NIVISEC_FORMAT', '<br /><div class="copyright" style="text-align:center;"> %s &copy; %s <a href="http://www.nivisec.com">Nivisec.com</a></div>');


if (!function_exists('copyright_nivisec'))
{
	/**
	* @return void
	* @desc Prints a sytlized line of copyright for module
	*/
	function copyright_nivisec($name, $year)
	{
		printf(COPYRIGHT_NIVISEC_FORMAT, $name, $year);
	}
}

if (!function_exists('find_lang_file_nivisec'))
{
	/**
	* @return boolean
	* @param filename string
	* @desc Tries to locate and include the specified language file.  Do not include the .php extension!
	*/
	function find_lang_file_nivisec($filename)
	{
		global $lang, $board_config;

		if (file_exists(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/' . $filename . '.' . PHP_EXT))
		{
			include_once(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/' . $filename . '.' . PHP_EXT);
		}
		elseif (file_exists(IP_ROOT_PATH . "language/lang_english/$filename." . PHP_EXT))
		{
			include_once(IP_ROOT_PATH . "language/lang_english/$filename." . PHP_EXT);
		}
		else
		{
			message_die(GENERAL_ERROR, "Unable to find a suitable language file for $filename!", '');
		}
		return true;
	}
}

if (!function_exists('config_update_nivisec'))
{
	/**
	* @return boolean
	* @param item string
	* @param value string
	* @param prefix [optional]string
	* @desc Updates a configuration item.  If the 3rd param is specified, that text is cut off before insertion.  Assumes $status_message is predefined.
	*/
	function config_update_nivisec($item, $value, $prefix = '')
	{
		global $board_config, $db, $status_message, $lang;

		if ($prefix != '') $item = preg_replace("/^$prefix/", '', $item);
		//Only bother updating if the value is different
		if ($board_config[$item] != $value)
		{
			$sql = 'UPDATE ' . CONFIG_TABLE . "
				SET config_value = '$value'
				WHERE config_name = '$item'";
			if (!$db->sql_query($sql))
			{
				return false;
			}
			$board_config[$item] = $value;
			$status_message .= sprintf($lang['Updated_Config'], $lang[$item]);
		}
		return true;
	}
}
if (!function_exists('set_filename_nivisec'))
{
	/**
	* @return boolean
	* @param filename string
	* @param handle string
	* @desc Sets the filename to handle in the $template class.  Saves typing for me :)
	*/
	function set_filename_nivisec($handle, $filename)
	{
		global $template;

		$template->set_filenames(array(
			$handle => $filename
			)
		);

		return true;
	}
}

if (!function_exists('sql_query_nivisec'))
{
	/**
	* @return array
	* @param sql string
	* @param error string
	* @param fast boolean
	* @param return_items int
	* @desc Does $sql query and returns a list if $fast = false.  $error displayed on error.  if $return_items = 1, then only the first row data is returned.  Usefull when querying unique entries.
	*/
	function sql_query_nivisec($sql, $error, $fast = true, $return_items = 0, $cache_sql = false)
	{
		global $db;

		switch($fast)
		{
			case true:
			{
				if ($cache_sql == true)
				{
					if (!$db->sql_query($sql, false, 'nivisec_'))
					{
						message_die(GENERAL_ERROR, $error, '', __LINE__, __FILE__, $sql);
					}
				}
				else
				{
					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, $error, '', __LINE__, __FILE__, $sql);
					}
				}
				return false;
			}
			case false:
			{
				if ($cache_sql == true)
				{
					//if (!$result = $db->sql_query($sql, false, 'nivisec_'))
					if (!$result = $db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, $error, '', __LINE__, __FILE__, $sql);
					}
				}
				else
				{
					if (!$result = $db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, $error, '', __LINE__, __FILE__, $sql);
					}
				}
				if ($return_items != 1)
				{
					return ($db->sql_fetchrowset($result));
				}
				else
				{
					return ($db->sql_fetchrow($result));
				}
			}

		}
	}
}

function jr_admin_check_file_hashes($file)
{
	global $userdata;

	//Include the file to get the module list
	$setmodules = 1;
	$file_full_path = IP_ROOT_PATH . ADM . '/' . $file;
	include($file_full_path);
	unset($setmodules);

	$jr_admin_userdata = jr_admin_get_user_info($userdata['user_id']);

	$user_modules = explode(EXPLODE_SEPERATOR_CHAR, $jr_admin_userdata['user_jr_admin']);

	foreach($module as $cat => $module_data)
	{
		foreach($module_data as $module_name => $module_file)
		{
			//Remove sid if we find one
			$module_file = preg_replace("/(\?|&|&amp;)sid=[A-Z,a-z,0-9]{32}/", '', $module_file);
			//Make our unique ID
			$file_hash = md5($cat . $module_name . $module_file);
			//See if it is in the array
			if (in_array($file_hash, $user_modules))
			{
				return true;
			}
		}
	}

	//If we get this far, the user has no business with the module filename
	return false;
}

function jr_admin_get_module_list($user_module_list = false)
{
	global $db, $lang, $board_config, $userdata;

	/* Debugging for this function. Debugging in this function causes changes to the way ADMIN users
	are interpreted.  You are warned */
	$debug = false;
	/* Even more debug info! */
	$verbose = false;

	// We need this for regular expressions... to avoid errors!!!
	$phpEx = PHP_EXT;

	//Read all the modules
	$setmodules = 1;
	$dir = @opendir(IP_ROOT_PATH . ADM . '/');
	$pattern = "/^admin_.+\.$phpEx$/";
	while (($file = @readdir($dir)) !== false)
	{
		if (preg_match($pattern, $file))
		{
			//include(IP_ROOT_PATH . ADM . '/' . $file);
			include_once(IP_ROOT_PATH . ADM . '/' . $file);
		}
	}
	@closedir($dir);
	unset($setmodules);

	@ksort($module);
	if ($debug && $verbose)
	{
		print '<pre><span class="text_green"><span class="gensmall">DEBUG - Module List Non Cache - <br />';
		print_r($module);
		print '</span></span><br /></pre>';
	}

	//Get the cache list we have and find non-existing and new items
	foreach ($module as $cat => $item_array)
	{
		foreach ($item_array as $module_name => $filename)
		{
			//Remove sid in case some retarted person appended it early *(cough admin_disallow.php cough)*
			$filename = preg_replace("/(\?|&|&amp;)sid=[A-Z,a-z,0-9]{32}/", '', $filename);
			if ($debug && $verbose) print "<span class=\"gensmall\"><span class=\"text_red\">DEBUG - filename = $filename</span></span><br />";
			//Note the md5 function compilation here to make a unique id
			$file_hash = md5($cat . $module_name . $filename);

			//Wee a 3-D array of our info!
			if ($user_module_list && (($userdata['user_level'] != ADMIN) || $debug))
			{
				//If we were passed a list of valid modules, make sure we are sending the correct list back
				$user_modules = explode(EXPLODE_SEPERATOR_CHAR, $user_module_list);
				if (in_array($file_hash, $user_modules))
				{
					$module_list[$cat][$module_name]['filename'] = $filename;
					$module_list[$cat][$module_name]['file_hash'] = $file_hash;
					if (isset($ja_module[$cat][$module_name]))
					{
						$module_list[$cat][$module_name]['junior_admin'] = $ja_module[$cat][$module_name];
					}
					else
					{
						$module_list[$cat][$module_name]['junior_admin'] = true;
					}
				}
			}
			else
			{
				//No list sent? Send back all of them because we should be an ADMIN!
				$module_list[$cat][$module_name]['filename'] = $filename;
				$module_list[$cat][$module_name]['file_hash'] = $file_hash;
				if (isset($ja_module[$cat][$module_name]))
				{
					$module_list[$cat][$module_name]['junior_admin'] = $ja_module[$cat][$module_name];
				}
				else
				{
					$module_list[$cat][$module_name]['junior_admin'] = true;
				}
			}
		}
	}

	return $module_list;
}

function jr_admin_secure($file)
{
	global $db, $lang, $userdata;

	/* Debugging in this function causes changes to the way ADMIN users are interpreted. You are warned */
	$debug = false;

	// We need this for regular expressions... to avoid errors!!!
	$phpEx = PHP_EXT;

	$jr_admin_userdata = jr_admin_get_user_info($userdata['user_id']);

	if ($debug)
	{
		if (!preg_match("/^index.$phpEx/", $file))
		{
			print '<pre><span class="gen"><span class="text_red">DEBUG - File Accessed - ';
			print $file;
			print '</pre></span></span><br />';
		}
	}
	if (($userdata['user_level'] == ADMIN) && !$debug)
	{
		//Admin always has access
		return true;
	}
	elseif (empty($jr_admin_userdata['user_jr_admin']))
	{
		//This user has no modules and no business being here
		return false;
	}
	elseif (preg_match("/^index.$phpEx/", $file))
	{
		//We are at the index file, which is already secure pretty much
		return true;
	}
	elseif (isset($_GET['module']) && in_array($_GET['module'], explode(EXPLODE_SEPERATOR_CHAR, $jr_admin_userdata['user_jr_admin'])))
	{
		//The user has access for sure by module_id security from GET vars only
		return true;
	}
	elseif (!isset($_GET['module']) && count($_POST))
	{
		//This user likely entered a post form, so let's use some checking logic
		//to make sure they are doing it from where they should be!

		//Get the filename without any arguments
		$file = preg_replace("/\?.+=.*$/", '', $file);
		//Return the check to make sure the user has access to what they are submitting
		return jr_admin_check_file_hashes($file);
	}
	elseif (!isset($_GET['module']) && isset($_GET['sid']))
	{
		//This user has clicked on a url that specified items
		if ($_GET['sid'] != $userdata['session_id'])
		{
			return false;
		}
		else
		{
			//Get the filename without any arguments
			$file = preg_replace("/\?.+=.*$/", '', $file);
			//Return the check to make sure the user has access to what they are submitting
			return jr_admin_check_file_hashes($file);
		}
	}
	else
	{
		//Something came up that shouldn't have!
		return false;
	}
}

function jr_admin_include_all_lang_files()
{
	global $lang, $board_config;

	// We need this for regular expressions... to avoid errors!!!
	$phpEx = PHP_EXT;

	$dir = @opendir(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang']);
	$pattern = "/^lang.+\.$phpEx$/";
	while (($file = @readdir($dir)) !== false)
	{
		if (preg_match($pattern, $file))
		{
			include_once(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/' . $file);
		}
	}
	@closedir($dir);
}

function jr_admin_make_left_pane()
{
	global $template, $lang, $module, $userdata, $theme;

	jr_admin_include_all_lang_files();
	include_once(IP_ROOT_PATH . ADM . '/acp_icons.' . PHP_EXT);

	// We need this for regular expressions... to avoid errors!!!
	$phpEx = PHP_EXT;

	@ksort($module);
	//Loop through and set up all the nice form names, etc
	//+MOD: DHTML Menu for ACP
	$menu_cat_id = 0;
	//-MOD: DHTML Menu for ACP
	foreach ($module as $cat => $module_array)
	{
		$cat_icon = '<img src="' . (isset($acp_icon[$cat]) ? $acp_icon[$cat] : $acp_icon['default']) . '" alt="' . $cat_name . '" style="vertical-align:middle;" />&nbsp;';
		$cat_name = ((isset($lang[$cat])) ? $lang[$cat] : preg_replace("/_/", ' ', $cat));
		$template->assign_block_vars('catrow', array(
			//+MOD: DHTML Menu for ACP
			'MENU_CAT_ID' => $menu_cat_id,
			'MENU_CAT_ROWS' => count($action_array),
			'MENU_CAT_ICON' => $cat_icon,
			//-MOD: DHTML Menu for ACP
			'ADMIN_CATEGORY' => $cat_name
			)
		);
	//die($cat);
		@ksort($module_array);
		$i = 0;
		foreach ($module_array as $module_name => $data_array)
		{
			//Compile our module url with lots of options
			$module_url = $data_array['filename'];
			$module_url .= (preg_match("/^.*\.$phpEx\?/", $module_url)) ? '&amp;' : '?';
			$module_url .= 'sid=' . $userdata['session_id'] . '&amp;module=' . $data_array['file_hash'];

			$template->assign_block_vars('catrow.modulerow', array(
				'ROW_CLASS' => (++$i % 2) ? $theme['td_class1'] : $theme['td_class2'],
				//+MOD: DHTML Menu for ACP
				'ROW_COUNT' => $i,
				//-MOD: DHTML Menu for ACP
				'ADMIN_MODULE' => (isset($lang[$module_name])) ? $lang[$module_name] : preg_replace("/_/", ' ', $module_name),
				'U_ADMIN_MODULE' => $module_url
				)
			);
		}
		//+MOD: DHTML Menu for ACP
		$menu_cat_id++;
		//-MOD: DHTML Menu for ACP
	}
}

function jr_admin_make_info_box()
{
	global $template, $lang, $module, $userdata, $board_config;

	/* Debug?  Changes the status stnading of ADMIN!!!  You are warned */
	$debug = false;

	if (($userdata['user_level'] != ADMIN) || $debug)
	{
		find_lang_file_nivisec('lang_jr_admin');

		$jr_admin_userdata = jr_admin_get_user_info($userdata['user_id']);

		$template->set_filenames(array('JR_ADMIN_INFO' => ADM_TPL . 'jr_admin_user_info_header.tpl'));

		$template->assign_vars(array(
			'JR_ADMIN_START_DATE' => create_date($board_config['default_dateformat'], $jr_admin_userdata['start_date'], $board_config['board_timezone']),
			'JR_ADMIN_UPDATE_DATE' => create_date($board_config['default_dateformat'], $jr_admin_userdata['update_date'], $board_config['board_timezone']),
			'JR_ADMIN_ADMIN_NOTES' => $jr_admin_userdata['admin_notes'],
			'L_VERSION' => $lang['Version'],
			'L_JR_ADMIN_TITLE' => $lang['Junior_Admin_Info'],
			'VERSION' => MOD_VERSION,
			'L_MODULE_COUNT' => $lang['Module_Count'],
			'L_NOTES' => $lang['Notes'],
			'L_ALLOW_VIEW' => $lang['Allow_View'],
			'L_START_DATE' => $lang['Start_Date'],
			'L_UPDATE_DATE' => $lang['Update_Date'],
			'L_ADMIN_NOTES' => $lang['Admin_Notes']
			)
		);

		//Switch the info area if allowed to view it
		if ($jr_admin_userdata['notes_view'])
		{
			$template->assign_block_vars('jr_admin_info_switch', array());
		}

		$template->assign_var_from_handle('JR_ADMIN_INFO_TABLE', 'JR_ADMIN_INFO');
	}
}

function jr_admin_get_user_info($user_id)
{
	global $lang;
	//Do the query and get the results, return the user row as well.
	return (
	sql_query_nivisec(
	"SELECT * FROM " . JR_ADMIN_TABLE . "
	WHERE user_id = '" . $user_id . "'",
	sprintf($lang['ERROR_TABLE'], JR_ADMIN_TABLE), false, 1, true));
}

function jr_admin_make_admin_link()
{
	global $lang, $userdata;

	if (!$userdata['session_logged_in'])
	{
		return '&nbsp;';
	}

	$full_server_url = create_server_url();
	if ($userdata['user_level'] == ADMIN)
	{
		return '<a href="' . $full_server_url . ADM . '/index.' . PHP_EXT . '?sid=' . $userdata['session_id'] . '">' . $lang['Admin_panel'] . '</a>';
	}

	$jr_admin_userdata = jr_admin_get_user_info($userdata['user_id']);

	if (!empty($jr_admin_userdata['user_jr_admin']))
	{
		return '<a href="' . $full_server_url . ADM . '/index.' . PHP_EXT . '?sid=' . $userdata['session_id'] . '">' . $lang['Admin_panel'] . '</a>';
	}
	else
	{
		return '&nbsp;';
	}
}

// Check founder id
function check_acp_module_access()
{
	global $userdata;
	$is_allowed = true;

	if (defined('MAIN_ADMINS_ID'))
	{
		$is_allowed = false;
		$allowed_admins = explode(',', MAIN_ADMINS_ID);
		if (defined('FOUNDER_ID'))
		{
			if ($userdata['user_id'] == FOUNDER_ID)
			{
				$is_allowed = true;
			}
		}
		if ($is_allowed == false)
		{
			for ($i = 0; $i < count($allowed_admins); $i++)
			{
				if ($userdata['user_id'] == $allowed_admins[$i])
				{
					$is_allowed = true;
					return true;
				}
			}
		}
		if ($is_allowed == false)
		{
			return false;
		}
	}
	return $is_allowed;
}

?>