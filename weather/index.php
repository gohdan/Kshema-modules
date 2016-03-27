<?php

// Base functions of the "weather" module

include_once ($config['modules']['location']."weather/config.php");

$config_file = $config['base']['doc_root']."/config/weather.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."weather/db.php");
include_once ($config['modules']['location']."weather/weather.php");

function weather_admin()
{
	debug ("*** weather_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => ''
    );
    $content['heading'] = "Администрирование страниц сайта";
	debug ("*** end: weather_admin ***");
    return $content;
}

function weather_frontpage()
{
        debug ("*** weather_frontpage ***");
        global $config;
        $content = array(
        	'content' => '',
			'if_show_admin_link' => ''
        );
		$priv = new Privileges();
		if ($priv -> has("weather", "admin", "write"))
			$content['if_show_admin_link'] = "yes";

		$weather = weather_get_from_db();
		foreach ($weather as $k => $v)
			$content[$k] = $v;

        debug ("*** end: weather_frontpage ***");
        return $content;
}

function weather_get_actions_list()
{
	debug ("*** weather_get_actions_list ***");
	global $user;
	global $debug;
	
	$actions_list = array(
		'help',
		'create_tables',
		'drop_tables',
		'update_tables',
		'categories_view',
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

	debug ("*** end: weather_get_actions_list ***");
	return $actions_list;
}

function weather_default_action()
{
        global $user;
        global $config;

        $content = "";

		if(isset($_GET['element']) && !isset($_GET['page']))
			$_GET['page'] = rtrim($_GET['element'], "/");

		$module_data = array (
			'module_name' => "weather",
			'module_title' => "Погодный информер"
		);
		$config['weather']['page_title'] = $module_data['module_title'];
		$config['themes']['page_title']['module'] = "Погодный информер";

		$priv = new Privileges();

        debug("<br>=== mod: weather ===");

        if (isset($_GET['action']))
        {

                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							$config['themes']['page_title']['action'] = "";
							$content .= gen_content("weather", "frontpage", weather_frontpage());
                        break;

                        case "create_tables":
							$config['themes']['page_title']['action'] = "Создание таблиц БД";
                            $content .= gen_content("weather", "tables_create", weather_tables_create());
                        break;

                        case "drop_tables":
							$config['themes']['page_title']['action'] = "Удаление таблиц БД";
							if ($priv -> has("weather", "admin", "write"))
	                            $content .= gen_content("weather", "drop_tables", weather_tables_drop());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "update_tables":
							$config['themes']['page_title']['action'] = "Обновление таблиц БД";
							if ($priv -> has("weather", "admin", "write"))
						        $content .= gen_content("weather", "tables_update", weather_tables_update());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "admin":
							$config['themes']['page_title']['action'] = "Администрирование";
							if ($priv -> has("weather", "admin", "write"))
                            	$content .= gen_content("weather", "admin", weather_admin());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "update":
							$config['themes']['page_title']['action'] = "Обновление информера";
							if ($priv -> has("weather", "admin", "write"))
                            	$content .= gen_content("weather", "update", weather_update());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                }
        }

        else
        {
                debug ("*** action: default");
				$config['themes']['page_title']['action'] = "";
                $content = gen_content("weather", "frontpage", weather_frontpage());
        }

        debug("=== end: mod: weather ===<br>");
        return $content;

}

?>
