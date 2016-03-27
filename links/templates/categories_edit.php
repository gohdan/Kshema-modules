<h1>Редактирование категорий</h1>

<p>
<a href="/index.php?module=links&action=admin">Вернуться к меню администрирования</a>
</p>

<p>
<a href="/index.php?module=links&action=add_category">Добавить категорию</a>
</p>

#content#

<form action="/index.php?module=links&action=category_edit" method="post">
<input type="hidden" name="id" value="#category_id#">
<input type="text" name="name" value="#name#">
<input type="submit" name="do_update" value="Записать">
</form>