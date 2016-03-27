<h1>Удаление категории</h1>

<p><a href="/shop/categories_view_adm/">К просмотру категорий</a></p>

<hr>

<p>#result#</p>

<p>#content#</p>

<p>Вы действительно хотите удалить категорию <b>#title#</b>?</p>

{{if:if_subcats:<p>Внимание! Категория содержит подкатегории!</p>}}

<form action="/shop/categories_view_adm/" method="post">
<input type="hidden" name="id" value="#id#">
<input type="submit" name="do_not_del_category" value="Нет">
<input type="submit" name="do_del_category" value="Да">
</form>
