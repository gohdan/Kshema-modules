{{if:category:<h1>#category#</h1>}}

{{if:result:<p>#result#</p>}}

{{if:if_show_admin_link:<p>
<a href="/banners/admin/">Администрирование</a><br>
<a href="/banners/view_categories/">Просмотр всех категорий</a><br>
<a href="/banners/add_banners/category:#category_id#">Добавить баннер</a><br>
<a href="/banners/add_banners_batch/category:#category_id#">Добавить пачку баннеров</a>
</p>}}

{{if:content:<p>#content#</p>}}

<table summary="Banners list table">
#banners#
</table>

