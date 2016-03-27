<h1>Добавление ссылки</h1>

<p>
<a href="/index.php?module=links&action=admin">К администрированию ссылок</a>
</p>

#content#

<form action="/index.php?module=links&action=add_links" method="post" enctype="multipart/form-data">
    Название <input type="text" name="name"><br>
    Категория:
    <select name="category">
    #categories#
    </select>
    <br>
	Изображение-описание: <input type="file" name="image">
    <br>
	URL: <input type="text" name="url"><br>
    Описание:<br>
    <textarea cols="40" rows="10" name="descr"></textarea><br>
    <input type="submit" name="do_add" value="Добавить">
</form>
