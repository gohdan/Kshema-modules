<h1>Удаление пользователя</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=shop&action=users_view" method="post">
<input type="hidden" name="id" value="#id#">
Вы действительно хотите удалить пользователя <b>#name# (#login#)</b>?<br>
<input type="submit" name="do_not_del" value="Нет">
<input type="submit" name="do_del" value="Да">
</form>
