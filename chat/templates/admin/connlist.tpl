{assign var=title value=Connections}
{include file=top.tpl}

<center>
<h4>Connections</h4>

<form name="connlist" id="connlist" action="connlist.php" method="post">
	<input type="hidden" id="sort" name="sort" value="none">
</form>	

{if $connections}

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
{foreach from=$connections item=connection}
<tr>
	<td>{$connection.id}</td>
	<td align=center>{$connection.updated}</td>
	<td align=center>{$connection.created}</td>
	<td align=center>
	{if $connection.userid}
		<a href=user.php?id={$connection.userid}>{$connection.login}</a>
	{else}
		-
	{/if}
	</td>
	<td align=center>{$connection.roomid}</td>
	<td align=center>{$connection.state}</td>
	<td>{$connection.color}</td>
	<td>{$connection.start}</td>
	<td align=center>{$connection.lang}</td>
	<td>{$connection.ip}</td>
	<td align=center>{$connection.tzoffset}</td>
	<td align=center>{$connection.host}</td>
</tr>

{/foreach}
{else}
	No connections found
{/if}

{include file=bottom.tpl}
