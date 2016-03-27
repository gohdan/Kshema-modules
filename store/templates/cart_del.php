<h1>Удаление товара из корзины</h1>

<p>#result#</p>

<p>#content#</p>

<p>Вы действительно хотите удалить товар из корзины?</b>

<form action="/index.php?module=store&action=cart_out" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>