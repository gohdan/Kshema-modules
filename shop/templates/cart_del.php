<h1>Удаление товара из корзины</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=shop&action=cart_view" method="post">
<input type="hidden" name="id" value="#id#">
Вы действительно хотите удалить из корзины товар <b>#name#</b>?<br>
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>