<?php

// Base functions of the "pages" module

include_once ($config['modules']['location']."pages/config.php");

$config_file = $config['base']['doc_root']."/config/pages.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."pages/db.php");
include_once ($config['modules']['location']."pages/pages.php");

include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function pages_admin()
{
	debug ("*** pages_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => ''
    );
    $content['heading'] = "Администрирование страниц сайта";
	debug ("*** end: pages_admin ***");
    return $content;
}

function pages_help()
{
	debug ("*** pages_help ***");
	global $config;
	global $user;
	$content['content'] = "";

	debug ("*** end: pages_help ***");
	return $content;
}

function pages_frontpage()
{
        debug ("*** pages_frontpage ***");
        global $config;
        $content = array(
        	'content' => '',
			'if_show_admin_link' => ''
        );
		$priv = new Privileges();
		if ($priv -> has("pages", "admin", "write"))
			$content['if_show_admin_link'] = "yes";

        debug ("*** end: pages_frontpage ***");
        return $content;
}

function pages_get_actions_list()
{
	debug ("*** pages_get_actions_list ***");
	global $user;
	global $debug;
	
	$actions_list = array(
		'help',
		'create_tables',
		'drop_tables',
		'update_tables',
		'categories_view',
		'categories_view_adm',
		'categories_add',
		'categories_del',
		'categories_edit',
		'view_by_category',
		'add',
		'del',
		'edit',
		'admin',
		'view',
		'list_view',
		'transfer'
	);

	debug ("*** end: pages_get_actions_list ***");
	return $actions_list;
}

function pages_default_action()
{
	global $user;
	global $config;

	debug("=== mod: pages ===");

	$content = "";

	if(isset($_GET['element']) && !isset($_GET['page']))
		$_GET['page'] = rtrim($_GET['element'], "/");

	$module_data = array (
		'module_name' => "pages",
		'module_title' => "Страницы"
	);
	$config['pages']['page_title'] = $module_data['module_title'];
	$config['themes']['page_title']['module'] = $module_data['module_title'];

	if ("" != $config['pages']['css'])
		$config['template']['css'][] = $config['pages']['css'];

	$priv = new Privileges();

	if (isset($_POST['do_del_category']))
	{
		if ($priv -> has("pages", "admin", "write"))
		{
			debug ("deleting category");
			$cat = new Category();
			$result = $cat -> del("ksh_pages_categories", "ksh_pages", $_POST['category']);
		}
	}

	$action = "default";
	if (isset($_GET['action']))
	{
		debug("*** have GET action");
		$action = $_GET['action'];
	}

	debug ("*** action: ".$action);

	if (in_array($action, $config['pages']['admin_actions']))
		$config['themes']['admin'] = "yes";

	if (in_array($action, $config['pages']['admin_actions']) && 
			!($priv -> has("pages", "admin", "write")) &&
			("create_tables" != $action))
		$content .= gen_content("auth", "show_login_form", auth_show_login_form());
	else switch ($action)
	{
		default:
			$config['themes']['page_title']['action'] = "";
			$content .= gen_content("pages", "frontpage", pages_frontpage());
			$content .= gen_content("pages", "view", pages_view("frontpage"));
		break;

		case "help":
			$config['themes']['page_title']['action'] = "Справка";
			$content .= gen_content("pages", "help", pages_help());
		break;

		case "create_tables":
			$config['themes']['page_title']['action'] = "Создание таблиц БД";
			$content .= gen_content("pages", "tables_create", pages_tables_create());
		break;

		case "drop_tables":
			$config['themes']['page_title']['action'] = "Удаление таблиц БД";
			$content .= gen_content("pages", "drop_tables", pages_tables_drop());
		break;

		case "update_tables":
			$config['themes']['page_title']['action'] = "Обновление таблиц БД";
			if (!in_array("ksh_pages_privileges", db_tables_list()))
				$priv -> create_table("ksh_pages_privileges");
	        $content .= gen_content("pages", "tables_update", pages_tables_update());
		break;

		case "categories_view":
			$config['themes']['page_title']['action'] = "Категории";
			$config['pages']['page_title'] .= " - Категории";
			$cat = new Category();
			$cnt = $cat -> view("ksh_pages_categories");
			$content .= gen_content("pages", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_view_adm":
			$config['themes']['page_title']['action'] = "Категории";
			$config['pages']['page_title'] .= " - Категории";
			$cat = new Category();
			$cnt = $cat -> view("ksh_pages_categories");
			$content .= gen_content("pages", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_add":
			$config['themes']['page_title']['action'] = "Добавление категории";
			$config['pages']['page_title'] .= " - Добавление категории";
			$cat = new Category();
			$cnt = $cat -> add("ksh_pages_categories");
			if (isset($_GET['page']))
				$page = $_GET['page'];
			else if (isset($_GET['element']))
				$page = $_GET['element'];
			$content .= gen_content("pages", "categories_add", array_merge($module_data, $cnt));
		break;

		case "categories_del":
			$config['themes']['page_title']['action'] = "Удаление категории";
			$config['pages']['page_title'] .= " - Удаление категории";
			if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;

			$cat = new Category();
			$cnt = $cat -> del("ksh_pages_categories", "ksh_pages", $category);
			$content .= gen_content("pages", "categories_del", array_merge($module_data, $cnt));
		break;

		case "categories_edit":
			$config['themes']['page_title']['action'] = "Редактирование категории";
			$config['pages']['page_title'] .= " - Редактирование категории";

			if (isset($_POST['category']))
				$category = $_POST['category'];
			else if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;
			$cat = new Category();
			$cnt = $cat -> edit("ksh_pages_categories", $category);
			$content .= gen_content("pages", "categories_edit", array_merge($module_data, $cnt));
		break;

		case "view_by_category":
			$config['themes']['page_title']['action'] = "Просмотр страниц в категории";
			$content_data = pages_view_by_category();
			$content .= gen_content("pages", "view_by_category", $content_data);
		break;

		case "add":
			$config['themes']['page_title']['action'] = "Добавление страницы";
			$config['themes']['admin'] = "yes";
			$content .= gen_content("pages", "add", pages_add());
		break;

		case "del":
			$config['themes']['page_title']['action'] = "Удаление страницы";
			$config['themes']['admin'] = "yes";
			$content .= gen_content("pages", "del", pages_del());
		break;

		case "edit":
			$config['themes']['page_title']['action'] = "Редактирование страницы";
			$config['themes']['admin'] = "yes";
			$content .= gen_content("pages", "edit", pages_edit());
		break;

		case "admin":
			$config['themes']['page_title']['action'] = "Администрирование";
			$content .= gen_content("pages", "admin", pages_admin());
		break;

		case "view":
			$config['themes']['page_title']['action'] = "Просмотр страницы";

			$_GET['module'] = "pages";
			$_GET['action'] = "view";

			if (isset($_GET['page']))
				$page = $_GET['page'];
			else if (isset($_GET['element']))
				$page = $_GET['element'];

			$content .= gen_content("pages", "view", pages_view($page));
		break;

		case "list_view":
			$config['themes']['page_title']['action'] = "Список страниц";
			$content .= gen_content("pages", "list_view", pages_list_view());
		break;

		case "transfer":
			$config['themes']['page_title']['action'] = "Перенос страниц";
			$content .= gen_content("pages", "transfer", pages_transfer());
		break;
	}

	debug("=== end: mod: pages ===");
	return $content;
}

?>
