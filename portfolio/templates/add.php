<h1>Добавление в портфолио</h1>

<p>
<a href="/portfolio/admin/">К администрированию портфолио</a><br>
</p>

<p>#result#</p>

<p>#content#</p>

<p>Дополнительные изображения можно будет добавить позже, в редактировании уже добавленного элемента.</p>

<form action="/portfolio/add/" method="post" enctype="multipart/form-data">
<input type="hidden" name="category" value="1">
<table summary="portfolio add table">
<tr><td>Название:</td><td><input type="text" name="title" size="50"></td></tr>
<tr><td>Дата <i>(ГГГГ-ММ-ДД, не меняйте формат!)</i>:</td><td><input type="text" name="date" size="10" align="right" value="#date#"></td></tr>
<tr><td>Порядок вывода (чем больше, тем раньше):</td><td><input type="text" name="order" size="3" align="right"></td></tr>
<tr><td>Годы (через запятую):</td><td><input type="text" name="year" size="50" align="right" value="#year#"></td></tr>
<tr><td>Теги (через запятую):</td><td><input type="text" name="tags" size="50" align="right" value="#tags#"></td></tr>
<tr><td>Изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Короткое описание:</td></tr>
<tr><td colspan="2"><textarea style="width: 300px; height: 200px;" name="descr"></textarea></td></tr>
<tr><td>Полный текст:</td></tr>
<tr><td colspan="2"><textarea cols="50" rows="20" name="full_text"></textarea></td></tr>
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
