<?php
/**
*
* @package Icy Phoenix
* @version $Id: stopwords.php 96 2009-04-27 16:48:19Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/*
* This script is meant to remove from search tables all stopwords and smileys
* to reduce table size but also to allow better tags indexing
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);
include(IP_ROOT_PATH . 'includes/functions_search.' . PHP_EXT);

// Start session management
$userdata = session_pagestart($user_ip);
init_userprefs($userdata);
// End session management

if(!$userdata['session_logged_in'])
{
	redirect(append_sid(LOGIN_MG . '?redirect=stopwords.' . PHP_EXT, true));
	/*
	$header_location = (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE'))) ? 'Refresh: 0; URL=' : 'Location: ';
	header($header_location . append_sid(LOGIN_MG . "?redirect=stopwords." . PHP_EXT, true));
	exit;
	*/
}

if($userdata['user_level'] != ADMIN)
{
	message_die(GENERAL_MESSAGE, 'You are not authorized to access this page');
}

$stopwords_array = file(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/search_stopwords.txt');

$exclude_list = '';
foreach($stopwords_array as $curr_word)
{
	$exclude_list .= (($exclude_list != '') ? ', ' : '') . "'" . trim($curr_word) . "'";
}

// Smileys
$sql = "SELECT code FROM " . SMILIES_TABLE;
if ($result = $db->sql_query($sql))
{
	while ($row = $db->sql_fetchrow($result))
	{
		$exclude_list .= (($exclude_list != '') ? ', ' : '') . "'" . trim(ereg_replace("[^A-Za-z0-9]", '', $row['code'])) . "'";
	}
	$db->sql_freeresult($result);
}

$sql = "SELECT word_id
	FROM " . SEARCH_WORD_TABLE . "
	WHERE word_text IN (" . $exclude_list . ")";
if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not obtain common word list', '', __LINE__, __FILE__, $sql);
}

$common_word_id = '';
while ($row = $db->sql_fetchrow($result))
{
	$common_word_id .= (($common_word_id != '') ? ', ' : '') . $row['word_id'];
}

if ($common_word_id == '')
{
	message_die(GENERAL_ERROR,'None of the words in the list are in your search_tables.<br />Note: This could also mean the list is empty ;-)');
}
//echo '>'.trim($curr_word)."<<br />";
//echo $exclude_list .'<br />'. $common_word_id;
//exit;

$sql = "DELETE FROM " . SEARCH_WORD_TABLE . "
	WHERE word_id IN ($common_word_id)";
if (!$db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not delete word match entry', '', __LINE__, __FILE__, $sql);
}
$sql = "OPTIMIZE TABLE " . SEARCH_WORD_TABLE;
if (!$db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not optimize', '', __LINE__, __FILE__, $sql);
}

$sql = "DELETE FROM " . SEARCH_MATCH_TABLE . "
	WHERE word_id IN ($common_word_id)";
if (!$db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not delete word match entry', '', __LINE__, __FILE__, $sql);
}
$sql = "OPTIMIZE TABLE " . SEARCH_MATCH_TABLE;
if (!$db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not optimize', '', __LINE__, __FILE__, $sql);
}

$db->clear_cache();

message_die(GENERAL_MESSAGE,'<b>Done!</b><br /><br />The following list-entries have been removed from your searchtables:<br /><br />' . $exclude_list);
//echo $exclude_list .'<br />'. $common_word_id;

?>