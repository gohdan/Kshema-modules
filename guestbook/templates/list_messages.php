<p style="text-align: right">#date# #time#</p>
{{if:name:<p>Имя: #name#</p>}}
{{if:contact:<p>Контактные данные: #contact#</p>}}
#text#
{{if:show_del_link:<p><a href="/guestbook/del/#id#/">Удалить</a></p>}}
{{if:show_approve_button:
<form action="/guestbook/moderate/" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_approve" value="Показать на сайте">
</form>
}}
{{if:show_del_button:
<form action="/guestbook/moderate/" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_del" value="Удалить">
</form>
}}
