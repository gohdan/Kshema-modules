<h1>Уничтожение таблиц базы данных гостевой книги</h1>

{{if:content:<p>#content#<p>}}

{{if:result:<p>#result#</p>}}

<p>
<a href="/guestbook/admin/">Вернуться к меню администрирования</a><br>
</p>


<p>
Уничтожить таблицы:
</p>
<form action="/guestbook/drop_tables/" method="post">
<input type="checkbox" name="drop_guestbook_table" value="ksh_guestbook">Сообщения<br>
<input type="checkbox" name="drop_privileges_table" value="ksh_guestbook_privileges">Привилегии<br>
<input type="checkbox" name="drop_access_table" value="ksh_guestbook_access">Права доступа<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
