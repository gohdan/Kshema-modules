<h1>Удаление автора</h1>

<p><a href="/index.php?module=shop&action=authors_view">К просмотру авторов</a></p>

<hr>

<p>Вы действительно хотите удалить автора <b>#name#</b>?</b>

<form action="/index.php?module=shop&action=authors_view" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>