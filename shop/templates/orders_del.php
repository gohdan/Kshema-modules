<h1>Удаление заказа</h1>

{{if:admin_link:<p><a href="/index.php?module=shop&action=orders_view_all">К просмотру заказов</a></p>}}

<hr>

<p>Вы действительно хотите удалить заказ <b>#id#</b>?</b>

<form action="/index.php?module=shop&action=orders_view_all" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>