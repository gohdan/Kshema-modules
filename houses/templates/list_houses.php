<tr>
<td style="padding-right: 10px; padding-bottom: 10px"><a href="/houses/view/houses:#id#/page_template:project" target="#id#">#image#</a></td>
<td class="search_project_properties">
		#name#<br>
        {{if:price:Стоимость <a href="#" onClick="javascript: popupWin = window.open('/houses/view_short/#id#/page_template:empty', 'composition', 'toolbar=no,scrollbars=no,resizeable=no,width=400,height=350')">комплекта</a>: #price# руб.<br>}}
        {{if:sq_common:Общая площадь: #sq_common# м2<br>}}
        {{if:sq_balcones:В т. ч. террасы и балконы: #sq_balcones# м2<br>}}
		{{if:sq_living:Жилая площадь: #sq_living# м2}}
</td>
</tr>
<tr>
<td colspan="3">
	<span class="more">#edit_link#</span>
</td>
</tr>
