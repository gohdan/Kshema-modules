<h1>Удаление таблиц БД склада</h1>

<p><a href="/index.php?module=store&action=admin">В главное меню администрирования склада</a></p>

<p>#content#</p>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=store&action=drop_tables" method="post">
<input type="checkbox" name="drop_categories_table" value="ksh_store_categories">Категории<br>
<input type="checkbox" name="drop_goods_table" value="ksh_store_goods">Товары<br>
<input type="checkbox" name="drop_objects_table" value="ksh_store_objects">Объекты<br>
<input type="checkbox" name="drop_users_table" value="ksh_store_users">Сотрудники<br>
<input type="checkbox" name="drop_cart_table" value="ksh_store_cart">Корзина<br>
<input type="checkbox" name="drop_inout_table" value="ksh_store_inout">Движение товаров<br>

<input type="submit" name="do_drop" value="Уничтожить">
</form>