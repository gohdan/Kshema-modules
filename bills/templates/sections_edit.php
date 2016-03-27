<h1>Назначение разделов</h1>

{{if:satellite_id:<p><a href="/bills/admin_satellite/#satellite_id#/">Обратно в администрирование объявлений на сателлите</a></p>}}

{{if:result:<p>#result#</p>}}

<form action="/bills/sections_edit/satellite_#satellite_id#/" method="post">

<p>
#categories_checkboxes#
</p>
<input type="submit" name="do_update" value="Сохранить">
</form>
