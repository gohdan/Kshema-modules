<h1>Добавление выпуска подкаста</h1>

{{if:result:<p>#result#</p>}}

<a href="/podcast/admin/">В меню администрирования</a>


<form action="#inst_root#/podcast/add/" method="post" enctype="multipart/form-data">
<input type="hidden" name="required_fields[]" value="author">
<input type="hidden" name="required_fields[]" value="title">
<input type="hidden" name="required_fields[]" value="subtitle">
<input type="hidden" name="required_fields[]" value="summary">
<input type="hidden" name="required_fields[]" value="min">
<input type="hidden" name="required_fields[]" value="sec">
<input type="hidden" name="category" value="1">

<table summary="podcast add table">
<tr><td>Автор:</td><td><input type="text" name="author" value="#author#" size="30"></td></tr>
<tr><td>Название:</td><td><input type="text" name="title" value="#title#" size="30"></td></tr>
<tr><td>Подзаголовок:</td><td><input type="text" name="subtitle" value="#subtitle#" size="30"></td></tr>
<tr><td style="vertical-align: top">Описание:</td><td><textarea name="summary">#summary#</textarea></td></tr>
<tr><td>Изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Файл:</td><td><input type="file" name="enclosure"></td></tr>
<tr><td>Длительность:</td><td><input type="text" name="min" value="#min#" size="3"> минут <input type="text" name="sec" value="#sec#" size="3"> секунд</td></tr>




<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
