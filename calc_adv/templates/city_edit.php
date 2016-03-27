<h1>Редактирование города</h1>

<p>#content#</p>

<p>#result#</p>

<p>
<a href="/index.php?module=calc_adv&action=view_cities">Обратно к списку городов</a>
</p>

<form action="/index.php?module=calc_adv&action=city_edit&city=#id#>" method="post">
Название: <input type="text" name="title" value="#title#"><br>
Описание:<br>
<textarea name="descr" rows="30" cols="40">#descr#</textarea><br>
Стоимость секунды:
<table>
<tr>
<td><input type="radio" name="calc_type" value="0" #calc_prime_checked#> По прайму:</td>
<td><input type="radio" name="calc_type" value="1" #calc_time_checked#> По времени</td>
</tr>
<tr>
<td>
Прайм: <input type="text" name="price_prime" size="4" value="#price_prime#"> руб.<br>
Не прайм: <input type="text" name="price_noprime" size="4" value="#price_noprime#"> руб.
</td>
<td>
<input type="text" name="time_0" size="9" value="#times_0#"> = <input type="text" name="time_price_0" size="5" value="#time_prices_0#"> руб.<br>
<input type="text" name="time_1" size="9" value="#times_1#"> = <input type="text" name="time_price_1" size="5" value="#time_prices_1#"> руб.<br>
<input type="text" name="time_2" size="9" value="#times_2#"> = <input type="text" name="time_price_2" size="5" value="#time_prices_2#"> руб.<br>
<input type="text" name="time_3" size="9" value="#times_3#"> = <input type="text" name="time_price_3" size="5" value="#time_prices_3#"> руб.<br>
<input type="text" name="time_4" size="9" value="#times_4#"> = <input type="text" name="time_price_4" size="5" value="#time_prices_4#"> руб.<br>
<input type="text" name="time_5" size="9" value="#times_5#"> = <input type="text" name="time_price_5" size="5" value="#time_prices_5#"> руб.<br>
<input type="text" name="time_6" size="9" value="#times_6#"> = <input type="text" name="time_price_6" size="5" value="#time_prices_6#"> руб.<br>
<input type="text" name="time_7" size="9" value="#times_7#"> = <input type="text" name="time_price_7" size="5" value="#time_prices_7#"> руб.<br>
<input type="text" name="time_8" size="9" value="#times_8#"> = <input type="text" name="time_price_8" size="5" value="#time_prices_8#"> руб.<br>
<input type="text" name="time_9" size="9" value="#times_9#"> = <input type="text" name="time_price_9" size="5" value="#time_prices_9#"> руб.<br>
<input type="text" name="time_10" size="9" value="#times_10#"> = <input type="text" name="time_price_10" size="5" value="#time_prices_10#"> руб.<br>
<input type="text" name="time_11" size="9" value="#times_11#"> = <input type="text" name="time_price_11" size="5" value="#time_prices_11#"> руб.<br>

</td>
</tr>
</table>
Сезонный коэффициент:<br>
<table>
#season_coefs_form#
</table>

Коэффициент иногородности: <input type="text" size="3" name="noresident_coef" value="#noresident_coef#"><br>
Тип скидки:<br>
<input type="radio" name="discount_type" value="0" #discount_time_checked#> по времени (после некоторого количества секунд)<br>
<input type="radio" name="discount_type" value="1" #discount_price_checked#> по стоимости (после некоторого количества рублей)<br>
<!--
Порог, с которого начинается скидка: <input type="text" name="discount_from" value="<?=$city['discount_from']?>"><br>
Размер скидки, %: <input type="text" name="discount" value="<?=$city['discount']?>"><br>
-->
Порог, с которого начинается скидка: <input type="text" name="discount_0" size="9" value="#discount_0#"> = <input type="text" name="discount_price_0" size="5" value="#discount_price_0#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_1" size="9" value="#discount_1#"> = <input type="text" name="discount_price_1" size="5" value="#discount_price_1#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_2" size="9" value="#discount_2#"> = <input type="text" name="discount_price_2" size="5" value="#discount_price_2#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_3" size="9" value="#discount_3#"> = <input type="text" name="discount_price_3" size="5" value="#discount_price_3#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_4" size="9" value="#discount_4#"> = <input type="text" name="discount_price_4" size="5" value="#discount_price_4#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_5" size="9" value="#discount_5#"> = <input type="text" name="discount_price_5" size="5" value="#discount_price_5#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_6" size="9" value="#discount_6#"> = <input type="text" name="discount_price_6" size="5" value="#discount_price_6#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_7" size="9" value="#discount_7#"> = <input type="text" name="discount_price_7" size="5" value="#discount_price_7#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_8" size="9" value="#discount_8#"> = <input type="text" name="discount_price_8" size="5" value="#discount_price_8#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_9" size="9" value="#discount_9#"> = <input type="text" name="discount_price_9" size="5" value="#discount_price_9#"> %<br>
Порог, с которого начинается скидка: <input type="text" name="discount_10" size="9" value="#discount_10#"> = <input type="text" name="discount_price_10" size="5" value="#discount_price_10#"> %<br>

<input type="submit" class="button" name="do_save" value="Сохранить">
</form>
