<h1>Удаление таблиц базы данных проектов домов</h1>

<a href="/houses/admin/">Вернуться в меню администрирования проектов домов</a>

#content#

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/houses/drop_tables/" method="post">
<input type="checkbox" name="drop_houses_categories_table" value="ksh_houses_categories">Категории проектов домов<br>
<input type="checkbox" name="drop_houses_table" value="ksh_houses">Проекты домов<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
