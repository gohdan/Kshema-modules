<h1>Добавление статей</h1>

{{if:result:<p>#result#</p>}}

{{if:show_admin_link:
<a href="/articles/admin/">К администрированию статей</a>
}}

{{if:show_user_articles_link:<a href="/articles/view_by_user/">Мои статьи</a>}}

{{if:category:<p><a href="#inst_root##module##action#/#category#/">Обратно в категорию</a></p>}}

<p><a href="/uploads/admin/" target="_upload">Загрузка изображений</a></p>

<form action="#inst_root#/articles/add/" method="post" enctype="multipart/form-data">
<input type="hidden" name="required_fields[]" value="title">
<input type="hidden" name="required_fields[]" value="full_text">
<!--
    Изображение-описание: <input type="file" name="image">
	Прикрепить документ: <input type="file" name="doc">
-->
<table summary="articles add table">
<tr><td>Название*:</td><td><input type="text" name="title" value="#title#"></td></tr>
<tr><td>Название для ЧПУ:</td><td><input type="text" name="name" value="#name#"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Описание:</td><td><textarea cols="40" rows="20" name="descr">#descr#</textarea></td></tr>
<tr><td>Текст*:</td><td><textarea cols="40" rows="20" name="full_text">#full_text#</textarea></td></tr>
{{if:show_captcha:<tr><td><img src="#inst_root#/libs/kcaptcha/index.php?#session_name#=#session_id#"></td><td>Проверочный код слева:<br><input type="text" name="keystring"></td></tr>}}
<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
