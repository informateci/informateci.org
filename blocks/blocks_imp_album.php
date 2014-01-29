<?php
/**
*
* @package Icy Phoenix
* @version $Id: blocks_imp_album.php 110 2009-07-14 08:09:47Z Mighty Gorgon $
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

if(!function_exists('imp_album_block_func'))
{
	function imp_album_block_func()
	{
		global $template, $db, $board_config, $lang, $images, $userdata;
		global $cms_global_blocks, $cms_page_id, $cms_config_vars, $cms_config_layouts, $block_id;

		$cms_page_id_tmp = 'album';
		$cms_auth_level_tmp = (isset($cms_config_layouts[$cms_page_id_tmp]['view']) ? $cms_config_layouts[$cms_page_id_tmp]['view'] : AUTH_ALL);
		$process_block = check_page_auth($cms_page_id_tmp, $cms_auth_level_tmp, true);
		if (!$process_block)
		{
			return;
		}

		if (!defined('IMG_THUMB'))
		{
			define('IMG_THUMB', true);
		}

		$template->_tpldata['recent_pics.'] = array();
		//reset($template->_tpldata['recent_pics.']);
		$template->_tpldata['recent_details.'] = array();
		//reset($template->_tpldata['recent_details.']);
		$template->_tpldata['no_pics'] = array();
		//reset($template->_tpldata['no_pics.']);

		/*
		echo($cms_config_vars['md_pics_all'][$block_id] . '<br />');
		echo($cms_config_vars[$block_id . '_' . 'md_pics_all']);
		exit;
		*/
		include_once(ALBUM_MOD_PATH . 'album_common.' . PHP_EXT);
		global $album_config;

		$sql = "SELECT c.*, COUNT(p.pic_id) AS count
				FROM " . ALBUM_CAT_TABLE . " AS c
					LEFT JOIN " . ALBUM_TABLE . " AS p ON c.cat_id = p.pic_cat_id
				" . (($cms_config_vars['md_pics_all'][$block_id] == '1') ? '' : 'WHERE cat_user_id = 0') . "
				GROUP BY cat_id
				ORDER BY cat_order ASC";
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
		}
		$catrows = array();
		while($row = $db->sql_fetchrow($result))
		{
			$album_user_access = album_user_access($row['cat_id'], $row, 1, 0, 0, 0, 0, 0); // VIEW
			if ($album_user_access['view'] == 1)
			{
				$catrows[] = $row;
			}
		}
		$db->sql_freeresult($result);
		if ($cms_config_vars['md_pics_all'][$block_id] == '1')
		{
			$allowed_cat = '0'; // For Recent Public Pics below
		}
		else
		{
			$allowed_cat = '';
		}

		// $catrows now stores all categories which this user can view. Dump them out!
		for ($i = 0; $i < count($catrows); $i++)
		{
			// Build allowed category-list (for recent pics after here)
			$allowed_cat .= ($allowed_cat == '') ? $catrows[$i]['cat_id'] : ',' . $catrows[$i]['cat_id'];

			// Get Last Pic of this Category
			if ($catrows[$i]['count'] == 0)
			{
				//
				// Oh, this category is empty
				//
				$last_pic_info = $lang['No_Pics'];
				$u_last_pic = '';
				$last_pic_title = '';
			}
			else
			{
				// Check Pic Approval
				if (($catrows[$i]['cat_approval'] == ALBUM_ADMIN) || ($catrows[$i]['cat_approval'] == ALBUM_MOD))
				{
					$pic_approval_sql = 'AND p.pic_approval = 1'; // Pic Approval ON
				}
				else
				{
					$pic_approval_sql = ''; // Pic Approval OFF
				}
			}
		}

		if ($cms_config_vars['md_pics_all'][$block_id] == '1')
		{
			$pics_allowed = '0';
		}
		else
		{
			$pics_allowed = '';
		}

		if ($allowed_cat != $pics_allowed)
		{
			$category_id = $cms_config_vars['md_cat_id'][$block_id];

			if ($cms_config_vars['md_pics_sort'][$block_id] == '1')
			{
				if ($category_id != 0)
				{
					$sql = "SELECT p.*, u.user_id, u.username, u.user_active, u.user_color, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments
						FROM " . ALBUM_TABLE . " AS p
							LEFT JOIN " . USERS_TABLE . " AS u ON p.pic_user_id = u.user_id
							LEFT JOIN " . ALBUM_CAT_TABLE . " AS ct ON p.pic_cat_id = ct.cat_id
							LEFT JOIN " . ALBUM_RATE_TABLE . " AS r ON p.pic_id = r.rate_pic_id
							LEFT JOIN " . ALBUM_COMMENT_TABLE . " AS c ON p.pic_id = c.comment_pic_id
						WHERE p.pic_cat_id IN ($allowed_cat) AND (p.pic_approval = 1 OR ct.cat_approval = 0) AND pic_cat_id IN ($category_id)
						GROUP BY p.pic_id
						ORDER BY RAND()
						LIMIT " . $cms_config_vars['md_pics_number'][$block_id];
				}
				else
				{
					$sql = "SELECT p.*, u.user_id, u.username, u.user_active, u.user_color, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments
						FROM " . ALBUM_TABLE . " AS p
							LEFT JOIN " . USERS_TABLE . " AS u ON p.pic_user_id = u.user_id
							LEFT JOIN " . ALBUM_CAT_TABLE . " AS ct ON p.pic_cat_id = ct.cat_id
							LEFT JOIN " . ALBUM_RATE_TABLE . " AS r ON p.pic_id = r.rate_pic_id
							LEFT JOIN " . ALBUM_COMMENT_TABLE . " AS c ON p.pic_id = c.comment_pic_id
						WHERE p.pic_cat_id IN ($allowed_cat) AND (p.pic_approval = 1 OR ct.cat_approval = 0)
						GROUP BY p.pic_id
						ORDER BY RAND()
						LIMIT " . $cms_config_vars['md_pics_number'][$block_id];
					}
			}
			elseif ($cms_config_vars['md_pics_sort'][$block_id] == '0')
			{
				if ($category_id != 0)
				{
					$sql = "SELECT p.*, u.user_id, u.username, u.user_active, u.user_color, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments
						FROM " . ALBUM_TABLE . " AS p
							LEFT JOIN " . USERS_TABLE . " AS u ON p.pic_user_id = u.user_id
							LEFT JOIN " . ALBUM_CAT_TABLE . " AS ct ON p.pic_cat_id = ct.cat_id
							LEFT JOIN " . ALBUM_RATE_TABLE . " AS r ON p.pic_id = r.rate_pic_id
							LEFT JOIN " . ALBUM_COMMENT_TABLE . " AS c ON p.pic_id = c.comment_pic_id
						WHERE p.pic_cat_id IN ($allowed_cat) AND (p.pic_approval = 1 OR ct.cat_approval = 0) AND pic_cat_id IN ($category_id)
						GROUP BY p.pic_id
						ORDER BY pic_time DESC
						LIMIT " . $cms_config_vars['md_pics_number'][$block_id];
				}
				else
				{
					$sql = "SELECT p.*, u.user_id, u.username, u.user_active, u.user_color, r.rate_pic_id, AVG(r.rate_point) AS rating, COUNT(DISTINCT c.comment_id) AS comments
						FROM " . ALBUM_TABLE . " AS p
							LEFT JOIN " . USERS_TABLE . " AS u ON p.pic_user_id = u.user_id
							LEFT JOIN " . ALBUM_CAT_TABLE . " AS ct ON p.pic_cat_id = ct.cat_id
							LEFT JOIN " . ALBUM_RATE_TABLE . " AS r ON p.pic_id = r.rate_pic_id
							LEFT JOIN " . ALBUM_COMMENT_TABLE . " AS c ON p.pic_id = c.comment_pic_id
						WHERE p.pic_cat_id IN ($allowed_cat) AND (p.pic_approval = 1 OR ct.cat_approval = 0)
						GROUP BY p.pic_id
						ORDER BY pic_time DESC
						LIMIT " . $cms_config_vars['md_pics_number'][$block_id];
				}
			}
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not query recent pics information', '', __LINE__, __FILE__, $sql);
			}
			$recentrow = array();

			while($row = $db->sql_fetchrow($result))
			{
				$recentrow[] = $row;
			}
			$db->sql_freeresult($result);

			$total_pics = count($recentrow);
			if ($total_pics > 0)
			{
				$total_rows = ceil($total_pics / $cms_config_vars['md_pics_rows_number'][$block_id]);
				$total_cols = ceil($total_pics / $cms_config_vars['md_pics_cols_number'][$block_id]);
				$image_counter = 0;

				while($image_counter < $total_pics)
				{
					for ($i = 0; $i < $cms_config_vars['md_pics_rows_number'][$block_id]; $i++)
					{
						$template->assign_block_vars('recent_pics', array());

						for ($j = 0; $j < $cms_config_vars['md_pics_cols_number'][$block_id]; $j++)
						{
							if ($image_counter >= $total_pics)
							{
								$template->assign_block_vars('recent_pics.recent_no_detail', array());
							}
							else
							{
								if (!$recentrow[$image_counter]['rating'])
								{
									$recentrow[$image_counter]['rating'] = $lang['Not_rated'];
								}
								else
								{
									$recentrow[$image_counter]['rating'] = round($recentrow[$image_counter]['rating'], 2);
								}

								if(($recentrow[$image_counter]['user_id'] == ALBUM_GUEST) || ($recentrow[$image_counter]['username'] == ''))
								{
									$recent_poster = ($recentrow[$image_counter]['pic_username'] == '') ? $lang['Guest'] : $recentrow[$image_counter]['pic_username'];
								}
								else
								{
									$recent_poster = colorize_username($recentrow[$image_counter]['user_id'], $recentrow[$image_counter]['username'], $recentrow[$image_counter]['user_color'], $recentrow[$image_counter]['user_active']);
								}

								$thumbnail_file = append_sid(album_append_uid('album_thumbnail.' . PHP_EXT . '?pic_id=' . $recentrow[$image_counter]['pic_id']));
								if (($album_config['thumbnail_cache'] == true) && ($album_config['quick_thumbs'] == true))
								{
									$thumbnail_file = picture_quick_thumb($recentrow[$image_counter]['pic_filename'], $recentrow[$image_counter]['pic_thumbnail'], $thumbnail_file);
								}

								$pic_sp_link = append_sid(album_append_uid('album_showpage.' . PHP_EXT . '?pic_id=' . $recentrow[$image_counter]['pic_id']));
								$pic_dl_link = append_sid(album_append_uid('album_pic.' . PHP_EXT . '?pic_id=' . $recentrow[$image_counter]['pic_id']));

								$template->assign_block_vars('recent_pics.recent_detail', array(
									'U_PIC' => ($album_config['fullpic_popup'] ? $pic_dl_link : $pic_sp_link),
									'U_PIC_SP' => $pic_sp_link,
									'U_PIC_DL' => $pic_dl_link,

									'THUMBNAIL' => $thumbnail_file,
									'DESC' => htmlspecialchars($recentrow[$image_counter]['pic_desc']),
									'TITLE' => htmlspecialchars($recentrow[$image_counter]['pic_title']),
									'POSTER' => $recent_poster,
									'TIME' => create_date_ip($board_config['default_dateformat'], $recentrow[$image_counter]['pic_time'], $board_config['board_timezone']),
									'VIEW' => $recentrow[$image_counter]['pic_view_count'],
									'RATING' => ($album_config['rate'] == 1) ? ($lang['Rating'] . ': ' . $recentrow[$image_counter]['rating'] . '<br />') : '',
									'COMMENTS' => ($album_config['comment'] == 1) ? ($lang['Comments'] . ': ' . $recentrow[$image_counter]['comments'] . '<br />') : '')
								);
							}

							$image_counter++;
						}
					}
				}
			}
			else
			{
				// No Pics Found
				$template->assign_block_vars('no_pics', array());
			}
		}
		else
		{
			// No Cats Found
			$template->assign_block_vars('no_pics', array());
		}

		$template->assign_vars(array(
			//'S_COL_WIDTH' => (100 / $cms_config_vars['md_pics_number'][$block_id]) . '%',
			'S_COL_WIDTH' => (100 / (($cms_config_vars['md_pics_cols_number'][$block_id] == 0) ? 4 : $cms_config_vars['md_pics_cols_number'][$block_id])) . '%',
			'TARGET_BLANK' => ($album_config['fullpic_popup']) ? 'target="_blank"' : '',
			'L_NO_PICS' => $lang['No_Pics'],
			'L_PIC_TITLE' => $lang['Pic_Title'],
			'L_VIEW' => $lang['View'],
			'L_POSTER' => $lang['Poster'],
			'L_POSTED' => $lang['Posted'],
			'U_ALBUM' => append_sid('album.' . PHP_EXT),
			'L_ALBUM' => $lang['Album']
			)
		);

	}
}

imp_album_block_func();

?>