<h1>Зарегистрированные пользователи</h1>

{{if:show_admin_link:<p><a href="/index.php?module=users&action=admin">Управление пользователями</a><br><a href="/index.php?module=shop&action=admin">Возврат к администрированию магазина</a></p>}}

<hr>

<p>#result#</p>

<p>#content#</p>

<table>
<tr>
<th style="padding: 0px 5px 0px 5px; vertical-align: top">ID</th>
<th style="padding: 0px 5px 0px 5px; vertical-align: top">login</th>
<th style="padding: 0px 5px 0px 5px; vertical-align: top">Отображаемое имя</th>
<th style="padding: 0px 5px 0px 5px; vertical-align: top">Заказы</th>
<th style="padding: 0px 5px 0px 5px; vertical-align: top">Заявки</th>
<th style="padding: 0px 5px 0px 5px; vertical-align: top">Корзина</th>
<th></th>
</tr>
#users#
</table>
