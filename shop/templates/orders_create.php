<h1>Оформление заказа</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=shop&action=order_send" method="post">
<table class="tbl_cart">
<tr><th>ФИО:</th><th colspan1="2"><b>#sur_name#&nbsp;#first_name#&nbsp;#second_name#</b></th></tr>
<tr><th>Страна:</th><td>#country#</td></tr>
<tr><th>Индекс:</th><td>#post_code#</td></tr>
<tr><th>Область:</th><td>#area#</td></tr>
<tr><th>Населённый пункт:</th><td>#city#</td></tr>
<tr><th>Улица/дом/квартира:</th><td>#address#</td></tr>
<tr><td><a href="/index.php?module=users&action=profile_edit">Редактировать данные</a></td></tr>
{{if:show_form:<tr><td><input type="submit" class="button" value="Послать на этот адрес"></td></tr>}}
</table>
</form>