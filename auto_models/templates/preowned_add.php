<h1>Добавление автомобиля с пробегом</h1>

{{if:result:<p>#result#</p>}}

{{if:show_admin_link:
<p><a href="/auto_models/admin/">К администрированию моделей автомобилей</a></p>
}}

<p><a href="/auto_models/preowned_view/">К списку</a></p>

<form action="#inst_root#/auto_models/preowned_add/" method="post" enctype="multipart/form-data">
<input type="hidden" name="category" value="1">


<table summary="preowned auto add table">
<tr><td>Изображение:<br><span style="font-size: 80%; font-style: italic">(можно будет добавить при редактировании)</span></td><td><input type="file" name="image"></td></tr>
<tr><td>Название:</td><td><input type="text" name="title" value="#title#"></td></tr>
<tr><td>Модель:</td><td><input type="text" name="model" value="#model#"></td></tr>
<tr><td>Цвет автомобиля:</td><td><input type="text" name="color" value="#color#"></td></tr>
<tr><td>Двигатель:</td><td><input type="text" name="engine" value="#engine#"></td></tr>
<tr><td>КПП:</td><td><input type="text" name="transmission" value="#transmission#"></td></tr>
<tr><td>Салон:</td><td><input type="text" name="chassis" value="#chassis#"></td></tr>
<tr><td>Год выпуска:</td><td><input type="text" name="year" value="#year#" size="4"></td></tr>
<tr><td>Производство:</td><td><input type="text" name="manufacturer" value="#manufacturer#"></td></tr>
<tr><td>Пробег:</td><td><input type="text" name="runout" value="#runout#"></td></tr>
<!--<tr><td>Тип привода:</td><td><input type="text" name="drive" value="#drive#"></td></tr>-->
<tr><td>Цена:</td><td><input type="text" name="price" value="#price#" size="10"></td></tr>
<tr><td>Цена нового авто:</td><td><input type="text" name="price_new" value="#price_new#"></td></tr>
<!--<tr><td>Максимальная комплектация:</td><td><textarea name="complectation">#complectation#</textarea></td></tr>-->
<tr><td>Дополнительная информация:</td><td><textarea name="info">#info#</textarea></td></tr>


<tr><td></td><td><input type="submit" name="do_add" value="Добавить"></td></tr>
</table>
</form>
