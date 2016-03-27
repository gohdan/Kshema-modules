<h1>Обновление программного кода</h1>

{{if:id:<a href="/bbcpanel/bb_edit/#id#/">Редактировать доску</a><br>}}

<p><a href="/bbcpanel/bbs_view_all">Список досок</a></p>

{{if:show_bb_select_form:
<form action="/bbcpanel/update_all/" method="post">
<select name="bb">#bbs_select#</select>
<input type="submit" name="do_select_bb" value="Выбрать">
</form>
}}

{{if:show_update_form:
<form action="/bbcpanel/update_all/" method="post">
<input type="hidden" name="bb" value="#id#">
<input type="submit" name="do_update" value="Обновить">
</form>
}}

{{if:result:#result#}}
