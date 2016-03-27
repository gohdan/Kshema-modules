{{if:title:
<tr>
<td><h2>#title#</h2></td>
</tr>
}}
<tr>
<td>
{{if:video:#video#}}
</td>
</tr>
<tr>
<td style="vertical-align: top">#descr#</td>
</tr>
<tr>
{{if:if_show_admin_link:<td colspan="2"><a href="/index.php?module=auto_models&action=videos_edit&videos=#id#">Редактировать</a> <a href="/index.php?module=auto_models&action=videos_del&videos=#id#">Удалить</a></td>}}
</tr>
