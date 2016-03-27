<h1>Получение товара</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=store" method="post">
<input type="hidden" name="good" value="#id#">
Название: <b>#name#</b><br>
Цена: <b>#price#</b><br>
Количество: <b>#qty#</b><br>
Единица измерения: <b>#measure#</b><br>
Статус: <b>#status#</b><br>
Комментарий:<br>
#commentary#

<hr>

Получить количество:<input name="qty" size="3"><br>

С объекта: <select name="object">#objects_select#</select><br>

От сотрудника: <select name="user">#users_select#</select><br>

Комментарий:
<textarea name="commentary"></textarea><br>

<input type="submit" name="do_in" value="Получить">

</form>