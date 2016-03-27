<?php

// Base functions of the "articles" module

include_once ($config['modules']['location']."articles/config.php");

$config_file = $config['base']['doc_root']."/config/articles.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."articles/db.php");
include_once ($config['modules']['location']."articles/categories.php");
include_once ($config['modules']['location']."articles/articles.php");


function articles_admin()
{
	debug ("*** articles_admin ***");
    global $config;
    $content = array(
    	'content' => ''
    );
	debug ("*** end: articles_admin ***");
    return $content;
}

function articles_admin_satellite($id)
{
	global $user;
	global $config;
	debug ("*** articles_admin_satellite ***");
	$content = array(
		'id' => $id
	);
	debug ("*** end: articles_admin_satellite ***");
	return $content;
}

function articles_frontpage()
{
	debug ("*** articles_frontpage ***");
	global $config;
    global $user;
    debug("*** articles ***");
    $content = array(
    	'content' => ''
    );

	$categories = exec_query("SELECT * FROM ksh_articles_categories WHERE parent='0'");
	while ($category = mysql_fetch_array($categories))
	{
		$content['content'] .= "<h2><a href=\"/index.php?module=articles&action=view_by_category&category=".stripslashes($category['id'])."\">".$category['name']."</a></h2>";

	}
	mysql_free_result($categories);

	debug ("*** end: articles_frontpage");
    return $content;
}

function articles_get_actions_list()
{
	debug ("*** articles_get_actions_list ***");
	global $user;
	global $debug;
	
	$actions_list = array(
		"admin",
		"admin_satellite",
		"config_edit",
		"install_tables",
		"drop_tables",
		"update_tables",
		"categories_view",
		"categories_add",
		"categories_del",
		"categories_edit",
		"add_articles",
		"view_by_category",
		"add",
		"edit",
		"del",
		"view",
		"articles_archive",
		"view_by_user",
		"privileges_edit",
		"sections_edit",
		"titles_edit",
		"moderate_edit",
		"moderate_del"
	);

	debug ("*** end: articles_get_actions_list ***");
	return $actions_list;
}

function articles_sections_edit()
{
	global $config;
	global $user;
	debug("*** articles_sections_edit ***");

	$content = array(
		'satellite_id' => '',
		'result' => '',
		'categories_checkboxes' => ''
	);

	if ($_GET['satellite'])
		$satellite = $_GET['satellite'];
	else
		$satellite = 0;
	debug("satellite id: ".$satellite);
	
	if ($satellite)
	{
		$content['satellite_id'] = $satellite;

		$cat = new Category;
		$sat = new Satellite;
		$sat -> id = $satellite;
		$sat -> url = $sat -> get_url();

		if (isset($_POST['do_update']))
		{
			debug("updating data on satellite");
			$categories = $cat -> get_categories_list("ksh_articles_categories");
			dump($categories);

			$sections = "";
			foreach($categories as $k => $v)
			{
				if(isset($_POST['subcats_'.$v]))
				{
					$sections .= "subcats_".$v."|";
					$subcats = $cat -> get_categories_list("ksh_articles_categories", $v);
					foreach ($subcats as $subcat_k => $subcat_v)
						if(isset($_POST['category_'.$subcat_v]))
							unset($_POST['category_'.$subcat_v]);
				}
				else if(isset($_POST['category_'.$v]))
					$sections .= $v."|";
			}
			$sections = rtrim($sections, "|");
			debug("new sections: ".$sections);

			$element_id = 0;
			$cnf = $sat -> get_config("ksh_articles_config");
			foreach($cnf as $k => $v)
				if ("sections" == $v['name'])
					$element_id = $v['id'];
			debug("element id: ".$element_id);
			$data_desc = array(
				'name' => 'string',
				'value' => 'string',
			);
			$data = array(
				'name' => "sections",
				'value' => $sections
			);
			if ($element_id)
			{
				debug("config parameter already exists, updating");
				$data_desc['id'] = "string";
				$data['id'] = $element_id;
				$sat -> send_element("ksh_articles_config", "update", $data, $data_desc);
			}
			else
			{
				debug("config parameter doesn't exist, inserting");
				$sat -> send_element("ksh_articles_config", "insert", $data, $data_desc);
			}

			$content['result'] = $sat -> do_action("articles|sections_update");
		}



		$cnf = $sat -> get_config("ksh_articles_config");

		debug("config:", 2);
		dump($cnf);
		
		$sections = array();
		foreach($cnf as $k => $v)
			if ("sections" == $v['name'])
				$sections = explode("|", $v['value']);

		debug("sections:", 2);
		dump($sections);

		foreach ($sections as $k => $v)
		{
			if ("subcats" == substr($v, 0, 7))
			{
				$parent = substr($v, 8);
				debug ("parent: ".$parent);
				$sections[$k] = $parent;
				$subsections = $cat -> get_categories_list("ksh_articles_categories", $parent);
				$sections = array_merge($sections, $subsections);
			}
		}
		$content['categories_checkboxes'] = $cat -> get_checkboxes("ksh_articles_categories", $sections);
	}

	debug("*** end: articles_sections_edit ***");
	return $content;
}

function articles_titles_edit()
{
	global $user;
	global $config;
	debug ("*** articles_titles_edit ***");

	$content = array(
		'satellite_id' => '',
		'show_bb_select_form' => '',
		'titles' => '',
		'bbs_select' => '',
		'result' => ''
	);

	$table_categories = "ksh_articles_categories";
	$table_titles = "ksh_articles_categories_titles";

	if (isset($_GET['satellite']))
		$satellite = $_GET['satellite'];
	else if ($_POST['id'])
		$satellite = $_POST['id'];

	$content['satellite_id'] = $satellite;
	$cat = new Category();
	$sat = new Satellite;
	$sat -> id = $satellite;
	$sat -> url = $sat -> get_url();

	$content['categories_select'] = $cat -> get_select($table_categories);

	if (isset($_POST['do_update']))
	{
		if ("" != $_POST['new_category'] && "" != $_POST['new_title'])
		{
			if ("" == $_POST['new_name'])
				$name = transliterate($_POST['new_title'], "ru", "en");
			else
				$name = $_POST['new_name'];

			$name = str_replace("/", "", $name);

			$sql_query = "INSERT INTO `".mysql_real_escape_string($table_titles)."`
				(`satellite`, `category`, `name`, `title`)
				VALUES (
				'".mysql_real_escape_string($satellite)."',
				'".mysql_real_escape_string($_POST['new_category'])."',
				'".mysql_real_escape_string($name)."',
				'".mysql_real_escape_string($_POST['new_title'])."'
				)";
			exec_query($sql_query);
		}
		if (isset($_POST['entries']))
			foreach($_POST['entries'] as $k => $v)
			{
				debug("updating entry ".$v);
				if ("" != $_POST['category_'.$v] && "" != $_POST['title_'.$v])
				{
					if ("" == $_POST['name_'.$v])
						$name = transliterate($_POST['title_'.$v], "ru", "en");
					else
						$name = $_POST['name_'.$v];

					$name = str_replace("/", "", $name);

					$sql_query = "UPDATE `".mysql_real_escape_string($table_titles)."` SET
						`category` = '".mysql_real_escape_string($_POST['category_'.$v])."',
						`name` = '".mysql_real_escape_string($name)."',
						`title` = '".mysql_real_escape_string($_POST['title_'.$v])."'
						WHERE `id` = '".mysql_real_escape_string($v)."'";
					exec_query($sql_query);
				}
				else
				{
					$sql_query = "DELETE FROM `".mysql_real_escape_string($table_titles)."` WHERE `id` = '".mysql_real_escape_string($v)."'";
					$result = exec_query($sql_query);
				}
			}
		$content['result'] = $sat -> do_action("articles|sections_update");
	}

	$sql_query = "SELECT * FROM `".mysql_real_escape_string($table_titles)."` WHERE `satellite` = '".mysql_real_escape_string($satellite)."' ORDER BY `id`";
	$result = exec_query($sql_query);
	$i = 0;
	while ($row = mysql_fetch_array($result))
	{
		debug ("processing custom title ".$i);
		$content['titles'][$i]['id'] = stripslashes($row['id']);
		$category = stripslashes($row['category']);
		debug ("category: ".$category);
		$content['titles'][$i]['category'] = $category;
		$content['titles'][$i]['name'] = stripslashes($row['name']);
		$content['titles'][$i]['title'] = stripslashes($row['title']);
		$content['titles'][$i]['categories_select'] = "";
		$categories = $cat -> get_select($table_categories, $category);
		debug("categories list:");
		dump($categories);
		foreach($categories as $k => $v)
			$content['titles'][$i]['categories_select'] .= gen_content("base", "list_categories_select", $v);
		$i++;
	}
	mysql_free_result($result);

	debug ("*** end: articles_titles_edit ***");
	return $content;
}

function articles_default_action()
{
        global $user;
		global $config;
		global $template;

        debug("<br>=== mod: articles ===");

        $content = "";

		$descr_file_path = $config['modules']['location']."articles/description.ini";
		debug ("descr_file_path: ".$descr_file_path);
		$module_data = parse_ini_file($descr_file_path);
		$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
		$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
		dump($module_data);

		if (isset($config['base']['inst_root']))
			$module_data['inst_root'] = $config['base']['inst_root'];
		else
			$module_data['inst_root'] = "";

		if (isset($config['articles']))
			array_merge($module_data, $config['articles']);
		else
			$config['articles'] = $module_data;
		dump($config['articles']);

		$config['themes']['page_title']['module'] = $module_data['title'];
		$config['modules']['current_module'] = "articles";

		if ($user['id'])
			$config['base']['use_captcha'] = "no";

		$priv = new Privileges();


		if ($priv -> has("articles", "admin", "write"))
			$module_data['show_admin_link'] = "yes";

		if ($priv -> has("articles", "add", "write"))
			$module_data['show_add_link'] = "yes";


        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("articles", "frontpage", articles_frontpage());
                        break;

						case "admin":
                                $content .= gen_content("articles", "admin", articles_admin());
                        break;

						case "admin_satellite":
							if (isset($_GET['element']))
								$sat_id = $_GET['element'];
							else
								$sat_id = 0;
							$content .= gen_content("articles", "admin_satellite", articles_admin_satellite($sat_id));
						break;

						case "config_edit":
							if ($priv -> has("articles", "config_edit", "write"))
							{
								$cnf = new Config;
								$cnf -> table = "ksh_articles_config";
								$cnt = $cnf -> edit();
								$content .= gen_content("articles", "config_edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;

						case "install_tables":
                                $content .= gen_content("articles", "install_tables", articles_install_tables());
                        break;

						case "drop_tables":
                                $content .= gen_content("articles", "drop_tables", articles_drop_tables());
                        break;

						case "update_tables":
                                $content .= gen_content("articles", "update_tables", articles_update_tables());
                        break;

						case "categories_view":
							$config['themes']['page_title']['action'] = "Категории";
							$cat = new Category();
							$cnt = $cat -> view("ksh_articles_categories");
							$content .= gen_content("articles", "categories_view", array_merge($module_data, $cnt));
						break;

                        case "categories_add":
							$config['themes']['page_title']['action'] = "Добавление категории";
							$cat = new Category();
							$cnt = $cat -> add("ksh_articles_categories");
                            $content .= gen_content("articles", "categories_add", array_merge($module_data, $cnt));
                        break;

                        case "categories_del":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Удаление категории";
							$cat = new Category();
							$cnt = $cat -> del("ksh_articles_categories", "ksh_articles", $_GET['category']);
							$content .= gen_content("articles", "categories_del", array_merge($module_data, $cnt));
                        break;

						case "categories_edit":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование категории";
							$cat = new Category();
							$cnt = $cat -> edit("ksh_articles_categories", $_GET['category']);
	                        $content .= gen_content("articles", "categories_edit", array_merge($module_data, $cnt));
                        break;


                        case "add_articles":
                                $content .= gen_content("articles", "add", articles_add());
                        break;

                        case "view_by_category":
							if(isset($_GET['category']))
								$category = $_GET['category'];
							else if (isset($_GET['element']))
								$category = $_GET['element'];
							else
								$category = 0;
	
							if ("" == $module_data['module'])
								$module_data['inst_root'] = rtrim($module_data['inst_root'], "/");

							$dbo = new DataObject;
							$dbo -> categories_table = "ksh_articles_categories";
							$dbo -> elements_table = "ksh_articles";
							$dbo -> elements_on_page = $config['articles']['elements_on_page'];
							$cnt = $dbo -> view_by_category($category);
		                    $content .= gen_content("articles", "view_by_category", array_merge($module_data, $cnt));						
                        break;

						case "add":
							$config['themes']['page_title']['action'] = "Добавление статьи";
							if ($user['id'])
								$module_data['show_user_articles_link'] = "yes";
							$dob = new DataObject();
							$dob -> table = "ksh_articles";
							$dob -> categories_table = "ksh_articles_categories";
							$cnt = $dob -> add();
		                    $content .= gen_content("articles", "add", array_merge($module_data, $cnt));
						break;

                        case "edit":
							$config['themes']['page_title']['action'] = "Редактирование статьи";
							if (!$user['id'])
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
							else
							{
								$module_data['show_user_articles_link'] = "yes";

								$element = 0;
								if (isset($_GET['element']))
									$element = $_GET['element'];
								if (isset($_POST['id']))
									$element = $_POST['id'];

								$dob = new DataObject();
								$dob -> table = "ksh_articles";
								$dob -> categories_table = "ksh_articles_categories";
								$cnt = $dob -> edit($element);
                                $content .= gen_content("articles", "edit", array_merge($module_data, $cnt));
							}
                        break;

                        case "del":
							$config['themes']['page_title']['action'] = "Удаление статьи";
							if ($user['id'])
								$module_data['show_user_articles_link'] = "yes";
							$dob = new DataObject();
							$dob -> table = "ksh_articles";
							$cnt = $dob -> del($_GET['element']);
							$content .= gen_content("articles", "del", array_merge($module_data, $cnt));						
                        break;

                        case "view":
							$config['themes']['page_title']['action'] = "Просмотр статьи";
							if ($user['id'])
								$module_data['show_user_articles_link'] = "yes";
							if ("" == $module_data['module'] && "" == $module_data['action'])
								$module_data['inst_root'] = rtrim($module_data['inst_root'], "/");
							$dob = new DataObject();
							$dob -> table = "ksh_articles";
							$dob -> categories_table = "ksh_articles_categories";
							$cnt = $dob -> view($_GET['element']);
							if ("1" == $user['id'])
								$cnt['show_admin_link'] = "yes";

							$content .= gen_content("articles", "view", array_merge($module_data, $cnt));
                        break;

                        case "articles_archive":
                                $content .= gen_content("articles", "archive", articles_archive());
                        break;

						case "view_by_user":
							if (!$user['id'])
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
							else
							{
								$config['themes']['page_title']['action'] = "Просмотр статей пользователя";
		
								if (isset($_GET['user']))
									$user_id = $_GET['user'];
								else
									$user_id = 0;
								$dob = new DataObject();
								$dob -> table = "ksh_articles";
								$dob -> elements_on_page = $config['articles']['elements_on_page'];
								$dob -> order_field = "date";
								$dob -> order_type = "DESC";
								$cnt = $dob -> view_by_user($user_id);
			                    $content .= gen_content("articles", "view_by_user", array_merge($module_data, $cnt));
							}
						break;

						case "privileges_edit":
							$config['themes']['page_title']['action'] = "Назначение прав";
							$priv = new Privileges();
							$cnt = $priv -> edit("articles");
							$content .= gen_content("articles", "privileges_edit", array_merge($module_data, $cnt));
						break;

						case "sections_edit":
							$config['themes']['page_title']['action'] = "Назначение разделов";
							if ($priv -> has("articles", "sections_edit", "write"))
								$content .= gen_content("articles", "sections_edit", array_merge($module_data, articles_sections_edit()));
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;
						
						case "titles_edit":
							$config['themes']['page_title']['action'] = "Назначение специфичных названий разделов";
							if ($priv -> has("articles", "titles_edit", "write"))
								$content .= gen_content("articles", "titles_edit", array_merge($module_data, articles_titles_edit()));
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;

                        case "moderate_edit":
							if (isset($_GET['element']))
								$_GET['article'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование статьи";
							$priv = new Privileges();
							if ($priv -> has("articles", "moderate_edit", "write"))
							{
								$dbo = new DataObject;
								$dbo -> table = $config['articles']['table'];
								$dbo -> categories_table = $config['articles']['categories_table'];
								if (isset($_GET['satellite']))
								{
									$dbo -> table .= "_".$_GET['satellite'];
									$dbo -> categories_table .= "_".$_GET['satellite'];
								}
								debug("table: ".$dbo -> table);
								$cnt = $dbo -> edit($_GET['element']);
	                            $content .= gen_content("articles", "moderate_edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", array());
                        break;						

						case "moderate_del":
							if (isset($_GET['element']))
								$_GET['article'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Удаление статьи";
							$priv = new Privileges();
							if ($priv -> has("articles", "moderate_del", "write"))
							{
								$dbo = new DataObject;
								$dbo -> table = $config['articles']['table'];
								$dbo -> categories_table = $config['articles']['categories_table'];
								if (isset($_GET['satellite']))
								{
									$dbo -> table .= "_".$_GET['satellite'];
									$dbo -> categories_table .= "_".$_GET['satellite'];
								}

								$cnt = $dbo -> del($_GET['element']);
								if (isset($result_del) && $result_del)
								{
									$cnt['show_del_form'] = '';
									$cnt['result'] = "Статья успешно удалена";
								}
	                            $content .= gen_content("articles", "moderate_del", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", array());
                        break;
						case "synchronize":
							if (isset($_GET['satellite']))
							{
								$satellite = $_GET['satellite'];
								debug("satellite: ".$satellite);
								$sat = new Satellite;
								$sat -> id = $satellite;
								$sat -> url = $sat -> get_url();

								$module_data['show_heading'] = "yes";
								$module_data['show_satellite_link'] = "yes";
								$table = $config['articles']['categories_table'];
								$cat = new Category();
								$cat -> create_table($table."_".$satellite);
								$cnt = $sat -> synchronize($table, "Категории");
								$content .= gen_content("base", "satellite_synchronize", array_merge($cnt, $module_data));								

								$module_data['show_heading'] = "";
								$module_data['show_satellite_link'] = "";
								$table = $config['articles']['table'];
								articles_table_create($table."_".$satellite);
								$cnt = $sat -> synchronize($table, "Статьи");
								$content .= gen_content("base", "satellite_synchronize", array_merge($cnt, $module_data));
							}
						break;
                }
        }

        else
        {
                debug ("*** action: default");
                //$content = gen_content("articles", "frontpage", articles_frontpage());
				
				if ("view_by_user" == $config['articles']['default_action'])
				{
					debug("action: view_by_user");
					if (!$user['id'])
						$content .= gen_content("auth", "show_login_form", auth_show_login_form());
					else
					{
						$config['themes']['page_title']['action'] = "Просмотр статей пользователя";

						if (isset($_GET['user']))
							$user_id = $_GET['user'];
						else
							$user_id = 0;
						$dob = new DataObject();
						$dob -> table = "ksh_articles";
						$dob -> elements_on_page = $config['articles']['elements_on_page'];
						$dob -> order_field = "date";
						$dob -> order_type = "DESC";
						$cnt = $dob -> view_by_user($user_id);
	                    $content .= gen_content("articles", "view_by_user", array_merge($module_data, $cnt));
					}
				}
				else
				{
					debug("action: view_by_category");

					if(isset($_GET['category']))
						$category = $_GET['category'];
					else if (isset($_GET['element']))
						$category = $_GET['element'];
					else
						$category = 0;

					if ("" == $module_data['module'])
						$module_data['inst_root'] = rtrim($module_data['inst_root'], "/");
	
					$dbo = new DataObject;
					$dbo -> categories_table = "ksh_articles_categories";
					$dbo -> elements_table = "ksh_articles";
					$dbo -> elements_on_page = $config['articles']['elements_on_page'];
					$cnt = $dbo -> view_by_category($category);
                    $content .= gen_content("articles", "view_by_category", array_merge($module_data, $cnt));
				}
        }

        debug("=== end: mod: articles ===<br>");
        return $content;
}

?>
