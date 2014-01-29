<?php
/**
*
* @package Icy Phoenix
* @version $Id: usercp_signature.php 93 2009-04-01 18:06:23Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
	exit;
}

// get the board & user settings ...
$html_on = ($userdata['user_allowhtml'] && $board_config['allow_html']) ? 1 : 0 ;
$bbcode_on = ($userdata['user_allowbbcode'] && $board_config['allow_bbcode']) ? 1 : 0 ;
$smilies_on = ($userdata['user_allowsmile'] && $board_config['allow_smilies']) ? 1 : 0 ;

$bbcode->allow_html = $html_on;
$bbcode->allow_bbcode = $bbcode_on;
$bbcode->allow_smilies = $smilies_on;
$bbcode->is_sig = true;

if (!empty($_POST['message']))
{
	$_POST['signature'] = $_POST['message'];
}

// check and set various parameters
$params = array('submit' => 'save', 'preview' => 'preview', 'mode' => 'mode');
while(list($var, $param) = @each($params))
{
	if (!empty($_POST[$param]) || !empty($_GET[$param]))
	{
		$$var = (!empty($_POST[$param])) ? $_POST[$param] : $_GET[$param];
	}
	else
	{
		$$var = '';
	}
}

$trim_var_list = array('signature' => 'signature');
while(list($var, $param) = @each($trim_var_list))
{
	if (!empty($_POST[$param]))
	{
		$$var = trim($_POST[$param]);
	}
}

$signature = str_replace('<br />', "\n", $signature);

// if cancel pressed then redirect to the index page
if (isset($_POST['cancel']))
{
	$redirect = FORUM_MG;

// redirect 2.0.4 only
	redirect(append_sid($redirect, true));

// redirect 2.0.x
//	$header_location = (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE'))) ? 'Refresh: 0; URL=' : 'Location: ';
//	header($header_location . append_sid($redirect, true));
//	exit;

}

include_once(IP_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);
include_once(IP_ROOT_PATH . 'includes/functions_post.' . PHP_EXT);

$page_title = $lang['Signature'];
$meta_description = '';
$meta_keywords = '';
$link_name = $lang['Signature'];
$nav_server_url = create_server_url();
$breadcrumbs_address = $lang['Nav_Separator'] . '<a href="' . $nav_server_url . append_sid('profile_main.' . PHP_EXT) . '"' . (!empty($link_name) ? '' : ' class="nav-current"') . '>' . $lang['Profile'] . '</a>' . (!empty($link_name) ? ($lang['Nav_Separator'] . '<a class="nav-current" href="#">' . $link_name . '</a>') : '');
include(IP_ROOT_PATH . 'includes/page_header.' . PHP_EXT);

// save new signature
if ($submit)
{
	$template->assign_block_vars('switch_save_sig', array());

	if (isset($signature))
	{
		if (strlen($signature) > $board_config['max_sig_chars'])
		{
			$save_message = $lang['Signature_too_long'];
		}

		else
		{
			$signature = prepare_message($signature, $html_on, $bbcode_on, $smilies_on);
			$user_id =  $userdata['user_id'];

			$sql = "UPDATE " . USERS_TABLE . "
			SET user_sig = '" . str_replace("\'", "''", $signature) . "'
			WHERE user_id = $user_id";

			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}

			else
			{
				$save_message = $lang['sig_save_message'];
			}
		}
	}
	else
	{
		message_die(GENERAL_MESSAGE, 'An Error occured while submitting Signature');
	}
}

// catch the submitted message and prepare it for a preview
elseif ($preview)
{
	$template->assign_block_vars('switch_preview_sig', array());

	if (isset($signature))
	{
		$preview_sig = $signature;

		if (strlen($preview_sig) > $board_config['max_sig_chars'])
		{
			$preview_sig = $lang['Signature_too_long'];
		}
		else
		{
			$preview_sig = htmlspecialchars(stripslashes($preview_sig));
			$preview_sig = stripslashes(prepare_message(addslashes(unprepare_message($preview_sig)), $html_on, $bbcode_on, $smilies_on));
			if($preview_sig != '')
			{
				$bbcode->is_sig = true;
				$preview_sig = $bbcode->parse($preview_sig);
				$bbcode->is_sig = false;
				$preview_sig = '<br />' . $board_config['sig_line'] . '<br />' . $preview_sig;
				//$preview_sig = nl2br($preview_sig);
				if (!$userdata['user_allowswearywords'])
				{
					$orig_word = array();
					$replacement_word = array();
					obtain_word_list($orig_word, $replacement_word);
				}
				if (!empty($orig_word) && count($orig_word) && !$userdata['user_allowswearywords'])
				{
					$preview_sig = preg_replace($orig_word, $replacement_word, $preview_sig);
				}
				//$preview_sig = kb_word_wrap_pass ($preview_sig);

			}
			else
			{
				$preview_sig = $lang['sig_none'];
			}
		}
	}
	else
	{
		message_die(GENERAL_MESSAGE, 'An Error occured while submitting Signature');
	}
}

// read current signature and prepare it for a preview
elseif ($mode)
{

	$template->assign_block_vars('switch_current_sig', array());

	$signature = $userdata['user_sig'];
	//$user_sig = prepare_message($userdata['user_sig'], $html_on, $bbcode_on, $smilies_on);
	$user_sig = $userdata['user_sig'];

	if($user_sig != '')
	{
		$bbcode->is_sig = true;
		$user_sig = $bbcode->parse($user_sig);
		$bbcode->is_sig = false;
		if(!empty($orig_word))
		{
			$user_sig = (!empty($user_sig)) ? preg_replace($orig_word, $replacement_word, $user_sig) : '';
		}
		$user_sig = '<br />' . $board_config['sig_line'] . '<br />' . $user_sig;
		//$user_sig = nl2br($user_sig);
	}
	else
	{
		$user_sig = $lang['sig_none'];
	}
}

$template->set_filenames(array('body' => 'profile_signature.tpl'));

$template->assign_vars(array(
	'SIG_SAVE' => $lang['sig_save'],
	'SIG_CANCEL' => $lang['Cancel'],
	'SIG_PREVIEW' => $lang['Preview'],
	'SIG_EDIT' => $lang['sig_edit'],
	'SIG_CURRENT' => $lang['sig_current'],
	'SIG_LINK' => append_sid(PROFILE_MG . '?mode=signature'),

	'L_SIGNATURE' => $lang['Signature'],
	'L_SIGNATURE_EXPLAIN' => sprintf($lang['Signature_explain'], $board_config['max_sig_chars']),
	'HTML_STATUS' => $html_status,
	'BBCODE_STATUS' => sprintf($bbcode_status, '<a href="' . append_sid('faq.' . PHP_EXT . '?mode=bbcode') . '" target="_blank">', '</a>'),
	'SMILIES_STATUS' => $smilies_status,

	'SIGNATURE' => stripslashes($signature),
	'CURRENT_PREVIEW' => $user_sig,
	'PREVIEW' => htmlspecialchars(stripslashes($signature)),
	'REAL_PREVIEW' => $preview_sig,
	'SAVE_MESSAGE' => $save_message,
	)
);

// BBCBMG - BEGIN
//$bbcbmg_in_acp = true;
include(IP_ROOT_PATH . 'includes/bbcb_mg.' . PHP_EXT);
$template->assign_var_from_handle('BBCB_MG', 'bbcb_mg');
// BBCBMG - END
// BBCBMG SMILEYS - BEGIN
include_once(IP_ROOT_PATH . 'includes/functions_post.' . PHP_EXT);
generate_smilies('inline');
include(IP_ROOT_PATH . 'includes/bbcb_smileys_mg.' . PHP_EXT);
$template->assign_var_from_handle('BBCB_SMILEYS_MG', 'bbcb_smileys_mg');
// BBCBMG SMILEYS - END

$template->pparse('body');

include(IP_ROOT_PATH . 'includes/page_tail.' . PHP_EXT);

?>
