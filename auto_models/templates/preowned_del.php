<h1>Удаление автомобиля с пробегом</h1>

<p><a href="/auto_models/preowned_view/">К списку</a></p>

{{if:result:<p>#result#</p>}}

{{if:show_del_form:
<p>Вы действительно хотите удалить автомобиль с пробегом <b>#title#</b>?</p>


<form action="/auto_models/preowned_del/#id#/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
}}
