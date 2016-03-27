<tr>
{{if:show_id:
<td style="padding: 0px 3px 3px 3px">
#id#
</td>
}}
{{if:image:<td><a href="/auto_models/#name#.html"><img src="#image#"></a></td>}}
{{if:show_name:
<td style="padding: 0px 3px 0px 3px">
#name#
</td>
}}
{{if:title:
<td style="padding: 0px 3px 0px 3px">
<a href="/auto_models/#name#.html">#title#</a>
</td>
}}
<td>{{if:if_show_edit_link:<a href="/index.php?module=auto_models&action=edit&model=#id#">Редактировать</a>}}</td>
<td style="padding: 0px 3px 0px 3px">{{if:if_show_del_link:<a href="/index.php?module=auto_models&action=del&model=#id#">Удалить</a>}}</td>
</tr>

