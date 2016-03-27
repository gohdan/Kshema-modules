<h1>Гостевая книга</h1>

{{if:show_admin_link:
<p>
<a href="/guestbook/admin/">В меню администрирования</a><br>
</p>}}

<a href="/guestbook/add/">Добавить сообщение</a><br>

{{if:show_link_on_main:<p><a href="/guestbook/">В начало гостевой книги</a></p>}}

{{if:guestbook:<h2>Сообщения</h2>}}
#messages#

{{if:pages:<p>#pages#</p>}}

