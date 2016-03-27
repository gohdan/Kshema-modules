<script language="javascript">

$v = '#1floor#';

function show_1()
{
	document.images['plan'].src='#1floor_t#';
    $v = '#1floor#';
}

function show_2()
{
	document.images['plan'].src='#2floor_t#';
    $v = '#2floor#';
}

function showimg()
{
	myWin= open($v, "displayWindow", "status=no,toolbar=no,menubar=no,resizable=yes")
}

</script>

<table class="project_main_layout">
<tr>
<td class="project_plans">

<table width="100%">
<tr>
	<td>
    	<table class="project_image">
        <tr>
        	<td class="project_corner_upleft"></td>
            <td class="project_rubber_up"></td>
            <td class="project_corner_upright"></td>
        </tr>
        <tr>
        	<td class="project_rubber_left"></td>
            <td class="project_image"><img src="#3d#"></td>
            <td class="project_rubber_right"></td>
        </tr>
        <tr>
        	<td class="project_corner_downleft"></td>
            <td class="project_rubber_down"></td>
            <td class="project_corner_downright"></td>
        </tr>
        </table>
        
        

    	<table class="project_image">
        <tr>
        	<td class="project_corner_upleft"></td>
            <td class="project_rubber_up"></td>
            <td class="project_corner_upright"></td>
        </tr>
        <tr>
        	<td class="project_rubber_left"></td>
            <td class="project_image"><IFRAME src="#fasad#" width="395" marginheight="0" marginwidth="0" scrolling="auto" frameborder="0"></IFRAME></td>
            <td class="project_rubber_right"></td>
        </tr>
        <tr>
        	<td class="project_corner_downleft"></td>
            <td class="project_rubber_down"></td>
            <td class="project_corner_downright"></td>
        </tr>
        </table>
    </td>
    <td>
    	Кликните, чтобы увеличить
	   	<table class="project_image">
        <tr>
        	<td class="project_corner_upleft"></td>
            <td class="project_rubber_up"></td>
            <td class="project_corner_upright"></td>
        </tr>
        <tr>
        	<td class="project_rubber_left"></td>
            <td class="project_image"><img src="#1floor_t#" name="plan" class="button" onclick="javascript: showimg()"></td>
            <td class="project_rubber_right"></td>
        </tr>
        <tr>
        	<td class="project_corner_downleft"></td>
            <td class="project_rubber_down"></td>
            <td class="project_corner_downright"></td>
        </tr>
        </table>
    </td>
</tr>
</table>

</td>
</tr>
<tr>
<td class="project_info">


<table class="project_info">
<tr>
	<td class="project_properties">
	
		<h1>#name#</h1>
	    <div class="project_properties">
        {{if:price:Стоимость комплекта: #price# руб.<br>}}
		{{if:sq_common:Общая площадь: #sq_common# м2<br>}}
		{{if:sq_balcones:В т. ч. террасы и балконы: #sq_balcones# м2<br>}}
		{{if:sq_living:Жилая площадь: #sq_living# м2}}
        </div>
		{{if:composition:<div class="project_composition"><b>Состав базового комплекта:</b><br> #composition#</div>}}
    </td>
    <td class="project_links">
    	<p>
        <img src="/themes/tamak/images/project_open_1st.gif" alt="Открыть план 1 этажа" width="159" height="31" class="button" onclick="javascript: show_1()">
        {{if:2floor:<img src="/themes/tamak/images/project_open_2nd.gif" alt="Открыть план 2 этажа" width="160" height="31" class="button" onclick="javascript: show_2()">}}
		{{if:pdf:<a href="#pdf#"><img src="/themes/tamak/images/project_open_pdf.gif" alt="Открыть проект в PDF" width="160" height="31"></a>}}
        </p>
<!--        <p><a href="/houses/view_by_category/#category_id#/">Все проекты из категории <br>"#category#"</a></p> -->
		

		<p>#edit_link#</p>
		<p>#del_link#</p>
        
        <div class="return">
		<a href="/"><img src="/themes/tamak/images/project_logo.gif"></a>
        </div>
        
        <div class="warning">
        Для просмотра планов JavaScript должен быть включен
        </div>

    </td>
</tr>
</table>

</td>
</tr>
</table>

