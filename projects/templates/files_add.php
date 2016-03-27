<h1>Добавление файла в проект "#project_title#"</h1>

<a href="/index.php?module=projects&action=admin">Вернуться к администрированию проектов</a>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=files_add" method="post" enctype="multipart/form-data">
	<input type="hidden" name="project" value="#project#">
	Название файла, показываемое пользователю: <input type="text" name="name"><br>
	Номер файла: <input type="text" name="number"><br>
	Часть файла: <input type="text" name="part"><br>
	Выберите файл для закачки: <input type="file" name="image"><br>
    Или введите его расположение, если он уже закачан (пример: <b>/uploads/file.zip</b>): <input type="text" name="file_path"><br>
    Описание:<br><textarea cols="40" rows="20" name="descr"></textarea><br>
    <input type="submit" name="do_add" value="Добавить">
</form>