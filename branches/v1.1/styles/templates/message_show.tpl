<form action="game.php?page=messages" method="post">
{foreach key=MessID item=MessInfo from=$MessageList}
<tr style="height: 20px;">
<td style="width:40px;" rowspan="2">
<td>{if $MessInfo.type == 50 && $MessCategory == 999}{$mg_game_message}{else}{$MessInfo.from}{/if}</td>
<td>{$MessInfo.subject}
{if $MessInfo.type == 1 && $MessCategory != 999}
<a href="javascript:f('game.php?page=messages&amp;mode=write&amp;id={$MessInfo.sender}&amp;subject=Re:{$MessInfo.subject|strip_tags}','');" title="Nachricht an {$MessInfo.from|strip_tags} schreiben">
<img src="{$dpath}img/m.gif" border="0"></a>
{/if}
</td></tr>
<tr>
<td colspan="3" class="left">{$MessInfo.text}</td>
</tr>
{/foreach}
<tr>
<td colspan="4">
<select id="deletemessages" name="deletemessages">
{if $MessCategory != 50}
<option value="deleteunmarked">{$mg_delete_unmarked}</option>
<option value="deleteall">{$mg_delete_all}</option>
</select>
<input value="{$mg_confirm_delete}" type="submit">
</td>
</tr>
</table>