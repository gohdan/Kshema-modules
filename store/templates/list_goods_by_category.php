<tr>

<td style="vertical-align: top; padding: 10px">
<b>#name#</b>
</td>


<td style="vertical-align: top; padding: 10px">#qty#</td>

<td style="vertical-align: top; padding: 10px">#status#</td>

<td style="vertical-align: top; padding: 10px">#measure#</td>

<td style="vertical-align: top; padding: 10px">#price#</td>

<td style="vertical-align: top; padding: 10px">#commentary#</td>


{{if:show_out_link:<td style="padding: 0px 3px 0px 3px"><a href="javascript:win_open('goods_out&goods=#id#', '700', '800')">Выдать</a></td>}}

{{if:show_in_link:<td style="padding: 0px 3px 0px 3px"><a href="javascript:win_open('goods_in&goods=#id#', '700', '800')">Получить</a></td>}}

<td><a href="javascript:win_open('view_by_categories&categories=#category#&good_move=up&good=#id#', '1', '1')">Выше</a></td>
<td><a href="javascript:win_open('view_by_categories&categories=#category#&good_move=down&good=#id#', '1', '1')">Ниже</a></td>

{{if:show_edit_link:<td style="padding: 0px 3px 0px 3px"><a href="javascript:win_open('goods_edit&goods=#id#', '600', '800')">Редактировать</a></td>}}
{{if:show_del_link:<td style="padding: 0px 3px 0px 3px"><a href="javascript:win_open('goods_del&goods=#id#', '400', '300')">Удалить</a></td>}}



</tr>
