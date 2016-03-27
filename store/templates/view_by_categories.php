<h1>#category_name#</h1>

<p>#result#</p>

<p>#content#</p>

<p><a href="/index.php?module=store&action=view_by_categories&categories=#category_id#">Обновить</a> (автообновление пока не работает)</a></p>

{{if:show_admin_link:<p><a href="/index.php?module=store">К просмотру категорий</a></p>}}

<p><a href="/index.php?module=store&action=goods_add&category=#category_id#">Добавить товар в категорию</a></p>


<table>
<tr>

<td style="vertical-align: top; padding: 20px">
Наименование
</td>

<td style="vertical-align: top; padding: 20px">Количество</td>

<td style="vertical-align: top; padding: 20px">Статус</td>

<td style="vertical-align: top; padding: 20px">Единица измерения</td>

<td style="vertical-align: top; padding: 20px">Цена</td>

<td style="vertical-align: top; padding: 20px">Комментарий</td>

</tr>

#goods_by_category#
</table>

