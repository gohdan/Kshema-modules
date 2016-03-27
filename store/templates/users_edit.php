<h1>Редактирование сотрудника</h1>

<p><a href="/index.php?module=store&action=users_view_all">К просмотру сотрудников</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=store&action=users_edit&users=#id#" method="post">
<input type="hidden" name="id" value="#id#">
ФИО: <input type="text" name="name" value="#name#"><br>
Статус: <select name="status">
<option value="0"{{if:option_0: selected}}>работает</option>
<option value="1"{{if:option_1: selected}}>не работает</option>
</select><br>
<input type="submit" name="do_update" value="Записать">
</form>