<h1>Редактирование товара</h1>

<p><a href="/shop/goods_view_all/">К просмотру товаров</a></p>

<p><a href="/shop/view_good/good:#id#" target="_view_#id#">Посмотреть этот товар</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<p>Незаполненные пункты в информации о товаре не показываются.</p>

<form action="/shop/goods_edit/goods:#id#/" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
<input type="hidden" name="image" value="#image#">
<input type="hidden" name="pdf" value="#pdf#">
<input type="hidden" name="if_new" value="0">
<input type="hidden" name="if_popular" value="0">
<input type="hidden" name="if_recommended" value="0">
<input type="hidden" name="if_hide" value="0">

<table>
{{if:image:<tr><td>Изображение:</td><td><img src="#image#" align="top"></td></tr>}}

<tr><td>Новое изображение:</td><td><input type="file" name="image"></td></tr>

{{if:images:<tr><td>Дополнительное изображение:</td><td><img src="#images#" align="top"><br><input type="checkbox" name="images_del" value="1"> удалить</td></tr>}}

<tr><td>Новое дополнительное изображение:</td><td><input type="file" name="images"></td></tr>

<tr><td>Системное название товара (для URL):</td><td><input type="text" name="name" size="30" value="#name#"></td></tr>
<tr><td>Название товара:</td><td><input type="text" name="title" size="30" value="#title#"></td></tr>
<tr><td>Автор:</td><td><select name="author">
#authors#
</select></td></tr>
<tr><td>Категория:</td><td><select name="category">
#categories_select#
</select></td></tr>
<tr><td>H1:</td><td><input type="text" name="h1" size="60" value="#h1#"></td></tr>
<tr><td>Meta keywords:</td><td><input type="text" name="meta_keywords" size="60" value="#meta_keywords#"></td></tr>
<tr><td>Meta description:</td><td><input type="text" name="meta_description" size="60" value="#meta_description#"></td></tr>
<tr><td>Метки (через запятую):</td><td><input type="text" name="tags" size="60" value="#tags#"></td></tr>
{{if:pdf:<tr><td>PDF:</td><td><a href="#pdf#">скачать</a> <input type="checkbox" name="pdf_del" value="1"> удалить</td></tr>}}
<tr><td>Закачать PDF:</td><td><input type="file" name="pdf"></td></tr>
{{if:epub:<tr><td>epub:</td><td><a href="#epub#">скачать</a> <input type="checkbox" name="epub_del" value="1"> удалить</td></tr>}}
<tr><td>Закачать epub:</td><td><input type="file" name="epub"></td></tr>
{{if:mp3:<tr><td>MP3:</td><td><a href="#mp3#">скачать</a> <input type="checkbox" name="mp3_del" value="1"> удалить</td></tr>}}
<tr><td>Закачать MP3:</td><td><input type="file" name="mp3"></td></tr>
<tr><td>Жанр:</td><td><input type="text" name="genre" size="30" value="#genre#"></td></tr>
<tr><td>Оригинальное название:</td><td><input type="text" name="original_name" size="30" value="#original_name#"></td></tr>
<tr><td>Формат:</td><td><input type="text" name="format" value="#format#"></td></tr>
<tr><td>Язык:</td><td><input type="text" name="language" value="#language#"></td></tr>
<tr><td>Издательство:</td><td><input type="text" name="publisher" value="#publisher#"></td></tr>

<tr><td>Год публикации:</td><td><input type="text" name="year" value="#year#"></td></tr>
<tr><td>Количество страниц:</td><td><input type="text" name="pages_qty" value="#pages_qty#"></td></tr>

<tr><td>Вес (только цифры!):</td><td><input type="text" name="weight" value="#weight#"></td></tr>
<tr><td>Количество (только цифры!):</td><td><input type="text" name="new_qty" value="#new_qty#"></td></tr>
<tr><td>Цена (только цифры!):</td><td><input type="text" name="new_price" value="#new_price#"></td></tr>

<tr><td>Новый</td><td><input type="checkbox" name="if_new" value="1"{{if:if_new: checked}}</td></tr>
<tr><td>Популярный</td><td><input type="checkbox" name="if_popular" value="1"{{if:if_popular: checked}}></td></tr>
<tr><td>Рекомендовать</td><td><input type="checkbox" name="if_recommended" value="1"{{if:if_recommended: checked}}></td></tr>
<tr><td>Скрыть</td><td><input type="checkbox" name="if_hide" value="1"{{if:if_hide: checked}}></td></tr>

<tr><td colspan="2">Ссылки:</td></tr>
#links_edit#
<tr><td>Добавить ссылку:</td>
<td><input type="text" name="new_link_title" size="30"> текст<br>
<input type="text" name="new_link_img" size="30"> изображение<br>
<input type="text" name="new_link_url" size="30"> ссылка</td></tr>

<tr><td colspan="2">Б/у экземпляры:</td>
<tr><td>Количество:</td><td><input type="text" name="used_qty" value="#used_qty#"></td></tr>
<tr><td>Цена:</td><td><input type="text" name="used_price" value = "#used_price#"></td></tr>
<tr><td colspan="2"><hr></td>

<tr><td colspan="2">embed:<br>
<textarea name="embed">#embed#</textarea></td></tr>
<tr><td colspan="2">Краткое описание:<br>
<textarea name="description_short">#description_short#</textarea></td></tr>
<tr><td colspan="2">Описание:<br>
<textarea name="description">#description#</textarea></td></tr>
</table>
<input type="submit" name="do_update" value="Записать">
</form>

