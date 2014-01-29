<?php
/**
*
* @package Icy Phoenix
* @version $Id: xs_edit_data.php 61 2008-10-30 09:25:26Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Vjacheslav Trushkin (http://www.stsoftware.biz)
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
$no_page_header = true;
require('./pagestart.' . PHP_EXT);

// check if mod is installed
if(empty($template->xs_version) || $template->xs_version !== 8)
{
	message_die(GENERAL_ERROR, isset($lang['xs_error_not_installed']) ? $lang['xs_error_not_installed'] : 'eXtreme Styles mod is not installed. You forgot to upload includes/template.php');
}

define('IN_XS', true);
include_once('xs_include.' . PHP_EXT);

$template->assign_block_vars('nav_left',array('ITEM' => '&raquo; <a href="' . append_sid('xs_edit_data.' . PHP_EXT) . '">' . $lang['xs_edit_styles_data'] . '</a>'));

$lang['xs_edittpl_back_list'] = str_replace('{URL}', append_sid('xs_edit_data.' . PHP_EXT), $lang['xs_edittpl_back_list']);

function xs_get_vars($theme)
{
	$arr1 = array();
	$arr2 = array();
	$vars_100 = array('head_stylesheet', 'body_background');
	$vars_30 = array('style_name');
	$vars_25 = array('tr_class', 'td_class');
	$vars_6 = array('body_bgcolor');
	$vars_4 = array('theme_public');
	foreach($theme as $var => $value)
	{
		if(!is_integer($var) && $var !== 'themes_id' && $var !== 'template_name')
		{
			// editable variable
			$len = 0;
			$sub = substr($var, 0, strlen($var) - 1);
			if(xs_in_array($var, $vars_100) || xs_in_array($sub, $vars_100))
			{
				$len = 100;
			}
			elseif(xs_in_array($var, $vars_30) || xs_in_array($sub, $vars_30))
			{
				$len = 30;
			}
			elseif(xs_in_array($var, $vars_25) || xs_in_array($sub, $vars_25))
			{
				$len = 25;
			}
			elseif(xs_in_array($var, $vars_6) || xs_in_array($sub, $vars_6))
			{
				$len = 6;
			}
			elseif(xs_in_array($var, $vrs_4) || xs_in_array($sub, $vars_4))
			{
				$len = 4;
			}
			elseif(strpos($var, 'class'))
			{
				$len = 25;
			}
			elseif(strpos($var, 'color'))
			{
				$len = 6;
			}
			if($len)
			{
				$item = array(
					'var'		=> $var,
					'len'		=> $len,
					'color'		=> $len == 6 ? true : false,
					'font'		=> $len == 25 ? true : false,
					);
				if($var === 'style_name' || $var === 'head_stylesheet' || $var === 'body_background')
				{
					$arr1[$var] = $item;
				}
				else
				{
					$arr2[$var] = $item;
				}
			}
		}
	}
	krsort($arr1);
	ksort($arr2);
	if(defined('XS_MODS_CATEGORY_HIERARCHY210'))
	{
		// force sort for the added fields
		$added = array(
			'style_name' => array(),
			'images_pack' => array('var' => $item['images_pack'], 'len' => 100, 'color' => false, 'font' => false),
			'custom_tpls' => array('var' => $item['custom_tpls'], 'len' => 100, 'color' => false, 'font' => false),
			'head_stylesheet' => array(),
		);
		$arr1 = array_merge($added, $arr1);
		// we need to add lang entries
		global $lang;
		$lang['xs_data_images_pack'] = $lang['Images_pack'];
		$lang['xs_data_images_pack_explain'] = $lang['Images_pack_explain'];
		$lang['xs_data_custom_tpls'] = $lang['Custom_tpls'];
		$lang['xs_data_custom_tpls_explain'] = $lang['Custom_tpls_explain'];
	}
	return array_merge($arr1, $arr2);
}

//
// submit
//
if(!empty($_POST['edit']) && !defined('DEMO_MODE'))
{
	$id = intval($_POST['edit']);
	$lang['xs_edittpl_back_edit'] = str_replace('{URL}', append_sid('xs_edit_data.' . PHP_EXT . '?edit='.$id), $lang['xs_edittpl_back_edit']);
	$data_item = array();
	$data_item_update = array();
	$data_name = array();
	$data_name_insert_vars = array('themes_id');
	$data_name_insert_values = array($id);
	$data_name_update = array();
	foreach($_POST as $var => $value)
	{
		if(substr($var, 0, 5) === 'edit_')
		{
			$var = substr($var, 5);
			$value = stripslashes($value);
			$data_item[$var] = $value;
			$data_item_update[] = $var . "='" . xs_sql($value) . "'";
		}
		elseif(substr($var, 0, 5) === 'name_')
		{
			$var = substr($var, 5).'_name';
			$value = stripslashes($value);
			$data_name[$var] = $value;
			$data_name_update[] = $var . "='" . xs_sql($value) . "'";
			$data_name_insert_vars[] = $var;
			$data_name_insert_values[] = xs_sql($value);
		}
	}
	// update item
	$sql = "UPDATE " . THEMES_TABLE . " SET " . implode(',', $data_item_update) . " WHERE themes_id='{$id}'";
	if(!$result = $db->sql_query($sql))
	{
		xs_error($lang['xs_edittpl_error_updating'] . '<br /><br />' . $lang['xs_edittpl_back_edit'] . '<br /><br />' . $lang['xs_edittpl_back_list'], __LINE__, __FILE__);
	}
	// regen themes cache
	if(defined('XS_MODS_CATEGORY_HIERARCHY210'))
	{
		if ( empty($themes) )
		{
			$themes = new themes();
		}
		if ( !empty($themes) )
		{
			$themes->read(true);
		}
	}
	xs_message($lang['Information'], $lang['xs_edittpl_style_updated'] . '<br /><br />' . $lang['xs_edittpl_back_edit'] . '<br /><br />' . $lang['xs_edittpl_back_list']);
}

//
// edit style
//
if(!empty($_GET['edit']))
{
	$id = intval($_GET['edit']);
	$sql = "SELECT * FROM " . THEMES_TABLE . " WHERE themes_id='{$id}'";
	if(!$result = $db->sql_query($sql))
	{
		xs_error($lang['xs_no_style_info'], __LINE__, __FILE__);
	}
	$item = $db->sql_fetchrow($result);
	if(empty($item['themes_id']))
	{
		xs_error($lang['xs_invalid_style_id'] . '<br /><br />' . $lang['xs_edittpl_back_list']);
	}
	$vars = xs_get_vars($item);
	// show variables
	$template->assign_vars(array(
		'U_ACTION'	=> append_sid('xs_edit_data.' . PHP_EXT),
		'TPL'		=> htmlspecialchars($item['template-name']),
		'STYLE'		=> htmlspecialchars($item['style_name']),
		'ID'		=> $id
		)
	);
	// all variables
	$i = 0;
	foreach($vars as $var => $value)
	{
		$row_class = $xs_row_class[$i % 2];
		$i++;
		if(isset($lang['xs_data_'.$var]))
		{
			$text = $lang['xs_data_'.$var];
		}
		else
		{
			$str = substr($var, 0, strlen($var) - 1);
			$str_fc = substr($var, 0, strlen($var) - 2);
			if(isset($lang['xs_data_'.$str_fc]))
			{
				$str1 = substr($var, strlen($var) - 2);
				$text = sprintf($lang['xs_data_'.$str_fc], $str1);
			}
			elseif(isset($lang['xs_data_'.$str]))
			{
				$str1 = substr($var, strlen($var) - 1);
				$text = sprintf($lang['xs_data_'.$str], $str1);
			}
			else
			{
				$text = sprintf($lang['xs_data_unknown'], $var);
			}
		}
		$template->assign_block_vars('row', array(
			'ROW_CLASS'	=> $row_class,
			'VAR'	=> $var,
			'VALUE'	=> isset($item[$var]) ? htmlspecialchars($item[$var]) : '',
			'LEN'	=> $value['len'],
			'SIZE'	=> $value['len'] < 10 ? 10 : 30,
			'TEXT'	=> htmlspecialchars($text),
			'EXPLAIN' => isset($lang['xs_data_' . $var . '_explain']) ? $lang['xs_data_' . $var . '_explain'] : '',
			)
		);
		if($value['color'])
		{
			$template->assign_block_vars('row.color', array());
		}
		if($value['font'])
		{
			$template->assign_block_vars('row.font', array());
		}
		if(isset($item_name[$var.'_name']))
		{
			$template->assign_block_vars('row.name', array(
				'DATA'	=> $item_name[$var.'_name']
				)
			);
		}
		else
		{
			$template->assign_block_vars('row.noname', array());
		}
	}
	$template->set_filenames(array('body' => XS_TPL_PATH . 'edit_data.tpl'));
	$template->pparse('body');
	xs_exit();
}


//
// show list of installed styles
//
$sql = 'SELECT themes_id, template_name, style_name FROM ' . THEMES_TABLE . ' ORDER BY style_name';
if(!$result = $db->sql_query($sql))
{
	xs_error($lang['xs_no_style_info'], __LINE__, __FILE__);
}
$style_rowset = $db->sql_fetchrowset($result);

$template->set_filenames(array('body' => XS_TPL_PATH . 'edit_data_list.tpl'));
for($i=0; $i<count($style_rowset); $i++)
{
	$item = $style_rowset[$i];
	$row_class = $xs_row_class[$i % 2];
	$template->assign_block_vars('styles', array(
		'ROW_CLASS'		=> $row_class,
		'TPL'			=> htmlspecialchars($item['template_name']),
		'STYLE'			=> htmlspecialchars($item['style_name']),
		'U_EDIT'		=> append_sid('xs_edit_data.' . PHP_EXT . '?edit='.$item['themes_id'])
		)
	);
}

$template->pparse('body');
xs_exit();

?>