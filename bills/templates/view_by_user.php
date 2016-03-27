{{if:title:<h1>#title#</h1>}}

<p>
<a href="/users/profile_view/">Ваш профиль</a>
</p>

{{if:show_admin_link:<p><a href="/#module_name#/admin/">Меню администрирования</a></p>}}

{{if:show_add_link:<p><a href="/#module_name#/add/">Добавить объявление</a></p>}}



{{if:result:#result#}}

<form action="/#module_name#/view_by_user{{if:page:/page#page#.html}}" method="post">
<table class="elements_sending">
<tr>
<td style="vertical-align: top">
<table summary="elements">
#elements#
</table>


{{if:pages:<p>#pages#</p>}}
</td>
<td class="elements_sending_bbs_selection">
{{if:categories_select:Тематика:<br><select name="categories[]" multiple size="5"><option value="0"}}
{{if:all_categories_selected: selected}}
{{if:categories_select:>Все</option>#categories_select#</select>}}

<br>

{{if:sections_select:Разделы:<br><select name="sections[]" multiple size="5"><option value="0"}}
{{if:all_sections_selected: selected}}
{{if:sections_select:>Все</option>#sections_select#</select>

<br>

<input type="submit" name="do_search" value="Подобрать">
}}
<br>
<br>

#satellites_2send#

<br> 
{{if:show_send_form:
<input type="radio" name="send_type" value="1" checked>Все объявления на всех досках<br>
<input type="radio" name="send_type" value="2">Все объявления на всех разделах всех досок<br>
<input type="radio" name="send_type" value="3">Ротация по разделам<br>
<input type="radio" name="send_type" value="4">Ротация по доскам<br>
<input type="submit" name="do_send" value="Разместить">
}}
</form>


</td>
</tr>
</table>


