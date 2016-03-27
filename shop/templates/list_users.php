<tr>
<td style="padding: 0px 5px 0px 5px">#id#</td>
<td style="padding: 0px 5px 0px 5px">#login#</td>
<td style="padding: 0px 5px 0px 5px">#name#</td>

<td style="padding: 0px 5px 0px 5px">{{if:orders_qty:<a href="/index.php?module=shop&action=orders_view_by_user&user=#id#">#orders_qty#</a>}}</td>

<td style="padding: 0px 5px 0px 5px">{{if:queries_qty:<a href="/index.php?module=shop&action=requests_view">#queries_qty#</a>}}</td>

<td style="padding: 0px 5px 0px 5px">{{if:cart_qty:<a href="/index.php?module=shop&action=cart_view&user=#id#">Смотреть</a>}}</td>
<td style="padding: 0px 5px 0px 5px"><a href="/index.php?module=shop&action=user_del&user=#id#">Удалить</a></td>


</tr>
