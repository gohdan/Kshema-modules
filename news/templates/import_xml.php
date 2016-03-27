<h1>Импорт новостей из XML</h1>

<p>
<a href="/news/admin/">Возврат в меню администрирования новостей</a><br>
<a href="/news/help#news_import">Справка</a>
</p>

<form action="/news/import_xml/" method="post">
Вставьте XML-файл сюда:<br>
<!-- <textarea name="xml" style="width: 800px; height: 600px"></textarea><br> -->
<input type="submit" name="do_import" value="Импортировать">
</form>

<p>
<b>Название категории:</b> #category_name#<br>
<b>ID главной категории:</b> #main_category_id#<br>
<b>Индекс:</b>#venue_zip#<br>
<b>Начало продаж:</b> #date_sales_from#<br>
<b>Описание:</b> #desc_local#<br>
<b>Venue ID:</b> #venue_id#<br>
<b>Окончание продаж:</b> #date_sales_to#<br>
<b>Организатор:</b> #organiser_name#<br>
<b>Название:</b> #venue_name#<br>
<b>Описание на английском:</b> #desc_en#<br>
<b>Страна:</b> #venue_iso2_country_code#<br>
<b>URL:</b> #url#<br>
<b>Город:</b> #venue_town#<br>
<b>Название события:</b> #event_name#<br>
<b>Большое изображение:</b> <img src="#image_big#"><br>
<b>Дата окончания:</b> #date_to#<br>
<b>Дата начала:</b> #date_from#<br>
<b>Маленькое изображение:</b> #image_small#
<b>ID категории:</b> #category_id#<br>
<b>Улица:</b> #venue_street#<br>
<b>ID события:</b> #event_id#<br>
<b>Название главной категории:</b> #main_category_name#<br>
<b>Среднее изображение:</b> <img src="#image_med#">
</p>
