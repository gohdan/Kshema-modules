<h1>Удаление таблиц базы данных новостей</h1>

<p>
<a href="/news/admin/">Вернуться в меню администрирования новостей</a><br>
<a href="/news/help#db_tables_drop">Справка</a>
</p>

#content#

{{if:result:<p>#result#</p>}}

<p>
Уничтожить таблицы:
</p>
<form action="/news/drop_tables/" method="post">
<input type="checkbox" name="drop_news_categories_table" value="ksh_news_categories">Категории новостей<br>
<input type="checkbox" name="drop_news_table" value="ksh_news">Новости<br>
<input type="checkbox" name="drop_news_privileges_table" value="ksh_news_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
