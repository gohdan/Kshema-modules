{{if:title:<h1>#title#</h1>}}

{{if:content:<p>#content#</p>}}

{{if:date:<p>#date#</p>}}

{{if:full_text:#full_text#}}

{{if:bbs:<p>Размещено на площадках: #bbs#}}


{{if:category:<p><a href="#module##action#/#category#/">В категорию</a></p>}}

{{if:show_user_bills_link:<a href="/bills/view_by_user/">Мои объявления</a>}}

{{if:show_admin_link:
<p>
<a href="/bills/admin/">Меню администрирования досок объявлений</a><br>
<a href="/bills/help#view">Справка</a>
</p>
}}

{{if:resemble_elements:
<h2>Похожие объявления</h2>
<table>
#resemble_elements#
</table>}}
