<h1>{L_TITLE}</h1>

<div class="forumline nav-div">
	<p class="nav-header">
		<a href="{U_INDEX}" class="nav">{L_INDEX}</a>{NAV_CAT_DESC}</span>
	</p>
	<div class="nav-links">
		<div class="nav-links-left">
			{L_TITLE_EXPLAIN}
		</div>
		&nbsp;<br />&nbsp;
	</div>
</div>

<form action="{S_ACTION}" name="post" method="post">
<table class="forumline" width="100%" cellspacing="0" cellpadding="0">
<tr>
	<th colspan="2">{L_FORUM}</th>
	<th>&nbsp;{L_TOPICS}&nbsp;</th>
	<th>&nbsp;{L_POSTS}&nbsp;</th>
	<th nowrap="nowrap">&nbsp;{L_ACTION}&nbsp;</th>
</tr>
<!-- BEGIN row -->
<tr>
	<td class="{row.COLOR} row-center" width="46"><img src="{row.FOLDER}" alt="{row.L_FOLDER}" title="{row.L_FOLDER}" /></td>
	<td class="{row.COLOR}" valign="top" height="50" width="60%">
		<!-- BEGIN forum_icon -->
		<table cellpadding="2" cellspacing="0" border="0" height="47" width="100%">
		<tr>
			<td width="46" align="center"><img src="{row.ICON_IMG}" alt="{row.ICON}" title="{row.ICON}" /></td>
			<td width="100%">
		<!-- END forum_icon -->
				<span class="forumlink"><a href="{row.U_FORUM}" class="forumlink" title="{row.FORUM_NAME}">{row.FORUM_NAME}</a></span>
				<span class="genmed"><br />{row.FORUM_DESC}</span>
				<span class="gensmall">{row.LINKS}</span>
		<!-- BEGIN forum_icon -->
			</td>
		</tr>
		</table>
		<!-- END forum_icon -->
	</td>
	<td width="5%" class="{row.COLOR} row-center" valign="middle"><span class="gensmall">{row.TOPICS}</span></td>
	<td width="5%" class="{row.COLOR} row-center" valign="middle"><span class="gensmall">{row.POSTS}</span></td>
	<td width="25%" class="{row.COLOR} row-center">
		<a href="{row.U_EDIT}"><img src="../images/cms/b_edit.png" alt="{L_EDIT}" title="{L_EDIT}" /></a>
		<a href="{row.U_DELETE}"><img src="../images/cms/b_delete.png" alt="{L_DELETE}" title="{L_DELETE}" /></a>
		<a href="{row.U_MOVEUP}"><img src="../images/cms/arrow_up.png" alt="{L_MOVEUP}" title="{L_MOVEUP}" /></a>
		<a href="{row.U_MOVEDW}"><img src="../images/cms/arrow_down.png" alt="{L_MOVEDW}" title="{L_MOVEDW}" /></a>
		<a href="{row.U_RESYNC}"><img src="../images/cms/b_refresh.png" alt="{L_RESYNC}" title="{L_RESYNC}" /></a>
	</td>
</tr>
<!-- END row -->
<!-- BEGIN empty -->
<tr><td class="row1" colspan="5" align="center"><span class="gen">{NO_SUBFORUMS}</span></td></tr>
<!-- END empty -->
<tr>
	<td class="cat" colspan="5" align="center">{S_HIDDEN_FIELDS}
		<span class="cattitle">
			<!-- BEGIN no_root -->
			<input type="submit" name="edit" value="{L_EDIT_FORUM}" class="mainoption" />&nbsp;
			<input type="submit" name="create" value="{L_CREATE_FORUM}" class="liteoption" />&nbsp;
			<input type="submit" name="delete" value="{L_DELETE_FORUM}" class="liteoption" />&nbsp;
			<input type="submit" name="resync" value="{L_RESYNC_FORUM}" class="liteoption" />
			<!-- END no_root -->
			<!-- BEGIN root -->
			<input type="submit" name="create" value="{L_CREATE_FORUM}" class="mainoption" />&nbsp;
			<input type="submit" name="resync" value="{L_RESYNC_FORUM}" class="liteoption" />
			<!-- END root -->
		</span>
	</td>
</tr>
</table>
</form>