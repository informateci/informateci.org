<?php

require_once './inc/config.srv.php';

$errmsg = '';

if( $_POST['submit'] )
{
	$errmsg = updateConfig();

	if( $errmsg == '' )
	{
		if( $_POST['caching']==0 )
		{
			redirect_inst("install.php?step=2&caching=0");
		}
		elseif( $_POST['caching']==1 )
		{
			redirect_inst("install.php?step=2&caching=1");
		}
		elseif( $_POST['caching']==2 )
		{
			redirect_inst("install.php?step=3&caching=2");
		}
	}

}

function updateConfig()
{
	//--- change common.php
	//$old_val = array("require_once(INC_DIR . 'cmses/statelessCMS.php');" , "//require_once(INC_DIR . 'cmses/{$_POST['cms']}.php');");
	//$new_val = array("//require_once(INC_DIR . 'cmses/statelessCMS.php');" , "require_once(INC_DIR . 'cmses/{$_POST['cms']}.php');");
	//$fname = './inc/common.php';
	$repl['cacheType'] = $_POST['caching'];
	$repl['CMSsystem'] = "''";
	$conf = getConfigData();
	$conf = changeConfigVariables($conf,$repl);
	$res  = writeConfig($conf);
	if(!$res) return "<b>Could not write to '/inc/config.php' file</b>";
	//---

	return '';
}


include INST_DIR . 'header.php';
?>
<TR>
	<TD colspan="2">
	</TD>
</TR>
<TR>
	<TD colspan="2" class="subtitle">		FlashChat Caching
	</TD>
</TR>


<tr><td colspan=2 class="error_border"><font color="red"><?php echo @$errmsg; ?></font></td></tr>

<FORM method="post" align="center" name="installInfo">
	<TR>
		<TD colspan="2">
			<TABLE width="100%" class="body_table" cellspacing="10">

				<TR>
					<TD>
						<INPUT type="radio" name="caching" value="1" checked>

						Enable limited caching (recommended).
					</td>
				</TR>
				<TR>
					<td colspan="2">
						This option will use some file reading and writing to improve performance and reduce your SQL overhead. All chats are stored in MySQL, but frequently accessed data is also stored in files on the server. A MySQL connection is only established when needed ("on demand" connections), further reducing the system overhead.
					</td>
				</TR>

				<TR>
					<TD>
						<INPUT type="radio" name="caching" value="2">

						Enable full caching.
					</td>
				</TR>
				<TR>
					<td colspan="2">
						This option does not require a MySQL connection at all. All data is stored in files. This may slow down performance of the admin tools, and chat messages cannot be saved in long-term storage (since that would degrade performance), but if you do not have MySQL available, or if you do not wish to incur the SQL overhead of a MySQL-based chatroom, this may be a good option. The "bot" feature is not available with this option, since bot data is stored in MySQL. If you have chosen to integrate FlashChat with CMS system (like a forum or website content manager), MySQL is still required, since FlashChat must be able to read user data from the CMS database.
					</td>
				</TR>

				<TR>
					<TD>
						<INPUT type="radio" name="caching" value="0">

						No, do not enable caching.
					</td>
				</TR>
			</TABLE>
	</TD>
	</TR>
	<TR>
		<TD>			&nbsp;
		</TD>
		<TD align="right">
			<INPUT type="submit" name="submit" value="Continue >>" >
		</TD>
	</TR>
</FORM>

<?php
include INST_DIR . 'footer.php';
?>


