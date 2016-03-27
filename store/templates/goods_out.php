<h1>Выдача товара</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=store" method="post">
<input type="hidden" name="good" value="#id#">
Название: <b>#name#</b><br>
Цена: <b>#price#</b><br>
Единица измерения: <b>#measure#</b><br>
Количество: <b>#qty#</b><br>
Комментарий:<br>
#commentary#

<hr>

Выдать количество:<input type="text" name="qty"><br>

На объект: <select name="object">#objects_select#</select><br>

Сотруднику: <select name="user">#users_select#</select><br>

Комментарий:
<textarea name="commentary"></textarea><br>

<input type="submit" name="do_out" value="Выдать">

</form>