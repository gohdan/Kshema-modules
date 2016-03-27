<h1>Удаление таблиц БД магазина</h1>

<p><a href="/shop/admin/">В главное меню администрирования магазина</a></p>

<p>#content#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/shop/drop_tables/" method="post">
<input type="checkbox" name="drop_privileges_table" value="ksh_shop_privileges">Привилегии<br>
<input type="checkbox" name="drop_authors_table" value="ksh_shop_authors">Авторы<br>
<input type="checkbox" name="drop_categories_table" value="ksh_shop_categories">Категории товаров<br>
<input type="checkbox" name="drop_goods_table" value="ksh_shop_goods">Товары<br>
<input type="checkbox" name="drop_cart_table" value="ksh_shop_carts">Корзины<br>
<input type="checkbox" name="drop_requests_table" value="ksh_shop_requests">Заявки на отсутствующие товары<br>
<input type="checkbox" name="drop_orders_table" value="ksh_shop_orders">Заказы<br>
<input type="checkbox" name="drop_orders_statuses_table" value="ksh_shop_orders_statuses">Статусы заказов<br>
<input type="checkbox" name="drop_ordered_goods_table" value="ksh_shop_ordered_goods">Заказанные товары<br>
<input type="checkbox" name="drop_demands_table" value="ksh_shop_demands">Заявки<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
