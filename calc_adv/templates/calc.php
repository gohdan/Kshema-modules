
{{if:if_show_cities_select:
<form action="/index.php?module=calc_adv&action=calc" method="post">
<select name="city">
#cities_select#
</select><br>
<select name="month">
#month_select#
</select>
<input type="submit" class="button" name="do_select_city" value="Выбрать">
}}

{{if:if_show_calculator:
<p>Город: #city_title# <a href="/index.php?module=calc_adv&action=city_info&city=#city_id#" target="_new">Информация о городе</a>
</p>

<form name="calculator" id="calculator" action="/index.php?module=calc_adv&action=calc" method="post">
<input type="hidden" name="city" value="#city_id#">
<input type="hidden" name="month" value="#month#">
<input type="hidden" name="do_calc" value="do_calc">
<table class="calculator">
<tr>
	<td class="bg">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<th width="57" height="225" rowspan="2" align="left" valign="top" scope="col"><img src="/themes/calc_adv/images/left.jpg" width="57" height="225" /></th>
			<th width="88%" height="114" scope="col"><table class="calculator_form" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<th rowspan="2" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th height="42" scope="col">
Хронометраж
</th>
<th rowspan="2" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
{{if:if_show_prime:
<th scope="col">
Прайм %
</th>
<th rowspan="2" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th scope="col">
Не прайм %
</th>
<th rowspan="2" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th scope="col">
Кол-во<br>выходов
</th>
<th rowspan="2" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
}}

{{if:if_show_time:#times#}}

<th scope="col">
Иногородний?
</th>
<th rowspan="2" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
</tr>
<tr>
<td height="10" align="center" valign="top" scope="col">
<input type="text" name="hron" size="3">
</td>

{{if:if_show_prime:
<td align="center" valign="top" scope="col">
<input type="text" size="2" name="prime">
</td>
<td align="center" valign="top" scope="col">
<input type="text" size="2" name="noprime">
</td>
<td align="center" valign="top" scope="col">
<input type="text" name="proc_qty" size="2"><br>
</td>
}}

{{if:if_show_time:#time_qtys#}}

<td align="center" valign="top" scope="col">
<input type="checkbox" name="noresident" #noresident_checked#>
</td>

</tr>
</table></th>
			<th width="5%" rowspan="2" align="right" scope="col"><img src="/themes/calc_adv/images/right.jpg" width="45" height="225" /></th>
		</tr>
		<tr>
			<th style="vertical-align: bottom" height="60" align="center" valign="bottom" scope="col"><a href="javascript:document.calculator.submit()"><img src="/themes/calc_adv/images/but_r.jpg" align="bottom" alt="Рассчитать стоимость" width="337" height="60" border="0" /></a></th>
		</tr>
		</table></td>
</tr>
</table>


</form>

}}

{{if:if_show_result_string:
<p class="result">
#result# <a href="/temp/#filename_html#" target="_new">Результат для печати</a> <a href="/temp/#filename_csv#">Результат в Excel</a>
</p>
}}

{{if:if_show_result_table:			
<table class="calculator">
<tr>
	<td class="bg">

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<th width="57" height="225" rowspan="2" align="left" valign="top" scope="col"><img src="/themes/calc_adv/images/left.jpg" width="57" height="225" />
			</th>
			<th width="88%" height="114" scope="col">
<table class="calculator_form">
<tr>
<th>Город</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>Месяц</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>Хронометраж</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
{{if:if_show_result_prime:
<th>
Выходов<br>в прайм
</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>
Выходов<br>не в прайм
</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
}}

{{if:result_if_show_result_time:#result_times#}}

<th>Стоимость</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>Сезонный<br>коэффициент</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>Стоимость<br>с коэфф-том</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
{{if:result_if_noresident:
<th>Коэфф-т<br>иногородности</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>Стоимость с поправкой<br>на иногородность</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
}}
{{if:result_if_show_discount:<th>Скидка, %</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
<th>Итоговая сумма</th>
<th rowspan="2" height="42" class="bb" scope="col"><img src="/themes/calc_adv/images/bb.png" width="2" height="131" /></th>
}}
</tr>
<tr>
<td>#result_city_title#</td>
<td>#result_month_name#</td>
<td>#result_hron#</td>
{{if:result_if_show_result_prime:
<td>
#result_prime#
</td>
<td>
#result_noprime#
</td>
}}

{{if:result_if_show_result_time:#result_time_qtys#}}

<td>#result_sum#</td>
<td>#result_season_coef#</td>
<td>#result_sum_season#</td>
{{if:result_if_noresident:
<td>#result_noresident_coef#</td>
<td>#result_sum_noresident#</td>
}}
{{if:result_if_show_discount:<td>#result_discount#</td>
<td>#result_sum_final#</td>
}}
</tr>
</table>


</th>
			<th width="5%" rowspan="2" align="right" scope="col"><img src="/themes/calc_adv/images/right.jpg" width="45" height="225" /></th>
		</tr>
		</table>
	</td>
</tr>
</table>

}}

