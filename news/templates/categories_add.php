<h1>Добавление категорий</h1>

<p>
<a href="/news/view_categories/">Вернуться к просмотру категорий</a><br>
<a href="/news/help#categories_add">Справка</a>
</p>

{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/news/add_category/" method="post">
<table summary="Category add table">
<tr><td>Системное название (латинские буквы и цифры):</td><td><input type="text" name="name"></td></tr>
<tr><td>Название для вывода пользователю (любые символы):</td><td><input type="text" name="title"></td></tr>
<tr><td>Шаблон всей страницы <i>(по умолчанию - default)</i>:</td><td><input type="text" name="page_template" value="default"></td></tr>
<tr><td>Шаблон просмотра категории <i>(по умолчанию - view_by_category)</i>:</td><td><input type="text" name="template" value="view_by_category"></td></tr>
<tr><td>Шаблон списка новостей <i>(по умолчанию - news)</i>:</td><td><input type="text" name="list_template" value="news"></td></tr>
<tr><td>Шаблон просмотра новости <i>(по умолчанию - view)</i>:</td><td><input type="text" name="news_template" value="view"></td></tr>
<tr><td>Шаблон меню <i>(по умолчанию - пусто)</i>:</td><td><input type="text" name="menu_template" value=""></td></tr>
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
