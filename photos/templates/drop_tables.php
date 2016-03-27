<h1>Удаление таблиц БД фотографий</h1>

<a href="/index.php?module=photos&action=admin">Вернуться к администрированию фотографий</a>

<p>#result#</p>

<p>#content#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=photos&action=drop_tables" method="post">
<input type="checkbox" name="drop_photos_categories_table" value="ksh_photos_categories">Категории коллекций<br>
<input type="checkbox" name="drop_photos_table" value="ksh_photos">Фотографии<br>
<input type="checkbox" name="drop_photos_access_table" value="ksh_photos_access">Таблица доступов<br>
<input type="checkbox" name="drop_photos_privileges_table" value="ksh_photos_privileges">Таблица прав<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
