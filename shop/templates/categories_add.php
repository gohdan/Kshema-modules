<h1>Добавление категорий</h1>

<p><a href="/shop/categories_view_adm/">К списку категорий</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<form action="/shop/categories_add/" method="post" enctype="multipart/form-data">
<table>
<tr><td>Системное название (латинские буквы и цифры):</td><td><input type="text" name="name" size="40"></td></tr>
<tr><td>Название для вывода пользователю (любые символы):</td><td><input type="text" name="title" size="40"></td></tr>
<tr><td>Изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Подкатегория в категории:</td><td><select name="parent"><option value="0">Нет</option>#categories_select#</select></td></tr>
<tr><td>Порядок вывода:</td><td><input type="text" name="position" size="2"></td></tr>
<tr><td>H1:</td><td><input type="text" name="h1" size="40"></td></tr>
<tr><td>Meta keywords:</td><td><input type="text" name="meta_keywords" size="40"></td></tr>
<tr><td>Meta description:</td><td><input type="text" name="meta_description" size="40"></td></tr>
<tr><td colspan="2">Описание:<br><textarea name="description"></textarea></td></tr>
<tr><td>Шаблон всей страницы <i>(по умолчанию - default)</i>:</td><td><input type="text" name="page_template" value="default"></td></tr>
<tr><td>Шаблон просмотра категории <i>(по умолчанию - view_by_category)</i>:</td><td><input type="text" name="template" value="view_by_category"></td></tr>
<tr><td>Шаблон списка элементов <i>(по умолчанию - elements)</i>:</td><td><input type="text" name="list_template" value="elements"></td></tr>
<tr><td>Шаблон просмотра отдельного элемента <i>(по умолчанию - view)</i>:</td><td><input type="text" name="element_template" value="view"></td></tr>
<tr><td>Шаблон меню <i>(по умолчанию - пусто)</i>:</td><td><input type="text" name="menu_template" value=""></td></tr>
<tr><td></td><td><input type="submit" name="do_add_category" value="Добавить"></td></tr>
</table>
</form>

