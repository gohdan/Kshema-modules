<h1>Редактирование категории "#title#"</h1>

<p>
<a href="/houses/admin/">Вернуться к меню администрирования</a>
</p>

<p>
<a href="/houses/add_category/">Добавить категорию</a>
</p>

<p>#result#</p>

<p>#content#</p>

<form action="/houses/category_edit/" method="post">
<input type="hidden" name="id" value="#category_id#">
Системное название (латинскими буквами; можно использовать цифры):<br>
<input type="text" name="name" value="#name#"><br>
Название для вывода пользователю (можно любой текст):<br>
<input type="text" name="title" value="#title#"><br>
<input type="submit" name="do_update" value="Записать">
</form>
