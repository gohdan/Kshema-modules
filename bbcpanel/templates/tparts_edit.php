<h1>Редактирование частей шаблонов</h1>

{{if:id:<a href="/bbcpanel/bb_edit/#id#/">Редактировать доску</a><br>}}

<a href="/bbcpanel/bbs_view_all">Список досок</a><br>

{{if:show_bb_select_form:
<form action="/bbcpanel/tparts_edit/" method="post">
<select name="bb">#bbs_select#</select>
<input type="submit" name="do_select_bb" value="Выбрать">
</form>
}}

{{if:show_tparts_form:
<form action="/bbcpanel/tparts_edit/#id#/" method="post">
<input type="hidden" name="id" value="#id#">
Сотрите название, чтобы удалить запись<br>
#tparts#
<br>
Добавить часть шаблона:<br>
Название: <input name="new_title" size="30"><br>
HTML-код:<br>
<textarea name="new_tpart" rows="20" cols="40"></textarea><br>

<input type="submit" name="do_update" value="Записать">
</form>
}}

