<h1>Удаление заполненной анкеты</h1>

<p>
<a href="/index.php?module=forms&action=view_submitted_forms&type=#type#">Вернуться к просмотру анкет в категории</a><br>
</p>

<p>Вы действительно хотите удалить анкету?</p>

#content#

<form action="/index.php?module=forms&action=view_submitted_forms&type=#type#" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>

<hr>