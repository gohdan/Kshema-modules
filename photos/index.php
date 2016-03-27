<?php

// Base functions of the "photos" module

include_once ($config['modules']['location']."photos/config.php");

$config_file = $config['base']['doc_root']."/config/photos.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."photos/db.php");
include_once ($config['modules']['location']."photos/photos.php");

function photos_admin()
{
	global $config;
	global $user;
	debug ("*** photos_admin ***");
	$content = array(
		'content' => '',
		'heading' => ''
	);
	$content['heading'] = "Администрирование фотографий";

	debug ("*** end: photos_admin ***");
    return $content;
}

function photos_frontpage()
{
	global $user;
	global $config;

	debug ("*** photos_frontpage ***");
	$content = array(
		'content' => '',
		'show_admin_link' => ''
	);

	$priv = new Privileges();
	if ($priv -> has("photos", "admin", "write"))
		$content['show_admin_link'] = "yes";
       
    debug ("*** end: photos_frontpage");
    return $content;
}

function photos_default_action()
{
	global $config;
	global $user;

	debug("<br>=== mod: photos ===");

	$content = "";

	if(isset($_GET['element']) && !isset($_GET['photo']))
		$_GET['photo'] = rtrim($_GET['element'], "/");

	$module_data = array (
		'module_name' => "photos",
		'module_title' => "Фотографии"
	);

	$config['themes']['page_title']['module'] = $module_data['module_title'];

	if ("" != $config['photos']['css'])
		$config['template']['css'][] = $config['photos']['css'];

	$priv = new Privileges();

	if (isset($_POST['do_del_category']))
	{
		if ($priv -> has("photos", "admin", "write"))
		{
			debug ("deleting category");
			$cat = new Category();
			$result = $cat -> del("ksh_photos_categories", "ksh_photos", $_POST['category']);
		}
	}

	if (isset($_POST['do_del']))
	{
		if ($priv -> has("photos", "admin", "write"))
		{
			debug ("deleting photo");
			$sql_query = "DELETE FROM `ksh_photo` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
		}
	}

	$action = "default";
	if (isset($_GET['action']))
	{
		debug("*** have GET action");
		$action = $_GET['action'];
	}

	debug ("*** action: ".$action);

	if (in_array($action, $config['photos']['admin_actions']))
		$config['themes']['admin'] = "yes";

	if (in_array($action, $config['photos']['admin_actions']) && 
			!($priv -> has("photos", "admin", "write")) &&
			("install_tables" != $action))
		$content .= gen_content("auth", "show_login_form", auth_show_login_form());
	else switch ($action)
	{
		default:
			$config['themes']['page_title']['action'] = "Главная страница";
			$content .= gen_content("photos", "frontpage", photos_frontpage());
		break;

		case "admin":
			$config['themes']['page_title']['action'] = "Администрирование";
			$content .= gen_content("photos", "admin", array_merge($module_data, photos_admin()));
		break;

		case "install_tables":
			$config['themes']['page_title']['action'] = "Создание таблиц БД";
			$content .= gen_content("photos", "install_tables", array_merge($module_data, photos_install_tables()));
		break;

		case "drop_tables":
			$config['themes']['page_title']['action'] = "Уничтожение таблиц БД";
			$content .= gen_content("photos", "drop_tables", array_merge($module_data, photos_drop_tables()));
		break;

		case "update_tables":
			$config['themes']['page_title']['action'] = "Обновление таблиц БД";
			if (!in_array("ksh_photos_privileges", db_tables_list()))
				$priv -> create_table("ksh_photos_privileges");
			$content .= gen_content("photos", "update_tables", array_merge($module_data, photos_update_tables()));
		break;

		case "categories_view":
			$config['themes']['page_title']['action'] = "Просмотр категорий";
			$cat = new Category();
			$cnt = $cat -> view("ksh_photos_categories");
			$content .= gen_content("photos", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_view_adm":
			$config['themes']['page_title']['action'] = "Просмотр категорий";
			$cat = new Category();
			$cnt = $cat -> view("ksh_photos_categories");
			$content .= gen_content("photos", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_add":
			$config['themes']['page_title']['action'] = "Добавление категории";

			$cat = new Category();
			$cnt = $cat -> add("ksh_photos_categories");
			if (isset($_GET['page']))
				$page = $_GET['page'];
			else if (isset($_GET['element']))
				$page = $_GET['element'];

			$content .= gen_content("photos", "categories_add", array_merge($module_data, $cnt));
		break;

		case "categories_del":
			$config['themes']['page_title']['action'] = "Удаление категории";

			if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;

			$cat = new Category();
			$cnt = $cat -> del("ksh_photos_categories", "ksh_photos", $category);

			$content .= gen_content("photos", "categories_del", array_merge($module_data, $cnt));
		break;

		case "categories_edit":
			$config['themes']['page_title']['action'] = "Редактирование категории";

			if (isset($_POST['category']))
				$category = $_POST['category'];
			else if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;

			$cat = new Category();
			$cnt = $cat -> edit("ksh_photos_categories", $category);

			$content .= gen_content("photos", "categories_edit", array_merge($module_data, $cnt));
		break;

		case "view_by_category":
			$config['themes']['page_title']['action'] = "Просмотр фотографий в категории";
			$content_data = photos_view_by_category();
			$content .= gen_content("photos", "view_by_category", array_merge($module_data, $content_data));
		break;

		case "add":
			$config['themes']['page_title']['action'] = "Добавление фотографии";
			$content .= gen_content("photos", "add", photos_add());
		break;

		case "del":
			$config['themes']['page_title']['action'] = "Удаление фотографии";
			$content .= gen_content("photos", "del", photos_del());
		break;

		case "edit":
			$config['themes']['page_title']['action'] = "Редактирование фотографии";
			$content .= gen_content("photos", "edit", photos_edit());
		break;
	}

	debug("=== end: mod: photos ===<br>");
	return $content;

}

?>
