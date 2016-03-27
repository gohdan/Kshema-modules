<h1>Уничтожение таблиц базы данных досок объявлений</h1>

{{if:content:<p>#content#<p>}}

{{if:result:<p>#result#</p>}}

<p>
<a href="/bbcpanel/admin/">Вернуться к меню администрирования</a><br>
<a href="/bbcpanel/help#db_tables_drop">Справка</a>
</p>


<p>
Уничтожить таблицы:
</p>
<form action="/bbcpanel/drop_tables/" method="post">
<input type="checkbox" name="drop_bbcpanel_categories_table" value="ksh_bbcpanel_categories">Категории досок объявлений<br>
<input type="checkbox" name="drop_bbcpanel_bbs_table" value="ksh_bbcpanel_bbs">Доски объявлений<br>
<input type="checkbox" name="drop_bbcpanel_privileges_table" value="ksh_bbcpanel_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
