<h1>Назначение специфичных названий разделов</h1>

{{if:id:<a href="/bbcpanel/bb_edit/#id#/">Редактировать доску</a><br>}}

<a href="/bbcpanel/bbs_view_all">Список досок</a><br>

{{if:show_bb_select_form:
<form action="/bbcpanel/titles_edit/" method="post">
<select name="bb">#bbs_select#</select>
<input type="submit" name="do_select_bb" value="Выбрать">
</form>
}}

{{if:show_titles_form:
<form action="/bbcpanel/titles_edit/#id#/" method="post">
<input type="hidden" name="id" value="#id#">
Сотрите название, чтобы удалить запись<br>
#titles#
<br>
Добавить название:<br>
<select name="new_category">#categories_select#</select><br>
Системное название: <input type="text" size="30" name="new_name"><br>
Название для вывода: <input type="text" size="30" name="new_title"><br>

<input type="submit" name="do_update" value="Записать">
</form>
}}

