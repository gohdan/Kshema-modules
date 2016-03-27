<h1>Выдача товара</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=store" method="post">
Товар: <select name="good">
#goods_select#
</select><br>

Выдать количество:<input type="text" name="qty"><br>

На объект: <select name="object">#objects_select#</select><br>

Сотруднику: <select name="user">#users_select#</select><br>

Комментарий:
<textarea name="commentary"></textarea><br>

<input type="submit" name="do_out_from_category" value="Выдать">

</form>