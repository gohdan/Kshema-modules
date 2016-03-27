<h1>Редактирование технических характеристик #model_title#</h1>

<p>#result#</p>

<p>#content#</p>

<p><a href="/index.php?module=auto_models&action=characteristics_view&model=#model#">Посмотреть</a></p>

{{if:if_show_edit_form:
<form action="/index.php?module=auto_models&action=characteristics_edit&model=#model#" method="post">
<input type="hidden" name="model" value="#model#">
<textarea name="full_text" cols="40" rows="30">#full_text#</textarea><br>
<input type="submit" name="do_save" value="Сохранить">
</form>
}}
