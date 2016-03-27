<h1>Редактирование товара</h1>

<p>#result#</p>

<p>#content#</p>

<p>Незаполненные пункты в информации о товаре не показываются.</p>

<form action="/index.php?module=store&action=goods_edit&goods=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">

Название товара: <input type="text" name="name" value="#name#"><br>
Категория: <select name="category">
#categories#
</select><br>

Цена (только цифры!): <input type="text" name="price" value="#price#"><br>
Единица измерения: <input type="text" name="measure" value="#measure#"><br>
Статус: <select name="status">
<option value="0"{{if:status_0: selected}}>в наличии</option>
<option value="1"{{if:status_1: selected}}>удалён</option>
</select>
<hr>

Комментарий:<br>
<textarea name="commentary">#commentary#</textarea><br>

<input type="submit" name="do_update" value="Записать">
</form>

