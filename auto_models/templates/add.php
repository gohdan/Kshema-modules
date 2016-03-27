<h1>Добавление моделей автомобилей</h1>

<p>#result#</p>

<p>#content#</p>

{{if:if_show_admin_link:
<p>
<a href="/index.php?module=auto_models&action=admin">Меню администрирования моделей автомобилей</a><br>
<a href="/index.php?module=auto_models&action=list_view">Список моделей автомобилей</a>
</p>
}}

{{if:if_show_add_form:
<form action="/index.php?module=auto_models&action=add" method="post" enctype="multipart/form-data">
Название (английскими буквами, например - getz): <input type="text" name="name"><br>
Название в меню: <input type="text" name="title"><br>
Категория: <select name="category">#categories_select#</select><br>
Ссылка: <input type="text" name="link" size="50"><br>
Шаблон: <input type="text" name="template" value="default"><br>
Изображение-описание: <input type="file" name="image"><br>
Описание:<br>
<textarea cols="60" rows="30" name="full_text"></textarea><br>
<input type="submit" name="do_add" value="Добавить">
</form>
}}
