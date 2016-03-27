<h1>Портфолио</h1>

{{if:show_admin_link:<p>
<a href="/portfolio/admin/">Администрирование</a><br>
<a href="/portfolio/add/">Добавить в портфолио</a>
</p>}}

{{if:result:<p>#result#</p>}}

#content#

<p>Годы: <a href="/portfolio/{{if:tag:tag:#tag#}}">все</a>, #years#</p>
<p class="portfolio_tags">Направления: <a href="/portfolio/{{if:year:year:#year#}}">все</a>, #tags#</p>

<table class="portfolio">
#portfolio#
</table>

<div class="portfolio_pages">#pages#</div>
