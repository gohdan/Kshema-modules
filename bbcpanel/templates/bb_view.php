<h1>Просмотр доски объявлений #title#</h1>

{{if:content:<p>#content#</p>}}

<p>
<a href="/bbcpanel/view_by_category/#category#/">Обратно в категорию</a>
</p>

{{if:show_admin_link:
<p>
<a href="/bbcpanel/admin/">Меню администрирования досок объявлений</a><br>
<a href="/bbcpanel/help#bb_add">Справка</a>
</p>
}}

<p>
ID: #id#<br>
Системное название: #name#<br>
Название для вывода пользователю: #title#<br>
Тематика: #subjects#<br>
Категория: #category_title#<br>
URL: {{if:url:<a href="#url#">#url#</a>}}<br>
Разделы: <ul>#sections#</ul><br>
Объявлений на странице: #bills_per_page#<br>
Режим просмотра объявлений: #bill_view_mode#<br>
Тема оформления: #theme#
</p>
