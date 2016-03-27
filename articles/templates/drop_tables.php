<h1>Удаление таблиц базы данных статей</h1>

<a href="#inst_root#/index.php?module=articles&action=admin">Вернуться к администрированию статей</a>

<p>#content#</p>

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="#inst_root#/index.php?module=articles&action=drop_tables" method="post">
<input type="checkbox" name="drop_articles_categories_table" value="ksh_articles_categories">Категории статей<br>
<input type="checkbox" name="drop_articles_table" value="ksh_articles">Статьи<br>
<input type="checkbox" name="drop_articles_privileges_table" value="ksh_articles_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
