<h1>Удаление категории</h1>

<p>
<a href="/banners/view_categories/">Вернуться к просмотру категорий</a><br>
<a href="/banners/help#categories_del">Справка</a>
</p>

<p>Вы действительно хотите удалить категорию <b>#name#</b>? Все баннеры из этой категории также будут удалены!</p>

{{if:content:<p>#content#</p>}}

<form action="/banners/view_categories/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
