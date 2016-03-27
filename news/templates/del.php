<h1>Удаление новости</h1>

<p>
<a href="/news/view_by_category/#category_id#/">Вернуться к просмотру новостей в категории</a><br>
<a href="/news/help#news_del">Справка</a>
</p>

<p>Вы действительно хотите удалить новость <b>#name#</b>?</p>

#content#

<form action="/news/view_by_category/#category_id#/" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>

