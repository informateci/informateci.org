<?php /* Smarty version 2.6.10, created on 2007-09-21 22:44:15
         compiled from bot.tpl */ ?>
<?php $this->assign('title', 'Bot'); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<center>
<h4>bot</h4>
<?php if ($this->_tpl_vars['enableBots']): ?>
	<form name="bot" action="bot.php" method="post">
		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['_REQUEST']['id']; ?>
">
		<table border="0" cellspacing="8">
		<tr><td align="right">bot name</td><td><input type="text" name="login" value="<?php echo $this->_tpl_vars['_REQUEST']['login']; ?>
"></td></tr>
		<tr>
			<td align="right">bot room list avatar</td>
			<td >
				<select name="room_avatar">
					<?php $this->assign('selected', ($this->_tpl_vars['_REQUEST']['bot']['room_avatar'])); ?>
					<option id="0" <?php if ($this->_tpl_vars['selected'] == ""): ?>selected<?php endif; ?>>--none--</option>
					<?php $_from = $this->_tpl_vars['_REQUEST']['smilies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['ordersel']):
?>
					<option id="<?php echo $this->_tpl_vars['key']; ?>
" <?php if ($this->_tpl_vars['ordersel'] == $this->_tpl_vars['selected']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['ordersel']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>				
			</td>
		</tr>
		<tr>
			<td align="right">bot main chat avatar</td>
			<td >
				<select name="chat_avatar">
					<?php $this->assign('selected', ($this->_tpl_vars['_REQUEST']['bot']['chat_avatar'])); ?>
					<option id="0" <?php if ($this->_tpl_vars['selected'] == ""): ?>selected<?php endif; ?>>--none--</option>
					<?php $_from = $this->_tpl_vars['_REQUEST']['smilies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['ordersel']):
?>
					<option id="<?php echo $this->_tpl_vars['key']; ?>
" <?php if ($this->_tpl_vars['ordersel'] == $this->_tpl_vars['selected']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['ordersel']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>				
			</td>
		</tr>
		<tr>
			<td align="right">login into room</td>
			<td >
				<select name="roomid">
					<?php $this->assign('selected', ($this->_tpl_vars['_REQUEST']['bot']['roomid'])); ?>
					<?php $_from = $this->_tpl_vars['_REQUEST']['rooms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['ordersel']):
?>
					<option id="<?php echo $this->_tpl_vars['key']; ?>
" <?php if ($this->_tpl_vars['key'] == $this->_tpl_vars['selected']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['ordersel']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>				
			</td>
		</tr>
		<tr>
			<td align="right">active when &lt;X users are present</td>
			<td >
				<input type="text" name="active_on_min_users" size="3" maxlength="2" value="<?php echo $this->_tpl_vars['_REQUEST']['bot']['active_on_min_users']; ?>
">
			</td>			
		</tr>
		<tr>
			<td align="right">active when &gt;X users are present</td>
			<td >
				<input type="text" name="active_on_max_users" size="3" maxlength="2" value="<?php echo $this->_tpl_vars['_REQUEST']['bot']['active_on_max_users']; ?>
">
			</td>			
		</tr>
		<!--
		<tr>
			<td align="right">
				<input type="checkbox" name="active_on_supportmode" id="active_on_supportmode" 
				<?php if ($this->_tpl_vars['_REQUEST']['bot']['active_on_supportmode'] == 1): ?> checked <?php endif; ?>>
			</td>
			<td>active when using FlashChat in "support" mode</td>
		</tr>
		-->
		<tr>
			<td align="right">active when an admin is not present</td>
			<td >
				<input type="checkbox" name="active_on_no_moderators" id="active_on_no_moderators" 
				<?php if ($this->_tpl_vars['_REQUEST']['bot']['active_on_no_moderators'] == 1): ?> checked <?php endif; ?>>
			</td>			
		</tr>
		<tr>
			<td align="right">active when there are no other bots in the room</td>
			<td >
				<input type="checkbox" name="active_on_no_bots" id="active_on_no_bots" 
				<?php if ($this->_tpl_vars['_REQUEST']['bot']['active_on_no_bots'] == 1): ?> checked <?php endif; ?>>
			</td>
			
		</tr>
		<tr>
			<td align="right">active when a particular user is present</td>
			<td >
				<select name="active_on_user">
					<?php $this->assign('selected', ($this->_tpl_vars['_REQUEST']['bot']['active_on_user'])); ?>
					<option id="0" <?php if ($this->_tpl_vars['selected'] == '0'): ?>selected<?php endif; ?>>--none--</option>
					<?php $_from = $this->_tpl_vars['_REQUEST']['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['ordersel']):
?>
					<option id="<?php echo $this->_tpl_vars['ordersel']['id']; ?>
" <?php if ($this->_tpl_vars['ordersel']['id'] == $this->_tpl_vars['selected']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['ordersel']['login']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>				
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit" name="submit" value="Submit"></td>
		</tr>
		</table>
	</form>
<?php else: ?>
Bots is disabled on your system.
<!--
The bot could not be added because the bot installation was skipped in the Flash Chat installer. 
Please re-run the installer to enable bot support.
-->
<?php endif; ?>	
</center>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>