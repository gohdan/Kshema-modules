<h1>Поиск на заказ - добавление</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/index.php?module=shop&action=demand_add" method="post">
Название товара: <input type="text" name="name"><br>
Автор: <input type="text" name="author"><br>
ISBN <i>(если известен)</i>: <input type="text" name="isbn"><br>
Комментарий:<br>
<textarea name="commentary" style="width: 400px; height: 200px;"></textarea><br>
<input type="submit" name="do_add" value="Добавить">
</form>