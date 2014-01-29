<?php
/**
*
* @package Icy Phoenix
* @version $Id: functions_stats.php 76 2009-01-31 21:11:24Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* (c) 2002 Meik Sievertsen (Acyd Burn)
*
*/

//
// Modules should be considered to already have access to the following variables which
// the parser will give out to it:

// $return_limit - Control Panel defined number of items to display
// $module_info['name'] - The module name specified in the info.txt file
// $module_info['email'] - The author email
// $module_info['author'] - The author name
// $module_info['version'] - The version
// $module_info['url'] - The author url
//
// To make the module more compatible, please do not use any functions here
// and put all your code inline to keep from redeclaring functions on accident.
//

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// For backward compatibility
function module_language_parse($lang_key, $lang_var)
{
	global $lang;
	$lang[$lang_key] = $lang_var;
}

function sql_quote($data)
{
	//$data = str_replace("'", "\'", $data);
	$data = ((STRIP) ? addslashes($data) : $data);
	return ($data);
}

function generate_module_info($module_data, $install = false)
{
	global $db, $__stats_config;

	$module_dir = trim($module_data['name']);

	// Get Info from Cache or not...
	$condition_mode = false;
	$ret_array['condition_result'] = true;
	$condition = '';

	if ($module_data['module_info_time'] == filemtime(IP_ROOT_PATH . $__stats_config['modules_dir'] . '/' . $module_dir . '_info.txt'))
	{
		$ret_array = unserialize(stripslashes($module_data['module_info_cache']));
	}
	else
	{
		$extra_info_mode = false;
		$ret_array['default_update_time'] = 0;
		$data_file = @file(IP_ROOT_PATH . trim($__stats_config['modules_dir']) . '/' . $module_dir . '_info.txt');

		while (list($key, $data) = @each($data_file))
		{
			if ((!$extra_info_mode) && (!$condition_mode))
			{
				if (preg_match("/\[name\]/", $data))
				{
					$ret_array['name'] = trim(str_replace("[name]", '', $data));
				}
				elseif (preg_match("/\[author\]/", $data))
				{
					$ret_array['author'] = trim(str_replace("[author]", '', $data));
				}
				elseif (preg_match("/\[email\]/", $data))
				{
					$ret_array['email'] = trim(str_replace("[email]", '', $data));
				}
				elseif (preg_match("/\[url\]/", $data))
				{
					$ret_array['url'] = trim(str_replace("[url]", '', $data));
				}
				elseif (preg_match("/\[version\]/", $data))
				{
					$ret_array['version'] = trim(str_replace("[version]", '', $data));
				}
				elseif (preg_match("/\[update_time\]/", $data))
				{
					$ret_array['default_update_time'] = trim(str_replace("[update_time]", '', $data));
				}
				elseif (preg_match("/\[stats_mod_version\]/", $data))
				{
					$ret_array['stats_mod_version'] = trim(str_replace("[stats_mod_version]", '', $data));
				}
				elseif (preg_match("/\[extra_info\]/", $data))
				{
					$extra_info_mode = true;
					$ret_array['extra_info'] =  trim(str_replace("[extra_info]", '', $data));
				}
			}
			else
			{
				if ($extra_info_mode)
				{
					if (preg_match("/\[\/extra_info\]/", $data))
					{
						$extra_info_mode = false;
					}
					else
					{
						$ret_array['extra_info'] .= $data;
					}
				}
			}
		}

		$sql = "UPDATE " . MODULES_TABLE . "
			SET module_info_cache = '" . addslashes(serialize($ret_array)) . "',
			module_info_time = " . filemtime(IP_ROOT_PATH . $__stats_config['modules_dir'] . '/' . $module_dir . '_info.txt') . "
			WHERE module_id = " . intval($module_data['module_id']);
		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update Info Cache', '', __LINE__, __FILE__, $sql);
		}

	}

	$ret_array['dname'] = $module_dir;
	$ret_array['update_time'] = $module_data['update_time'];
	$ret_array['auth_value'] = $module_data['auth_value'];
	$ret_array['active'] = $module_data['active'];

	if ($install)
	{
		$data_file = @file(IP_ROOT_PATH . trim($__stats_config['modules_dir']) . '/' . $module_dir . '_info.txt');

		while (list($key, $data) = @each($data_file))
		{
			if (!$condition_mode)
			{
				if (preg_match("/\[condition\]/", $data))
				{
					$condition_mode = true;
					$condition =  trim(str_replace("[condition]", '', $data));
				}
			}
			else
			{
				if (preg_match("/\[\/condition\]/", $data))
				{
					$condition_mode = false;
				}
				else
				{
					$condition .= $data;
				}
			}
		}

		// Parse the condition
		if ($condition != '')
		{
			$return_val = true;
			eval($condition);
			$ret_array['condition_result'] = $return_val;
		}
	}

	return $ret_array;
}

// Get and update Module List
function update_module_list()
{
	global $db, $__stats_config;

	// Returns a list of modules found by directory and updates the database as needed
	$ret_list = array();

	$handle = @opendir(IP_ROOT_PATH . $__stats_config['modules_dir']);

	if (!$handle)
	{
		message_die(GENERAL_ERROR, "Unable to open directory " . IP_ROOT_PATH . $__stats_config['modules_dir']);
	}

	$modules_list = '';
	$module_suffix = '_module.' . PHP_EXT;

	while ($file = readdir($handle))
	{
		// Mighty Gorgon: Make sure we have a module by checking the substring
		if (($file != '.') && ($file != '..') && (substr($file, -strlen($module_suffix)) == $module_suffix))
		{
			$module_name = str_replace($module_suffix, '', $file);
			$modules_list .= ($modules_list == '') ? "'$module_name'" : ", '$module_name'";

			$sql = "SELECT MAX(display_order) as max
			FROM " . MODULES_TABLE;

			if (!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Unable to select display order', '', __LINE__, __FILE__, $sql);
			}

			$row = $db->sql_fetchrow($result);

			$curr_max = $row['max'];

			$sql = "SELECT module_id, name, display_order, active
			FROM " . MODULES_TABLE . "
			WHERE (name = '" . trim($module_name) . "')";

			if (!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not query Modules Table', '', __LINE__, __FILE__, $sql);
			}

			if ($db->sql_numrows($result) == 0)
			{
				$sql = "SELECT MAX(module_id) as next_id FROM " . MODULES_TABLE;

				if (!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Unable to get next Module ID', '', __LINE__, __FILE__, $sql);
				}

				$row = $db->sql_fetchrow($result);
				$next_id = $row['next_id'] + 1;

				$sql = "INSERT INTO  " . MODULES_TABLE . "
				(module_id, name, display_order, module_info_cache, module_db_cache, module_result_cache)
				VALUES (" . $next_id . ", '" . trim($module_name) . "', " . ($curr_max + 10) . ", '', '', '')";

				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Could not insert data into Modules Table', '', __LINE__, __FILE__, $sql);
				}

				$sql = "SELECT module_id, display_order, active
				FROM " . MODULES_TABLE . "
				WHERE module_id = " . $next_id;

				if (!$result = $db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Unable to select created Module Entry', '', __LINE__, __FILE__, $sql);
				}

				$row = $db->sql_fetchrow($result);
			}
			else
			{
				$row = $db->sql_fetchrow($result);
			}

		}
	}

	// Kill old module folders that were deleted
	$sql = "DELETE FROM " . MODULES_TABLE . "
	WHERE (name NOT IN ($modules_list))";

	if (!$db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not delete obsolete Modules', '', __LINE__, __FILE__, $sql);
	}
}

// Get complete Module List from Database
function get_module_list_from_db()
{
	global $db, $__stats_config;

	// Returns a list of modules stored in the database
	$ret_list = array();

	$sql = "SELECT module_id, name, display_order
	FROM " . MODULES_TABLE . "
	WHERE (active = 1) AND (installed = 1)
	ORDER BY display_order ASC";

	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not get Module List', '', __LINE__, __FILE__, $sql);
	}

	if ($db->sql_numrows($result) != 0)
	{
		$rows = $db->sql_fetchrowset($result);

		for ($i = 0; $i < count($rows); $i++)
		{
			$ret_list[$rows[$i]['module_id']] = $rows[$i]['name'];
		}
	}

	return ($ret_list);
}

// Get complete Module Data from Database
function get_module_data_from_db()
{
	global $db, $__stats_config;

	// Returns a list of modules stored in the database
	$ret_list = array();

	$sql = "SELECT *
	FROM " . MODULES_TABLE . "
	WHERE (active = 1) AND (installed = 1)
	ORDER BY display_order ASC";

	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not get Module List', '', __LINE__, __FILE__, $sql);
	}

	if (($num_rows = $db->sql_numrows($result)) != 0)
	{
		$rows = $db->sql_fetchrowset($result);

		for ($i = 0; $i < $num_rows; $i++)
		{
			$ret_list[$rows[$i]['module_id']] = $rows[$i];
		}
	}

	return ($ret_list);
}


// Check Module Authentication Only ALL, REG and ADMIN is supported
function module_auth_check($module_data, $userdata)
{
	// FALSE = Not Authorized
	// TRUE = Authorized
	global $db;

	$auth_value = intval($module_data['auth_value']);

	switch ($auth_value)
	{
		case AUTH_ALL:
			return (true);
			break;

		case AUTH_REG:
			if ($userdata['session_logged_in'] && ($userdata['user_id'] != ANONYMOUS))
			{
				return (true);
			}
			else
			{
				return (false);
			}
			break;

		case AUTH_MOD:
			if ($userdata['session_logged_in'] && (($userdata['user_level'] == ADMIN) || ($userdata['user_level'] == MOD)))
			{
				return (true);
			}
			else
			{
				return (false);
			}
			break;

		case AUTH_ADMIN:
			if ($userdata['session_logged_in'] && ($userdata['user_level'] == ADMIN))
			{
				return (true);
			}
			else
			{
				return (false);
			}
			break;
	}

	return (false);
}

// FUNCTIONS
// sort multi-dimensional array - from File Attachment Mod
// Used in TOP SMILEYS!
function smilies_sort_multi_array_attachment ($sort_array, $key, $sort_order)
{
	$last_element = count($sort_array) - 1;

	$string_sort = (is_string($sort_array[$last_element-1][$key])) ? true : false;

	for ($i = 0; $i < $last_element; $i++)
	{
		$num_iterations = $last_element - $i;

		for ($j = 0; $j < $num_iterations; $j++)
		{
			$next = 0;

			// do checks based on key
			$switch = false;
			if (!($string_sort))
			{
				if ((($sort_order == 'DESC') && (intval($sort_array[$j][$key]) < intval($sort_array[$j + 1][$key]))) || (($sort_order == 'ASC') && (intval($sort_array[$j][$key]) > intval($sort_array[$j + 1][$key]))))
				{
					$switch = true;
				}
			}
			else
			{
				if ((($sort_order == 'DESC') && (strcasecmp($sort_array[$j][$key], $sort_array[$j + 1][$key]) < 0)) || (($sort_order ==   'ASC') && (strcasecmp($sort_array[$j][$key], $sort_array[$j + 1][$key]) > 0)))
				{
					$switch = true;
				}
			}

			if ($switch)
			{
				$temp = $sort_array[$j];
				$sort_array[$j] = $sort_array[$j + 1];
				$sort_array[$j + 1] = $temp;
			}
		}
	}

	return ($sort_array);
}

?>