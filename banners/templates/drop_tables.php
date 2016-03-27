<h1>Удаление таблиц базы данных баннеров</h1>

<p>
<a href="/banners/admin/">Вернуться в меню администрирования баннеров</a><br>
<a href="/banners/help#db_tables_drop">Справка</a>
</p>

#content#

{{if:result:<p>#result#</p>}}

<p>
Уничтожить таблицы:
</p>
<form action="/banners/drop_tables/" method="post">
<input type="checkbox" name="drop_banners_categories_table" value="ksh_banners_categories">Категории баннеров<br>
<input type="checkbox" name="drop_banners_table" value="ksh_banners">Баннеры<br>
<input type="checkbox" name="drop_banners_privileges_table" value="ksh_banners_privileges">Привилегии<br>
<input type="checkbox" name="drop_banners_access_table" value="ksh_banners_access">Права доступа<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
