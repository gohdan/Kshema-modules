<h1>Редактирование статьи</h1>

{{if:category:
<p>
<a href="#inst_root#/articles/view_by_category/#category#/">Обратно в категорию</a>
</p>
}}

{{if:show_admin_link:
<p>
<a href="#inst_root#/articles/admin/">Меню администрирования статей</a><br>
<a href="#inst_root#/articles/add/">Добавить статью</a><br>
<a href="#inst_root#/articles/help#edit">Справка</a>
</p>
}}

{{if:show_user_articles_link:<a href="#inst_root#/articles/view_by_user/">Мои статьи</a>}}

{{if:content:<p>#content#</p>}}

{{if:result:<p>#result#</p>}}

<p><a href="/uploads/admin/" target="_upload">Загрузка изображений</a></p>

<form action="#inst_root#/articles/edit/#id#/{{if:satellite:satellite_#satellite#/}}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="#id#">
<!--	
    <input type="hidden" name="old_image" value="#image#">
    <input type="hidden" name="old_doc" value="#doc#">

    <img src="#image#"><br>
    Новое изображение-описание: <input type="file" name="image">
    <br>
	Прикреплённый документ: #doc#<br>
	Прикрепить другой документ: <input type="file" name="doc">
-->

<table summary="articles edit table">
<tr><td>Название:</td><td><input type="text" name="title" value="#title#"></td></tr>
<tr><td>Название для ЧПУ:</td><td><input type="text" name="name" value="#name#"></td></tr>
<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>Описание:</td><td><textarea cols="40" rows="20" name="descr">#descr#</textarea></td></tr>
<tr><td>Текст:</td><td><textarea cols="40" rows="20" name="full_text">#full_text#</textarea></td></tr>

<tr><td></td><td><input type="submit" name="do_update" value="Записать"></td></tr>
</table>
	
</form>
