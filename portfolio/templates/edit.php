<h1>Редактирование портфолио</h1>

<p>
<a href="/portfolio/admin/">Администрирование портфолио</a><br>
<a href="/portfolio/view/#id#/">Просмотр этого элемента</a>
</p>

{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/portfolio/edit/#id#/" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="category" value="#category#">
<input type="hidden" name="old_image" value="#image#">

<table summary="portfolio edit table">
<tr><td>Название:</td><td><input type="text" name="title" value="#title#" size="50"></td></tr>
<tr><td>Системное название:</td><td><input type="text" name="name" value="#name#" size="50"></td></tr>
<tr><td>Дата <i>(ГГГГ-ММ-ДД, не меняйте формат!)</i>:</td><td><input type="text" name="date" size="10" align="right" value="#date#"></td></tr>
<tr><td>Порядок вывода (чем больше, тем раньше):</td><td><input type="text" name="order" size="3" align="right" value="#order#"></td></tr>
<tr><td>Годы (через запятую):</td><td><input type="text" name="year" size="50" align="right" value="#year#"></td></tr>
<tr><td>Теги (через запятую):</td><td><input type="text" name="tags" size="50" align="right" value="#tags#"></td></tr>
<tr><td colspan="2"><img src="#image#"></td></tr>
<tr><td>Новое изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Короткое описание:</td></tr>
<tr><td colspan="2"><textarea style="width: 300px; height: 200px;" name="descr">#descr#</textarea></td></tr>
<tr><td>Полный текст:</td></tr>
<tr><td colspan="2"><textarea cols="50" rows="20" name="full_text">#full_text#</textarea></td></tr>
<tr><td colspan="2">Добавить изображения:</td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_0"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_1"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_2"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_3"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_4"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_5"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_6"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_7"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_8"></td></tr>
<tr><td colspan="2"><input type="file" name="add_new_image_9"></td></tr>
<tr><td></td><td><input type="submit" name="do_update" value="Записать"></td></tr>
</table>

<h2>Изображения</h2>

#images#

<input type="submit" name="do_update" value="Записать">

</form>


