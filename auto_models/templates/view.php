<h1>#title#</h1>

<p>
{{if:id:
<a href="/index.php?module=auto_models&action=characteristics_view&model=#id#">Технические характеристики</a><br>
<a href="/index.php?module=auto_models&action=prices_view&model=#id#">Комплектация и цены</a><br>
<a href="/index.php?module=auto_models&action=equipment_view&model=#id#">Дополнительное оборудование</a><br>
<a href="/index.php?module=auto_models&action=images_view&model=#id#">Фотогалерея</a><br>
<a href="/index.php?module=auto_models&action=videos_view&model=#id#">Видеоролики</a><br>
<a href="/index.php?module=auto_models&action=colors_view&model=#id#">Цвета</a><br>
}}
{{if:link:<a href="#link#">Ссылка</a><br>}}
</p>

{{if:if_show_edit_link:
<p><a href="/index.php?module=auto_models&action=edit&model=#id#">Редактировать</a></p>
}}

<p>#content#</p>

#full_text#
