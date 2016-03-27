<?php

// Base functions of the "guestbook" module

include_once ($config['modules']['location']."guestbook/config.php");

$config_file = $config['base']['doc_root']."/config/guestbook.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."guestbook/db.php");
include_once ($config['modules']['location']."guestbook/guestbook.php");

function guestbook_admin()
{
	debug ("*** guestbook_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => ''
    );
    $content['heading'] = "Администрирование гостевой книги";
	debug ("*** end: guestbook_admin ***");
    return $content;
}

function guestbook_default_action()
{
	global $user;
	global $config;
	global $template;

	debug("<br>=== mod: guestbook ===");

	$content = "";

	$descr_file_path = $config['modules']['location']."guestbook/description.ini";
	debug ("descr_file_path: ".$descr_file_path);
	$module_data = parse_ini_file($descr_file_path);
	$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
	$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
	dump($module_data);

	if (isset($config['guestbook']))
		array_merge($module_data, $config['guestbook']);
	else
		$config['guestbook'] = $module_data;
	dump($config['guestbook']);

	$config['themes']['page_title'] .= " - ".$module_data['title'];
	$config['modules']['current_module'] = "guestbook";

	if ("" != $config['guestbook']['css'])
		$config['template']['css'][] = $config['guestbook']['css'];

	if (isset($_POST['do_del']))
	{
		$priv = new Privileges();
		debug ("have message to delete");
		if ($priv -> has("guestbook", "del", "write"))
		{
			debug ("user has admin rights, deleting message");
			$sql_query = "DELETE FROM `ksh_guestbook` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
		}
		else
			debug ("user doesn't have admin rights");
	}
			
	if (isset($_GET['element']))
		$_GET['element'] = rtrim($_GET['element'], "/");
			
	if (isset($_GET['page']))
		$_GET['page'] = rtrim($_GET['page'], "/");

	$action = "default";
	if (isset($_GET['action']))
	{
		debug("*** have GET action");
		$action = $_GET['action'];
	}

	debug ("*** action: ".$action);

	$priv = new Privileges();

	if (in_array($action, $config['guestbook']['admin_actions']))
		$config['themes']['admin'] = "yes";

	if (in_array($action, $config['guestbook']['admin_actions']) && 
			!($priv -> has("guestbook", "admin", "write")) &&
			("create_tables" != $action))
		$content .= gen_content("auth", "show_login_form", auth_show_login_form());
	else switch ($action)
	{
		default:
			$config['themes']['page_title'] .= " - Просмотр гостевой книги";
			$gb = new Guestbook();
			$cnt = $gb -> view();
		    $content .= gen_content("guestbook", "view", array_merge($module_data, $cnt));
		break;

		case "admin":
			$content .= gen_content("guestbook", "admin", guestbook_admin());
		break;

		case "create_tables":
			$content .= gen_content("guestbook", "tables_create", guestbook_tables_create());
		break;

		case "drop_tables":
			$content .= gen_content("guestbook", "drop_tables", guestbook_tables_drop());
		break;

		case "update_tables":
			$content .= gen_content("guestbook", "tables_update", guestbook_tables_update());
		break;

		case "view":
			$config['themes']['page_title'] .= " - Просмотр сообщений";
			$template['title'] .= " - Просмотр сообщений";
			$gb = new Guestbook();
			$cnt = $gb -> view();
			$content .= gen_content("guestbook", "view", array_merge($module_data, $cnt));
		break;

		case "add":
			if (isset($_GET['element']))
				$_GET['category'] = $_GET['element'];
			$config['themes']['page_title'] .= " - Добавление сообщения";
			$gb = new Guestbook();
			$cnt = $gb -> add();
			$content .= gen_content("guestbook", "add", array_merge($module_data, $cnt));
		break;

		case "del":
			$config['themes']['page_title'] .= " - Удаление сообщения";
			$gb = new Guestbook();
			$cnt = $gb -> del();
			$content .= gen_content("guestbook", "del", array_merge($module_data, $cnt));
		break;

		case "moderate":
			$config['themes']['page_title'] .= " - Модерирование сообщений";
			$gb = new Guestbook();
			$cnt = $gb -> moderate();
			$content .= gen_content("guestbook", "moderate", array_merge($module_data, $cnt));
		break;

	}

	debug("=== end: mod: guestbook ===<br>");
	return $content;

}

?>
