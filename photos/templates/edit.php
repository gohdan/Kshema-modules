<h1>Редактирование фотографии</h1>


<p><a href="/photos/admin">В меню администрирования</a></p>
<p><a href="/photos/view_by_category/#category#">В категорию</a></p>

<p>#result#</p>

<p>#content#</p>

<form action="/photos/edit/#id/" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
<table>
<tr><td></td><td><img src="#image#"></td></tr>
<tr><td>Новое изображение:</td><td><input type="file" name="image"></td></tr>
<tr><td>Название:</td><td><input type="text" name="title" value="#title#" size="60"></td></tr>
<tr><td>Дата:</td><td><input type="text" name="date" value="#date#" size="10"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Описание:</td></tr>
<tr><td colspan="2"><textarea cols="40" rows="10" name="descr">#descr#</textarea></td></tr>	
<tr><td></td><td><input type="submit" name="do_update" value="Записать"></td></tr>
</table>
</form>
