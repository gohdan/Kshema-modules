<h1>Редактирование категории проектов</h1>

<p>
<a href="/index.php?module=projects&action=admin">Вернуться к администрированию проектов</a>
</p>

<p>
<a href="/index.php?module=projects&action=add_category">Добавить категорию</a>
</p>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=category_edit" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#category_id#">
<input type="hidden" name="old_image" value="#image#">
Системное название (латинские буквы и цифры): <input type="text" name="name" value="#name#"><br>
Название для пользователей (любые символы): <input type="text" name="title" value="#title#"><br>
Автор: <input type="text" name="author" value="#author#"><br>
Отдельная история в томе номер: <input type="text" name="att_project" size="2" value="#att_project#"><br>
Статус:  <select name="status">#statuses#</select><br>
<img src="#image#"><br>
Новое изображение-описание: <input type="file" name="image"><br>
Описание:<br>
<textarea name="descr" style="width: 600px; height: 300px">#descr#</textarea><br>

<input type="submit" name="do_update" value="Записать">
</form>