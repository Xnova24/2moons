{include file="adm/overall_header.tpl"}
<form action="" method="post">
<input type="hidden" name="mode" value="agregar">
<table width="40%">
<td colspan="3" class="c">{$po_add_planet}</td>
<tr>
   <td>{$input_id_user}</td>
   <td><input name="id" type="text" size="4"></td>
</tr><tr>
   <td>{$new_creator_coor}</td>
   <td><input name="galaxy" type="text" size="3" maxlength="1" onMouseOver='return overlib("{$po_galaxy}", CENTER, OFFSETX, 0, OFFSETY, -40);' onMouseOut='return nd();'>&nbsp; :
   <input name="system" type="text" size="3" maxlength="3" onMouseOver='return overlib("{$po_system}", CENTER, OFFSETX, 0, OFFSETY, -40);' onMouseOut='return nd();'>&nbsp; :
   <input name="planet" type="text" size="3" maxlength="2" onMouseOver='return overlib("{$po_planet}", CENTER, OFFSETX, 0, OFFSETY, -40);' onMouseOut='return nd();'><br>
   </td>
</tr><tr>
   <td>{$po_name_planet}</td>
   <td><input name="name" type="text" size="15" maxlength="25" value="{$po_colony}"></td>
</tr><tr>
   <td>{$po_fields_max}</td>
   <td><input name="field_max" type="text" size="6" maxlength="10"></td>
</tr>{if $admin_auth == 3}
<tr>
	<td>{$universum}</td>
	<td colspan="2">
    <select name="uni">
    	{foreach $AvailableUnis as $ID => $Universe}<option value="{$ID}">{$Universe.game_name}</option>{/foreach}
    </select>
    </td>
</tr>
{/if}<tr>
   <td colspan="2"><input type="Submit" value="{$button_add}"></td>
</tr><tr>
   <td colspan="2" style="text-align:left;"><a href="?page=create">{$new_creator_go_back}</a>&nbsp;<a href="?page=create&amp;mode=planet">{$new_creator_refresh}</a></td>
</tr>
</table>
</form>
{include file="adm/overall_footer.tpl"}