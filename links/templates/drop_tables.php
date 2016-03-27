<h1>Удаление таблиц БД ссылок</h1>

<a href="/index.php?module=links&action=admin">Вернуться к меню администрирования</a>

#content#

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=links&action=drop_tables" method="post">
<input type="checkbox" name="drop_ksh_links_categories_table" value="ksh_links_categories">Категории ссылок<br>
<input type="checkbox" name="drop_ksh_links_table" value="ksh_links">Ссылки<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
