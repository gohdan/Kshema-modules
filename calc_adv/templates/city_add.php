<h1>Добавление нового города</h1>

<p>#content#</p>

<p>#result#</p>

<p>
<a href="/index.php?module=calc_adv&action=view_cities">Обратно к списку городов</a>
</p>

<form action="/index.php?module=calc_adv&action=city_add" method="post">
Название: <input type="text" name="title"><br>
Описание:<br>
<textarea name="descr" rows="30" cols="40"></textarea><br>
Стоимость секунды:
<table>
<tr>
<td><input type="radio" name="calc_type" value="0" checked> По прайму:</td>
<td><input type="radio" name="calc_type" value="1"> По времени</td>
</tr>
<tr>
<td>
Прайм: <input type="text" name="price_prime" size="4"> руб.<br>
Не прайм: <input type="text" name="price_noprime" size="4"> руб.
</td>
<td>
Время: <input type="text" name="time_0" size="9"> = <input type="text" name="time_price_0" size="5"> руб.<br>
Время: <input type="text" name="time_1" size="9"> = <input type="text" name="time_price_1" size="5"> руб.<br>
Время: <input type="text" name="time_2" size="9"> = <input type="text" name="time_price_2" size="5"> руб.<br>
Время: <input type="text" name="time_3" size="9"> = <input type="text" name="time_price_3" size="5"> руб.<br>
Время: <input type="text" name="time_4" size="9"> = <input type="text" name="time_price_4" size="5"> руб.<br>
Время: <input type="text" name="time_5" size="9"> = <input type="text" name="time_price_5" size="5"> руб.<br>
Время: <input type="text" name="time_6" size="9"> = <input type="text" name="time_price_6" size="5"> руб.<br>
Время: <input type="text" name="time_7" size="9"> = <input type="text" name="time_price_7" size="5"> руб.<br>
Время: <input type="text" name="time_8" size="9"> = <input type="text" name="time_price_8" size="5"> руб.<br>
Время: <input type="text" name="time_9" size="9"> = <input type="text" name="time_price_9" size="5"> руб.<br>
Время: <input type="text" name="time_10" size="9"> = <input type="text" name="time_price_10" size="5"> руб.<br>
Время: <input type="text" name="time_11" size="9"> = <input type="text" name="time_price_11" size="5"> руб.<br>

</td>
</tr>
</table>
Сезонный коэффициент:<br>
<table>
#month_coefs_form#
</table>
Коэффициент иногородности: <input type="text" size="3" name="noresident_coef"><br>
Тип скидки:<br>
<input type="radio" name="discount_type" value="0" checked> по времени (после некоторого количества секунд)<br>
<input type="radio" name="discount_type" value="1"> по стоимости (после некоторого количества рублей)<br>
<!--
Порог, с которого начинается скидка: <input type="text" name="discount_from"><br>
Размер скидки, %: <input type="text" name="discount"><br>
-->
Порог, с которого начинается скидка: <input type="text" name="discount_0" size="9"> = <input type="text" name="discount_price_0" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_1" size="9"> = <input type="text" name="discount_price_1" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_2" size="9"> = <input type="text" name="discount_price_2" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_3" size="9"> = <input type="text" name="discount_price_3" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_4" size="9"> = <input type="text" name="discount_price_4" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_5" size="9"> = <input type="text" name="discount_price_5" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_6" size="9"> = <input type="text" name="discount_price_6" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_7" size="9"> = <input type="text" name="discount_price_7" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_8" size="9"> = <input type="text" name="discount_price_8" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_9" size="9"> = <input type="text" name="discount_price_9" size="5"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_10" size="9"> = <input type="text" name="discount_price_10" size="5"> %<br>

<input type="submit" class="button" name="do_add" value="Добавить">
</form>
