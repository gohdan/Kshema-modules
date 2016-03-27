<form action="/index.php?module=store&action=objects_sort&objects=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
Поместить перед объектом: <select name="position">
#objects_select#
</select>
<input type="submit" name="do_sort" value="Поместить">
</form>