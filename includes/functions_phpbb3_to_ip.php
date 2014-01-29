<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}


/**
* Alias for user class
*/
class user
{

	var $lang = array();
	var $help = array();
	var $theme = array();
	var $date_format;
	var $timezone;
	var $dst;

	var $lang_name = false;
	var $lang_id = false;
	var $lang_path;
	var $img_lang;
	var $img_array = array();

	// Able to add new option (id 7)
	var $keyoptions = array('viewimg' => 0, 'viewflash' => 1, 'viewsmilies' => 2, 'viewsigs' => 3, 'viewavatars' => 4, 'viewcensors' => 5, 'attachsig' => 6, 'bbcode' => 8, 'smilies' => 9, 'popuppm' => 10);
	var $keyvalues = array();

	var $cookie_data = array();
	var $page = array();
	var $data = array();
	var $browser = '';
	var $forwarded_for = '';
	var $host = '';
	var $session_id = '';
	var $ip = '';
	var $load = 0;
	var $time_now = 0;
	var $update_session_page = true;

	/**
	* Session begin
	*/
	function session_begin()
	{
		global $board_config, $config, $userdata, $user_ip;

		$config = &$board_config;

		$userdata = session_pagestart($user_ip);

		return true;
	}

	/**
	* User setup
	*/
	function setup()
	{
		global $userdata, $lang;

		init_userprefs($userdata);

		$this->data = &$userdata;
		$this->lang = &$lang;

		$this->data['is_registered'] = $userdata['session_logged_in'] ;

		return true;
	}

	/**
	* More advanced language substitution
	* Function to mimic sprintf() with the possibility of using phpBB's language system to substitute nullar/singular/plural forms.
	* Params are the language key and the parameters to be substituted.
	* This function/functionality is inspired by SHS` and Ashe.
	*
	* Example call: <samp>$user->lang('NUM_POSTS_IN_QUEUE', 1);</samp>
	*/
	function lang()
	{
		$args = func_get_args();
		$key = $args[0];

		if (is_array($key))
		{
			$lang = &$this->lang[array_shift($key)];

			foreach ($key as $_key)
			{
				$lang = &$lang[$_key];
			}
		}
		else
		{
			$lang = &$this->lang[$key];
		}

		// Return if language string does not exist
		if (!isset($lang) || (!is_string($lang) && !is_array($lang)))
		{
			return $key;
		}

		// If the language entry is a string, we simply mimic sprintf() behaviour
		if (is_string($lang))
		{
			if (sizeof($args) == 1)
			{
				return $lang;
			}

			// Replace key with language entry and simply pass along...
			$args[0] = $lang;
			return call_user_func_array('sprintf', $args);
		}

		// It is an array... now handle different nullar/singular/plural forms
		$key_found = false;

		// We now get the first number passed and will select the key based upon this number
		for ($i = 1, $num_args = sizeof($args); $i < $num_args; $i++)
		{
			if (is_int($args[$i]))
			{
				$numbers = array_keys($lang);

				foreach ($numbers as $num)
				{
					if ($num > $args[$i])
					{
						break;
					}

					$key_found = $num;
				}
			}
		}

		// Ok, let's check if the key was found, else use the last entry (because it is mostly the plural form)
		if ($key_found === false)
		{
			$numbers = array_keys($lang);
			$key_found = end($numbers);
		}

		// Use the language string we determined and pass it to sprintf()
		$args[0] = $lang[$key_found];
		return call_user_func_array('sprintf', $args);
	}

	/**
	* Add lang files
	*/
	function add_lang($lang_set, $use_db = false, $use_help = false)
	{
		global $board_config, $lang;

		if (!empty($lang_set) && !is_array($lang_set))
		{
			$lang_file = IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/' . $lang_set . '.' . PHP_EXT;
			if (@file_exists($lang_file))
			{
				@include($lang_file);
			}
		}
		elseif (!empty($lang_set) && is_array($lang_set))
		{
			foreach ($lang_set as $key => $lang_file)
			{
				$lang_file = IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/' . $lang_file . '.' . PHP_EXT;
				if (@file_exists($lang_file))
				{
					@include($lang_file);
				}
			}
		}

		$this->lang = &$lang;

		return true;
	}

	/**
	* Date format
	*/
	function format_date($timestamp)
	{
		global $board_config;

		$output_date = create_date_ip($board_config['default_dateformat'], $timestamp, $board_config['board_timezone']);

		return $output_date;
	}

}

/**
* Alias for auth class
*/
class auth
{
	var $acl = array();
	var $cache = array();
	var $acl_options = array();
	var $acl_forum_ids = false;

	/**
	* ACL
	*/
	function acl(&$userdata)
	{

		return true;
	}

	/**
	* ACL GET
	*/
	function acl_get($opt, $f = 0)
	{
		global $userdata;
		$return_value = true;

		if (substr($opt, 0, 2) === 'a_')
		{
			$return_value = (($userdata['user_level'] == ADMIN) ? true : false);
		}
		elseif ((substr($opt, 0, 2) === 'm_') || (substr($opt, 0, 2) === 'f_'))
		{
			$return_value = ((($userdata['user_level'] == ADMIN) || ($userdata['user_level'] == MOD)) ? true : false);
		}

		return $return_value;
	}

}

/**
* Page Header
*/
function page_header($title)
{
	global $page_title, $meta_description, $meta_keywords;

	$page_title = !empty($page_title) ? $page_title : $title;
	$meta_description = !empty($meta_description) ? $meta_description : '';
	$meta_keywords = !empty($meta_keywords) ? $meta_keywords : '';
	include(IP_ROOT_PATH . 'includes/page_header.' . PHP_EXT);

	return true;
}

/**
* Page Footer
*/
function page_footer()
{
	include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);

	return true;
}

/**
* BBCodes
*/

/**
* Censor
*/
function censor_text($message)
{
	global $board_config, $userdata;

	if (!$userdata['user_allowswearywords'])
	{
		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);
	}

	if (!empty($orig_word) && count($orig_word) && !$userdata['user_allowswearywords'])
	{
		$message = str_replace('\"', '"', substr(preg_replace('#(\>(((?>([^><]+|(?R)))*)\<))#se', "preg_replace(\$orig_word, \$replacement_word, '\\0')", '>' . $message . '<'), 1, -1));
	}

	return $message;
}

/**
* nl2br
*/
function bbcode_nl2br($message)
{

	return $message;
}

/**
* Smileys
*/
function smiley_text($message)
{

	return $message;
}

if (empty($bbcode))
{
	include_once(IP_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);
}

class phpbb3_bbcode extends BBCode
{

	/**
	* Smileys
	*/
	function bbcode_second_pass($message, $bbcode_uid, $bbcode_bitfield)
	{
		global $board_config, $userdata;

		$this->allow_html = ($userdata['user_allowhtml'] && $board_config['allow_html']) ? true : false;
		$this->allow_bbcode = ($userdata['user_allowbbcode'] && $board_config['allow_bbcode']) ? true : false;
		$this->allow_smilies = ($userdata['user_allowsmile'] && $board_config['allow_smilies']) ? true : false;
		$message = $this->parse($message);

		return $message;
	}

}

// Initialazing classes...
$user = new user();
$auth = new auth();
unset($bbcode);
$bbcode = new phpbb3_bbcode();

?>