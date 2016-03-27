<h1>Редактирование сателлита</h1>

<p>
<a href="/bbcpanel/bbs_view_all/">Список сателлитов</a>
</p>

{{if:result:<p>#result#</p>}}

{{if:show_admin_link:
<p>
<a href="/bbcpanel/admin/">Меню администрирования сателлитов</a><br>
<a href="/bbcpanel/bb_add/">Добавить сателлит</a><br>
</p>
}}

<p>
<a href="/bbcpanel/tparts_edit/#id#/">Редактировать части шаблонов</a><br>
<a href="/bbcpanel/update_all/#id#/">Обновить программный код</a>
</p>

<p>
#modules#
</p>

{{if:content:<p>#content#</p>}}
{{if:result:<p>#result#</p>}}

<form action="/bbcpanel/bb_edit/#id#/" method="post">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="name" value="">
<table summary="bb edit table">
<tr><td>Название для пользователя:</td><td><input type="text" name="title" value="#title#"></td></tr>
<tr><td>Тематика:</td><td><select name="category">#categories_select#</select></td></tr>
<tr><td>URL:</td><td><input type="text" name="url" value="#url#"></td></tr>
<tr><td>Каталог, в который установлен</td><td><input type="text" name="instroot" value="#instroot#"></td></tr>
<tr><td></td><td><input type="submit" name="do_update" value="Сохранить"></td></tr>
</table>
</form>
