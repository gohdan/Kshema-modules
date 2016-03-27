<?php

// Base functions of the "calc_adv" module


include_once ("db.php");
include_once ("cities.php");
include_once ("calc.php");


function calc_adv_admin()
{
        $content['content'] = "";
        return $content;
}

function calc_adv_frontpage()
{
    debug ("*** calc_adv_frontpage ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'admin_link' => '',
        'result' => '',
		'cities_select' => '',
		'month_select' => ''
    );
	$cities = array();
	$i = 0;


    if (1 == $user['id'])
    {
        debug ("user has admin rights");
    }

	$sql_query = "SELECT * FROM ksh_calc_adv_cities";
	$result = exec_query($sql_query);
	while ($city = mysql_fetch_array($result))
	{
		stripslashes($city);
		$content['cities_select'][$city['id']] = $city;
	}
	mysql_free_result($result);

	for ($i = 1; $i <= 12; $i++)
	{
		$content['month_select'][$i]['month_id'] = $i;
		$content['month_select'][$i]['month_name'] = base_get_month_name($i);
	}

    debug ("*** end: calc_adv_frontpage");
    return $content;
}


function calc_adv_default_action()
{
	global $config;
        global $user;
        $content = "";
        $nav_string = "";

        $content .= $nav_string;

        debug("<br>=== mod: calc_adv ===");

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("calc_adv", "frontpage", calc_adv_frontpage());
                        break;

                        case "admin":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "admin", calc_adv_admin());
                        break;

                        case "install_tables":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "install_tables", calc_adv_install_tables());
                        break;

                        case "drop_tables":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "drop_tables", calc_adv_drop_tables());
                        break;

                        case "update_tables":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "update_tables", calc_adv_update_tables());
                        break;

                        case "export_tables":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "export_tables", calc_adv_export_tables());
                        break;

						case "view_cities":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "view_cities", calc_adv_view_cities());
                        break;

						case "city_add":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "city_add", calc_adv_city_add());
                        break;

						case "city_edit":
								$config['themes']['page_tpl'] = "calc_adv_admin";
                                $content .= gen_content("calc_adv", "city_edit", calc_adv_city_edit());
                        break;

                        case "city_info":
                                $content .= gen_content("calc_adv", "city_info", calc_adv_city_info());
                        break;

						case "calc":
                                $content .= gen_content("calc_adv", "calc", calc_adv_calc());
                        break;

                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("calc_adv", "frontpage", calc_adv_frontpage());
        }

        debug("=== end: mod: calc_adv ===<br>");
        return $content;

}

?>

