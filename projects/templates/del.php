<h1>Удаление проекта</h1>

<p>
<a href="/index.php?module=projects&action=view_by_category&category=#category_id#">Вернуться к просмотру проектов в категории</a><br>
</p>


<p>Вы действительно хотите удалить проект <b>#name#</b>?</p>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=view_by_category&category=#category_id#" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
