<h1>Новости - справка</h1>

<p><a href="/news/admin/">Обратно к администрированию</a></p>

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
<li><a href="#news">Новости</a>
<ul>
<li><a href="#news_common">Общие сведения</a></li>
<li><a href="#news_add">Добавление</a></li>
<li><a href="#news_edit">Редактирование</a></li>
<li><a href="#news_del">Удаление</a></li>
<li><a href="#news_import">Импорт из RSS</a></li>
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

<p>Модуль новостей позволяет публиковать новости.</p>

<h2><a name="categories">Категории</a></h2>

<h3><a name="categories_common">Общие сведения</a></h3>

<p>Категории позволяют объединять новости в группы, которым потом можно назначать некоторые общие свойства:</p>

<ul>
<li>Шаблон всей страницы сайта при просмотре категории</li>
<li>Шаблон области просмотра категории</li>
<li>Шаблон списка элементов</li>
<li>Шаблон области просмотра элемента</li>
<li>Шаблон меню</li>
</ul>

<p>Сами категории можно добавлять, редактировать и удалять. При удалении все элементы, принадлежащие категории, тоже удаляются.</p>

<h3><a name="categories_add">Добавление</a></h3>

<ul>
<li>Системное название - должно состоять из латинских букв и цифр, может содержать знаки подчёркивания. Используется для идентификации 
категории в системе, составления URL.</li>
<li>Название для вывода пользователю может содержать любые символы. Используется для показа посетителям сайта, составления меню.</li>
<li>Шаблон всей страницы сайта при просмотре категории - по умолчанию - default. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон всей страницы.</li>
<li>Шаблон области просмотра категории - по умолчанию - view_by_category. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон области просмотра категории.</li>
<li>Шаблон списка элементов - по умолчанию - news. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон списка новостей.</li>
<li>Шаблон области просмотра элемента - по умолчанию - view. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон области просмотра элемента.</li>
<li>Шаблон меню - по умолчанию - news. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон меню.</li>
</ul>


<h3><a name="categories_edit">Редактирование</a></h3>

<ul>
<li>Системное название - должно состоять из латинских букв и цифр, может содержать знаки подчёркивания. Используется для идентификации 
категории в системе, составления URL.</li>
<li>Название для вывода пользователю может содержать любые символы. Используется для показа посетителям сайта, составления меню.</li>
<li>Шаблон всей страницы сайта при просмотре категории - по умолчанию - default. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон всей страницы.</li>
<li>Шаблон области просмотра категории - по умолчанию - view_by_category. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон области просмотра категории.</li>
<li>Шаблон списка элементов - по умолчанию - news. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон списка новостей.</li>
<li>Шаблон области просмотра элемента - по умолчанию - view. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон области просмотра элемента.</li>
<li>Шаблон меню - по умолчанию - news. Должен состоять только из латинских букв, цифр, 
знаков подчёркивания. По содержимому этого поля отыскивается PHP-файл, содержащий шаблон меню.</li>
</ul>

<h3><a name="categories_del">Удаление</a></h3>

<p>При удалении категории удаляются также и все принадлежащие к ней элементы.</p>

<h2><a name="news">Новости</a></h2>

<p>К новостям можно прикреплять изображения.</p>

<h3><a name="news_common">Общие сведения</a></h3>

<p>Новости - небольшие публикации. Они имеют некоторые общие характеристики - дату и изображение-описание, 
которое может быть показано в списке новостей.</p>

<h3><a name="news_add">Добавление</a></h3>

<ul>
<li>Название может содержать любые символы. Оно будет показано пользователю в списке новостей и т. п.</li>
<li>Дата - должна быть в формате ГГГГ-ММ-ДД.</li>
<li>Категория - каждая новость относится к какой-либо категории.</li>
<li>Текст - может содержать любые символы, HTML-код, может заполняться с помощью визуального редактора.</li>
</ul>

<h3><a name="news_edit">Редактирование</a></h3>

<ul>
<li>Название может содержать любые символы. Оно будет показано пользователю в списке новостей и т. п.</li>
<li>Дата - должна быть в формате ГГГГ-ММ-ДД.</li>
<li>Категория - каждая новость относится к какой-либо категории.</li>
<li>Текст - может содержать любые символы, HTML-код, может заполняться с помощью визуального редактора.</li>
</ul>

<h3><a name="news_del">Удаление</a></h3>

<h3><a name="news_import">Импорт из RSS</a></h3>

<p>Функция "Импорт из RSS" предназначена импортирования готовых новостей из уже имеющихся XML-файлов или RSS-лент.
Пока находится в стадии разработки.</p>

<h2><a name="db">База данных</a></h2>

<h3><a name="db_common">Общие сведения</a></h3>

<p>В базе данных новости хранятся в следующих таблицах:</p>

<ul>
<li>ksh_news_categories - категории новостей;</li>
<li>ksh_news - сами новости.</li>
</ul>

<p>На диске элементы, принадлежащие к новостям, хранятся в каталоге /uploads/news.</p>

<h3><a name="db_tables_create">Создание таблиц</a></h3>

<p>При исполнении этого экшена создаются таблицы в базе данных.</p>

<h3><a name="db_tables_drop">Удаление таблиц</a></h3>

<p>При исполнении этого экшена даётся возможность удалить таблицы из базы данных - можно выбрать, какие.</p>

<h3><a name="db_tables_update">Обновление таблиц</a></h3>

<p>Этот экшен занимается обновлением таблиц базы данных, сохранившихся с более старых версий модуля.</p>
