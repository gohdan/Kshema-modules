<h1>Добавление товаров</h1>

<p>#result#</p>

<p>#content#</p>


<form action="/index.php?module=store&action=goods_add&category=#category_id#" method="post" enctype="multipart/form-data">
Название товара: <input type="text" name="name"><br>
Категория: <select name="category">
#categories_select#
</select><br>
Добавить перед товаром: <select name="position">
#goods_select#
</select><br>
<hr>
Единица измерения: <input type="text" name="measure"><br>
Цена (только цифры!): <input type="text" name="price"><br>
<hr>
Получить количество (только цифры!): <input type="text" name="qty" size="3"><br>
С объекта: <select name="object">#objects_select#</select><br>
От сотрудника: <select name="user">#users_select#</select><br>

Комментарий:<br>
<textarea name="commentary"></textarea><br>


<input type="submit" name="do_add" value="Добавить">
</form>

