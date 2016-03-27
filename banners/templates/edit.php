<h1>Редактирование баннера</h1>

<p>
<a href="/banners/view_categories/">Категории баннеров</a><br>
<a href="/banners/view_by_category/#category#/">В категорию</a><br>
<a href="/banners/help#banners_edit">Справка</a>
</p>


{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/banners/edit/#id#/" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
<table summary="Banner edit table">
<tr><td>Системное название:</td><td><input type="text" name="name" value="#name#"></td></tr>
<tr><td>Название для вывода пользователю:</td><td><input type="text" name="title" value="#title#" size="40"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td colspan="2">{{if:image:<img src="#image#" alt="Old image">}}</td></tr>
<tr><td>Новый файл:</td><td><input type="file" name="image"></td></tr>
<tr><td>Тип:</td><td><select name="type">
<option value="img">Изображение</option>
<option value="bg">Фоновое изображение</option>
<option value="swf">Flash</option>
</select></td></tr>
<tr><td>Параметры Flash:</td><td><input type="text" name="params" value="#params#"></td></tr>
<tr><td>Класс оформления (CSS):</td><td><input type="text" name="class" value="#class#"></td></tr>
<tr><td>Описание:</td><td><input type="text" name="descr" value="#descr#"></td></tr>
<tr><td>Alt:</td><td><input type="text" name="alt" value="#alt#"></td></tr>
<tr><td>Ширина:</td><td><input type="text" name="width" value="#width#"></td></tr>
<tr><td>Высота:</td><td><input type="text" name="height" value="#height#"></td></tr>
<tr><td>Ссылка:</td><td><input name="link" type="text" value="#link#" size="40"></td></tr>
<tr><td>(<a href="/uploads/admin/" target="_upload">загрузить файл</a>)</td></tr>
<tr><td></td><td><input type="submit" name="do_update" value="Записать"></td></tr>
</table>
</form>
