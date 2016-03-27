<h1>Удаление таблиц базы данных анкет</h1>

<a href="/index.php?module=forms&action=admin">Вернуться в меню администрирования анкет</a>

#content#

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=forms&action=drop_tables" method="post">
<input type="checkbox" name="drop_forms_categories_table" value="ksh_forms_categories">Категории анкет<br>
<input type="checkbox" name="drop_forms_table" value="ksh_forms">Анкеты<br>
<input type="checkbox" name="drop_forms_submitted_table" value="ksh_forms_submitted">Заполненные анкеты<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
