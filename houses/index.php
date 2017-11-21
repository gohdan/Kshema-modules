<?php

// Base functions of the "houses" module


include_once ("db.php");
include_once ("categories.php");
include_once ("houses.php");

function houses_admin()
{
        $content['content'] = "";
        return $content;
}

function houses_frontpage()
{
    debug ("*** houses_frontpage ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'houses' => '',
        'admin_link' => '',
        'result' => ''
    );
	$i = 0;

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have houses to delete");
            exec_query("DELETE FROM ksh_houses WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Проект успешно удален";
        }
        else
        {
            debug ("don't have houses to delete");
        }

        $content['admin_link'] .= "<p><a href=\"/houses/admin/\">Администрировать проекты домов</a></p>";
    }

    $result = exec_query("SELECT * FROM ksh_houses ORDER BY id DESC LIMIT ".mysql_real_escape_string($config['houses']['last_houses_qty'])."");

    while ($row = mysql_fetch_array($result))
    {
        debug("show houses ".$row['id']);
		if ("" != $row['descr_image'])
			$content['houses'][$i]['descr_image'] = "<img src=\"".$row['descr_image']."\">";

        $content['houses'][$i]['date'] = $row['date'];
		$content['houses'][$i]['full_text'] = $row['full_text'];

        if (1 == $user['id'])
            $content['houses'][$i]['edit_link'] = "<a href=\"/houses/edit/".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/houses/del/".$row['id']."\">Удалить</a>";
        else $content['houses'][$i]['edit_link'] = "";
		$i++;
    }
    mysql_free_result($result);

    debug ("*** end: houses_frontpage");
    return $content;
}


function houses_default_action()
{
	global $config;
        global $user;
        $content = "";
        $nav_string = "";

		$mod_info = parse_ini_file($config['modules']['location']."houses/description.ini");
		if (isset($config['houses']))
			$config['houses'] = array_merge($config['houses'], $mod_info);
		else
			$config['houses'] = $mod_info;


		$content .= $nav_string;

        debug("<br>=== mod: houses ===");

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("houses", "frontpage", houses_frontpage());
                        break;

                        case "admin":
                                $content .= gen_content("houses", "admin", houses_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("houses", "install_tables", houses_install_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("houses", "drop_tables", houses_drop_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("houses", "update_tables", houses_update_tables());
                        break;

                        case "view_categories":
							if (isset($_GET['element']) & !isset($_GET['categories']))
								$_GET['categories'] = $_GET['element'];
                            $content .= gen_content("houses", "categories_view", houses_categories_view());
                        break;

                        case "add_category":
							if (isset($_GET['element']) & !isset($_GET['categories']))
								$_GET['categories'] = $_GET['element'];
                            $content .= gen_content("houses", "categories_add", houses_categories_add());
                        break;

                        case "del_category":
							if (isset($_GET['element']) & !isset($_GET['categories']))
								$_GET['categories'] = $_GET['element'];

                                $content .= gen_content("houses", "categories_del", houses_categories_del());
                        break;

                        case "add_houses":
							if (isset($_GET['element']) & !isset($_GET['category']))
								$_GET['category'] = $_GET['element'];

							if (isset($_GET['element']) & !isset($_GET['houses']))
								$_GET['houses'] = $_GET['element'];

                                $content .= gen_content("houses", "add", houses_add());
                        break;

                        case "view_by_category":
							if (isset($_GET['element']) & !isset($_GET['category']))
								$_GET['category'] = $_GET['element'];

                                $content .= gen_content("houses", "view_by_category", houses_view_by_category());
                        break;

                        case "edit":
							if (isset($_GET['element']) & !isset($_GET['houses']))
								$_GET['houses'] = $_GET['element'];

                                $content .= gen_content("houses", "edit", houses_edit());
                        break;

                        case "del":
							if (isset($_GET['element']) & !isset($_GET['houses']))
								$_GET['houses'] = $_GET['element'];

                                $content .= gen_content("houses", "del", houses_del());
                        break;

                        case "view":
							if (isset($_GET['element']) & !isset($_GET['houses']))
								$_GET['houses'] = $_GET['element'];

                                $content .= gen_content("houses", "view", houses_view());
                        break;
						
						case "view_short":
							if (isset($_GET['element']) & !isset($_GET['houses']))
								$_GET['houses'] = $_GET['element'];

                                $content .= gen_content("houses", "view_short", houses_view_short());
                        break;

                        case "houses_archive":
                                $content .= gen_content("houses", "archive", houses_archive());
                        break;

                        case "category_edit":
							if (isset($_GET['element']) & !isset($_GET['categories']))
								$_GET['categories'] = $_GET['element'];

                                $content .= gen_content("houses", "categories_edit", houses_categories_edit());
                        break;
                        
                        case "search":
                        		$content .= gen_content("houses", "search", houses_search());
                        break;
                        
                        case "read_csv":
                        	$content .= gen_content("houses", "read_csv", houses_read_csv());
                        break;
                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("houses", "frontpage", houses_frontpage());
        }

        debug("=== end: mod: houses ===<br>");
        return $content;

}

?>
