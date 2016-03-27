<h1>Добавление видеороликов #model_title#</h1>

<p>
<a href="/index.php?module=auto_models&action=videos_view&model=#model#">К просмотру видеороликов этой модели</a>
</p>

<p>#result#</p>

<p>#content#</p>

{{if:if_show_add_form:
<form action="/index.php?module=auto_models&action=videos_add&model=#model#" method="post">
<input type="hidden" name="model" value="#model#">
Название: <input type="text" name="title"><br>
Код ролика:<br>
<textarea name="video" style="width: 500px; height: 200px"></textarea><br>
Описание:<br>
<textarea name="descr" rows="30" cols="40"></textarea><br>
<input type="submit" name="do_add" value="Добавить">
</form>
}}
