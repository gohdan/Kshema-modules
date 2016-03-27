<h1>Удаление модели</h1>

<p>
<a href="/index.php?module=auto_models&action=list_view">Вернуться к просмотру моделей</a><br>
</p>

<p>Вы действительно хотите удалить модель <b>#title#</b>?</p>

#content#

<form action="/index.php?module=auto_models&action=list_view" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
