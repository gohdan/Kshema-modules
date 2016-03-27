<h1>Удаление объекта</h1>

<p><a href="/index.php?module=store&action=objects_view_all">К просмотру объектов</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<p>Вы действительно хотите завершить объект <b>#name#</b>?</b>

<form action="/index.php?module=store&action=objects_view_all" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>