<h1>Редактирование фотографии</h1>

<p>
<a href="/index.php?module=auto_models&action=images_view&model=#model#">К просмотру фотографий этой модели</a>
</p>

<p>#result#</p>

<p>#content#</p>

{{if:if_show_edit_form:
<form action="/index.php?module=auto_models&action=images_edit&images=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
Название: <input type="text" name="title" value="#title#"><br>
Текущее изображение: {{if:image:<img src="#image#">}}<br>
Новое изображение: <input type="file" name="image"><br>
<textarea name="descr" rows="30" cols="40">#descr#</textarea><br>
<input type="submit" name="do_save" value="Сохранить">
</form>
}}
