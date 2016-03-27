<h1>Просмотр заказа</h1>

<p>#result#</p>

<p>#content#</p>

{{if:admin_link:<p><a href="/index.php?module=shop&action=orders_view_all">К просмотру заказов</a></p>}}

<p>
<b>Заказ номер #id#</b><br>
<b>Статус: #order_status#</b><br>
<b>Изменён: #date#</b>
</p>

{{if:cancel:<p><a href="/index.php?module=shop&action=orders_cancel&orders=#id#">Отменить заказ</a></p>}}

{{if:delete:<p><a href="/index.php?module=shop&action=orders_del&orders=#id#">Удалить заказ</a></p>}}
{{if:change:
<form action="/index.php?module=shop&action=orders_view&order=#id#" method="post">
<input type="hidden" name="id" value="#id#">
Назначить статус:
<select name="status">
#statuses#

<input type="submit" name="do_change_status" value="Сменить">
</form>
</select>}}
<table class="tbl_cart">
<tr>
<th></th>
<th style="padding: 0px 3px 0px 3px">Название</th>
<th style="padding: 0px 3px 0px 3px">Цена</th>
<th style="padding: 0px 3px 0px 3px">Количество</th>
</tr>
#cart_goods#
</table>

<table class="props">
<tr>
<th>Количество товаров:</th>
<td align="right">#sum_qty#</td>
</tr>
<tr>
<th>Вес</th>
<td align="right">#sum_weight#</td>
</tr>
<tr>
<th>Стоимость товаров:</th>
<td align="right">#sum_price# руб.</th>
</tr>
<tr>
<th>Стоимость доставки:</th>
<td align="right">#sum_delivery# руб.</td>
</tr>
<tr>
<th><b>Стоимость с доставкой:</b></th>
<td align="right"><b>#sum_cost# руб.</b></td>
</tr>
</table>

<h2>Адрес</h2>
<table class="tbl_form">
<tr><td>Фамилия:</td><td>#sur_name#</td></tr>
<tr><td>Имя</td><td>#first_name#</td></tr>
<tr><td>Отчество:</td><td>#second_name#</td></tr>
<tr><td>Страна:</td><td>Россия</td></tr>
<tr><td>Индекс:</td><td>#post_code#</td></tr>
<tr><td>Область:</td><td>#area#</td></tr>
<tr><td>Город:</td><td>#city#</td></tr>
<tr><td>Улица/дом/квартира:</td><td>#address#</td>
</tr>
</table>
