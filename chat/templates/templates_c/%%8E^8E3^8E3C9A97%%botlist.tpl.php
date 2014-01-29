<?php /* Smarty version 2.6.10, created on 2007-09-14 18:29:48
         compiled from botlist.tpl */ ?>
<?php $this->assign('title', 'Bots'); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<center>
	<h4>Bots</h4>
<?php if ($this->_tpl_vars['enableBots']): ?>	
	<form name="botlist" id="botlist" action="botlist.php" method="post">
		<input type="hidden" id="sort" name="sort" value="none">
	</form>		
	<a href="bot.php?id=0">Add new bot</a><br>
	<br>
<?php if ($this->_tpl_vars['botnames']): ?>
	<table border="1" cellpadding="2">
		<tr>
			<th><a href="javascript:my_getbyid('sort').value = 'login'; my_getbyid('botlist').submit()">Bot Name</a></th>
			<th>Delete</th>
		</tr>
	<?php $_from = $this->_tpl_vars['botnames']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['bot']):
?>
		<tr>
			<td><a href="bot.php?id=<?php echo $this->_tpl_vars['bot']['id']; ?>
"><?php echo $this->_tpl_vars['bot']['login']; ?>
</a></td>
			<td align="center">
				<input type="Button" class="submit" onclick="javascript:decision('Do you really want delete the bot?','botlist.php?id=<?php echo $this->_tpl_vars['bot']['id']; ?>
')" value="  del  ">
			</td>			
		</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	No bots found
<?php endif; ?>
<?php else: ?>
<p align="left">
The bot feature is currently disabled. To enable bot support, set 'enableBots' to true in your /inc/config.php file.
You may need to re-run the FlashChat installer to add the necessary knowledge bases, too.
</p>
<?php endif; ?>
</center>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>