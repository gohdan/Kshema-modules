
{{if:show_left:
<table summary="Bookings calendar table" class="booking_calendar_table">
<tr>
<th colspan="2" class="booking_calendar_top">&nbsp;</th>}}
<th colspan="#month_days_cur#" class="booking_calendar_top">#month_title# #year#</th>

{{if:abc:<th class="booking_calendar_top">&nbsp;</th>
<th class="booking_calendar_top">&nbsp;</th>
<th class="booking_calendar_top">&nbsp;</th>
<th class="booking_calendar_top">&nbsp;</th>
<th class="booking_calendar_top">&nbsp;</th>}}

{{if:show_right:</tr>}}

{{if:show_left:<tr>
<th class="booking_calendar_top2">номер</th>
<th class="booking_calendar_top2">этаж</th>}}

{{if:abc:
#month_days_cur_th#}}

{{if:show_right:
<th class="booking_calendar_top3">завтрак</th>
<th class="booking_calendar_top3">трансфер</th>
<th class="booking_calendar_top3">итого</th>
<th class="booking_calendar_top3">предоплата</th>
<th class="booking_calendar_top3">остаток</th>}}

{{if:show_right:</tr>}}

{{if:abc:
#calendar_row_left#

#calendar_rows#

#calendar_row_right#
}}
{{if:show_right:</table>}}


