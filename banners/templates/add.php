<h1>Добавление баннера</h1>

<p>
<a href="/banners/admin/">К администрированию баннеров</a><br>
{{if:category:<a href="/banners/view_by_category/#category#/">В категорию</a><br>}}
<a href="/banners/help#banners_add">Справка</a>
</p>

{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/banners/add_banners/" method="post" enctype="multipart/form-data">
<table summary="Banner add table">
<tr><td>Системное название:</td><td><input type="text" name="name"></td></tr>
<tr><td>Название для вывода пользователю:</td><td><input type="text" name="title" size="40">
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Файл:</td><td><input type="file" name="image"></td></tr>
<tr><td>Тип:</td><td><select name="type">
<option value="img">Изображение</option>
<option value="bg">Фоновое изображение</option>
<option value="swf">Flash</option>
</select></td></tr>
<tr><td>Параметры Flash:</td><td><input name="params" type="text"></td></tr>
<tr><td>Класс оформления (CSS):</td><td><input name="class" type="text"></td></tr>
<tr><td>Описание:</td><td><input name="descr" type="text"></td></tr>
<tr><td>Alt:</td><td><input name="alt" type="text"></td></tr>
<tr><td>Ширина:</td><td><input name="width" type="text"></td></tr>
<tr><td>Высота:</td><td><input name="height" type="text"></td></tr>
<tr><td>Ссылка:</td><td><input name="link" type="text" size="40"></td></tr>
<tr><td>(<a href="/uploads/admin/" target="_upload">загрузить файл</a>)</td></tr>
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
