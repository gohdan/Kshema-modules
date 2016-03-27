<h1>Удаление категории</h1>

<p>
<a href="/news/view_categories/">Вернуться к просмотру категорий</a><br>
<a href="/news/help#categories_del">Справка</a>
</p>

<p>Вы действительно хотите удалить категорию <b>#name#</b>? Все новости в этой категории также будут удалены!</p>

{{if:content:<p>#content#</p>}}

<form action="/news/view_categories/" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>
