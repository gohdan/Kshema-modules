<h1>Удаление доски объявлений</h1>

<p>
<a href="/bbcpanel/admin/">Вернуться в меню администрирования</a><br>
<a href="/bbcpanel/bbs_view_all/">Список всех досок</a><br>
</p>

<p>Вы действительно хотите удалить доску <b>#title#</b>?</p>

#content#

<form action="/bbcpanel/bbs_view_all/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_bb_del" value="Удалить">
</form>
