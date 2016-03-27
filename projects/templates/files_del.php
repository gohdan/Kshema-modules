<h1>Удаление файла</h1>

<p>
<a href="/index.php?module=projects&action=files_view_by_project&project=#project#">Вернуться к просмотру файлов в проекте</a><br>
</p>

<p>Вы действительно хотите удалить файл <b>#name#</b>?</p>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=projects&action=files_view_by_project&project=#project#" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
