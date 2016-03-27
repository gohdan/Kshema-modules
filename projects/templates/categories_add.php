<h1>Добавление категорий проектов</h1>

<p>
<a href="/index.php?module=projects&action=view_categories">Вернуться к просмотру категорий</a><br>
</p>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=add_category" method="post" enctype="multipart/form-data">
Системное название (латинские буквы и цифры): <input type="text" name="name"><br>
Название для пользователей (любые символы): <input type="text" name="title"><br>
Отдельная история в томе номер: <input type="text" name="att_project" size="2"><br>
Автор: <input type="text" name="author"><br>
Статус:  <select name="status">#statuses#</select><br>
Изображение-описание: <input type="file" name="image"><br>
Описание:<br>
<textarea name="descr" style="width: 600px; height: 300px"></textarea><br>
<input type="submit" name="do_add" value="Добавить">
</form>

<hr>