<h1>#h1#</h1>

<p>
<a href="/">Главная</a> / <a href="/shiny/">Продажа шин в Тамбове</a> / 


{{if:source: <a href="/shiny/#source_url#/x/x/x/x/x/x/x/">#source#</a> / }}
{{if:season: <a href="/shiny/x/#season_url#/x/x/x/x/x/x/">#season#</a> / }}
{{if:spikes: <a href="/shiny/x/x/#spikes_url#/x/x/x/x/x/">#spikes#</a> / }}
{{if:brand: <a href="/shiny/x/x/x/#brand_url#/x/x/x/x/">#brand#</a> / }}
{{if:model: <a href="/shiny/#source_url#/#season_url#/#spikes_url#/#brand_url#/#model_url#/#radius_url#/#width_url#/#profile_url#/#rn_url#/">#model#</a> / }}
{{if:show_all_breadcrumbs:{{if:radius: <a href="/shiny/x/x/x/x/x/x/#radius_url#/x/">R#radius#</a> / }}
{{if:width: <a href="/shiny/x/x/x/x/x/x/x/#width_url#/">Ширина #width#</a> / }}
{{if:profile: <a href="/shiny/x/x/x/x/x/#profile_url#/x/x/">Профиль #profile#</a> / }}}}
</p>


{{if:show_filters_cancel:
{{if:if_results:<div class="well smallmargin smallpadding"><a href="/shiny/">Сбросить фильтры</a><br>
{{if:brand:<span class="tires_filter_cancel">Бренд: #brand#</a> <a href="#url_no_brand#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
{{if:source:<span class="tires_filter_cancel">происхождение: #source# <a href="#url_no_source#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
{{if:season:<span class="tires_filter_cancel">сезон: #season# <a href="#url_no_season#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
{{if:spikes:<span class="tires_filter_cancel">шипованная: #spikes# <a href="#url_no_spikes#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
{{if:profile:<span class="tires_filter_cancel">профиль: #profile# <a href="#url_no_profile#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
{{if:radius:<span class="tires_filter_cancel">диаметр (R): #radius# <a href="#url_no_radius#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
{{if:width:<span class="tires_filter_cancel">ширина: #width# <a href="#url_no_width#"><img src="/shiny/themes/tires/images/cancel.png"></a></span><br>}}
</div>}}
}}

#content#

<div class="well smallmargin smallpadding">
<form action="/shiny/" method="post">

<b>Быстрый подбор</b><br>
<span class="tires_quickfilter_item">Сезон: <select name="season"><option value="#unk#">-</option>#season_select#</select></span>
<span class="tires_quickfilter_item">Диаметр (R): <select name="radius"><option value="#unk#">-</option>#radius_select#</select></span>
<span class="tires_quickfilter_item">Ширина: <select name="width"><option value="#unk#">-</option>#width_select#</select></span>
<span class="tires_quickfilter_item">Профиль: <select name="profile"><option value="#unk#">-</option>#profile_select#</select></span>
<span class="tires_quickfilter_submit"><input type="submit" class="btn" name="do_quick_select" value="Выбрать"></span>
</form>
</div>

{{if:brands:<div class="well smallmargin smallpadding"><b>Бренд:</b><br>{{if:no_brands: нет вариантов}}<table><tr>
<td class="tires_brand_col">#brands_1#</td>
<td class="tires_brand_col">#brands_2#</td>
<td class="tires_brand_col">#brands_3#</td>
<td class="tires_brand_col">#brands_4#</td>
<td class="tires_brand_col">#brands_5#</td>
<td class="tires_brand_col">#brands_6#</td>
{{if:brands_7:<td class="tires_brand_col">#brands_7#</td>}}
</tr></table></div>}}

<div class="row nomargin nopadding">
{{if:seasons:<div class="span5 well smallmargin smallpadding"><b>Сезон:</b> #seasons#</div>}}

{{if:spikess:<div class="span6 well smallmargin smallpadding"><b>Шипы:</b> #spikess#</div>}}
</div>

{{if:radiuss:<div class="well smallmargin smallpadding"><b>Диаметр (R):</b> #radiuss#</div>}}

{{if:widths:<div class="well smallmargin smallpadding"><b>Ширина:</b> #widths#</div>}}

{{if:profiles:<div class="well smallmargin smallpadding"><b>Профиль:</b> #profiles#</div>}}

{{if:sources:<div class="well smallmargin smallpadding"><b>Происхождение:</b> #sources#</div>}}

{{if:if_results:<div class="well smallmargin smallpadding"><b>Результат подбора:</b><br>
<table>
#results#
</table>
</div>}}

{{if:tire:#tire#}}
