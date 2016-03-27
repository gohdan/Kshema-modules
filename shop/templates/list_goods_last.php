{{if:begin_row:<tr>
<td colspan="#lastitems_qty#" class="shop_last_goods_title">#category_title# - новые поступления</td>
</tr>
<tr>}}

<td valign="top">
<table class="shop_last_goods_row_table">
{{if:image:<tr><td><a href="/index.php?module=shop&action=view_good&good=#id#"><img class="shop_last_goods_image" src="#image#" alt="#name#" title="#name#"></a></td></tr>}}
{{if:name:<tr><td><span class="shop_last_goods_title"><a href="/index.php?module=shop&action=view_good&good=#id#">#name#</a></span></td></tr>}}
{{if:price:<tr><td>#price#</td></tr>}}
</table>
</td>

{{if:end_row:</tr>}}

{{if:show_last_goods_link:<tr><td colspan="#lastitems_qty#"><a href="/index.php?module=shop&action=view_last&categories=#category_id#">Посмотреть #last_goods_qty# новых поступлений в разделе #category_title#</a></td></tr>}}
