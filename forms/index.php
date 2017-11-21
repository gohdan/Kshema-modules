<?php

// Base functions of the "forms" module

include_once ("config.php");
include_once ("db.php");
include_once ("forms.php");

function forms_admin()
{
        $content['content'] = "";
        return $content;
}

function forms_frontpage()
{
        debug ("*** forms_frontpage ***");
        global $config;
		global $user;
        $content = array(
        	'content' => ''
        );
        $content['content'] = "";

		$priv = new Privileges();
		if ($priv -> has("forms", "admin", "write"))
			$content['show_admin_link'] = "yes";
		else if (1 == $user['id'])
			$content['show_install_link'] = "yes";


        debug ("*** end: forms_frontpage ***");
        return $content;
}

function forms_default_action()
{
	global $user;
    global $config;

    debug("=== mod: forms ===");

    $content = "";

	if(isset($_GET['element']) && !isset($_GET['page']))
		$_GET['page'] = rtrim($_GET['element'], "/");

	$module_data = array (
		'module_name' => "forms",
		'module_title' => "Формы"
	);
	$config['pages']['page_title'] = $module_data['module_title'];
	$config['themes']['page_title']['module'] = $module_data['module_title'];

	if ("" != $config['forms']['css'])
		$config['template']['css'][] = $config['forms']['css'];

	$priv = new Privileges();

	if (isset($_POST['do_del_category']))
	{
		if ($priv -> has("forms", "admin", "write"))
		{
			debug ("deleting category");
			$cat = new Category();
			$result = $cat -> del("ksh_forms_categories", "ksh_forms", $_POST['category']);
		}
	}

	$action = "default";
	if (isset($_GET['action']))
	{
		debug("*** have GET action");
		$action = $_GET['action'];
	}

	debug ("*** action: ".$action);

	if (in_array($action, $config['forms']['admin_actions']))
		$config['themes']['admin'] = "yes";

	if (in_array($action, $config['forms']['admin_actions']) && 
			!($priv -> has("forms", "admin", "write")) &&
			("install_tables" != $action))
		$content .= gen_content("auth", "show_login_form", auth_show_login_form());
	else switch ($action)
	{
        default:
			if (!$user['id'])
				$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			else
				$content .= gen_content("forms", "frontpage", forms_frontpage());
		break;

		case "categories_view_adm":
			$config['themes']['page_title']['action'] = "Категории";
			$config['forms']['page_title'] .= " - Категории";
			$cat = new Category();
			$cnt = $cat -> view("ksh_forms_categories");
			$content .= gen_content("forms", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_view":
			$config['themes']['page_title']['action'] = "Категории";
			$config['forms']['page_title'] .= " - Категории";
			$cat = new Category();
			$cnt = $cat -> view("ksh_forms_categories");
			$content .= gen_content("forms", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_add":
			$config['themes']['page_title']['action'] = "Добавление категории";
			$config['forms']['page_title'] .= " - Добавление категории";
			$cat = new Category();
			$cnt = $cat -> add("ksh_forms_categories");
			if (isset($_GET['page']))
				$page = $_GET['page'];
			else if (isset($_GET['element']))
				$page = $_GET['element'];
			$content .= gen_content("forms", "categories_add", array_merge($module_data, $cnt));
		break;

		case "categories_del":
			$config['themes']['page_title']['action'] = "Удаление категории";
			$config['forms']['page_title'] .= " - Удаление категории";
			if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;

			$cat = new Category();
			$cnt = $cat -> del("ksh_forms_categories", "ksh_forms", $category);
			$content .= gen_content("forms", "categories_del", array_merge($module_data, $cnt));
		break;

		case "categories_edit":
			$config['themes']['page_title']['action'] = "Редактирование категории";
			$config['forms']['page_title'] .= " - Редактирование категории";

			if (isset($_POST['category']))
				$category = $_POST['category'];
			else if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;
			$cat = new Category();
			$cnt = $cat -> edit("ksh_forms_categories", $category);
			$content .= gen_content("forms", "categories_edit", array_merge($module_data, $cnt));
		break;

        case "send":
			if (isset($_POST['send']))
                $content .= gen_content("forms", "send", forms_send());
			else
				$content .= gen_content("forms", "submit", forms_submit());
        break;

        case "submit":
            if (isset($_POST['send']))
                $content .= gen_content("forms", "send", forms_send());
			else
				$content .= gen_content("forms", "submit", forms_submit());
        break;

        case "view_submitted_forms":
	        $content .= gen_content("forms", "view_submitted_forms", forms_view_submitted_forms());
        break;

        case "admin":
            $content .= gen_content("forms", "admin", forms_admin());
        break;

        case "install_tables":
            $content .= gen_content("forms", "install_tables", forms_install_tables());
        break;

        case "drop_tables":
            $content .= gen_content("forms", "drop_tables", forms_drop_tables());
        break;

        case "update_tables":
            $content .= gen_content("forms", "update_tables", forms_update_tables());
        break;

        case "add":
            $content .= gen_content("forms", "add", forms_add());
        break;

        case "edit":
          	$content .= gen_content("forms", "edit", forms_edit());
        break;

        case "list":
           	$content .= gen_content("forms", "list", forms_list());
        break;

		case "view_submitted":
           	$content .= gen_content("forms", "view_submitted", forms_view_submitted());
        break;

		case "del_submitted":
          	$content .= gen_content("forms", "del_submitted", forms_del_submitted());
        break;
   }

   debug("=== end: mod: forms ===<br>");
   return $content;
}

?>

