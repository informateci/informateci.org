<script type="text/javascript">
<!--
function update_icon(newimage)
{
	if(newimage != '')
	{
		document.icon_image.src = '../' + newimage;
		document.post.icon.value = newimage;
	}
	else
	{
		document.icon_image.src = '../images/spacer.gif';
		document.post.icon.value = '';
	}
}
//-->
</script>

<p>{L_FORUM_EXPLAIN}</p>

<form action="{S_FORUM_ACTION}" method="post" name="post">
<table class="forumline" width="100%" cellspacing="0" cellpadding="0">
<tr><th colspan="2">{L_FORUM_SETTINGS}</th></tr>
<tr>
	<td class="row1"><b>{L_FORUM_NAME}</b></td>
	<td class="row2"><input type="text" size="25" name="forumname" value="{FORUM_NAME}" class="post" /></td>
</tr>
<tr>
	<td class="row1"><b>{L_FORUM_DESCRIPTION}</b></td>
	<td class="row2"><textarea rows="5" cols="45" name="forumdesc" class="post">{DESCRIPTION}</textarea></td>
</tr>
<tr>
	<td class="row1" wrap="wrap" width="300"><b>{L_ICON}</b><br /><span class="gensmall">{L_ICON_EXPLAIN}</span></td>
	<td class="row2">{ICON_LIST}</td>
</tr>
<tr>
	<td class="row1"><b>{L_CATEGORY}</b></td>
	<td class="row2"><select name="c">{S_CAT_LIST}</select></td>
</tr>
<tr>
	<td class="row1"><b>{L_COPY_AUTH}</b><!-- <br /><span class="gensmall">{L_COPY_AUTH_EXPLAIN}</span> --></td>
	<td class="row2"><select name="dup_auth">{S_FORUM_LIST}</select></td>
</tr>
<tr>
	<td class="row1"><b>{L_FORUM_STATUS}</b></td>
	<td class="row2"><select name="forumstatus">{S_STATUS_LIST}</select></td>
</tr>
<tr>
	<td class="row1"><b>{L_FORUM_THANK}</b></td>
	<td class="row2">{S_THANK_RADIO}</td>
</tr>
<tr>
	<td class="row1"><span class="genmed"><b>{L_FORUM_SIMILAR_TOPICS}</b></span><br /><span class="gensmall">{L_FORUM_SIMILAR_TOPICS_EXPLAIN}</span></td>
	<td class="row2"><input type="radio" name="forum_similar_topics" value="1"{FORUM_SIMILAR_TOPICS_YES} />&nbsp;<span class="genmed">{L_YES}</span>&nbsp;&nbsp;<input type="radio" name="forum_similar_topics" value="0"{FORUM_SIMILAR_TOPICS_NO} />&nbsp;<span class="genmed">{L_NO}</span></td>
</tr>
<tr>
	<td class="row1"><span class="genmed"><b>{L_FORUM_TOPIC_VIEWS}</b></span><br /><span class="gensmall">{L_FORUM_TOPIC_VIEWS_EXPLAIN}</span></td>
	<td class="row2"><input type="radio" name="forum_topic_views" value="1"{FORUM_TOPIC_VIEWS_YES} />&nbsp;<span class="genmed">{L_YES}</span>&nbsp;&nbsp;<input type="radio" name="forum_topic_views" value="0"{FORUM_TOPIC_VIEWS_NO} />&nbsp;<span class="genmed">{L_NO}</span></td>
</tr>
<tr>
	<td class="row1"><span class="genmed"><b>{L_FORUM_TAGS}</b></span><br /><span class="gensmall">{L_FORUM_TAGS_EXPLAIN}</span></td>
	<td class="row2"><input type="radio" name="forum_tags" value="1"{FORUM_TAGS_YES} />&nbsp;<span class="genmed">{L_YES}</span>&nbsp;&nbsp;<input type="radio" name="forum_tags" value="0"{FORUM_TAGS_NO} />&nbsp;<span class="genmed">{L_NO}</span></td>
</tr>
<tr>
	<td class="row1"><span class="genmed"><b>{L_FORUM_SORT_BOX}</b></span><br /><span class="gensmall">{L_FORUM_SORT_BOX_EXPLAIN}</span></td>
	<td class="row2"><input type="radio" name="forum_sort_box" value="1"{FORUM_SORT_BOX_YES} />&nbsp;<span class="genmed">{L_YES}</span>&nbsp;&nbsp;<input type="radio" name="forum_sort_box" value="0"{FORUM_SORT_BOX_NO} />&nbsp;<span class="genmed">{L_NO}</span></td>
</tr>
<tr>
	<td class="row1"><span class="genmed"><b>{L_FORUM_KB_MODE}</b></span><br /><span class="gensmall">{L_FORUM_KB_MODE_EXPLAIN}</span></td>
	<td class="row2"><input type="radio" name="forum_kb_mode" value="1"{FORUM_KB_MODE_YES} />&nbsp;<span class="genmed">{L_YES}</span>&nbsp;&nbsp;<input type="radio" name="forum_kb_mode" value="0"{FORUM_KB_MODE_NO} />&nbsp;<span class="genmed">{L_NO}</span></td>
</tr>
<tr>
	<td class="row1"><span class="genmed"><b>{L_FORUM_INDEX_ICONS}</b></span><br /><span class="gensmall">{L_FORUM_INDEX_ICONS_EXPLAIN}</span></td>
	<td class="row2"><input type="radio" name="forum_index_icons" value="1"{FORUM_INDEX_ICONS_YES} />&nbsp;<span class="genmed">{L_YES}</span>&nbsp;&nbsp;<input type="radio" name="forum_index_icons" value="0"{FORUM_INDEX_ICONS_NO} />&nbsp;<span class="genmed">{L_NO}</span></td>
</tr>
<tr>
	<td class="row1"><b>{L_FORUM_NOTIFY}</b></td>
	<td class="row2"><select name="notify_enable">{S_NOTIFY_ENABLED}</select></td>
</tr>
<tr>
	<td class="row1"><b>{L_POSTCOUNT}</b></td>
	<td class="row2"><input type="checkbox" name="forum_postcount" value="1" {S_FORUM_POSTCOUNT} />&nbsp;{L_ENABLED}</td>
</tr>
<tr>
	<td class="row1"><b>{L_AUTO_PRUNE}</b></td>
	<td class="row2"><table cellspacing="0" cellpadding="1" border="0">
		<tr>
		<td align="right" valign="middle"><b>{L_ENABLED}</b></td>
		<td align="left" valign="middle"><input type="checkbox" name="prune_enable" value="1" {S_PRUNE_ENABLED} /></td>
		</tr>
		<tr>
		<td align="right" valign="middle"><b>{L_PRUNE_DAYS}</b></td>
		<td align="left" valign="middle">&nbsp;<input type="text" name="prune_days" value="{PRUNE_DAYS}" size="5" class="post" />&nbsp;{L_DAYS}</td>
		</tr>
		<tr>
		<td align="right" valign="middle"><b>{L_PRUNE_FREQ}</b></td>
		<td align="left" valign="middle">&nbsp;<input type="text" name="prune_freq" value="{PRUNE_FREQ}" size="5" class="post" />&nbsp;{L_DAYS}</td>
		</tr>
	</table></td>
</tr>
<tr>
	<td class="row1"><b>{L_LINK}</b>&nbsp;</td>
	<td class="row2 row-center">
		<table cellspacing="0" cellpadding="3" border="0">
		<tr>
			<td align="right" valign="top"><b>{L_FORUM_LINK}</b>&nbsp;</td>
			<td>
				<input type="text" name="forum_link" value="{FORUM_LINK}" size="60" class="post" /><br />
				<span class="gensmall">{L_FORUM_LINK_EXPLAIN}</span>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap" valign="top"><b>{L_FORUM_LINK_INTERNAL}</b>&nbsp;</td>
			<td class="row">
				<input type="radio" name="forum_link_internal" value="1" {FORUM_LINK_INTERNAL_YES} />&nbsp;{L_YES}&nbsp;&nbsp;<input type="radio" name="forum_link_internal" value="0" {FORUM_LINK_INTERNAL_NO} />&nbsp;{L_NO}<br />
				<span class="gensmall">{L_FORUM_LINK_INTERNAL_EXPLAIN}</span>
			</td>
		</tr>
		<tr>
			<td align="right" valign="top"><b>{L_FORUM_LINK_HIT_COUNT}</b>&nbsp;</td>
			<td>
				<input type="radio" name="forum_link_hit_count" value="1" {FORUM_LINK_HIT_COUNT_YES} />&nbsp;{L_YES}&nbsp;&nbsp;<input type="radio" name="forum_link_hit_count" value="0" {FORUM_LINK_HIT_COUNT_NO} />&nbsp;{L_NO}<br />
				<span class="gensmall">&nbsp;{L_FORUM_LINK_HIT_COUNT_EXPLAIN}</span>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr><th colspan="2">{L_MOD_OS_FORUMRULES}</th></tr>
<tr>
	<td class="row1"><b>{L_RULES_DISPLAY_TITLE}</b></td>
	<td class="row2"><input type="checkbox" name="rules_display_title" value="1" {S_RULES_DISPLAY_TITLE_ENABLED} />&nbsp;{L_ENABLED}</td>
</tr>
<tr>
	<td class="row1"><b>{L_RULES_CUSTOM_TITLE}</b></td>
	<td class="row2"><input type="text" name="rules_custom_title" value="{RULES_CUSTOM_TITLE}" size="50" maxlength="80" class="post" /></td>
</tr>
<tr>
	<td class="row1" valign="top"><b>{L_FORUM_RULES}</b></td>
	<td class="row2"><textarea rows="8" cols="70" name="rules" class="post">{RULES}</textarea></td>
</tr>
<tr>
	<td class="row1" valign="top"><b>{L_RULES_APPEAR_IN}</b></td>
	<td class="row2">
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td><input type="checkbox" name="rules_in_viewforum" value="1" {S_RULES_VIEWFORUM_ENABLED} /></td>
				<td>{L_RULES_IN_VIEWFORUM}</td>
			</tr>
			<tr>
				<td><input type="checkbox" name="rules_in_viewtopic" value="1" {S_RULES_VIEWTOPIC_ENABLED} /></td>
				<td>{L_RULES_IN_VIEWTOPIC}</td>
			</tr>
			<tr>
				<td><input type="checkbox" name="rules_in_posting" value="1" {S_RULES_POSTING_ENABLED} /></td>
				<td>{L_RULES_IN_POSTING}</span></td>
			</tr>
		</table>
	</td>
</tr>
<tr><td class="cat" colspan="2" align="center">{S_HIDDEN_FIELDS}<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" class="mainoption" /></td></tr>
</table>
</form>

<br clear="all" />
