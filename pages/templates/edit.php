<h1>Редактирование страницы</h1>

<p>
<a href="/#lang#/pages/admin/">Меню администрирования страниц</a><br>
<a href="/#lang#/pages/list_view/">Список страниц</a><br>
<a href="/#lang#/pages/add/">Добавить страницу</a><br>
<a href="/#lang#/pages/help#pages_edit">Справка</a>
</p>

<p><a href="/#lang#/pages/view/page:#name#">Посмотреть, как выглядит страница</a></p>

<p>
<a href="/uploads/admin/" target="uploads">Закачать файл</a>
</p>

{{if:content:<p>#content#</p>}}

<form action="/#lang#/pages/edit/page:#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
<table summary="Page edit table" class="pages_edit_table">
<tr><td>Название (английскими буквами, например - page):</td><td><input type="text" name="name" value="#name#" size="50"></td></tr>
<tr><td>Название в меню:</td><td><input type="text" name="title" value="#title#" size="50"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Главная страница категории:</td><td><select name="subcategory"><option value="0">-</option>#subcategories_select#</select></td></tr>
<tr><td>Порядок вывода:</td><td><input type="text" name="position" size="2" value="#position#"></td></tr>
<tr><td>Изображение-описание:</td><td>{{if:image:<img src="#image#">}}</td></tr>
<tr><td>Новое изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Ключевые слова:</td><td><input type="text" name="meta_keywords" value="#meta_keywords#"></td></tr>
<tr><td>Описание:</td><td><input type="text" name="meta_description" value="#meta_description#"></td></tr>
<tr><td>Общий шаблон <i>(по умолчанию - default)</i>:</td><td><input type="text" name="template" value="#template#"></td></tr>
<tr><td>Шаблон меню <i>(по умолчанию - default)</i>:</td><td><input type="text" name="menu_template" value="#menu_template#"></td></tr>
<tr><td>Дополнительный CSS-файл:</td><td><input type="text" name="css" value="#css#" size="40"></td></tr>
<tr><td>Содержание:</td></tr>
<tr><td colspan="2"><textarea name="full_text">#full_text#</textarea></td></tr>
<tr><td></td><td><input type="submit" name="do_update" value="Сохранить"></td></tr>
</table>
</form>
