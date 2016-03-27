{{if:title:
<tr>
<td><h2>#title#</h2></td>
</tr>
}}
<tr>
<td>
{{if:image:<img src="#image#">}}
</td>
</tr>
<tr>
<td style="vertical-align: top">#descr#</td>
</tr>
<tr>
{{if:if_show_admin_link:<td colspan="2"><a href="/index.php?module=auto_models&action=images_edit&images=#id#">Редактировать</a> <a href="/index.php?module=auto_models&action=images_del&images=#id#">Удалить</a></td>}}
</tr>
