<h1>Добавление страниц</h1>

#content#

<p>
<a href="/pages/admin/">Меню администрирования страниц</a><br>
{{if:category:<a href="/pages/view_by_category/#category#/">В категорию</a><br>}}
<a href="/pages/list_view/">Список всех страниц</a><br>
<a href="/pages/help#pages_add">Справка</a>
</p>

<p>
<a href="/uploads/admin/" target="uploads">Закачать файл</a>
</p>

<form action="/pages/add/#category#" method="post" enctype="multipart/form-data">
<table summary="Pages add table" class="pages_add_table">
<tr><td>Системное название (английскими буквами, например - page):</td><td><input type="text" name="name" size="50"></td></tr>
<tr><td>Название для вывода пользователю:</td><td><input type="text" name="title" size="50"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Главная страница категории:</td><td><select name="subcategory"><option value="0">-</option>#subcategories_select#</select></td></tr>
<tr><td>Порядок вывода:</td><td><input type="text" name="position" size="2"></td></tr>
<tr><td>Изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Ключевые слова:</td><td><input type="text" name="meta_keywords" value=""></td></tr>
<tr><td>Описание:</td><td><input type="text" name="meta_description" value=""></td></tr>
<tr><td>Общий шаблон:</td><td><input type="text" name="template" value="default"></td></tr>
<tr><td>Шаблон меню:</td><td><input type="text" name="menu_template" value="default"></td></tr>
<tr><td>Дополнительный CSS-файл:</td><td><input type="text" name="css" value="" size="40"></td></tr>
<tr><td>Содержание:</td></tr>
<tr><td colspan="2"><textarea name="full_text"></textarea></td></tr>
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
