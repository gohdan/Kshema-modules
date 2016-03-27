<h1>Добавление проекта</h1>

<a href="/index.php?module=projects&action=admin">К администрированию проектов</a>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=add_projects" method="post" enctype="multipart/form-data">
    Системное название (латинские буквы и цифры): <input type="text" name="name"><br>
	Название для пользователей (любые символы): <input type="text" name="title"><br>
    Категория:
    <select name="category">
    #categories_select#
    </select><br>
    Изображение-описание: <input type="file" name="image">
    <br>
    Описание:<br>
    <textarea cols="40" rows="20" name="descr"></textarea><br>
    <input type="submit" name="do_add" value="Добавить">
</form>