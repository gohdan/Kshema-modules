<tr>
<td>
{{if:image:<a href="/index.php?module=shop&action=view_good&good=#id#"><img src="#image#" style="float: left"></a>}}
</td>

<td style="vertical-align: top; padding: 20px">
<b><a href="/index.php?module=shop&action=view_good&good=#id#">#name#</a></b><br>
<b><a href="/index.php?module=shop&action=view_by_author&author=#author#">#author#</a></b><br>
</td>

{{if:show_edit_link:<td style="padding: 0px 3px 0px 3px"><a href="/index.php?module=shop&action=goods_edit&goods=#id#">Редактировать</a></td>}}
{{if:show_del_link:<td style="padding: 0px 3px 0px 3px"><a href="/index.php?module=shop&action=goods_del&goods=#id#">Удалить</a></td>}}

</tr>

<tr>
<td colspan="4">
{{if:new_qty:<p><input type="checkbox" name="good_#id#" value="#id#"><b>#new_price# руб.</b></p>}}
<p>#presence#</p>
</td>
</tr>

<tr>
<td colspan="4"><hr color="#DAEDFF"></td>
</tr>