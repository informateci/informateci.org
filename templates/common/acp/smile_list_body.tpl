<h1>{L_SMILEY_TITLE}</h1>
<P>{L_SMILEY_TEXT}</p>

<form method="post" action="{S_SMILEY_ACTION}">
<table class="forumline" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td class="cat">{S_HIDDEN_FIELDS}<input type="submit" name="add" value="{L_SMILEY_ADD}" class="mainoption" />&nbsp;&nbsp;<input class="liteoption" type="submit" name="import_pack" value="{L_IMPORT_PACK}">&nbsp;&nbsp;<input class="liteoption" type="submit" name="export_pack" value="{L_EXPORT_PACK}"></td>
</tr>
<tr>
	<td class="row1" style="padding: 0px;" valign="top">
		<table class="nav-div" width="100%" align="center" style="padding: 0px;" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th style="text-align: center; width: 190px;">{L_SMILE}</th>
			<th style="text-align: center; width: 170px;">{L_CODE}</th>
			<th style="text-align: center;">{L_EMOT}</th>
			<th style="text-align: center; width: 150px;">{L_ACTION}</th>
		</tr>
		</table>
		<ul id="smileys" style="margin: 0px; padding: 0px; list-style-type: none;">
		<!-- BEGIN smiles -->
		<li id="item_{smiles.SMILEY_ID}" style="cursor: move;">
		<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
		<tr class="{smiles.ROW_CLASS}h">
			<td class="{smiles.ROW_CLASS} row-center" style="padding: 0px; background: none; width: 200px;"><img src="{smiles.SMILEY_IMG}" alt="{smiles.CODE}" /></td>
			<td class="{smiles.ROW_CLASS}" style="padding: 0px; background: none; width: 180px;">{smiles.CODE}</td>
			<td class="{smiles.ROW_CLASS}" style="padding: 0px; background: none;">{smiles.EMOT}</td>
			<td class="{smiles.ROW_CLASS} row-center" style="padding: 0px; background: none; width: 160px;"><a href="{smiles.U_SMILEY_MOVE_TOP}"><img src="../templates/common/images/2uparrow.png" alt="{L_MOVE_TOP} " title="{L_MOVE_TOP}" /></a><a href="{smiles.U_SMILEY_MOVE_UP}"><img src="../templates/common/images/1uparrow.png" alt="{L_MOVE_UP} " title="{L_MOVE_UP}" /></a><a href="{smiles.U_SMILEY_MOVE_DOWN}"><img src="../templates/common/images/1downarrow.png" alt="{L_MOVE_DOWN} " title="{L_MOVE_DOWN}" /></a><a href="{smiles.U_SMILEY_MOVE_END}"><img src="../templates/common/images/2downarrow.png" alt="{L_MOVE_END} " title="{L_MOVE_END}" /></a>&nbsp;<a href="{smiles.U_SMILEY_EDIT}"><img src="../images/cms/b_edit.png" alt="{L_EDIT}" title="{L_EDIT}" /></a>&nbsp;<a href="{smiles.U_SMILEY_DELETE}"><img src="../images/cms/b_delete.png" alt="{L_DELETE}" title="{L_DELETE}" /></a></td>
		</tr>
		</table>
		</li>
		<!-- END smiles -->
		</ul>
	</td>
</tr>
<tr>
	<td class="cat">{S_HIDDEN_FIELDS}<input type="submit" name="add" value="{L_SMILEY_ADD}" class="mainoption" />&nbsp;&nbsp;<input class="liteoption" type="submit" name="import_pack" value="{L_IMPORT_PACK}">&nbsp;&nbsp;<input class="liteoption" type="submit" name="export_pack" value="{L_EXPORT_PACK}"></td>
</tr>
</table>
</form>

<form method="post" action="{S_POSITION_ACTION}">
<table class="forumline" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr><th colspan="2">{L_SMILEY_CONFIG}</th></tr>
<tr><td class="row1">{L_POSITION_NEW_SMILIES}</td><td class="row2">{POSITION_SELECT}</td></tr>
<tr><td class="cat" align="center" colspan="2">{S_HIDDEN_FIELDS}<input type="submit" name="change" value="{L_SMILEY_CHANGE_POSITION}" class="mainoption" /></td></tr>
</table>
</form>

<div id="sort-info-box" class="row-center" style="position: fixed; top: 10px; right: 10px; z-index: 1; background: none; border: none; width: 300px; padding: 3px;"></div>

<script type="text/javascript">
//<![CDATA[
var box_begin = '<div id="result-box" style="height: 16px; border: solid 1px #228822; background: #77dd99;"><span class="text_green">';
var box_end = '<\/span><\/div>';
function update_order()
{
	var request_options = {method: 'post', parameters: 'mode=update_smileys_order&' + Sortable.serialize("smileys") + '&sid=' + S_SID};
	new Ajax.Request(ip_root_path + 'cms_db_update.' + php_ext, request_options);
}
Sortable.create('smileys', {onUpdate:function(){update_order(); $('sort-info-box').innerHTML = box_begin + '{L_SMILEYS_UPDATED}' + box_end; new Effect.Highlight('result-box', {duration: 0.5}); window.setTimeout("new Effect.Fade('result-box',{duration: 0.5})", 2500);}});
//]]>
</script>