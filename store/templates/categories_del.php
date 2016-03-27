<h1>Удаление категории</h1>

<p><a href="/index.php?module=store">К просмотру категорий</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<p>Вы действительно хотите удалить категорию <b>#name#</b>?</b>

<form action="/index.php?module=store" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>