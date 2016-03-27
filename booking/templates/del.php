<h1>Удаление брони</h1>

<p>
<a href="/booking/admin/">Вернуться в меню администрирования</a><br>
</p>

<p>Вы действительно хотите удалить бронь <b>#id#</b> от #date#?</p>

#content#

<form action="/booking/list_view/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
