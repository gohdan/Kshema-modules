<h1>Удаление страницы</h1>

<p>
<a href="/pages/admin/">Вернуться в меню администрирования</a><br>
<a href="/pages/help#pages_del">Справка</a>
</p>

<p>Вы действительно хотите удалить страницу <b>#title#</b>?</p>

#content#

<form action="/pages/list_view/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
