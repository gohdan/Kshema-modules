<h1>Рекомендованные товары</h1>

<p>#result#</p>

<p>#content#</p>

{{if:show_admin_link:<p><a href="/shop/admin/">Администрировать магазин</a></p>}}

<p>Страницы: #pages# |<p>

{{if:show_multiple_add_form:<form action="/shop/cart_add_multiple/" method="post">}}


<table>
#goods_recommended#
</table>

{{if:show_multiple_add_form:<input type="submit" name="do_add" value="Положить в корзину"></form>}}

<p>Страницы: #pages# |<p>
