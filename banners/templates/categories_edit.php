<h1>Редактирование категории "#title#"</h1>

<p>
<a href="/banners/admin/">Вернуться к меню администрирования</a><br>
<a href="/banners/view_categories/">Вернуться к просмотру категорий</a><br>
<a href="/banners/help#categories_edit">Справка</a>
</p>


{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/banners/category_edit/" method="post">
<input type="hidden" name="id" value="#category_id#">
Системное название (латинскими буквами; можно использовать цифры):<br>
<input type="text" name="name" value="#name#"><br>
Название для вывода пользователю (можно любой текст):<br>
<input type="text" name="title" value="#title#" size="40"><br>

<input type="submit" name="do_update" value="Записать">
</form>
