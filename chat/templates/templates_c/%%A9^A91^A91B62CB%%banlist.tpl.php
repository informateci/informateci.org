<?php /* Smarty version 2.6.10, created on 2007-09-14 18:29:45
         compiled from banlist.tpl */ ?>
<?php $this->assign('title', 'Bans');  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<center>
	<h4>Bans</h4>

<?php if ($this->_tpl_vars['error']): ?>
	<font color="red"><?php echo $this->_tpl_vars['error']; ?>
</font><br><br>
<?php endif;  if ($this->_tpl_vars['notice']): ?>
	<font color="green"><?php echo $this->_tpl_vars['notice']; ?>
</font><br><br>
<?php endif; ?>

<form name="banlist" id="banlist" action="banlist.php" method="post">
	<input type="hidden" id="sort" name="sort" value="none">
</form>	

<?php if ($this->_tpl_vars['bannedlist']): ?>
<table border="1">
<tr>
	<th><a href="javascript:my_getbyid('sort').value = 'created'; my_getbyid('banlist').submit()">created</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'user'; my_getbyid('banlist').submit()">user</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'buser'; my_getbyid('banlist').submit()">banneduser</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'roomid'; my_getbyid('banlist').submit()">roomid</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'ip'; my_getbyid('banlist').submit()">ip</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'banlevel'; my_getbyid('banlist').submit()">ban level</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'buser'; my_getbyid('banlist').submit()">remove ban</a></th>
</tr>

<?php $_from = $this->_tpl_vars['bannedlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['banned']):
?>
	<td><?php echo $this->_tpl_vars['banned']['created']; ?>
</td>
	<td align=center><a href="user.php?id=<?php echo $this->_tpl_vars['banned']['userid']; ?>
"><?php echo $this->_tpl_vars['banned']['user']; ?>
</a></td>
	<td align=center><a href="user.php?id=<?php echo $this->_tpl_vars['banned']['banneduserid']; ?>
"><?php echo $this->_tpl_vars['banned']['buser']; ?>
</a></td>
	<td align=center><?php echo $this->_tpl_vars['banned']['roomid']; ?>
</td>
	<td><?php echo $this->_tpl_vars['banned']['ip']; ?>
</td>
	<td><center><?php echo $this->_tpl_vars['banned']['banlevel']; ?>
</center></td>
	<td align=center><a href="banlist.php?unbanid=<?php echo $this->_tpl_vars['banned']['banneduserid']; ?>
"><?php echo $this->_tpl_vars['banned']['banneduserid']; ?>
</a></td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php else: ?>
	No bans found
<?php endif; ?>
</center>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>