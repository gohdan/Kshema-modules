<h1>Добавление категорий</h1>

<p>
<a href="/banners/view_categories/">Вернуться к просмотру категорий</a><br>
<a href="/banners/help#categories_add">Справка</a>
</p>

{{if:result:<p>#result#</p>}}

{{if:content:<p>#content#</p>}}

<form action="/banners/add_category/" method="post">
Системное название (латинские буквы и цифры):<br>
<input type="text" name="name"><br>
Название для вывода пользователю (любые символы):<br>
<input type="text" name="title" size="40"><br>

<input type="submit" name="do_add" value="Добавить">
</form>
