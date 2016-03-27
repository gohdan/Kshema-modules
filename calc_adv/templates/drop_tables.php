<h1>Удаление таблиц базы данных калькулятора</h1>

<a href="/index.php?module=calc_adv&action=admin">Вернуться в меню администрирования калькулятора</a>

#content#

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=calc_adv&action=drop_tables" method="post">
<input type="checkbox" name="drop_calc_adv_cities_table" value="ksh_calc_adv_cities">Города<br>
<input type="checkbox" name="drop_calc_adv_calcs_table" value="ksh_calc_adv_calcs">Расчёты<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
