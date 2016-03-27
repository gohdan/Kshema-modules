{{if:name:<h1>#name#</h1>}}

{{if:if_show_admin_link:<p><a href="/index.php?module=photos&action=edit&photo=#id#">Редактировать</a> <a href="/index.php?module=photos&action=del&photo=#id#">Удалить</a></p>}}

{{if:gallery:<p><a href="/index.php?module=photos&action=view_gallery&gallery=#gallery#">Обратно в галерею</a></p>}}

{{if:descr:<div>#descr#</div>}}


{{if:image:<img src="#image#">}}
