<h1>Редактирование цен</h1>

<p><a href="/booking/admin/">В основное меню администрирования</a></p>

<form action="/booking/prices_edit/" method="post">

<p>Если не указать дату начала и окончания, цена будет считаться стандартной</p>

<table class="prices">
<tr>
<th>Начало<br>(гггг-мм-дд)</th>
<th>Окончание<br>(гггг-мм-дд)</th>
<th>Тип</th>
<th>Цена</th>
</tr>
#prices#
<tr><td colspan="4">Добавить цену:</td></tr>
<tr>
<td><input type="text" name="new_date_from" size="10"></td>
<td><input type="text" name="new_date_to" size="10"></td>
<td><select name="new_type">
<option value="1">Standard</option>
<option value="2">Deluxe</option>
<option value="3">Apartments</option>
</select></td>
<td><input type="text" name="new_price" size="3"></td>
</tr>
</table>
<input type="submit" name="do_update" value="Записать">
</form>
