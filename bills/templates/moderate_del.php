<h1>Удаление объявления</h1>

{{if:show_admin_link:
<p>
<a href="/bills/admin/">Вернуться в меню администрирования</a><br>
<a href="/bills/help#bb_del">Справка</a>
</p>
}}


{{if:result:<p>#result#</p>}}

{{if:show_del_form:
<p>Вы действительно хотите удалить объявление <b>#title#</b>?</p>


<form action="/bills/moderate_del/#id#/{{if:satellite:satellite_#satellite#/}}" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
}}
