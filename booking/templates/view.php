{{if:show_edit_link:<p><a href="/booking/edit/#id#/">Редактировать</a></p>}}

#content#

<table summary="Booking edit table" class="booking_edit_table">
<tr><td>Номер заказа</td><td>#id#</td></tr>
<tr><td>Дата заказа</td><td>#date#</td></tr>
<tr><td>Комната</td><td>#room#</td></tr>
<tr><td>Заселение</td><td>#date_from# #time_from#</td></tr>
<tr><td>Выселение</td><td>#date_to# #time_to#</td></tr>
<tr><td>Итого дней</td><td>#days#</td></tr>
<tr><td>Имя</td><td>#name#</td></tr>
<tr><td>Фамилия</td><td>#surname#</td></tr>
<tr><td>Телефон</td><td>#phone#</td></tr>
<tr><td>E-mail</td><td>#email#</td></tr>
<tr><td>Страна</td><td>#country#</td></tr>
<tr><td>Вариант</td><td>#variant_type#</td></tr>
<tr><td>Количество номеров</td><td>#rooms_qty#</td></tr>
<tr><td>Количество взрослых</td><td>#adults_qty#</td></tr>
<tr><td>Комментарий</td><td>#comment#</td></tr>
<tr><td>Дети до 12-ти лет</td><td>{{if:if_children:да}}</td></tr>
<tr><td>Дополнительная кровать</td><td>{{if:if_extra_bed:да}}</td></tr>
<tr><td>Трансфер из аэропорта</td><td>{{if:if_transfer:да}}</td></tr>
<tr><td>Количество завтраков</td><td>#breakfast_qty#</td></tr>
<tr><td>Паспортные данные</td><td>#passport#</td></tr>
<tr><td>Вид оплаты</td><td>#payment_type#</td></tr>
<tr><td>Предоплата</td><td>#prepayment#</td></tr>
<tr><td>Остаток</td><td>#leftover#</td></tr>
<tr><td>Статус оплаты</td><td>#payment_status#</td></tr>
<tr><td>Менеджер</td><td>#manager#</td></tr>
<tr><td>Посредник</td><td>#dealer#</td></tr>

</table>
