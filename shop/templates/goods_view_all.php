<h1>Все товары</h1>

{{if:show_admin_link:<p><a href="/index.php?module=shop&action=admin">Администрирование магазина</a></p>}}

<p>#result#</p>

<p>#content#</p>


{{if:show_add_link:<p><a href="/index.php?module=shop&action=goods_add">Добавить товар</a></p>}}

<table>
#all_goods#
</table>