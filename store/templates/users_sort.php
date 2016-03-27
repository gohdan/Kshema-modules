<form action="/index.php?module=store&action=users_sort&users=#id#" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="#id#">
Поместить перед сотрудником: <select name="position">
#users_select#
</select>
<input type="submit" name="do_sort" value="Поместить">
</form>