<h1>Контрольная панель досок объявлений - справка</h1>

<p><a href="/index.php?module=bills&amp;action=admin">Обратно к администрированию</a></p>

<ul>
<li><a href="#common">Общие сведения</a></li>
<li><a href="#categories">Категории</a>
<ul>
<li><a href="#categories_common">Общие сведения</a></li>
<li><a href="#categories_add">Добавление</a></li>
<li><a href="#categories_edit">Редактирование</a></li>
<li><a href="#categories_del">Удаление</a></li>
</ul>
</li>
<li><a href="#bbs">Доски объявлений</a>
<ul>
<li><a href="#bbs_common">Общие сведения</a></li>
<li><a href="#bbs_add">Добавление</a></li>
<li><a href="#bbs_edit">Редактирование</a></li>
<li><a href="#bbs_del">Удаление</a></li>
<li><a href="#bbs_view_by_category">Просмотр по категориям</a></li>
</ul>
</li>
<li><a href="#db">База данных</a>
<ul>
<li><a href="#db_common">Общие сведения</a></li>
<li><a href="#db_tables_create">Создание таблиц</a></li>
<li><a href="#db_tables_drop">Удаление таблиц</a></li>
<li><a href="#db_tables_update">Обновление таблиц</a></li>
</ul>
</li>
</ul>

<h2><a name="common">Общие сведения</a></h2>

<p>Контрольная панель досок объявлений позволяет управлять досками объявлений.</p>

<h2><a name="categories">Категории</a></h2>

<h3><a name="categories_common">Общие сведения</a></h3>

<p><a href="/index.php?module=base&amp;action=help#categories_common">Основной раздел справки</a></p>

<h3><a name="categories_add">Добавление</a></h3>

<p><a href="/index.php?module=base&amp;action=help#categories_add">Основной раздел справки</a></p>

<h3><a name="categories_edit">Редактирование</a></h3>

<p><a href="/index.php?module=base&amp;action=help#categories_edit">Основной раздел справки</a></p>

<h3><a name="categories_del">Удаление</a></h3>

<p><a href="/index.php?module=base&amp;action=help#categories_del">Основной раздел справки</a></p>

<h2><a name="bbs">BBS</a></h2>

<h3><a name="bbs_common">Общие сведения</a></h3>

<h3><a name="bbs_add">Добавление</a></h3>

<ul>
<li>Системное название - название, по которому страницу идентифицирует система. 
Должно состоять из латинских букв и цифр, может содержать символ подчёркивания.</li>
<li>Название для показа пользователю может содержать любые символы. Оно может быть показано пользователю в списке страниц, меню и т. п.</li>
<li>Категория - каждая страница относится к какой-либо категории.</li>
</ul>

<h3><a name="bbs_edit">Редактирование</a></h3>

<ul>
<li>Системное название - название, по которому страницу идентифицирует система. 
Должно состоять из латинских букв и цифр, может содержать символ подчёркивания.</li>
<li>Название для показа пользователю может содержать любые символы. Оно может быть показано пользователю в списке страниц, меню и т. п.</li>
<li>Категория - каждая страница относится к какой-либо категории.</li>
</ul>

<h3><a name="bbs_del">Удаление</a></h3>

<h3><a name="bbs_view_by_category">Просмотр по категориям</a></h3>

<p><a href="/index.php?module=base&amp;action=help#dataobject_view_by_category">Основной раздел справки</a></p>

<h2><a name="db">База данных</a></h2>

<h3><a name="db_common">Общие сведения</a></h3>

<p>В базе данных страницы хранятся в следующих таблицах:</p>

<ul>
<li>ksh_bills_categories - категории страниц;</li>
<li>ksh_bills_bbs - доски объявлений.</li>
</ul>

<p>На диске элементы, принадлежащие к доскам объявлений, хранятся в каталоге /uploads/bills.</p>

<h3><a name="db_tables_create">Создание таблиц</a></h3>

<p>При исполнении этого экшена создаются таблицы в базе данных.</p>

<h3><a name="db_tables_drop">Удаление таблиц</a></h3>

<p>При исполнении этого экшена даётся возможность удалить таблицы из базы данных - можно выбрать, какие.</p>

<h3><a name="db_tables_update">Обновление таблиц</a></h3>

<p>Этот экшен занимается обновлением таблиц базы данных, сохранившихся с более старых версий модуля.</p>
