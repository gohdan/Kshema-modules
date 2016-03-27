<html>
<head>
<title>Результат для печати</title>
<style>
table
{
	border: 1px solid black;
	border-collapse: collapse;
}

th
{
	border: 1px solid black;
}

td
{
	border: 1px solid black;
}
</style>
</head>
<body>
<table>
<tr>
<th>Город</th>

<th>Месяц</th>

<th>Хронометраж</th>

{{if:result_if_show_result_prime:
<th>
Выходов<br>в прайм
</th>

<th>
Выходов<br>не в прайм
</th>

}}

{{if:result_if_show_result_time:#result_times_pure#}}

<th>Стоимость</th>

<th>Сезонный<br>коэффициент</th>

<th>Стоимость<br>с коэфф-том</th>

{{if:result_if_noresident:
<th>Коэфф-т<br>иногородности</th>

<th>Стоимость с поправкой<br>на иногородность</th>

}}
{{if:result_if_show_discount:<th>Скидка, %</th>

<th>Итоговая сумма</th>

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


</body>
