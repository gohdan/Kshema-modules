<h1>Удаление таблиц базы данных счётчика посещений</h1>

<a href="/index.php?module=counter&action=admin">Вернуться в меню администрирования счётчика посещений</a>

#content#

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=counter&action=drop_tables" method="post">
<input type="checkbox" name="drop_counter_days_table" value="ksh_counter_days">Статистика по дням<br>
<input type="checkbox" name="drop_counter_monthes_table" value="ksh_counter_monthes">Статистика по месяцам<br>
<input type="checkbox" name="drop_counter_years_table" value="ksh_counter_years">Статистика по годам<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
