<h1>Удаление статьи</h1>

{{if:show_admin_link:
<p>
<a href="#inst_root#/articles/admin/">Вернуться в меню администрирования</a><br>
<a href="#inst_root#/articles/help#bb_del">Справка</a>
</p>
}}

{{if:show_user_articles_link:<p><a href="#inst_root#/articles/view_by_user/">Мои статьи</a></p>}}

{{if:category:<p><a href="#inst_root#/articles/view_by_category/#category#/">В категорию</a></p>}}

{{if:result:<p>#result#</p>}}

{{if:show_del_form:
<p>Вы действительно хотите удалить статью <b>#title#</b>?</p>


<form action="#inst_root#/articles/del/#id#/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
}}
