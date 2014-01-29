<?php /* Smarty version 2.6.10, created on 2007-09-14 23:05:24
         compiled from load_avatar_bg.tpl */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>FlashChat v<?php echo $this->_tpl_vars['data']['version']; ?>
 - <?php echo $this->_tpl_vars['data']['win_title']; ?>
</title>
		<meta http-equiv=Content-Type content="text/html;  charset=UTF-8">
		<?php echo '
		<script language=JavaScript type=text/javascript>
		<!--// open print window
		function myOnSubmit()
		{
			var fname = document.setup.file.value;
			if( fname == \'\')
			{
				'; ?>

				var msg = '<?php echo $this->_tpl_vars['data']['pls_select_file']; ?>
';
				<?php echo '
				window.alert(msg);
				return false;
			}
			
			'; ?>

			var allowExt = "<?php echo $this->_tpl_vars['data']['allowFileExt']; ?>
";
			<?php echo '
			var ind = fname.lastIndexOf(\'.\');
			if(allowExt != \'\' && ind > 0)
			{
				var ext = fname.substring(ind+1,fname.length).toUpperCase();
				allowExt = \',\' + allowExt + \',\';
				if( allowExt.indexOf(\',\'+ext+\',\') < 0 )
				{
					'; ?>

					var msg = '<?php echo $this->_tpl_vars['data']['ext_not_allowed']; ?>
';
					<?php echo '
					msg = msg.replace(\'FILE_EXT\', ext);
					window.alert(msg);
					return false;
				}
			}
			return true;
		}
		//-->
	</script>
	'; ?>

	</head>

<?php echo '
<style type=text/css>
<!--
body,td {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 13px;
	font-weight: normal;
	color: ';  echo $this->_tpl_vars['data']['bodyText']; ?>
;<?php echo '
}
.small {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: normal;
}
.title {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 16px;
	font-weight: bold;
}
input {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 13px;
	font-weight: normal;
}
select {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 13px;
	font-weight: normal;
}
A {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #0000FF;
}
-->
</style>
'; ?>

	<body bgcolor="<?php echo $this->_tpl_vars['data']['publicLogBackground']; ?>
" onLoad="window.focus()" leftmargin=5 topmargin=0 marginwidth=10 marginheight=5>

	<form name="setup" method="post" enctype="multipart/form-data" onSubmit="return myOnSubmit()">


		<table width="100%" height="100%">
		<tr><td valign="middle" align="center">
			<table border="0" cellpadding="4">

				<?php if ($this->_tpl_vars['data']['not_errmsg']): ?> <tr><td align=center><?php echo $this->_tpl_vars['data']['errmsg']; ?>
</td></tr><?php endif; ?>

				<tr><td><?php echo $this->_tpl_vars['data']['win_choose']; ?>
</td></tr>
				<tr><td><input type="hidden" name="MAX_FILE_SIZE1" value="<?php echo $this->_tpl_vars['data']['maxSize']; ?>
">
						<input name="file" type="file" size="45"><br>						
					</td>
				</tr>
				<tr>
					<td><?php echo $this->_tpl_vars['data']['file_info']; ?>
</td>
				</tr>
				<tr>
					<td>
						<table border="0" cellpadding="0">
							<tr>
								<td valign="top" rowspan="5"><?php echo $this->_tpl_vars['data']['use_label']; ?>
</td>
							<!--	
								<td>
									<input name="RB_CHOICE" type="radio" value="0" checked>
									<?php echo $this->_tpl_vars['data']['rb_mainchat_avatar']; ?>

								</td>
							</tr>
							<tr>
								<td>
									<input name="RB_CHOICE" type="radio" value="1">
									<?php echo $this->_tpl_vars['data']['rb_roomlist_avatar']; ?>

								</td>
							</tr>
							<tr>
								<td>
									<input name="RB_CHOICE" type="radio" value="2">
									<?php echo $this->_tpl_vars['data']['rb_mc_rl_avatar']; ?>

								</td>
							</tr>
							<tr>
							-->
								<td>
									<input name="RB_CHOICE" type="radio" value="3" checked>
									<?php echo $this->_tpl_vars['data']['rb_this_theme']; ?>

								</td>
							</tr>
														<tr>
								<td>
									<input name="RB_CHOICE" type="radio" value="4">
									<?php echo $this->_tpl_vars['data']['rb_all_themes']; ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" nowrap><input name="submit" type="submit" class="input"  value="<?php echo $this->_tpl_vars['data']['win_upl_btn']; ?>
"></td>
				</tr>
			</table>
			</td>
		</tr>
		</table>
	</form>
	
	</body>
	
</html>