<h1>Добавление анкеты</h1>

<p><a href="/index.php?module=forms&action=admin">Вернуться в меню администрирования анкет</a></p>

<hr>


<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=forms&action=add" method="post">
    Название: <input type="text" name="name"><br>
    Шаблон:<br>
    <textarea name="template" cols="80" rows="30"></textarea><br>
    <input type="submit" name="do_add" value="Добавить">
</form>