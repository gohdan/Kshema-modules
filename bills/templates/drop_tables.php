<h1>Уничтожение таблиц базы данных объявлений</h1>

{{if:content:<p>#content#<p>}}

{{if:result:<p>#result#</p>}}

<p>
<a href="/bills/admin/">Вернуться к меню администрирования</a><br>
<a href="/bills/help#db_tables_drop">Справка</a>
</p>


<p>
Уничтожить таблицы:
</p>
<form action="/bills/drop_tables/" method="post">
<input type="checkbox" name="drop_categories_table" value="ksh_bills_categories">Категории объявлений<br>
<input type="checkbox" name="drop_bills_table" value="ksh_bills">Объявления<br>
<input type="checkbox" name="drop_privileges_table" value="ksh_bills_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
