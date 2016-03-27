{{if:title:
<tr>
<td colspan="2"><h2>#title#</h2></td>
</tr>
}}
<tr>
<td>
{{if:image:<img src="#image#">}}
</td>
<td style="vertical-align: top">#code#</td>
</tr>
<tr>
{{if:if_show_admin_link:<td colspan="2"><a href="/index.php?module=auto_models&action=colors_edit&colors=#id#">Редактировать</a> <a href="/index.php?module=auto_models&action=colors_del&colors=#id#">Удалить</a></td>}}
</tr>
