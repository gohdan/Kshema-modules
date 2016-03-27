<h1>Удаление сообщения</h1>

{{if:show_admin_link:
<p>
<a href="/guestbook/admin/">Вернуться в меню администрирования</a><br>
</p>
}}

<p><a href="/guestbook/view/">Обратно в гостевую книгу</a></p>

<p>Вы действительно хотите удалить сообщение <b>#id#</b>?</p>

#content#

<form action="/guestbook/view/" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
