<?php
/**
*
* @package Icy Phoenix
* @version $Id: admin_forum_prune.php 88 2009-02-22 19:54:45Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Icy Phoenix is based on phpBB
* @copyright (c) 2008 phpBB Group
*
*/

define('IN_ICYPHOENIX', true);

if ( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['1200_Forums']['130_Prune'] = $filename;

	return;
}

// Load default header
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
require('./pagestart.' . PHP_EXT);
require(IP_ROOT_PATH . 'includes/prune.' . PHP_EXT);
include_once(IP_ROOT_PATH . 'includes/functions_admin.' . PHP_EXT);

//
// Get the forum ID for pruning
//
if( isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]) )
{
//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//	$forum_id = ( isset($_POST[POST_FORUM_URL]) ) ? $_POST[POST_FORUM_URL] : $_GET[POST_FORUM_URL];
//
//	if( $forum_id == -1 )
//	{
//		$forum_sql = '';
//	}
//	else
//	{
//		$forum_id = intval($forum_id);
//		$forum_sql = "AND forum_id = $forum_id";
//	}
//-- add
	$fid = ( isset($_POST[POST_FORUM_URL]) ) ? $_POST[POST_FORUM_URL] : $_GET[POST_FORUM_URL];
	$type = substr($fid, 0, 1);
	$id = intval(substr($fid, 1));
	$cat_id = -1;
	$forum_id = -1;
	if ($fid == 'Root') $type = POST_CAT_URL;
	if ($type == POST_CAT_URL)
	{
		$cat_id = $id;
	}
	else
	{
		$forum_id = $id;
	}
	$fid = $type . $id;
	if ( empty($fid) || ( $fid == POST_CAT_URL . '0' ) )
	{
		$fid = 'Root';
	}

	// set the sql request
	$tkeys = array();
	$tkeys = get_auth_keys($fid, true);
	$forum_rows = array();
	for ($i=0; $i < count($tkeys['id']); $i++)
	{
		if ($tree['type'][$tkeys['idx'][$i]] == POST_FORUM_URL)
		{
			$forum_rows[] = $tree['data'][$tkeys['idx'][$i]];
		}
	}
//-- fin mod : categories hierarchy ----------------------------------------------------------------
}
else
{
//-- mod : categories hierarchy --------------------------------------------------------------------
//-- add
	$forum_rows = array();
//-- fin mod : categories hierarchy ----------------------------------------------------------------
	$forum_id = '';
	$forum_sql = '';
}
//
// Get a list of forum's or the data for the forum that we are pruning.
//
//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
// $sql = "SELECT f.*
//	FROM " . FORUMS_TABLE . " f, " . CATEGORIES_TABLE . " c
//	WHERE c.cat_id = f.cat_id
//	$forum_sql
//	ORDER BY c.cat_order ASC, f.forum_order ASC";
// if( !($result = $db->sql_query($sql)) )
// {
//	message_die(GENERAL_ERROR, 'Could not obtain list of forums for pruning', '', __LINE__, __FILE__, $sql);
// }
//
// $forum_rows = array();
// while( $row = $db->sql_fetchrow($result) )
// {
//	$forum_rows[] = $row;
// }
//-- fin mod : categories hierarchy ----------------------------------------------------------------

//
// Check for submit to be equal to Prune. If so then proceed with the pruning.
//
if( isset($_POST['doprune']) )
{
	$prunedays = ( isset($_POST['prunedays']) ) ? intval($_POST['prunedays']) : 0;

	// Convert days to seconds for timestamp functions...
	$prunedate = time() - ( $prunedays * 86400 );

	$template->set_filenames(array(
		'body' => ADM_TPL . 'forum_prune_result_body.tpl')
	);

	for($i = 0; $i < count($forum_rows); $i++)
	{
		$p_result = prune($forum_rows[$i]['forum_id'], $prunedate);
		sync('forum', $forum_rows[$i]['forum_id']);

		$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];

		$template->assign_block_vars('prune_results', array(
			'ROW_CLASS' => $row_class,
//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//			'FORUM_NAME' => $forum_rows[$i]['forum_name'],
//-- add
			'FORUM_NAME' => get_object_lang(POST_FORUM_URL . $forum_rows[$i]['forum_id'], 'name'),
//-- fin mod : categories hierarchy ----------------------------------------------------------------
			'FORUM_TOPICS' => $p_result['topics'],
			'FORUM_POSTS' => $p_result['posts'])
		);
	}

	$template->assign_vars(array(
		'L_FORUM_PRUNE' => $lang['Forum_Prune'],
		'L_FORUM' => $lang['Forum'],
		'L_TOPICS_PRUNED' => $lang['Topics_pruned'],
		'L_POSTS_PRUNED' => $lang['Posts_pruned'],
		'L_PRUNE_RESULT' => $lang['Prune_success'])
	);
}
else
{
	//
	// If they haven't selected a forum for pruning yet then
	// display a select box to use for pruning.
	//
	if( empty($_POST[POST_FORUM_URL]) )
	{
		//
		// Output a selection table if no forum id has been specified.
		//
		$template->set_filenames(array(
			'body' => ADM_TPL . 'forum_prune_select_body.tpl')
		);

//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//		$select_list = '<select name="' . POST_FORUM_URL . '">';
//		$select_list .= '<option value="-1">' . $lang['All_Forums'] . '</option>';
//
//		for($i = 0; $i < count($forum_rows); $i++)
//		{
//			$select_list .= '<option value="' . $forum_rows[$i]['forum_id'] . '">' . $forum_rows[$i]['forum_name'] . '</option>';
//		}
//		$select_list .= '</select>';
//-- add
		$select_list = selectbox(POST_FORUM_URL, false, '', true);
//-- fin mod : categories hierarchy ----------------------------------------------------------------

		//
		// Assign the template variables.
		//
		$template->assign_vars(array(
			'L_FORUM_PRUNE' => $lang['Forum_Prune'],
			'L_SELECT_FORUM' => $lang['Select_a_Forum'],
			'L_LOOK_UP' => $lang['Look_up_Forum'],

			'S_FORUMPRUNE_ACTION' => append_sid("admin_forum_prune." . PHP_EXT),
			'S_FORUMS_SELECT' => $select_list)
		);
	}
	else
	{
//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//		$forum_id = intval($_POST[POST_FORUM_URL]);
//-- fin mod : categories hierarchy ----------------------------------------------------------------

		//
		// Output the form to retrieve Prune information.
		//
		$template->set_filenames(array(
			'body' => ADM_TPL . 'forum_prune_body.tpl')
		);

//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//		$forum_name = ( $forum_id == -1 ) ? $lang['All_Forums'] : $forum_rows[0]['forum_name'];
//-- add
		$forum_name = ($fid == 'Root') ? $lang['All_Forums'] : get_object_lang($fid, 'name');
//-- fin mod : categories hierarchy ----------------------------------------------------------------

		$prune_data = $lang['Prune_topics_not_posted'] . " ";
		$prune_data .= '<input class="post" type="text" name="prunedays" size="4"> ' . $lang['Days'];

//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//		$hidden_input = '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';
//-- add
		$hidden_input = '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $fid . '" />';
//-- fin mod : categories hierarchy ----------------------------------------------------------------

		//
		// Assign the template variables.
		//
		$template->assign_vars(array(
			'FORUM_NAME' => $forum_name,

//-- mod : categories hierarchy --------------------------------------------------------------------
//-- delete
//			'L_FORUM' => $lang['Forum'],
//-- add
			'L_FORUM' => ( $cat_id > 0 ) ? $lang['Category'] : $lang['Forum'],
//-- fin mod : categories hierarchy ----------------------------------------------------------------
			'L_FORUM_PRUNE' => $lang['Forum_Prune'],
			'L_FORUM_PRUNE_EXPLAIN' => $lang['Forum_Prune_explain'],
			'L_DO_PRUNE' => $lang['Do_Prune'],

			'S_FORUMPRUNE_ACTION' => append_sid("admin_forum_prune." . PHP_EXT),
			'S_PRUNE_DATA' => $prune_data,
			'S_HIDDEN_VARS' => $hidden_input)
		);
	}
}
//
// Actually output the page here.
//
$template->pparse('body');

include('./page_footer_admin.' . PHP_EXT);

?>