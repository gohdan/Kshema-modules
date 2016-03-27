<h1>Удаление объявления</h1>

<p>
<a href="/bills/admin/">Вернуться в меню администрирования</a><br>
<a href="/bills/help#bb_del">Справка</a>
</p>

{{if:show_user_bills_link:<a href="/bills/view_by_user/">Мои объявления</a>}}

{{if:result:<p>#result#</p>}}

{{if:title:<p>Вы действительно хотите удалить объявление <b>#title#</b>?</p>}}

#content#

{{if:show_del_form:<form action="/bills/del/#id#/" method="post">}}
{{if:satellite:<input type="hidden" name="satellite" value="#satellite#">}}
{{if:show_del_form:<input type="hidden" name="category" value="#category#">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>}}
