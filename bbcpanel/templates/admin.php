<h1>#heading#</h1>

{{if:show_profile_link:<a href="/users/profile_view/">Ваш профиль</a><br>}}

{{if:show_categories_view_link:
<p>
<a href="/bbcpanel/categories_view/">Тематика досок</a>
</p>
}}

{{if:show_privileges_edit_link:
<p>
<a href="/bbcpanel/privileges_edit/">Назначение прав</a>
</p>
}}

{{if:show_bbs_view_all_link:
<p>
<a href="/bbcpanel/bbs_view_all/">Доски</a>
</p>
}}

{{if:show_categories_view_link:
<p>
<a href="/bills/categories_view/">Разделы объявлений</a>
</p>
}}

{{if:show_view_by_user_link:
<p>
<a href="/bills/view_by_user/">Мои объявления</a>
</p>
}}


{{if:show_admin_link:
<hr>

<h2>Администрирование базы данных досок объявлений</h2>

<p>
<a href="/bbcpanel/create_tables/">Создать таблицы базы данных</a><br>
<a href="/bbcpanel/drop_tables/">Уничтожить таблицы базы данных</a><br>
<a href="/bbcpanel/update_tables/">Обновить таблицы базы данных</a>
</p>
}}
