<h1>Последние поступления в разделе "#category_name#"</h1>

<p>#result#</p>

<p>#content#</p>

{{if:show_admin_link:<p><a href="/index.php?module=shop&action=admin">Администрировать магазин</a></p>}}

{{if:show_add_link:<p><a href="/index.php?module=shop&action=goods_add&category=#category_id#">Добавить товар в категорию</a></p>}}

{{if:show_multiple_add_form:<form action="/index.php?module=shop&action=cart_add_multiple" method="post">}}


<table>
#goods_by_category#
</table>

{{if:show_multiple_add_form:<input type="submit" name="do_add" value="Положить в корзину"></form>}}
