{{if:show_user_articles_link:<p><a href="#inst_root#/articles/view_by_user/">Мои статьи</a></p>}}

{{if:title:<h1>#title#</h1>}}

{{if:show_admin_link:
<p>
<a href="/articles/edit/#id#">Редактировать</a><br>
<a href="#inst_root#/articles/admin/">Меню администрирования статей</a><br>
<a href="#inst_root#/articles/help#view">Справка</a>
</p>
}}

{{if:content:<p>#content#</p>}}

{{if:date:<p>#date#</p>}}

{{if:full_text:#full_text#}}

{{if:category:<p><a href="#inst_root##module##action#/#category#/">Обратно в категорию</a></p>}}



{{if:resemble_elements:
<h2>Похожие статьи</h2>
<table>
#resemble_elements#
</table>}}

