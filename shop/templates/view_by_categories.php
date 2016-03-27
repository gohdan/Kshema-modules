<h1>#h1#</h1>

<p>#result#</p>

<p>#content#</p>

{{if:show_admin_link:<p><a href="/shop/admin/">Администрировать магазин</a></p>}}

{{if:show_add_link:<p><a href="/shop/goods_add/category:#category_id#">Добавить товар в категорию</a></p>}}

{{if:subcategories:
<h2>Подкатегории</h2>

<p>
#subcategories#
<p>
}}

{{if:description:#description#}}

<p>Страницы: #pages# |<p>

{{if:show_multiple_add_form:<form action="/shop/cart_add_multiple/" method="post">}}


<table>
#goods_by_category#
</table>

{{if:show_multiple_add_form:<input type="submit" name="do_add" value="Положить в корзину"></form>}}

<p>Страницы: #pages# |<p>
