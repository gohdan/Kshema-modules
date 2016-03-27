<h1>#module_title# - Редактирование темы "#title#"</h1>

<p>
<a href="/#module_name#/categories_view/">Вернуться к списку</a><br>
</p>


<p>#result#</p>

<p>#content#</p>

<form action="/#module_name#/categories_edit/#id#/" method="post">
<input type="hidden" name="id" value="#id#">
<table>
<tr><td>Название:</td><td><input type="text" name="title" value="#title#"></td></tr>
<tr><td></td><td><input type="submit" name="do_update_category" value="Записать"></td></tr>
</table>
</form>
