<!-- INCLUDE pa_header.tpl -->
<!-- INCLUDE pa_links.tpl -->

{IMG_THL}{IMG_THC}<span class="forumlink">{L_STATISTICS}</span>{IMG_THR}<table class="forumlinenb" width="100%" cellspacing="0" cellpadding="0">
<tr><th colspan="2" align="center">{L_GENERAL_INFO}</th></tr>
<tr><td colspan="2" class="row1"><span class="genmed">{STATS_TEXT}</span></td></tr>
<tr>
	<th width="50%" align="center"><span class="cattitle">{L_DOWNLOADS_STATS}</span></th>
	<th width="50%" align="center"><span class="cattitle">{L_RATING_STATS}</span></th>
</tr>
<tr><td class="row2" colspan="2" align="center" width="50%"><span class="genmed">{L_OS}</span></td></tr>
<tr>
	<td class="row1" width="50%">
		<table cellspacing="0" cellpadding="2" border="0">
		<!-- BEGIN downloads_os -->
		<tr>
			<td><img src="{downloads_os.OS_IMG}" alt="" />&nbsp;<span class="gen">{downloads_os.OS_NAME}</span></td>
			<td class="row1" width="50%">
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="1%" nowrap="nowrap"><img src="{U_VOTE_LCAP}" height="13" /><img src="{downloads_os.OS_OPTION_IMG}" width="{downloads_os.OS_OPTION_IMG_WIDTH}%" height="13" alt="{downloads_os.OS_OPTION_IMG_WIDTH}%" /><img src="{U_VOTE_RCAP}" height="13" alt="" /></td>
				</tr>
				</table>
			</td>

			<td align="center"><span class="gen">[ {downloads_os.OS_OPTION_RESULT} ]</span></td>
		</tr>
		<!-- END downloads_os -->
		</table>
	</td>
	<td class="row1" width="50%">
		<table cellspacing="0" cellpadding="2" border="0">
			<!-- BEGIN rating_os -->
			<tr>
				<td><img src="{rating_os.OS_IMG}" alt="" />&nbsp;<span class="gen">{rating_os.OS_NAME}</span></td>
				<td class="row1" align="left" width="50%">
					<table cellspacing="0" cellpadding="0" border="0" align="left">
					<tr>
						<td width="1%" nowrap="nowrap"><img src="{U_VOTE_LCAP}" height="13" /><img src="{downloads_os.OS_OPTION_IMG}" width="{downloads_os.OS_OPTION_IMG_WIDTH}%" height="13" alt="{downloads_os.OS_OPTION_IMG_WIDTH}" /><img src="{U_VOTE_RCAP}" height="13" alt="" /></td>
					</tr>
					</table>
				</td>
				<td align="center"><span class="gen">[ {rating_os.OS_OPTION_RESULT} ]</span></td>
			</tr>
			<!-- END rating_os -->
		</table>
	</td>
</tr>
<tr><td class="row2" colspan="2" align="center"><span class="genmed">{L_BROWSERS}</span></td></tr>
<tr>
	<td class="row1" width="50%">
		<table cellspacing="0" cellpadding="2" border="0">
		<!-- BEGIN downloads_b -->
		<tr>
			<td><img src="{downloads_b.B_IMG}" alt="" />&nbsp;<span class="gen">{downloads_b.B_NAME}</span></td>
			<td class="row1" align="left" width="50%">
				<table cellspacing="0" cellpadding="0" border="0" align="left">
				<tr>
					<td width="1%" nowrap="nowrap"><img src="{U_VOTE_LCAP}" height="13" /><img src="{downloads_b.B_OPTION_IMG}" width="{downloads_b.B_OPTION_IMG_WIDTH}%" height="13" alt="{downloads_b.B_OPTION_RESULT}" /><img src="{U_VOTE_RCAP}" height="13" alt="" /></td>
				</tr>
				</table>
			</td>

			<td align="center"><span class="gen">[ {downloads_b.B_OPTION_RESULT} ]</span></td>
		</tr>
		<!-- END downloads_b -->
		</table>
	</td>
	<td class="row1" width="50%">
		<table cellspacing="0" cellpadding="2" border="0">
		<!-- BEGIN rating_b -->
		<tr>
			<td><img src="{rating_b.B_IMG}" alt="" />&nbsp;<span class="gen">{rating_b.B_NAME}</span></td>
			<td class="row1" width="50%">
				<table cellspacing="0" cellpadding="0" border="0" align="left">
				<tr>
					<td width="1%" nowrap="nowrap"><img src="{U_VOTE_LCAP}" height="13" /><img src="{downloads_b.B_OPTION_IMG}" width="{downloads_b.B_OPTION_IMG_WIDTH}%" height="13" alt="{downloads_b.B_OPTION_RESULT}" /><img src="{U_VOTE_RCAP}" height="13" alt="" /></td>
				</tr>
				</table>
			</td>
			<td align="center"><span class="gen">[ {rating_b.B_OPTION_RESULT} ]</span></td>
		</tr>
		<!-- END rating_b -->
		</table>
	</td>
</tr>
<tr><td colspan="2" class="cat" height="28">&nbsp;</td></tr>
</table>{IMG_TFL}{IMG_TFC}{IMG_TFR}
<br />
<!-- INCLUDE pa_footer.tpl -->