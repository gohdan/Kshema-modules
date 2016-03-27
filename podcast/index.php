<?php

// Base functions of the "podcast" module

include_once($config['modules']['location']."/files/index.php"); // to upload files

include_once ("db.php");

function podcast_admin()
{
	debug ("*** podcast_admin ***");
    global $config;
    $content = array(
    	'content' => ''
    );
	debug ("*** end: podcast_admin ***");
    return $content;
}

function podcast_frontpage()
{
	global $config;
    global $user;
    debug("*** podcast_frontpage ***");

	debug ("*** end: podcast_frontpage");
    return $content;
}

function podcast_get_actions_list()
{
	debug ("*** podcast_get_actions_list ***");
	global $user;
	global $debug;
	
	$actions_list = array(
		"admin",
		"config_edit",
		"privileges_edit",
		"install_tables",
		"drop_tables",
		"update_tables",
		"add",
		"edit",
		"del",
		"view",
		"rss"
	);

	debug ("*** end: podcast_get_actions_list ***");
	return $actions_list;
}

function podcast_view_by_category()
{
	debug ("*** podcast_view_by_category ***");
	global $user;
	global $config;

	$dob = new DataObject();
	$dob -> categories_table = "ksh_podcast_categories";
	$dob -> elements_table = "ksh_podcast";
	$dob -> elements_on_page = $config['podcast']['elements_on_page'];
	$dob -> order_field = "id";
	$dob -> order_type = "DESC";

	$cnt = $dob -> view_by_category(1);

	foreach($cnt['elements'] as $k => $v)
	{
		$cnt['elements'][$k]['sec'] = $v['duration'] % 60;
		$cnt['elements'][$k]['min'] = ($v['duration'] - $cnt['elements'][$k]['sec']) / 60;
		if ($cnt['elements'][$k]['sec'] < 10)
			$cnt['elements'][$k]['sec'] = "0".$cnt['elements'][$k]['sec'];

		$fl = new File();
		$cnt['elements'][$k]['filesize'] = $fl -> get_size($v['enclosure']);

		$dt = explode(".", $cnt['elements'][$k]['date']);
		$tm = explode(":", $cnt['elements'][$k]['time']);

		$cnt['elements'][$k]['pubdate'] = date(DATE_RFC2822, mktime($tm[0], $tm[1], $tm[2], $dt[1], $dt[0], $dt[2]));
	}


	$sql_query = "SELECT * FROM `ksh_podcast_config`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		if (phpversion() >= 5.4)
			$value = htmlspecialchars(stripslashes($row['value']), ENT_SUBSTITUTE, $config['base']['output_charset']);
		else
			$value = htmlspecialchars(stripslashes($row['value']));

		$cnt[stripslashes($row['name'])] = $value;
	}


	debug ("*** end: podcast_view_by_category ***");
	return $cnt;
}

function podcast_default_action()
{
        global $user;
		global $config;
		global $template;

        debug("<br>=== mod: podcast ===");

        $content = "";

		$descr_file_path = $config['modules']['location']."podcast/description.ini";
		debug ("descr_file_path: ".$descr_file_path);
		$module_data = parse_ini_file($descr_file_path);
		$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
		$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
		dump($module_data);

		if (isset($config['base']['inst_root']))
			$module_data['inst_root'] = $config['base']['inst_root'];
		else
			$module_data['inst_root'] = "";

		if (isset($config['podcast']))
			array_merge($module_data, $config['podcast']);
		else
			$config['podcast'] = $module_data;
		dump($config['podcast']);

		$config['themes']['page_title']['module'] = $module_data['title'];
		$config['modules']['current_module'] = "podcast";

		if ($user['id'])
			$config['base']['use_captcha'] = "no";

		$priv = new Privileges();


		if ($priv -> has("podcast", "admin", "write"))
			$module_data['show_admin_link'] = "yes";

		if ($priv -> has("podcast", "add", "write"))
			$module_data['show_add_link'] = "yes";

		if (isset($_GET['element']))
		{
			debug("GET element is set, using it");
			$element = $_GET['element'];
		}
		else if (isset($_GET['action']) && !in_array($_GET['action'], podcast_get_actions_list()))
		{
			debug("GET element is not set, using GET action instead");
			$element = $_GET['action'];
		}
		else
		{
			debug("GET element is not set, GET action cannot be used, setting element to 0");
			$element = 0;
		}

		debug("element: ".$element);

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							$config['themes']['page_title']['action'] = "Просмотр выпусков подкаста";							
							$cnt = podcast_view_by_category();
							$content .= gen_content("podcast", "view_by_category", array_merge($module_data, $cnt));
                        break;

						case "admin":
							if ($priv -> has("podcast", "admin", "write"))
                                $content .= gen_content("podcast", "admin", podcast_admin());
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;


						case "config_edit":
							if ($priv -> has("podcast", "config_edit", "write"))
							{
								$cnf = new Config;
								$cnf -> table = "ksh_podcast_config";
								$cnt = $cnf -> edit();
								$content .= gen_content("podcast", "config_edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;

						case "install_tables":
                                $content .= gen_content("podcast", "install_tables", podcast_install_tables());
                        break;

						case "drop_tables":
                                $content .= gen_content("podcast", "drop_tables", podcast_drop_tables());
                        break;

						case "update_tables":
                                $content .= gen_content("podcast", "update_tables", podcast_update_tables());
                        break;

						case "add":
							if ($priv -> has("podcast", "admin", "write"))
							{
								$config['themes']['page_title']['action'] = "Добавление выпуска подкаста";

								$fl = new File();
								$_POST['image'] = $fl -> upload("image");
								$_POST['enclosure'] = $fl -> upload("enclosure");

								$_POST['duration'] = $_POST['min'] * 60 + $_POST['sec'];

								$dob = new DataObject();
								$dob -> table = "ksh_podcast";
								$dob -> categories_table = "ksh_podcast_categories";
								$cnt = $dob -> add();

								$cnt['author'] = users_get_name($user['id']);

			                    $content .= gen_content("podcast", "add", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;

                        case "edit":
							if ($priv -> has("podcast", "admin", "write"))
							{
								$config['themes']['page_title']['action'] = "Редактирование выпуска подкаста";

								$fl = new File();
								$_POST['image'] = $fl -> upload("image");
								$_POST['enclosure'] = $fl -> upload("enclosure");

								$_POST['duration'] = $_POST['min'] * 60 + $_POST['sec'];

								$dob = new DataObject();
								$dob -> table = "ksh_podcast";
								$dob -> categories_table = "ksh_podcast_categories";
								$cnt = $dob -> edit($_GET['element']);

								$cnt['site_url'] = $config['base']['site_url'];

								$cnt['sec'] = $cnt['duration'] % 60;
								$cnt['min'] = ($cnt['duration'] - $cnt['sec']) / 60;

			                    $content .= gen_content("podcast", "edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "del":
							if ($priv -> has("podcast", "admin", "write"))
							{
								$config['themes']['page_title']['action'] = "Удаление выпуска подкаста";

								$dob = new DataObject();
								$dob -> table = "ksh_podcast";
								$cnt = $dob -> del($_GET['element']);
								$content .= gen_content("podcast", "del", array_merge($module_data, $cnt));						
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
                        break;

                        case "view_by_category":
							$config['themes']['page_title']['action'] = "Просмотр выпусков подкаста";							
							$cnt = podcast_view_by_category();
							$content .= gen_content("podcast", "view_by_category", array_merge($module_data, $cnt));
                        break;

						case "privileges_edit":
							$config['themes']['page_title']['action'] = "Назначение прав";
							$priv = new Privileges();
							$cnt = $priv -> edit("podcast");
							$content .= gen_content("podcast", "privileges_edit", array_merge($module_data, $cnt));
						break;
						
						case "rss":
							$config['themes']['page_title']['action'] = "RSS";
							$config['themes']['page_tpl'] = "podcast";
							header("content-type: application/rss+xml");
							$cnt = podcast_view_by_category();
							$cnt['rss_items'] = $cnt['elements'];
							$content .= gen_content("podcast", "rss", array_merge($module_data, $cnt));
						break;

                }
        }

        else
        {
                debug ("*** action: default");
				$config['themes']['page_title']['action'] = "Просмотр выпусков подкаста";							
				$cnt = podcast_view_by_category();
				$content .= gen_content("podcast", "view_by_category", array_merge($module_data, $cnt));
        }

        debug("=== end: mod: podcast ===<br>");
        return $content;
}

?>
