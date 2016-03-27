<h1>Удаление баннера</h1>

<p>
<a href="/banners/view_by_category/category:#category_id#">Вернуться к просмотру баннеров в категории</a><br>
<a href="/banners/help#banners_del">Справка</a>
</p>

<p>Вы действительно хотите удалить баннер <b>#name#</b>?</p>

#content#

<form action="/banners/view_by_category/category:#category_id#" method="post">

<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del" value="Не удалять">
<input type="submit" name="do_del" value="Удалить">
</form>

