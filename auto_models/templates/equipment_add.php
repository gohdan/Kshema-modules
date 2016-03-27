<h1>Добавление дополнительного оборудования для #model_title#</h1>

<p>
<a href="/index.php?module=auto_models&action=equipment_view&model=#model#">К просмотру дополнительного оборудования</a>
</p>

<p>#result#</p>

<p>#content#</p>

{{if:if_show_add_form:
<form action="/index.php?module=auto_models&action=equipment_add&model=#model#" method="post" enctype="multipart/form-data">
<input type="hidden" name="model" value="#model#">
Название: <input type="text" name="title"><br>
Изображение-описание: <input type="file" name="image"><br>
<textarea name="full_text" rows="30" cols="40"></textarea><br>
<input type="submit" name="do_add" value="Добавить">
</form>
}}
