<?php

// Base functions of the "rss" module

include_once ($config['modules']['location']."rss/config.php");

$config_file = $config['base']['doc_root']."/config/rss.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."rss/db.php");
include_once ($config['modules']['location']."rss/rss.php");

function rss_admin()
{
	debug ("*** rss_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => ''
    );
    $content['heading'] = "Администрирование RSS";
	debug ("*** end: rss_admin ***");
    return $content;
}

function rss_default_action()
{
        global $user;
        global $config;

        $content = "";

		if(isset($_GET['element']) && !isset($_GET['page']))
			$_GET['page'] = rtrim($_GET['element'], "/");

		$module_data = array (
			'module_name' => "rss",
			'module_title' => "RSS"
		);
		$config['rss']['page_title'] = $module_data['module_title'];
		$config['themes']['page_title']['module'] = "Страницы";


        debug("<br>=== mod: rss ===");

        if (isset($_GET['action']))
        {

			
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							$config['themes']['page_title']['action'] = "";
							$config['themes']['page_tpl'] = "rss";
							$content .= gen_content("rss", "view", rss_view());
                        break;

                        case "create_tables":
							$config['themes']['page_title']['action'] = "Создание таблиц БД";
                            $content .= gen_content("rss", "tables_create", rss_tables_create());
                        break;

                        case "drop_tables":
							$config['themes']['page_title']['action'] = "Удаление таблиц БД";
                            $content .= gen_content("rss", "drop_tables", rss_tables_drop());
                        break;

                        case "update_tables":
							$config['themes']['page_title']['action'] = "Обновление таблиц БД";
					        $content .= gen_content("rss", "tables_update", rss_tables_update());
                        break;

                        case "admin":
							$config['themes']['page_title']['action'] = "Администрирование";
                            $content .= gen_content("rss", "admin", rss_admin());
                        break;

                        case "view":
							$config['themes']['page_title']['action'] = "Просмотр ленты RSS";
							$config['themes']['page_tpl'] = "rss";
							$content .= gen_content("rss", "view", rss_view($_GET['page']));
                        break;


                }
        }

        else
        {
                debug ("*** action: default");
				$config['themes']['page_title']['action'] = "";
				$config['themes']['page_tpl'] = "rss";
				$content .= gen_content("rss", "view", rss_view());
        }

        debug("=== end: mod: rss ===<br>");
        return $content;

}

?>
