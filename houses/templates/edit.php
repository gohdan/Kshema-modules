<h1>Редактирование проекта дома</h1>

<a href="/houses/view_categories/">Категории проектов домов</a>

<p>#result#</p>

<p>#content#</p>

<form action="/houses/edit/" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="#id#">
    <input type="hidden" name="old_image" value="#image#">
	<input type="hidden" name="old_3d" value="#3d#">
	<input type="hidden" name="old_fasad" value="#fasad#">
	<input type="hidden" name="old_1floor_t" value="#1floor_t#">
	<input type="hidden" name="old_1floor" value="#1floor#">
	<input type="hidden" name="old_2floor_t" value="#2floor_t#">
	<input type="hidden" name="old_2floor" value="#2floor#">
	<input type="hidden" name="old_pdf" value="#pdf#">
    Название: <input type="text" name="name" value="#name#"><br>
    Категория:
    <select name="category">
    #categories_select#
    </select><br>
	Показывать пользователям: <input type="checkbox" name="if_show" value="yes"{{if:if_show: checked}}><br>
    Цена: <input type="text" name="price" size="12" value="#price#"><br>
	Общая площадь: <input type="text" name="sq_common" size="3" value="#sq_common#"><br>
    В том числе террасы и балконы: <input type="text" name="sq_balcones" size="3" value="#sq_balcones#"><br>
    Жилая площадь: <input type="text" name="sq_living" size="3" value="#sq_living#"><br>
	Состав:<br>
	<textarea name="composition">#composition#</textarea>
    Изображение-описание:<br><img src="#image#"><br>
    Новое изображение-описание: <input type="file" name="image"><br>
	3D-изображение:<br><img src="#3d#"><br>
    Новое 3D-изображение: <input type="file" name="3d"><br>
	Изображение фасада:<br><img src="#fasad#"><br>
    Новое изображение фасада: <input type="file" name="fasad"><br>
	План 1 этажа:<br><img src="#1floor_t#"><br>
    Новый план 1 этажа (маленький): <input type="file" name="1floor_t"><br>
	План 1 этажа (большой):<br><img src="#1floor#"><br>
    Новый план 1 этажа (большой): <input type="file" name="1floor"><br>
	План 2 этажа (маленький):<br><img src="#2floor_t#"><br>
    Новый план 2 этажа (маленький): <input type="file" name="2floor_t"><br>
	План 2 этажа (большой):<br><img src="#2floor#"><br>
    Новый план 2 этажа (большой): <input type="file" name="2floor"><br>
	<a href="#pdf#">PDF</a><br>
    Новый PDF: <input type="file" name="pdf"><br>
    <input type="submit" name="do_update" value="Записать">
</form>
