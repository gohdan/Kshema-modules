<h1>Добавление проекта дома</h1>

<a href="/houses/admin/">К администрированию проектов домов</a>

<p>#result#</p>

<p>#content#</p>


<form action="/houses/add_houses/" method="post" enctype="multipart/form-data">
	<input type="hidden" name="if_show" value="">
    Название: <input type="text" name="name"><br>
    Категория:
    <select name="category">
    #categories_select#
    </select><br>
	Показывать пользователям: <input type="checkbox" name="if_show" value="yes" checked><br>
    Цена: <input type="text" name="price" size="12"><br>
    Общая площадь: <input type="text" name="sq_common" size="3"><br>
    В том числе террасы и балконы: <input type="text" name="sq_balcones" size="3"><br>
    Жилая площадь: <input type="text" name="sq_living" size="3"><br>
	Состав:<br>
	<textarea name="composition"></textarea>
    Изображение-описание: <input type="file" name="image"><br>
    3D-изображение: <input type="file" name="3d"><br>
    Изображение фасада: <input type="file" name="fasad"><br>
    План 1 этажа (маленький): <input type="file" name="1floor_t"><br>
    План 1 этажа (большой): <input type="file" name="1floor"><br>
    План 2 этажа (маленький): <input type="file" name="2floor_t"><br>
    План 2 этажа (большой): <input type="file" name="2floor"><br>
    PDF: <input type="file" name="pdf"><br>
    <br>
    <input type="submit" name="do_add" value="Добавить">
</form>
