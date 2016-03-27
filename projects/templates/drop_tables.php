<h1>Удаление таблиц базы данных проектов</h1>

<a href="/index.php?module=projects&action=admin">Вернуться к администрированию проектов</a>

<p>#result#</p>

<p>#content#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=projects&action=drop_tables" method="post">
<input type="checkbox" name="drop_projects_categories_table" value="ksh_projects_categories">Категории проектов<br>
<input type="checkbox" name="drop_projects_table" value="ksh_projects">Проекты<br>
<input type="checkbox" name="drop_projects_files_table" value="ksh_projects_files">Файлы проектов<br>
<input type="checkbox" name="drop_projects_statuses_table" value="ksh_projects_statuses">Статусы проектов<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
