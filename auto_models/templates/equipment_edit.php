<h1>Редактирование дополнительного оборудования</h1>

<p>
<a href="/index.php?module=auto_models&action=equipment_view&model=#model#">К просмотру дополнительного оборудования для этой модели</a>
</p>

<p>#result#</p>

<p>#content#</p>

{{if:if_show_edit_form:
<form action="/index.php?module=auto_models&action=equipment_edit&equipment=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="old_image" value="#image#">
Название: <input type="text" name="title" value="#title#"><br>
Текущее изображение-описание: {{if:image:<img src="#image#">}}<br>
Новое изображение-описание: <input type="file" name="image"><br>
<textarea name="full_text" rows="30" cols="40">#full_text#</textarea><br>
<input type="submit" name="do_save" value="Сохранить">
</form>
}}
