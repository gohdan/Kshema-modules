<tr>
<td style="padding: 0px 3px 3px 3px">
{{if:state_up:<span style="color: green; font-weight: bold">+</span>}}
{{if:state_down:<span style="color: red; font-weight: bold">!</span>}}
</td>
<td>
<input type="checkbox" name="bbs[]" value="#id#">
</td>
<td style="padding: 0px 3px 3px 3px">
#id#
</td>
<td style="padding: 0px 3px 0px 3px">
<a href="/bbcpanel/bb_edit/#id#/">#title#</a>
</td>
<td style="padding: 0px 3px 0px 3px">
{{if:url:<a href="#url#">#url#</a>}}
</td>
<td style="padding: 0px 3px 0px 3px">
#category_title#
</td>
<td style="padding: 0px 3px 0px 3px">
{{if:show_edit_link:<a href="/bbcpanel/bb_edit/#id#/">Редактировать</a>}} 
{{if:show_del_link:<a href="/bbcpanel/bb_del/#id#/">Удалить</a>}}
</td>
</tr>
