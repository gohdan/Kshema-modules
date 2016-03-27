<h1>Удаление выпуска подкаста</h1>

<p>
<a href="#inst_root#/podcast/admin/">Вернуться в меню администрирования</a><br>
<a href="#inst_root#/podcast/view_by_category/1/">Список выпусков</a><br>
</p>

{{if:result:<p>#result#</p>}}

{{if:show_del_form:
<p>Вы действительно хотите удалить выпуск <b>"#title#"</b>?</p>


<form action="#inst_root#/podcast/del/#id#/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
}}
