<h1>Поиск проектов домов</h1>

<p>#result#</p>

<p>#content#</p>

<form action="/houses/search/type:#type#/id:#id#/page_template:#page_template#" method="post">
	Общая площадь: от <input type="text" name="sq_from" size="3" value="#sq_from#"> до <input type="text" name="sq_to" size="3" value="#sq_to#">
    <input type="submit" name="do_search" value="Искать">
</form>

<table>
#houses#
</table>
