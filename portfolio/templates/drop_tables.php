<h1>Удаление таблиц базы данных портфолио</h1>

<p>
<a href="/portfolio/admin/">Вернуться в меню администрирования</a><br>
</p>

#content#

{{if:result:<p>#result#</p>}}

<p>
Уничтожить таблицы:
</p>
<form action="/portfolio/drop_tables/" method="post">
<input type="checkbox" name="drop_portfolio_categories_table" value="ksh_portfolio_categories">Категории портфолио<br>
<input type="checkbox" name="drop_portfolio_table" value="ksh_portfolio">Портфолио<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
