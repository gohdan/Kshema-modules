<h1>Редактирование анкеты</h1>

<p><a href="/index.php?module=forms&action=list">Вернуться к списку анкет</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=forms&action=edit&forms=#id#" method="post">

Название: <input type="text" name="title" value="#title#"><br>
Системное название: <input type="text" name="name" value="#name#"><br>

<h2>Поля</h2>
#fields#

<p>
Добавить поле:<br>
<span style="font-size: 8pt">Пожалуйста, не используйте символ "|"</span><br>
Системное название: <input type="text" name="new_field_name"> Расшифровка: <input type="text" name="new_field_value">
</p>

<h2>Шаблон</h2>
<textarea name="template" cols="80" rows="30">#template#</textarea><br>

<input type="submit" name="do_update" value="Записать">

</form>