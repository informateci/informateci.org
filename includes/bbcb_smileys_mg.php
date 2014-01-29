<?php
/**
*
* @package Icy Phoenix
* @version $Id: bbcb_smileys_mg.php 92 2009-04-01 13:58:18Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

// Usage
/*
// BBCBMG SMILEYS - BEGIN
define('IN_ICYPHOENIX', true);
generate_smilies('inline');
include(IP_ROOT_PATH . 'includes/bbcb_smileys_mg.' . PHP_EXT);
$template->assign_var_from_handle('BBCB_SMILEYS_MG', 'bbcb_smileys_mg');
// BBCBMG SMILEYS - END
*/

@include_once(IP_ROOT_PATH . 'language/lang_' . $board_config['default_lang'] . '/lang_bbcb_mg.' . PHP_EXT);

if ( defined('IN_PA_POSTING') )
{
	$pafiledb_template->set_filenames(array('bbcb_smileys_mg' => 'bbcb_smileys_mg.tpl'));
}
else
{
	$template->set_filenames(array('bbcb_smileys_mg' => 'bbcb_smileys_mg.tpl'));
}

if ( $view_pic_upload == true )
{
	if ( defined('IN_PA_POSTING') )
	{
		$pafiledb_template->assign_block_vars('switch_sm_pic_upload', array());
	}
	else
	{
		$template->assign_block_vars('switch_sm_pic_upload', array());
	}
}

$bbcbmg_path_prefix = '';
if (isset($bbcbmg_in_acp))
{
	$bbcbmg_path_prefix = ($bbcbmg_in_acp == true) ? '../' : '';
}

$parsing_template = array(
	'BBCB_MG_PATH_PREFIX' => $bbcbmg_path_prefix,

	'L_MORE_SMILIES' => $lang['More_emoticons'],
	'U_MORE_SMILIES' => append_sid('posting.' . PHP_EXT . '?mode=smilies'),

	'L_SMILEY_CREATOR' => $lang['Smiley_creator'],
	'U_SMILEY_CREATOR' => append_sid('smiley_creator.' . PHP_EXT . '?mode=text2shield'),

	'L_UPLOAD_IMAGE' => $lang['Upload_Image_Local'],
	'U_UPLOAD_IMAGE' => append_sid('upload.' . PHP_EXT),
);

if ( defined('IN_PA_POSTING') )
{
	$pafiledb_template->assign_vars($parsing_template);
}
else
{
	$template->assign_vars($parsing_template);
}

?>