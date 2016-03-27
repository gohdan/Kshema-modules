<form action="/index.php?module=store&action=categories_sort&categories=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
Поместить перед категорией: <select name="position">
#categories_select#
</select>
<input type="submit" name="do_sort" value="Поместить">
</form>