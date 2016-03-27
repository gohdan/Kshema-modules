<h1>Уничтожение таблиц базы данных моделей автомобилей</h1>

<p>#content#<p>

<p>#result#</p>

<a href="/index.php?module=auto_models&action=admin">Вернуться к меню администрирования</a>

<p>
Уничтожить таблицы:
</p>
<form action="/index.php?module=auto_models&action=drop_tables" method="post">
<input type="checkbox" name="drop_auto_models_table" value="ksh_auto_models">Модели<br>
<input type="checkbox" name="drop_auto_models_equipment_table" value="ksh_auto_models_equipment">Дополнительное оборудование<br>
<input type="checkbox" name="drop_auto_models_characteristics_table" value="ksh_auto_models_characteristics">Технические характеристики<br>
<input type="checkbox" name="drop_auto_models_prices_table" value="ksh_auto_models_prices">Комплектация и цены<br>
<input type="checkbox" name="drop_auto_models_colors_table" value="ksh_auto_models_colors">Цвета кузова<br>
<input type="checkbox" name="drop_auto_models_images_table" value="ksh_auto_models_images">Фотографии<br>
<input type="checkbox" name="drop_auto_models_videos_table" value="ksh_auto_models_videos">Видеоролики<br>
<input type="checkbox" name="drop_auto_models_present_table" value="ksh_auto_models_present">Автомобили в наличии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
