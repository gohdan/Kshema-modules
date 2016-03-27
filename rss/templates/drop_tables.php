<h1>Уничтожение таблиц базы данных RSS</h1>

{{if:content:<p>#content#<p>}}

{{if:result:<p>#result#</p>}}

<p>
<a href="/rss/admin/">Вернуться к меню администрирования</a><br>
</p>


<p>
Уничтожить таблицы:
</p>
<form action="/rss/drop_tables/" method="post">
<input type="checkbox" name="drop_rss_table" value="ksh_rss">Элементы RSS<br>
<input type="checkbox" name="drop_privileges_table" value="ksh_rss_privileges">Привилегии<br>
<input type="checkbox" name="drop_privileges_table" value="ksh_rss_access">Доступ<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
