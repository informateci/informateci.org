<script type="text/javascript">
	function select_switch(status)
	{
		for (i = 0; i < document.post.length; i++)
		{
			document.post.elements[i].checked = status;
		}
	}
</script>
<form action="{S_POST_ACTION}" method="post" name="post">
{IMG_THL}{IMG_THC}<span class="forumlink">{L_SEARCH_MATCHES}</span>{IMG_THR}<table class="forumlinenb" width="100%" cellspacing="0" cellpadding="0">
<tr>
	<th width="18">&nbsp;</th>
	<th>{L_FORUM}</th>
	<th>{L_TOPICS}</th>
	<th>{L_REPLIES}</th>
	<th>{L_AUTHOR}</th>
	<th>{L_VIEWS}</th>
	<th>{L_LASTPOST}</th>
	<!-- BEGIN switch_upi2db_on -->
	<th>{L_MAR}</th>
	<!-- END switch_upi2db_on -->
</tr>
<!-- BEGIN searchresults -->
<tr>
	<td class="row1 row-center">
		{searchresults.U_MARK_ALWAYS_READ}
		<!-- <img src="{searchresults.TOPIC_FOLDER_IMG}" alt="{searchresults.L_TOPIC_FOLDER_ALT}" title="{searchresults.L_TOPIC_FOLDER_ALT}" /> -->
	</td>
	<td class="row1h{searchresults.CLASS_NEW} row-forum" onclick="window.location.href='{searchresults.U_VIEW_FORUM}'">
		<span class="topiclink{searchresults.CLASS_NEW}"><a href="{searchresults.U_VIEW_FORUM}">{searchresults.FORUM_NAME}</a></span>
	</td>
	<td class="row1h{searchresults.CLASS_NEW} row-forum" onclick="window.location.href='{searchresults.U_VIEW_TOPIC}'">
		<div class="topic-title-hide-flow"><span class="topiclink{searchresults.CLASS_NEW}">{searchresults.NEWEST_POST_IMG}{searchresults.TOPIC_TYPE}<a href="{searchresults.U_VIEW_TOPIC}" class="{searchresults.TOPIC_CLASS}">{searchresults.TOPIC_TITLE}</a></span> <!-- BEGIN display_reg -->[{searchresults.REG_OPTIONS}]&nbsp;{searchresults.REG_USER_OWN_REG}<!-- END display_reg --></div>
		<!-- IF searchresults.GOTO_PAGE -->
		<br /><span class="gotopage">{searchresults.GOTO_PAGE}</span>
		<!-- ENDIF -->
	</td>
	<td class="row1 row-center-small">{searchresults.REPLIES}</td>
	<td class="row2 row-center-small">{searchresults.TOPIC_AUTHOR}</td>
	<td class="row1 row-center-small">{searchresults.VIEWS}</td>
	<td class="row2 row-center-small">{searchresults.LAST_POST_TIME}<br />{searchresults.LAST_POST_AUTHOR} {searchresults.LAST_POST_IMG}</td>
	<!-- BEGIN switch_upi2db_on -->
	<td class="row1 row-center-small" nowrap="nowrap"><input type="checkbox" name="mar_topic_id[]" value="{searchresults.TOPIC_ID}" {searchresults.NO_AGM} /></td>
	<!-- END switch_upi2db_on -->
</tr>
<!-- END searchresults -->
<tr><td class="spaceRow" colspan="8"><img src="{SPACER}" width="1" height="3" alt="" /></td></tr>
<tr>
	<td class="catBottom" colspan="8">
		{S_HIDDEN_FIELDS}
		<!-- BEGIN switch_upi2db_on -->
		<input type="submit" class="mainoption" value="{L_SUBMIT_MARK_READ}" />
		<input name="mar" type="hidden" value="1" />
		<!-- END switch_upi2db_on -->
	</td>
</tr>
</table>{IMG_TFL}{IMG_TFC}{IMG_TFR}
<!-- BEGIN switch_upi2db_on -->
<table class="empty-table" width="100%" align="center" cellspacing="0">
<tr><td valign="middle" align="right"><a href="javascript:select_switch(true);" class="gensmall">{L_MARK_ALL}</a>&nbsp;|&nbsp;<a href="javascript:select_switch(false);" class="gensmall">{L_UNMARK_ALL}</a></td></tr>
</table>
<!-- END switch_upi2db_on -->
</form>

<table class="empty-table" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td align="left" valign="top"><span class="gensmall">{PAGE_NUMBER}</span></td>
	<td align="right" valign="top" nowrap="nowrap"><span class="pagination">{PAGINATION}</span><br /><span class="gensmall">{S_TIMEZONE}</span></td>
</tr>
</table>

<div align="right">{JUMPBOX}</div>