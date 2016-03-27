<h1>Добавление пачки баннеров</h1>

<p>
<a href="/banners/admin/">К администрированию баннеров</a><br>
{{if:category:<a href="/banners/view_by_category/#category#/">В категорию</a><br>}}
</p>

{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/banners/add_banners_batch/" method="post" enctype="multipart/form-data">
<table summary="Banner add table">


<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Тип:</td><td><select name="type">
<option value="img">Изображение</option>
<option value="bg">Фоновое изображение</option>
<option value="swf">Flash</option>
</select></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_0"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_1"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_2"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_3"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_4"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_5"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_6"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_7"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_8"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image_9"></td></tr>


<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
