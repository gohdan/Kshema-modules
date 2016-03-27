<h1>Редактирование объявления</h1>

<p>
<a href="/bills/view_by_category/#category#/">Обратно в категорию</a>
</p>

{{if:show_admin_link:
<p>
<a href="/bills/admin/">Меню администрирования объявлений</a><br>
<a href="/bills/add/">Добавить объявление</a><br>
<a href="/bills/help#edit">Справка</a>
</p>
}}

{{if:show_user_bills_link:<a href="/bills/view_by_user/">Мои объявления</a>}}

{{if:content:<p>#content#</p>}}
{{if:result:<p>#result#</p>}}

<form action="/bills/#action#/#id#/" method="post">
<input type="hidden" name="id" value="#id#">
{{if:satellite:<input type="hidden" name="satellite" value="#satellite#">}}
<table summary="bill edit table">
<tr><td>Название объявления:</td><td><input type="text" name="title" value="#title#"></td></tr>
<tr><td>Название для ЧПУ:</td><td><input type="text" name="name" value="#name#"></td></tr>
{{if:categories_select:<tr><td>Категория:</td><td><select name="category">#categories_select#</select></td></tr>}}
<tr><td>Текст:</td><td><textarea name="full_text">#full_text#</textarea></td></tr>
<tr><td></td><td><input type="submit" name="do_update" value="Сохранить"></td></tr>
</table>
</form>
