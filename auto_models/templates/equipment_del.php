<h1>Удаление дополнительного оборудования</h1>

<p>#content#</p>

<p>
<a href="/index.php?module=auto_models&action=equipment_view&model=#model#">К просмотру дополнительного оборудования для этой модели</a>
</p>

{{if:if_show_del_form:
<form action="/index.php?module=auto_models&action=equipment_view&model=#model#" method="post">
<input type="hidden" name="id" value="#id#">
Вы действительно хотите удалить <b>#title#</b>?<br>
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>
}}
