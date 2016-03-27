<h1>Удаление таблиц базы данных подкаста</h1>

<a href="#inst_root#/podcast/admin/">Вернуться к администрированию подкаста</a>

<p>#content#</p>

<p>#result#</p>

<p>
Уничтожить таблицы:
</p>
<form action="#inst_root#/podcast/drop_tables/" method="post">
<input type="checkbox" name="drop_podcast_table" value="ksh_video">Видео<br>
<input type="checkbox" name="drop_podcast_privileges_table" value="ksh_video_privileges">Привилегии<br>
<input type="submit" name="do_drop" value="Уничтожить">
</form>
