<h1>Редактирование категории</h1>

<p><a href="/index.php?module=store">К просмотру категорий</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=store&action=categories_edit&categories=#id#" method="post">
<input type="hidden" name="id" value="#id#">
Название: <input type="text" name="name" value="#name#"><br>
<input type="submit" name="do_update" value="Записать">
</form>