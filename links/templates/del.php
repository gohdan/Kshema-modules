<h1>Удаление ссылки</h1>

<p>
<a href="/index.php?module=links&action=view_by_category&category=#category_id#">Вернуться к просмотру ссылок в категории</a><br>
</p>

<p>Вы действительно хотите удалить ссылку <b>#name#</b>?</p>

#content#

<form action="/index.php?module=links&action=view_by_category&category=#category_id#" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>

<hr>