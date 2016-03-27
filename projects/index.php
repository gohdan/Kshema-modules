<?php

// Base functions of the "projects" module

include_once ($config['modules']['location']."projects/config.php");

$config_file = $config['base']['doc_root']."/config/projects.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."projects/db.php");
include_once ($config['modules']['location']."projects/categories.php");
include_once ($config['modules']['location']."projects/projects.php");
include_once ($config['modules']['location']."projects/files.php");

function projects_admin()
{
	debug ("*** projects_admin ***");
    global $config;
    $content = array(
    	'content' => ''
    );
	debug ("*** end: projects_admin ***");
    return $content;
}

function projects_frontpage()
{
	debug ("*** projects_frontpage ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
        'active_categories' => '',
        'finished_categories' => '',
		'lone_categories' => '',
		'future_categories' => ''
    );
    debug("*** projects ***");

    $i = 0;
	$categories = exec_query("SELECT * FROM ksh_projects_categories WHERE status='1'");
	debug ("; show active categories");
	while ($category = mysql_fetch_array($categories))
	{
		debug ("show category ".$category['id']);
		$content['active_categories'][$i]['id'] = stripslashes($category['id']);
        $content['active_categories'][$i]['descr_image'] = stripslashes($category['descr_image']);
        $content['active_categories'][$i]['title'] = stripslashes($category['title']);
        $content['active_categories'][$i]['author'] = stripslashes($category['author']);
        $i++;

	}
	mysql_free_result($categories);

    $i = 0;
	$categories = exec_query("SELECT * FROM ksh_projects_categories WHERE status='2'");
	debug ("; show finished categories");
	while ($category = mysql_fetch_array($categories))
	{
		debug ("show category ".$category['id']);
		$content['finished_categories'][$i]['id'] = stripslashes($category['id']);
        $content['finished_categories'][$i]['descr_image'] = stripslashes($category['descr_image']);
        $content['finished_categories'][$i]['title'] = stripslashes($category['title']);
        $content['finished_categories'][$i]['author'] = stripslashes($category['author']);
        $i++;
	}
	mysql_free_result($categories);

    $i = 0;
	$categories = exec_query("SELECT * FROM ksh_projects_categories WHERE status='3'");
	debug ("; show lone categories");
	while ($category = mysql_fetch_array($categories))
	{
		debug ("show category ".$category['id']);
		$content['lone_categories'][$i]['id'] = stripslashes($category['id']);
        $content['lone_categories'][$i]['descr_image'] = stripslashes($category['descr_image']);
        $content['lone_categories'][$i]['title'] = stripslashes($category['title']);
        $content['lone_categories'][$i]['author'] = stripslashes($category['author']);
        $i++;
	}
	mysql_free_result($categories);

	$i = 0;
	$categories = exec_query("SELECT * FROM ksh_projects_categories WHERE status='4'");
	debug ("; show future categories");
	while ($category = mysql_fetch_array($categories))
	{
		debug ("show category ".$category['id']);
		$content['future_categories'][$i]['id'] = stripslashes($category['id']);
        $content['future_categories'][$i]['descr_image'] = stripslashes($category['descr_image']);
        $content['future_categories'][$i]['title'] = stripslashes($category['title']);
        $content['future_categories'][$i]['author'] = stripslashes($category['author']);
        $i++;
	}
	mysql_free_result($categories);

	debug ("*** end: projects_frontpage ***");
    return $content;
}


function projects_default_action()
{
        global $user;
        $content = "";
        $nav_string = "";

        $content .= $nav_string;

        debug("<br>=== mod: projects ===");

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("projects", "frontpage", projects_frontpage());
                        break;

                        case "admin":
                                $content .= gen_content("projects", "admin", projects_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("projects", "install_tables", projects_install_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("projects", "drop_tables", projects_drop_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("projects", "update_tables", projects_update_tables());
                        break;

                        case "view_categories":
                                $content .= gen_content("projects", "categories_view", projects_categories_view());
                        break;

                        case "add_category":
                                $content .= gen_content("projects", "categories_add", projects_categories_add());
                        break;

                        case "del_category":
                                $content .= gen_content("projects", "categories_del", projects_categories_del());
                        break;

                        case "add_projects":
                                $content .= gen_content("projects", "add", projects_add());
                        break;

                        case "view_by_category":
                                $content .= gen_content("projects", "view_by_category", projects_view_by_category());
                        break;

                        case "edit":
                                $content .= gen_content("projects", "edit", projects_edit());
                        break;

                        case "del":
                                $content .= gen_content("projects", "del", projects_del());
                        break;

                        case "view":
                                $content .= gen_content("projects", "view", projects_view());
                        break;

                        case "projects_archive":
                                $content .= gen_content("projects", "archive", projects_archive());
                        break;

                        case "category_edit":
                                $content .= gen_content("projects", "categories_edit", projects_categories_edit());
                        break;

						case "files_view_by_project":
                                $content .= gen_content("projects", "files_view_by_project", projects_files_view_by_project());
                        break;

						case "files_view_by_date":
                                $content .= gen_content("projects", "files_view_by_date", projects_files_view_by_date());
                        break;

						case "files_add":
                                $content .= gen_content("projects", "files_add", projects_files_add());
                        break;

						case "files_del":
                                $content .= gen_content("projects", "files_del", projects_files_del());
                        break;

						case "files_edit":
                                $content .= gen_content("projects", "files_edit", projects_files_edit());
                        break;
                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("projects", "frontpage", projects_frontpage());
        }

        debug("=== end: mod: projects ===<br>");
        return $content;

}

?>
