<h1>Уничтожение таблиц базы данных страниц сайта</h1>

{{if:content:<p>#content#<p>}}

{{if:result:<p>#result#</p>}}

<p>
<a href="/pages/admin/">Вернуться к меню администрирования</a><br>
<a href="/pages/help#db_tables_drop">Справка</a>
</p>


<p>
Уничтожить таблицы:
</p>
<form action="/pages/drop_tables/" method="post">
<input type="checkbox" name="drop_categories_table" value="ksh_pages_categories">Категории страниц<br>
<input type="checkbox" name="drop_pages_table" value="ksh_pages">Страницы<br>
<input type="checkbox" name="drop_privileges_table" value="ksh_pages_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
