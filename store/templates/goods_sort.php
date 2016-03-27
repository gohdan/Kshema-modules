<form action="/index.php?module=store&action=goods_sort&goods=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
Поместить перед товаром: <select name="position">
#goods_select#
</select>
<input type="submit" name="do_sort" value="Поместить">
</form>