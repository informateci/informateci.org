<?php /* Smarty version 2.6.10, created on 2007-09-14 18:29:43
         compiled from connlist.tpl */ ?>
<?php $this->assign('title', 'Connections');  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<center>
<h4>Connections</h4>

<form name="connlist" id="connlist" action="connlist.php" method="post">
	<input type="hidden" id="sort" name="sort" value="none">
</form>	

<?php if ($this->_tpl_vars['connections']): ?>

<table border="1">
<tr>
	
	<th><a href="javascript:my_getbyid('sort').value = 'id'; my_getbyid('connlist').submit()">id</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'updated'; my_getbyid('connlist').submit()">updated</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'created'; my_getbyid('connlist').submit()">created</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'login'; my_getbyid('connlist').submit()">user</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'roomid'; my_getbyid('connlist').submit()">roomid</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'state'; my_getbyid('connlist').submit()">state</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'color'; my_getbyid('connlist').submit()">color</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'start'; my_getbyid('connlist').submit()">start</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'lang'; my_getbyid('connlist').submit()">lang</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'ip'; my_getbyid('connlist').submit()">ip</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'tzoffset'; my_getbyid('connlist').submit()">tzoffset</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'host'; my_getbyid('connlist').submit()">host</a></th>
</tr>
<?php $_from = $this->_tpl_vars['connections']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['connection']):
?>
<tr>
	<td><?php echo $this->_tpl_vars['connection']['id']; ?>
</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['updated']; ?>
</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['created']; ?>
</td>
	<td align=center>
	<?php if ($this->_tpl_vars['connection']['userid']): ?>
		<a href=user.php?id=<?php echo $this->_tpl_vars['connection']['userid']; ?>
><?php echo $this->_tpl_vars['connection']['login']; ?>
</a>
	<?php else: ?>
		-
	<?php endif; ?>
	</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['roomid']; ?>
</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['state']; ?>
</td>
	<td><?php echo $this->_tpl_vars['connection']['color']; ?>
</td>
	<td><?php echo $this->_tpl_vars['connection']['start']; ?>
</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['lang']; ?>
</td>
	<td><?php echo $this->_tpl_vars['connection']['ip']; ?>
</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['tzoffset']; ?>
</td>
	<td align=center><?php echo $this->_tpl_vars['connection']['host']; ?>
</td>
</tr>

<?php endforeach; endif; unset($_from);  else: ?>
	No connections found
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>