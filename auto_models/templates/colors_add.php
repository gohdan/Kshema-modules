<h1>Добавление цветов #model_title#</h1>

<p>
<a href="/index.php?module=auto_models&action=colors_view&model=#model#">К просмотру цветов этой модели</a>
</p>

<p>#result#</p>

<p>#content#</p>

{{if:if_show_add_form:
<form action="/index.php?module=auto_models&action=colors_add&model=#model#" method="post" enctype="multipart/form-data">
<input type="hidden" name="model" value="#model#">
Название: <input type="text" name="title"><br>
Изображение: <input type="file" name="image"><br>
Код: <input type="text" name="code"><br>
<input type="submit" name="do_add" value="Добавить">
</form>
}}
