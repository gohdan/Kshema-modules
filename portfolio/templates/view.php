<h1>#title#</h1>

{{if:show_admin_link:
<p>
<a href="/portfolio/admin/">Администрирование портфолио</a><br>
<a href="/portfolio/edit/#id#/">Редактировать</a><br>
<a href="/portfolio/del/#id#/">Удалить</a>
</p>
}}

<p><a href="/">Главная</a> / <a href="/portfolio/">Портфолио</a> / #title#</p>

{{if:image:<p><img src="#image#"></p>}}

{{if:date:<p>#date#</p>}}
<p>Год: #year#</p>
<p>Метки: #tags#</p>


#full_text#

#images#

<p><a href="/portfolio/">Вернуться к портфолио проектов</a></p>
