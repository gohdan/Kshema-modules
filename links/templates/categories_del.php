<h1>Удаление категории</h1>

<p>
<a href="/index.php?module=links&action=view_categories">Вернуться к просмотру категорий</a><br>
</p>

<p>Вы действительно хотите удалить категорию <b>#name#</b>? Все ссылки в этой категории также будут удалены!</p>

#content#

<form action="/index.php?module=links&action=view_categories" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>

<hr>