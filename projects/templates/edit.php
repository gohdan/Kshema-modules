<h1>Редактирование проекта</h1>

<a href="/index.php?module=projects&action=view_categories">Категории проектов</a>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=edit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="#id#">
    <input type="hidden" name="old_image" value="#image#">
    Системное название (латинские буквы и цифры): <input type="text" name="name" value="#name#"><br>
	Название для пользователя (любые символы): <input type="text" name="title" value="#title#"><br>
    Категория:
    <select name="category">
    #categories_select#
    </select><br>
    <img src="#image#"><br>
    Новое изображение-описание: <input type="file" name="image">
    <br>
    Описание:<br>
    <textarea cols="40" rows="10" name="descr">#descr#</textarea><br>
    <input type="submit" name="do_update" value="Записать">
</form>