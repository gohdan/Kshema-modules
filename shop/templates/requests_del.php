<h1>Удаление заявки</h1>

{{if:admin_link:<p><a href="/index.php?module=shop&action=requests_view">К просмотру заявок</a></p>}}

<hr>

<p>Вы действительно хотите удалить заявку<b>#id#</b>?</b>

<form action="/index.php?module=shop&action=requests_view" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>