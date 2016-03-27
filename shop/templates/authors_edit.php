<h1>Редактирование автора</h1>

<p><a href="/shop/authors_view/">К просмотру авторов</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<form action="/shop/authors_edit/#id#/" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="image" value="#image#">
<input type="hidden" name="if_hide" value="0">

<table>
<tr><td>Название:</td><td><input type="text" name="name" value="#name#" size="30"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories#</select></td></tr>
<tr><td>Описание:</td><td><textarea name="descr">#descr#</textarea></td></tr>
<tr><td>Скрыть:</td><td><input type="checkbox" name="if_hide" value="1"{{if:if_hide: checked}}></td></tr>
{{if:image:<tr><td>Изображение:</td><td><img src="#image#" align="top"></td></tr>}}
<tr><td>Новое изображение:</td><td><input type="file" name="image"></td></tr>
<tr><td></td><td><input type="submit" name="do_update" value="Записать"></td></tr>
</table>

</form>
