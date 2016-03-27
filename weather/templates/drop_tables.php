<h1>Уничтожение таблиц базы данных погодного информера</h1>

{{if:content:<p>#content#<p>}}

{{if:result:<p>#result#</p>}}

<p>
<a href="/weather/admin/">Вернуться к меню администрирования</a><br>
</p>


<p>
Уничтожить таблицы:
</p>
<form action="/weather/drop_tables/" method="post">
<input type="checkbox" name="drop_categories_table" value="ksh_weather_categories">Категории информеров<br>
<input type="checkbox" name="drop_weather_table" value="ksh_weather">Информеры<br>
<input type="checkbox" name="drop_privileges_table" value="ksh_weather_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
