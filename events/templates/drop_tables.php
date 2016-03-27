<h1>Удаление таблиц базы данных событий</h1>

<a href="/index.php?module=events&action=admin">Вернуться в меню администрирования событий</a>

#content#

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=events&action=drop_tables" method="post">
<input type="checkbox" name="drop_events_table" value="ksh_events">События<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
