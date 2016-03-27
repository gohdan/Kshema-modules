<h1>Корзина</h1>

<p>#result#</p>

<p>#content#</p>

{{if:show_cart:
<table class="tbl_cart">
<tr><th></th><th>Название</th><th>Цена</th><th>Количество</th><th>Удалить</th></tr>
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

<form action="/index.php?module=shop&action=order_create" method="post" style="display:inline"><input type="submit" class="button" value="Оформить заказ"></form>
}}