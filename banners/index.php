<?php

// Base functions of the "banners" module

include_once ("db.php");
include_once ("categories.php");
include_once ("banners.php");

function banners_insert_by_page()
{
	debug ("*** banners_insert_by_page ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'image' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	$content['image'] = $config['pages']['page_name'].".jpg";

	debug ("*** end:banners_insert_by_page ***");
	return $content;
}

function banners_admin()
{
        $content['content'] = "";
        return $content;
}

function banners_help()
{
	debug ("*** banners_help ***");
	global $user;
	global $config;
	$content['content'] = "";
	debug ("*** end: banners_help ***");
	return $content;
}

function banners_frontpage()
{
    debug ("*** banners_frontpage ***");
	$content = array (

	);
    debug ("*** end: news_frontpage");
    return $content;
}



function banners_default_action()
{
		global $config;
        global $user;
        $content = "";


        debug("<br>=== mod: banners ===");

		if(isset($_GET['element']) && !isset($_GET['banners']))
			$_GET['banners'] = rtrim($_GET['element'], "/");

		$module_data = array (
			'module_name' => "banners",
			'module_title' => "Баннеры"
		);

		$config['pages']['page_title'] = $module_data['module_title'];
		$config['themes']['page_title']['module'] = "Баннеры";

		$priv = new Privileges();

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							if ($priv -> has("banners", "admin", "write"))
							{
								$config['themes']['page_title']['action'] = "";
    	                        //$content .= gen_content("banners", "frontpage", banners_frontpage());
        	                    $content .= gen_content("banners", "admin", banners_admin());
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "admin":
							$config['themes']['page_title']['action'] = "Администрирование";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "admin", banners_admin());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "help":
							$config['themes']['page_title']['action'] = "Справка";
                            $content .= gen_content("banners", "help", banners_help());
                        break;

                        case "install_tables":
							$config['themes']['page_title']['action'] = "Создание таблиц БД";
                            $content .= gen_content("banners", "install_tables", banners_install_tables());
                        break;

                        case "drop_tables":
							$config['themes']['page_title']['action'] = "Уничтожение таблиц БД";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "drop_tables", banners_drop_tables());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "update_tables":
							$config['themes']['page_title']['action'] = "Обновление таблиц БД";

							if (!in_array("ksh_banners_privileges", db_tables_list()))
								$priv -> create_table("ksh_banners_privileges");

							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "update_tables", banners_update_tables());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "view_categories":
							$config['themes']['page_title']['action'] = "Просмотр категорий";
                            $content .= gen_content("banners", "categories_view", banners_categories_view());
                        break;

                        case "add_category":
							$config['themes']['page_title']['action'] = "Добавление категории";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "categories_add", banners_categories_add());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "del_category":
							$config['themes']['page_title']['action'] = "Удаление категории";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "categories_del", banners_categories_del());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "add_banners":
							$config['themes']['page_title']['action'] = "Добавление баннеров";
							if ($priv -> has("banners", "admin", "write"))
	                           $content .= gen_content("banners", "add", banners_add());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "add_banners_batch":
							$config['themes']['page_title']['action'] = "Добавление пачки баннеров";
							if ($priv -> has("banners", "admin", "write"))
	                           $content .= gen_content("banners", "add_batch", banners_add_batch());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "view_by_category":
							$config['themes']['page_title']['action'] = "Просмотр баннеров в категории";
							$content_data = banners_view_by_category();
                            $content .= gen_content("banners", "view_by_category", $content_data);
                        break;

                        case "edit":
							$config['themes']['page_title']['action'] = "Редактирование баннера";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "edit", banners_edit());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "del":
							$config['themes']['page_title']['action'] = "Удаление баннера";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "del", banners_del());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "view":
							$config['themes']['page_title']['action'] = "Просмотр баннера";
							$content_data = banners_view();
                            $content .= gen_content("banners", $config['banners']['banners_template'], $content_data);
                        break;

                        case "category_edit":
							$config['themes']['page_title']['action'] = "Редактирование категории";
							if ($priv -> has("banners", "admin", "write"))
	                            $content .= gen_content("banners", "categories_edit", banners_categories_edit());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;
				}
        }

        else
        {
                debug ("*** action: default");
				$config['themes']['page_title']['action'] = "Администрирование";
                //$content = gen_content("banners", "frontpage", banners_frontpage());
                $content = gen_content("banners", "admin", banners_admin());
	     }

        debug("=== end: mod: banners ===<br>");
        return $content;
}

?>
