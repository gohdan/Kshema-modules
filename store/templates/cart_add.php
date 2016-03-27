<h1>Добавление товара в корзину</h1>
<form action="/index.php?module=store&action=cart_add" method="post">
<input type="hidden" name="id" value="#id#">
Количество: <input type="text" name="qty">
<input type="submit" name="do_add" value="Добавить">
</form>