<h1>Назначение специфичных названий разделов</h1>

{{if:satellite_id:<a href="/bills/admin_satellite/#satellite_id#/">В меню администрирования сателлита</a><br>}}

<form action="/bills/titles_edit/satellite_#satellite_id#/" method="post">
<input type="hidden" name="id" value="#satellite_id#">
Сотрите название для вывода, чтобы удалить запись<br>
#titles#
<br>
Добавить название:<br>
<select name="new_category">#categories_select#</select><br>
Системное название: <input type="text" size="30" name="new_name"><br>
Название для вывода: <input type="text" size="30" name="new_title"><br>

<input type="submit" name="do_update" value="Записать">
</form>

