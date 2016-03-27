<h1>Редактирование файла</h1>

<a href="/index.php?module=projects&action=files_view_by_project&project=#project#">Просмотр файлов в проекте</a>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=files_edit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="#id#">
	<input type="hidden" name="old_filepath" value="#file_path#">
	Название файла, показываемое пользователю (любые символы): <input type="text" name="name" value="#name#"><br>
	Дата (не меняйте формат!): <input type="text" name="date" size="10" value="#date#"><br>
	Номер файла: <input type="text" name="number" value="#number#"><br>
	Часть файла: <input type="text" name="part" value="#part#"><br>
	Текущее расположение файла: <b>#file_path#</b><br>
	Выберите другой файл для закачки: <input type="file" name="image"><br>
	Или введите его расположение, если он уже закачан (пример: <b>/uploads/file.zip</b>): <input type="text" name="file_path"><br>
    <br>
    Описание:<br>
    <textarea cols="40" rows="10" name="descr">#descr#</textarea><br>
    <input type="submit" name="do_update" value="Записать">
</form>