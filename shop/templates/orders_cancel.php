<h1>Отмена заказа</h1>

{{if:admin_link:<p><a href="/index.php?module=shop&action=orders_view_all">К просмотру заказов</a></p>}}

<hr>

<p>Вы действительно хотите отменить заказ <b>#id#</b>?</b>

<form action="/index.php?module=shop&action=orders_view&order=#id#" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_cancel" value="Нет">
<input type="submit" name="do_cancel" value="Да">
</form>