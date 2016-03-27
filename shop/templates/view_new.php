<h1>Новые товары</h1>

<p>#result#</p>

<p>#content#</p>

{{if:show_admin_link:<p><a href="/index.php?module=shop&action=admin">Администрировать магазин</a></p>}}

{{if:show_multiple_add_form:<form action="/index.php?module=shop&action=cart_add_multiple" method="post">}}


<table>
#goods_new#
</table>

{{if:show_multiple_add_form:<input type="submit" name="do_add" value="Положить в корзину"></form>}}

<p>Страницы: #pages# |<p>