<?php
/**
*
* @package Icy Phoenix
* @version $Id: lang_blocks.php 110 2009-07-14 08:09:47Z Mighty Gorgon $
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Extra credits for this file
* Lopalong
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'Title_ads' => 'Ads',
	'Title_album' => 'Album',
	'Title_birthdays' => 'Birthdays',
	'Title_center_downloads' => 'Downloads',
	'Title_clock' => 'Clock',
	'Title_donate' => 'Donations',
	'Title_dyn_menu' => 'Quick Links',
	'Title_flash_news' => 'Flash News',
	'Title_forum' => 'News',
	'Title_forum_attach' => 'News',
	'Title_forum_list' => 'Forum List',
	'Title_global_header' => 'Global Header',
	'Title_global_header_simple' => 'Global Header Simple',
	'Title_gsearch' => 'Google Search',
	'Title_gsearch_hor' => 'Google Search Horizontal',
	'Title_kb' => 'Knowledge Base',
	'Title_links' => 'Links',
	'Title_menu' => 'Board Navigation',
	'Title_nav_header' => 'Header',
	'Title_nav_links' => 'Site Map',
	'Title_nav_logo' => 'Logo',
	'Title_news' => 'News',
	'Title_news_archive' => 'Archive',
	'Title_news_posters' => 'News Posters',
	'Title_online_users' => 'Who is Online',
	'Title_online_users2' => 'Who is Online',
	'Title_paypal' => 'PayPal',
	'Title_poll' => 'Poll',
	'Title_random_attach' => 'Random Attach',
	'Title_random_topics' => 'Random Topics',
	'Title_random_topics_ver' => 'Random Topics',
	'Title_random_user' => 'Random User',
	'Title_recent_articles' => 'Recent Articles',
	'Title_recent_topics' => 'Recent Topics',
	'Title_recent_topics_wide' => 'Recent Topics',
	'Title_referers' => 'Referrers',
	'Title_rss' => 'RSS',
	'Title_search' => 'Search',
	'Title_sec_menu' => 'Extra Menu',
	'Title_shoutbox' => '<a href="shoutbox_max.' . PHP_EXT . '?sid=' . $userdata['session_id'] . '">Shoutbox</a>',
	'Title_staff' => 'Staff',
	'Title_statistics' => 'Statistics',
	'Title_style' => 'Style',
	'Title_top_downloads' => 'Top Downloads',
	'Title_top_posters' => 'Top Posters',
	'Title_user_block' => 'User Block',
	'Title_users_visited' => 'Active Users',
	'Title_visit_counter' => 'Visit Counter',
	'Title_welcome' => 'Welcome',
	'Title_wordgraph' => 'Tags',

	'Advanced_GSearch' => 'Advanced Google Search',
	'Advanced_search' => 'Advanced Search',
	'Album' => 'Album',
	'All_News_Archives' => 'All News Archives',
	'All_News_Categories' => 'All News Categories',
	'Articles' => 'Articles',
	'Articles_time' => 'Posted on',
	'Articles_options' => 'Options',
	'Article_Reply' => 'Post a comment',
	'Article_Print' => 'Print this article',
	'Article_Email' => 'Email to a friend',
	'Censor' => 'Censor',
	'Click_to_join_chat' => 'Click to join chat',
	'Comments' => 'Comments',
	'Credits' => 'Credits',
	'Day_users' => '%d registered users visited during the last %d hours:',
	'Disable_BBCode_post' => 'Disable BBCode in this post',
	'Disable_HTML_post' => 'Disable HTML in this post',
	'Dls' => 'Downloads',
	'Donate_Funds' => 'Make A Donation',
	'GSearch' => 'Search',
	'GSearch2' => 'Google Search:',
	'GSearch_At' => 'Search at',
	'Guest' => 'Guest',
	'Guest_user_total' => '%d Guest',
	'Guest_users_total' => '%d Guests',
	'Guest_users_zero_total' => '0 Guests',
	'Hidden_user_total' => '%d Hidden and ',
	'Hidden_users_total' => '%d Hidden and ',
	'Hidden_users_zero_total' => '0 Hidden and ',
	'How_Many_Chatters' => 'There are <b>%d</b> users in chat right now.',
	'Kb_name' => 'Knowledge Base',
	'IP_info' => 'IP Information',
	'Login_to_join_chat' => 'Please login to the forum to use chat',
	'Login_to_vote' => 'You must login to vote',
	'Lookup_IP' => 'Look up IP address',
	'New_donations' => 'New Donations',
	'New_downloads' => 'New Downloads',
	'News_And' => 'and',
	'News_Archives' => 'News Archives',
	'News_Categories' => 'News Categories',
	'News_Cats' => 'News Categories',
	'News_Comments' => 'Comments',
	'News_Email' => 'E-Mail this Topic',
	'News_Print' => 'Print this Topic',
	'News_Reply' => 'Reply to this News Item',
	'News_Summary' => 'This news item has',
	'News_Views' => 'Views',
	'No_News_Cats' => 'Sorry, no News Categories available!',
	'No_Pics' => 'No Pics',
	'No_poll' => 'No poll at the moment',
	'No_topics_found' => 'No topics were found.',
	'None' => 'None',
	'Not_day_users' => '%d registered users <span style="color:red">DIDN\'T</span> visit during the last %d hours:',
	'Not_found' => 'No Attachments found.',
	'Not_rated' => 'Not Rated',
	'Online_user_total' => 'In total there is <b>%d</b> user online:',
	'Online_users_total' => 'In total there are <b>%d</b> users online:',
	'Online_users_zero_total' => 'In total there are <b>0</b> users online:',
	'Other_IP_this_user' => 'Other IP addresses this user has posted from',
	'Pic_Title' => 'Pic Title',
	'Poll' => 'Poll',
	'Post_your_comment' => 'Post your comment',
	'Posted' => 'Posted',
	'POSTED_ON' => 'on',
	'Poster' => 'Poster',
	'Posts' => 'Posts',
	'Rating' => ' Rating',
	'Read_Full' => 'Read Full',
	'Record_online_users' => 'Most users ever online was <b>%s</b> on %s',
	'Reg_user_total' => '%d Registered, ',
	'Reg_users_total' => '%d Registered, ',
	'Reg_users_zero_total' => '0 Registered, ',
	'Register_new_account' => 'Don\'t have an account yet?<br />You can %sregister%s for FREE',
	'Registered_users' => 'Registered Users:',
	'Remember_me' => 'Automatic Login',
	'Save_Topic' => 'Save topic as a file',
	'Shout_refresh' => 'Refresh',
	'Shout_text' => 'Your text',
	'Shoutbox_date' => 'D G:i \\w\r\o\t\e ',
	'SH_Visit_counter_statement' => 'This site had <b>%d</b> visitors in total since %s',
	'Tell_Friend' => 'Send as e-mail to a Friend',
	'This_posts_IP' => 'IP address for this post',
	'Top_downloads' => 'Top Downloads',
	'Total_votes' => 'Total Votes',
	'Users_this_IP' => 'Users posting from this IP address',
	'View' => 'View',
	'View_comments' => 'View Comments',
	'View_complete_list' => 'View complete list',
	'Visit_counter' => 'Visit Counter',
	'Visit_counter_statement' => 'This site has <b>%d</b> page views in total since %s',
	'Vote' => 'Vote',

	'donated_by' => 'donated by',
	'search' => 'Search',
	'search2' => 'Search:',
	'search_at' => 'Search at',
	'total_topics' => ' within <b>%s</b> topics',


// Blocks Config
/*
	'cms_var_' => '',
	'cms_option_' => '',
	'cms_value_' => '',
*/

	'cms_var_kb_cat_id' => 'Category ID',
	'cms_var_kb_cat_id_explain' => 'Choose the category ID for the KB (dynamic menu ID)',
	'cms_var_md_ads_type' => 'Ads type',
	'cms_var_md_ads_type_explain' => 'Select the ad type (H = Horizontal, V = Vertical, B = Box, 1 = Small, 2 = Medium, 3 = Large)',
	'cms_var_md_cat_id' => 'Category To Retrieve Pics From',
	'cms_var_md_cat_id_explain' => 'Enter 0 for all categories or comma delimited entries',
	'cms_var_md_col' => 'Number of Columns',
	'cms_var_md_col_explain' => 'Select the number of index columns',
	'cms_var_md_news_cat_id' => 'Category To Retrieve News From',
	'cms_var_md_news_cat_id_explain' => 'Enter 0 for all categories or comma delimited entries',
	'cms_var_md_list_forum_id' => 'Category To Be Listed',
	'cms_var_md_list_forum_id_explain' => 'Enter 0 for all categories or comma delimited entries',
	'cms_var_md_display_not_visit' => 'Display users who did not visit',
	'cms_var_md_display_not_visit_explain' => 'Tick to display users who didn\'t visit the site',
	'cms_var_md_full_search_option_text' => 'Full search option text',
	'cms_var_md_full_search_option_text_explain' => 'Text displayed as the default option',
	'cms_var_md_gsearch_banner' => 'Search site banner',
	'cms_var_md_gsearch_banner_explain' => 'Banner of the site (the url without http://)',
	'cms_var_md_gsearch_site' => 'Search site',
	'cms_var_md_gsearch_site_explain' => 'Site where the search should be performed (the url without http://)',
	'cms_var_md_gsearch_style' => 'Search style',
	'cms_var_md_gsearch_style_explain' => 'Choose between horizontal and vertical',
	'cms_var_md_gsearch_text' => 'Search option text',
	'cms_var_md_gsearch_text_explain' => 'Text displayed as the default option',
	'cms_var_md_hours_track_users' => 'Number of hours to track users',
	'cms_var_md_hours_track_users_explain' => '',
	'cms_var_md_ignore_auth_view' => 'Ignore auth view permission?',
	'cms_var_md_ignore_auth_view_explain' => 'Enabling this forums view permissions will be ignored (this is important if you want to use hidden forums with NONE as view permission)',
	'cms_var_md_links_code' => 'Links -> Code',
	'cms_var_md_links_code_explain' => 'Show HTML for your own link button',
	'cms_var_md_links_own1' => 'Links -> Own (Top)',
	'cms_var_md_links_own1_explain' => 'Show your own link button above other buttons',
	'cms_var_md_links_own2' => 'Links -> Own (Bottom)',
	'cms_var_md_links_own2_explain' => 'Show your own link button below other buttons',
	'cms_var_md_links_style' => 'Links -> Style',
	'cms_var_md_links_style_explain' => 'Choose between static and scrolling',
	'cms_var_md_jumpbox_align' => 'Alignment',
	'cms_var_md_jumpbox_align_explain' => 'Select jumpbox alignment',
	'cms_var_md_menu_id' => 'Menu block ID',
	'cms_var_md_menu_id_explain' => 'Enter the ID of the menu block you want to show (0 = default).',
	'cms_var_md_menu_show_hide' => 'Show/Hide Switch',
	'cms_var_md_menu_show_hide_explain' => 'Enable Show/Hide switch to hide menu (it will work only on left GLOBAL block!!!).',
	'cms_var_md_news_number' => 'Number Of News To Display',
	'cms_var_md_news_number_explain' => '',
	'cms_var_md_news_sort' => 'Random Or Newest News?',
	'cms_var_md_news_sort_explain' => '',
	'cms_var_md_news_length' => 'Length of News',
	'cms_var_md_news_length_explain' => 'Number of characters displayed',
	'cms_var_md_news_archive_type' => 'Show Archive Or Categories?',
	'cms_var_md_news_archive_type_explain' => 'Choose if you want to show Archives or Categories',
	'cms_var_md_news_images_width' => 'News Image Width',
	'cms_var_md_news_images_width_explain' => 'Choose image width in pixels or percentage',
	'cms_var_md_news_forum_id' => 'News Forum ID(s)',
	'cms_var_md_news_forum_id_explain' => 'Comma delimited',
	'cms_var_md_news_length' => 'Length of news',
	'cms_var_md_news_length_explain' => 'Number of characters displayed (enter 0 for all)',
	'cms_var_md_news_posters_page_link' => 'Page Link',
	'cms_var_md_news_posters_page_link_explain' => 'The link to the page which contains news archive (i.e. <b>index.php</b>)',
	'cms_var_md_news_posters_sort' => 'Sort',
	'cms_var_md_news_posters_sort_explain' => 'Select the sort method',
	'cms_var_md_news_posters_avatar' => 'Show Avatars',
	'cms_var_md_news_posters_avatar_explain' => '',
	'cms_var_md_num_new_downloads' => 'New Downloads',
	'cms_var_md_num_new_downloads_explain' => 'Number of new downloads displayed',
	'cms_var_md_num_news' => 'Number of news on portal',
	'cms_var_md_num_news_explain' => '',
	'cms_var_md_num_posts' => 'Number of posts',
	'cms_var_md_num_posts_explain' => 'Number of posts to be displayed',
	'cms_var_md_num_random_topics' => 'Number of random topics',
	'cms_var_md_num_random_topics_explain' => 'Number of topics displayed',
	'cms_var_md_num_random_topics_ver' => 'Number of random topics',
	'cms_var_md_num_random_topics_ver_explain' => 'Number of topics displayed',
	'cms_var_md_num_recent_topics' => 'Number of recent topics',
	'cms_var_md_num_recent_topics_explain' => 'Number of topics displayed',
	'cms_var_md_num_recent_topics_wide' => 'Number Of Recent Topics',
	'cms_var_md_num_recent_topics_wide_explain' => 'Number of topics displayed',
	'cms_var_md_num_top_downloads' => 'Top Downloads',
	'cms_var_md_num_top_downloads_explain' => 'Number of top downloads displayed',
	'cms_var_md_pics_all' => 'Display From What Galleries?',
	'cms_var_md_pics_all_explain' => '',
	'cms_var_md_pics_cols_number' => 'Number Of Columns',
	'cms_var_md_pics_cols_number_explain' => '',
	'cms_var_md_pics_number' => 'Number Of Images To Display',
	'cms_var_md_pics_number_explain' => '',
	'cms_var_md_pics_rows_number' => 'Number Of Rows',
	'cms_var_md_pics_rows_number_explain' => '',
	'cms_var_md_pics_sort' => 'Random Or Newest Pics?',
	'cms_var_md_pics_sort_explain' => '',
	'cms_var_md_poll_bar_length' => 'Poll Bar Length',
	'cms_var_md_poll_bar_length_explain' => 'Decrease/increase the value for 1 vote bar length',
	'cms_var_md_poll_forum_id' => 'Poll Forum ID(s)',
	'cms_var_md_poll_forum_id_explain' => 'Comma delimited',
	'cms_var_md_poll_type' => 'Random Or Newest Poll?',
	'cms_var_md_poll_type_explain' => 'Choose if you want to show latest or random',
	'cms_var_md_posts_forum_id' => 'Posts Forum ID(s)',
	'cms_var_md_posts_forum_id_explain' => 'Comma delimited',
	'cms_var_md_posts_length' => 'Length of posts',
	'cms_var_md_posts_length_explain' => 'Number of characters displayed',
	'cms_var_md_posts_random' => 'Recent or random?',
	'cms_var_md_posts_random_explain' => 'Select recent or random topics',
	'cms_var_md_posts_show_portal' => 'All topics or only marked?',
	'cms_var_md_posts_show_portal_explain' => 'Select all topics or only marked with "Show in Home Page"',
	'cms_var_md_ran_att_forums_excl' => 'Random Attach Exclude Forum ID(s)',
	'cms_var_md_ran_att_forums_excl_explain' => 'Comma delimited; leave blank for no exclusions',
	'cms_var_md_ran_att_forums_incl' => 'Random Attach Include Forum ID(s)',
	'cms_var_md_ran_att_forums_incl_explain' => 'Comma delimited; leave blank for all forums',
	'cms_var_md_ran_att_height' => 'Random Attach Max Height',
	'cms_var_md_ran_att_height_explain' => 'When the height > the width, this will be set as height in the img tag',
	'cms_var_md_ran_att_max' => 'Random Attach Max Files',
	'cms_var_md_ran_att_max_explain' => 'Maximum number of files to return',
	'cms_var_md_ran_att_width' => 'Random Attach Max Width',
	'cms_var_md_ran_att_width_explain' => 'When the width > the height, this will be set as width in the img tag',
	'cms_var_md_random_topics_style' => 'Random Topics Style',
	'cms_var_md_random_topics_style_explain' => 'Choose between static and scrolling',
	'cms_var_md_random_topics_forums' => 'Forum IDs',
	'cms_var_md_random_topics_forums_explain' => 'IDs of the forums to be processed (0 = all)',
	'cms_var_md_random_topics_ver_forums' => 'Forum IDs',
	'cms_var_md_random_topics_ver_forums_explain' => 'IDs of the forums to be processed (0 = all)',
	'cms_var_md_recent_articles_style' => 'Recent Articles Style',
	'cms_var_md_recent_articles_style_explain' => 'Choose between static and scrolling',
	'cms_var_md_recent_topics_style' => 'Recent Topics Style',
	'cms_var_md_recent_topics_style_explain' => 'Choose between static and scrolling',
	'cms_var_md_recent_topics_wide_style' => 'Recent Topics Style',
	'cms_var_md_recent_topics_wide_style_explain' => 'Choose between static and scrolling',
	'cms_var_md_rss_feeder' => 'RSS Feed Address',
	'cms_var_md_rss_feeder_explain' => 'Enter the address of the RSS feed (i.e. http://www.icyphoenix.com/rss.php)',
	'cms_var_md_rss_title' => 'RSS Feed Title',
	'cms_var_md_rss_title_explain' => 'Enter the title of the RSS feed (i.e. Icy Phoenix)',
	'cms_var_md_rss_style' => 'RSS Block Style',
	'cms_var_md_rss_style_explain' => 'Choose between horizontal and vertical',
	'cms_var_md_rss_scroll' => 'RSS Block Scroll',
	'cms_var_md_rss_scroll_explain' => 'Choose between static and scrolling',
	'cms_var_md_show_background' => 'Show Cats Background?',
	'cms_var_md_show_background_explain' => 'Select YES if you want to show cats background',
	'cms_var_md_show_cats_icon' => 'Show Cats Icons?',
	'cms_var_md_show_cats_icon_explain' => 'Select YES if you want to show cats icons',
	'cms_var_md_show_desc' => 'Show Links Descriptions?',
	'cms_var_md_show_desc_explain' => 'Select YES if you want to show links descriptions with mouse-hover effect',
	'cms_var_md_show_links_icon' => 'Show Links Icons?',
	'cms_var_md_show_links_icon_explain' => 'Select YES if you want to show links icons',
	'cms_var_md_show_sep_icon' => 'Show Separator Icons?',
	'cms_var_md_show_sep_icon_explain' => 'Select YES if you want to show separator icons',
	'cms_var_md_show_title' => 'Show Cats Title?',
	'cms_var_md_show_title_explain' => 'Select YES if you want to show cats title',
	'cms_var_md_single_post_auto_id' => 'Get ID From Address',
	'cms_var_md_single_post_auto_id_explain' => 'Enabling this will get the ID directly from address (post_id=XXX)',
	'cms_var_md_single_post_id' => 'Posts ID',
	'cms_var_md_single_post_id_explain' => 'Enter post ID',
	'cms_var_md_single_post_retrieve' => 'Retrieve Single Post',
	'cms_var_md_single_post_retrieve_explain' => 'Enabling this only one post will be shown, the one specified below, all other settings will be ignored',
	'cms_var_md_scroll_delay' => 'Scroll delay',
	'cms_var_md_scroll_delay_explain' => 'Higher values means slower scroll',
	'cms_var_md_search_option_text' => 'Search Field Description',
	'cms_var_md_search_option_text_explain' => 'Search field description',
	'cms_var_md_show_avatars' => 'Show Avatars',
	'cms_var_md_show_avatars_explain' => '',
	'cms_var_md_total_articles' => 'Number of Recent Articles',
	'cms_var_md_total_articles_explain' => 'Number of articles shown',
	'cms_var_md_total_poster' => 'Number Of Top Posters',
	'cms_var_md_total_poster_explain' => '',
	'cms_var_md_wordgraph_count' => 'Enable Tags Counts',
	'cms_var_md_wordgraph_count_explain' => 'Display the total number of tags next to each word',
	'cms_var_md_wordgraph_words' => 'Maximum Tags',
	'cms_var_md_wordgraph_words_explain' => 'Select the maximum number of tags to display',

	'cms_option_All_Topics' => 'All Topics',
	'cms_option_Alphabetical' => 'Alphabetical',
	'cms_option_Archive' => 'Archive',
	'cms_option_Categories' => 'Categories',
	'cms_option_News' => 'News',
	'cms_option_Newest' => 'Newest',
	'cms_option_Public' => 'Public',
	'cms_option_Public_and_Personal' => 'Public and Personal',
	'cms_option_Random' => 'Random',
	'cms_option_Recent' => 'Recent',
	'cms_option_Scroll' => 'Scroll',
	'cms_option_Static' => 'Static',
	'cms_option_Horizontal' => 'Horizontal',
	'cms_option_Vertical' => 'Vertical',
	'cms_option_Show_In_Portal' => 'Show in Home Page',
	'cms_option_Yes' => 'Yes',
	'cms_option_No' => 'No',
	'cms_option_Left' => 'Left',
	'cms_option_Center' => 'Centre',
	'cms_option_Right' => 'Right',

	'cms_value_All_Topics' => '0',
	'cms_value_Archive' => '0',
	'cms_value_Categories' => '1',
	'cms_value_Newest' => '0',
	'cms_value_Public' => '0',
	'cms_value_Public_and_Personal' => '1',
	'cms_value_Random' => '1',
	'cms_value_Recent' => '0',
	'cms_value_Show_In_Portal' => '1',
	)
);

?>