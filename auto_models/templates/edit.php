<h1>Редактирование модели автомобиля</h2>

<p>
<a href="/index.php?module=auto_models&action=admin">Меню администрирования моделей автомобилей</a><br>
<a href="/index.php?module=auto_models&action=list_view">Список моделей</a>
</p>

<p><a href="/index.php?module=auto_models&action=view&model=#name#" target="_new">Посмотреть, как выглядит модель</a></p>

<p>#content#</p>

<form action="/index.php?module=auto_models&action=edit" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
Название (английскими буквами, например - getz): <input type="text" name="name" value="#name#"><br>
Название в меню: <input type="text" name="title" value="#title#"><br>
Категория: <select name="category">#categories_select#</select><br>
Ссылка: <input type="text" name="link" value="#link#" size="50"><br>
Шаблон <i>(по умолчанию - default)</i>: <input type="text" name="template" value="#template#"><br>
<img src="#image#">
Новое изображение-описание: <input type="file" name="image"><br>
Описание:<br>
<textarea cols="60" rows="30" name="full_text">#full_text#</textarea><br>
<input type="submit" name="do_update" value="Сохранить">
</form>
