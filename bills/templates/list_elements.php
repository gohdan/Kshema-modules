<tr>
<td colspan="2">
{{if:show_checkbox:<input type="checkbox" name="bill_#id#"> }}{{if:show_checked_checkbox:<input type="checkbox" name="bill_#id#" checked> }}<a href="#module#/#name#.html">#title#</a><br>
#date#
</td>
</tr>
<tr>
<td style="padding: 0px 3px 0px 3px">
#full_text#
</td>
</tr>
{{if:show_admin_link:
<tr>
<td colspan="2">
<a href="/bills/edit/#id#/">Редактировать</a> 
<a href="/bills/del/#id#/">Удалить</a> 
</td>
</tr>
}}
</tr>
