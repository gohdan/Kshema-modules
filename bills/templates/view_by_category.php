{{if:title:<h1>#category_title#</h1>}}

{{if:show_admin_link:
<p>
<a href="/bills/categories_view/">К списку категорий</a><br>
<a href="/bills/help#view_by_category">Справка</a>
</p>}}

{{if:show_link_on_main:<p><a href="/#module_name#/">На главную страницу</a></p>}}

{{if:category_id:<p><a href="/bills/add/#category_id#/">Добавить объявление</a></p>}}

{{if:parent_link:<p><a href="#module_name##action#/#parent_link#/">На уровень выше</a></p>}}

{{if:parents:Последовательность категорий: #parents#}}

{{if:subcategories:<h2>Категории</h2>}}

#subcategories#

{{if:bills:<h2>Объявления</h2>}}
<table summary="bills">
#elements#
</table>

{{if:category_pages:<p>#category_pages#</p>}}

{{if:result:#result#}}

