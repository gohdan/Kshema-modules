<h1>Добавление новости</h1>

<p>
<a href="/news/admin/">К администрированию новостей</a><br>
<a href="/news/help#news_add">Справка</a>
</p>

<p>
<a href="/uploads/admin/" target="uploads">Закачать файл</a>
</p>

<p>#result#</p>

<p>#content#</p>

<form action="/news/add_news/" method="post" enctype="multipart/form-data">
<table summary="News add table">
<tr><td>Название:</td><td><input type="text" name="name"></td></tr>
<tr><td>Дата <i>(Не меняйте формат!)</i>:</td><td><input type="text" name="date" size="10" align="right" value="#date#"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Короткое описание:<br><i>(Одно-два предложения для кратких списков новостей)</i></td></tr>
<tr><td colspan="2"><textarea style="width: 300px; height: 200px;" name="short_descr"></textarea></td></tr>
<tr><td>Описание:<br><i>(Более полное описание для подробных списков новостей)</i></td></tr>
<tr><td colspan="2"><textarea cols="50" rows="20" name="descr"></textarea></td></tr>
<tr><td>Полный текст новости:<br><i>(Показывается при просмотре новости)</i></td></tr>
<tr><td colspan="2"><textarea cols="50" rows="20" name="full_text"></textarea></td></tr>
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
