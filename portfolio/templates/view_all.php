<h1>Портфолио</h1>

{{if:result:<p>#result#</p>}}

{{if:show_admin_link:<p>
<a href="/portfolio/admin/">Администрирование</a><br>
<a href="/portfolio/view_categories/">Просмотр всех категорий</a><br>
<a href="/portfolio/add/">Добавить в портфолио</a>
</p>}}

{{if:content:<p>#content#</p>}}

#categories_titles#

<table>
#portfolio#
</table>

#pages#
