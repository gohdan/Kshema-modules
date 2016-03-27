<h1>Редактирование ссылки</h1>

<p>
<a href="/index.php?module=links&action=admin">К администрированию ссылок</a><br>
<a href="/index.php?module=links&action=view_categories">Категории ссылок</a>
</p>

#content#

<form action="/index.php?module=links&action=edit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="#id#">
	<input type="hidden" name="old_image" value="#image#">
    Название <input type="text" name="name" value="#name#"><br>
    Категория:
    <select name="category">
    #categories#
    </select>
    <br>
	<img src="#image#"><br>
    Новое изображение-описание: <input type="file" name="image">
    <br>
	URL: <input type="text" name="url" value="#url#"><br>
    Описание:<br>
    <textarea cols="40" rows="10" name="descr">#descr#</textarea><br>
    <input type="submit" name="do_update" value="Записать">
</form>
