<?php

// Base functions of the "portfolio" module

include_once ($config['modules']['location']."portfolio/config.php");

$config_file = $config['base']['doc_root']."/config/portfolio.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."portfolio/db.php");
include_once ($config['modules']['location']."portfolio/portfolio.php");

function portfolio_admin()
{
	debug ("*** portfolio_admin ***");
	global $config;
	global $user;
	$content = array (
		'content' => '',
		'heading' => ''
	);
	$content['heading'] = "Администрирование новостей";
	debug ("*** end: portfolio_admin ***");
	return $content;
}

function portfolio_help()
{
	debug ("*** portfolio_help ***");
	global $config;
	global $user;
	$content['content'] = "";
	debug ("*** end: portfolio_help ***");
	return $content;
}

function portfolio_frontpage()
{
    debug ("*** portfolio_frontpage ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'portfolio' => '',
        'admin_link' => '/portfolio/admin/',
        'result' => ''
    );

	$content = portfolio_view_all();

    debug ("*** end: portfolio_frontpage");
    return $content;
}


function portfolio_default_action()
{
	global $config;
	global $user;

	debug("<br>=== mod: portfolio ===");

	$content = "";

	if(isset($_GET['element']) && !isset($_GET['page']))
		$_GET['page'] = rtrim($_GET['element'], "/");

	if (isset($config['portfolio']['page_tpl']) && "" != $config['portfolio']['page_tpl'])
		$config['themes']['page_tpl'] = $config['portfolio']['page_tpl'];

	if (isset($config['portfolio']['menu_tpl']) && "" != $config['portfolio']['menu_tpl'])
		$config['themes']['menu_tpl'] = $config['portfolio']['menu_tpl'];

	$module_data = array (
		'module_name' => "portfolio",
		'module_title' => "Портфолио"
	);

	$config['pages']['page_title'] = $module_data['module_title'];
	$config['themes']['page_title']['module'] = "Портфолио";

	if ("" != $config['portfolio']['css'])
		$config['template']['css'][] = $config['portfolio']['css'];

	$priv = new Privileges();

	if (isset($_POST['do_del_category']))
	{
		if ($priv -> has("portfolio", "admin", "write"))
		{
			debug ("deleting category");
			$cat = new Category();
			$result = $cat -> del("ksh_portfolio_categories", "ksh_portfolio", $_POST['category']);
		}
	}

	if (isset($_POST['do_del']))
	{
		if ($priv -> has("portfolio", "admin", "write"))
		{
			debug ("deleting element");
			$dob = new DataObject();
			$dob -> table = "ksh_portfolio";
			$result = $dob -> del($_POST['id']);
		}
	}


	if (isset($_GET['action']))
	{
		debug ("*** action: ".$_GET['action']);
		switch ($_GET['action'])
		{
			default:
				$config['themes']['page_title']['action'] = "Портфолио";
				$content .= gen_content("portfolio", "frontpage", portfolio_frontpage());
			break;

			case "admin":
				$config['themes']['page_title']['action'] = "Администрирование";
				if ($priv -> has("portfolio", "admin", "write"))
					$content .= gen_content("portfolio", "admin", portfolio_admin());
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "install_tables":
				$config['themes']['page_title']['action'] = "Создание таблиц БД";
				if ($priv -> has("portfolio", "admin", "write"))
					$content .= gen_content("portfolio", "install_tables", portfolio_install_tables());
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "drop_tables":
				$config['themes']['page_title']['action'] = "Уничтожение таблиц БД";
				if ($priv -> has("portfolio", "admin", "write"))
					$content .= gen_content("portfolio", "drop_tables", portfolio_drop_tables());
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "update_tables":
				$config['themes']['page_title']['action'] = "Обновление таблиц БД";
				if (!in_array("ksh_portfolio_privileges", db_tables_list()))
					$priv -> create_table("ksh_portfolio_privileges");

				if ($priv -> has("portfolio", "admin", "write"))
					$content .= gen_content("portfolio", "update_tables", portfolio_update_tables());
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "view_categories":
				$config['themes']['page_title']['action'] = "Просмотр категорий";
				if ($priv -> has("portfolio", "admin", "write"))
				{
					$cat = new Category();
					$cnt = $cat -> view("ksh_portfolio_categories");
					$content .= gen_content("portfolio", "categories_view", array_merge($module_data, $cnt));
				}
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "categories_add":
				$config['themes']['page_title']['action'] = "Добавление категории";
				if ($priv -> has("portfolio", "admin", "write"))
				{
					$cat = new Category();
					$cnt = $cat -> add("ksh_portfolio_categories");
					$content .= gen_content("portfolio", "categories_add", array_merge($module_data, $cnt));
				}
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "categories_del":
				$config['themes']['page_title']['action'] = "Удаление категории";
				if ($priv -> has("portfolio", "admin", "write"))
				{
					$cat = new Category();
					$cnt = $cat -> del("ksh_portfolio_categories", "ksh_portfolio", $_GET['element']);
					$content .= gen_content("portfolio", "categories_del", array_merge($module_data, $cnt));
				}
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "categories_edit":
				$config['themes']['page_title']['action'] = "Редактирование категории";
				if ($priv -> has("portfolio", "admin", "write"))
				{
					$cat = new Category();
					$cnt = $cat -> edit("ksh_portfolio_categories", $_GET['element']);
					$content .= gen_content("portfolio", "categories_edit", array_merge($module_data, $cnt));
				}
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "add":
				$config['themes']['page_title']['action'] = "Добавление";
				if ($priv -> has("portfolio", "admin", "write"))
					$content .= gen_content("portfolio", "add", array_merge($module_data, portfolio_add()));
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "view_by_category":
				$config['themes']['page_title']['action'] = "Просмотр категории";
				$content_data = portfolio_view_by_category();
				$content .= gen_content("portfolio", $config['portfolio']['category_template'], $content_data);
			break;

			case "edit":
				$config['themes']['page_title']['action'] = "Редактирование";
				if ($priv -> has("portfolio", "admin", "write"))
					$content .= gen_content("portfolio", "edit", array_merge($module_data, portfolio_edit()));
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "del":
				$config['themes']['page_title']['action'] = "Удаление";
				if ($priv -> has("portfolio", "admin", "write"))
				{
					$dob = new DataObject();
					$dob -> table = "ksh_portfolio";
					$dob -> categories_table = "ksh_portfolio_categories";
					$cnt = $dob -> del($_GET['element']);
					$content .= gen_content("portfolio", "del", array_merge($module_data, $cnt));
				}
				else
					$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			break;

			case "view":
				$config['themes']['page_title']['action'] = "Просмотр";
				$dob = new DataObject();
				$dob -> table = "ksh_portfolio";
				$dob -> categories_table = "ksh_portfolio_categories";
				$cnt = $dob -> view($_GET['element']);

				if ($priv -> has("portfolio", "admin", "write"))
					$cnt['show_admin_link'] = "yes";

				$images = explode("|", $cnt['images']);
				$cnt['images'] = array();
				$i = 0;
				foreach($images as $k => $v)
					if ("" != $v)
					{
						$cnt['images'][$i]['id'] = $i;
						$cnt['images'][$i]['image'] = $v;
						$i++;
					}

				if (isset($cnt['images'][0]['image']))
					$cnt['image_first'] = $cnt['images'][0]['image'];

				$cnt['images_view'] = $cnt['images'];

				if (isset($cnt['tags']))
					$cnt['tags'] = portfolio_tags_decode($cnt['tags']);

				if (isset($cnt['year']))
					$cnt['year'] = portfolio_tags_decode($cnt['year']);

				$content .= gen_content("portfolio", "view", array_merge($module_data, $cnt));
			break;

			case "view_all":
				$config['themes']['page_title']['action'] = "Просмотр";
				$content_data = portfolio_view_all();
				$content .= gen_content("portfolio", "view_all", $content_data);
			break;

		}
	}

	else
	{
		debug ("*** action: default");
		$config['themes']['page_title']['action'] = "Портфолио";
		$content = gen_content("portfolio", "frontpage", portfolio_frontpage());
	}

	debug("=== end: mod: portfolio ===<br>");
	return $content;

}

?>

