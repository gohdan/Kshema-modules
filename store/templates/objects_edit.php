<h1>Редактирование объекта</h1>

<p><a href="/index.php?module=store&action=objects_view_all">К просмотру объектов</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=store&action=objects_edit&objects=#id#" method="post">
<input type="hidden" name="id" value="#id#">
Название: <input type="text" name="name" value="#name#"><br>
Статус: <select name="status">
<option value="0"{{if:option_0: selected}}>в работе</option>
<option value="1"{{if:option_1: selected}}>завершён</option>
</select><br>
<input type="submit" name="do_update" value="Записать">
</form>