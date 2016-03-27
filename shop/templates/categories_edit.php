<h1>Редактирование категории</h1>

<p><a href="/shop/categories_view_adm">К списку категорий</a></p>

<p>#result#</p>

<p>#content#</p>

<form action="/shop/categories_edit/#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
<table>
<tr><td>Системное название (латинскими буквами; можно использовать цифры):</td><td><input type="text" name="name" value="#name#" size="40"></td></tr>
<tr><td>Название для вывода пользователю (можно любой текст):</td><td><input type="text" name="title" value="#title#" size="40"></td></tr>
{{if:image:<tr><td colspan="2"><img src="#image#"></td></tr>}}
<tr><td>Новое изображение-описание:</td><td><input type="file" name="image"></td></tr>
<tr><td>Подкатегория в категории:</td><td><select name="parent"><option value="0">Нет</option>#categories_select#</select></td></tr>
<tr><td>Порядок вывода:</td><td><input type="text" name="position" size="2" value="#position#"></td></tr>
<tr><td>H1:</td><td><input type="text" name="h1" size="40" value="#h1#"></td></tr>
<tr><td>Meta keywords:</td><td><input type="text" name="meta_keywords" size="40" value="#meta_keywords#"></td></tr>
<tr><td>Meta description:</td><td><input type="text" name="meta_description" size="40" value="#meta_description#"></td></tr>
<tr><td colspan="2">Описание:<br><textarea name="description">#description#</textarea></td></tr>
<tr><td>Шаблон всей страницы <i>(по умолчанию - default)</i>:</td><td><input type="text" name="page_template" value="#page_template#"></td></tr>
<tr><td>Шаблон просмотра категории <i>(по умолчанию - view_by_category)</i>:</td><td><input type="text" name="template" value="#template#"></td></tr>
<tr><td>Шаблон списка элементов <i>(по умолчанию - elements)</i>:</td><td><input type="text" name="list_template" value="#list_template#"></td></tr>
<tr><td>Шаблон просмотра отдельного элемента <i>(по умолчанию - view)</i>:</td><td><input type="text" name="element_template" value="#element_template#"></td></tr>
<tr><td>Шаблон меню <i>(по умолчанию - пусто)</i>:</td><td><input type="text" name="menu_template" value="#menu_template#"></td></tr>
<tr><td></td><td><input type="submit" name="do_update_category" value="Записать"></td></tr>
</table>
</form>
