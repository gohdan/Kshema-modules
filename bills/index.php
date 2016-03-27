<?php

// Base functions of the "bills" module

include_once ($config['modules']['location']."bills/config.php");

$config_file = $config['base']['doc_root']."/config/bills.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."bills/db.php");
include_once ($config['modules']['location']."bills/bills.php");
//include_once ("xmlrpcclient.php");

// XMLRPC functionality class
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpc.inc");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpc_wrappers.inc");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpcs.inc");


function bills_admin()
{
	debug ("*** bills_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => ''
    );
    $content['heading'] = "Администрирование объявлений";
	debug ("*** end: bills_admin ***");
    return $content;
}

function bills_admin_satellite($id)
{
	global $user;
	global $config;
	debug ("*** bills_admin_satellite ***");
	$content = array(
		'id' => $id
	);
	debug ("*** end: bills_admin_satellite ***");
	return $content;
}

function bills_help()
{
	debug ("*** bills_help ***");
	global $config;
	global $user;
	$content['content'] = "";

	debug ("*** end: bills_help ***");
	return $content;
}

function bills_get_actions_list()
{
	debug ("*** bills_get_actions_list ***");
	global $user;
	global $debug;
	
	$actions_list = array(
		'admin',
		'admin_satellite',
		'config_edit',
		'help',
		'create_tables',
		'drop_tables',
		'update_tables',
		'privileges_edit',
		'categories_view',
		'categories_add',
		'categories_del',
		'categories_edit',
		'view_by_category',
		'view_by_user',
		'add',
		'del',
		'edit',
		'view',
		'sections_update',
		'sections_edit',
		'titles_edit',
		'moderate_edit',
		'moderate_del',
		'synchronize'
	);

	debug ("*** end: bills_get_actions_list ***");
	return $actions_list;
}

function bills_sections_edit()
{
	global $config;
	global $user;
	debug("*** bills_sections_edit ***");

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
			$categories = $cat -> get_categories_list("ksh_bills_categories");
			dump($categories);

			$sections = "";
			foreach($categories as $k => $v)
			{
				if(isset($_POST['subcats_'.$v]))
				{
					$sections .= "subcats_".$v."|";
					$subcats = $cat -> get_categories_list("ksh_bills_categories", $v);
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
			$cnf = $sat -> get_config("ksh_bills_config");
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
				$sat -> send_element("ksh_bills_config", "update", $data, $data_desc);
			}
			else
			{
				debug("config parameter doesn't exist, inserting");
				$sat -> send_element("ksh_bills_config", "insert", $data, $data_desc);
			}

			$content['result'] = $sat -> do_action("bills|sections_update");
		}



		$cnf = $sat -> get_config("ksh_bills_config");

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
				$subsections = $cat -> get_categories_list("ksh_bills_categories", $parent);
				$sections = array_merge($sections, $subsections);
			}
		}
		$content['categories_checkboxes'] = $cat -> get_checkboxes("ksh_bills_categories", $sections);
	}

	debug("*** end: bills_sections_edit ***");
	return $content;
}


function bills_titles_edit()
{
	global $user;
	global $config;
	debug ("*** bills_titles_edit ***");

	$content = array(
		'satellite_id' => '',
		'show_bb_select_form' => '',
		'titles' => '',
		'bbs_select' => '',
		'result' => ''
	);

	$table_categories = "ksh_bills_categories";
	$table_titles = "ksh_bills_categories_titles";

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
		$content['result'] = $sat -> do_action("bills|sections_update");
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

	debug ("*** end: bills_titles_edit ***");
	return $content;
}


function bills_default_action()
{
        global $user;
        global $config;
		global $template;

        $content = "";

		$descr_file_path = $config['modules']['location']."bills/description.ini";
		debug ("descr_file_path: ".$descr_file_path);
		$module_data = parse_ini_file($descr_file_path);
		$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
		$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
		dump($module_data);

		if (isset($config['bills']))
			$config['bills'] = array_merge($config['bills'], $module_data);
		else
			$config['bills'] = $module_data;

		$cnf = new Config;
		$cnf -> table = "ksh_bills_config";
		$config['bills'] = array_merge($config['bills'], $cnf -> get_list());

		debug("bills config:", 2);
		dump($config['bills']);

		$config['themes']['page_title']['module'] = $module_data['title'];
		$config['modules']['current_module'] = "bills";

        debug("<br>=== mod: bills ===");

		$priv = new Privileges();

		if ($priv -> has("bills", "admin", "write"))
			$module_data['show_admin_link'] = "yes";

		if ($priv -> has("bills", "add", "write"))
			$module_data['show_add_link'] = "yes";

		if (isset($_POST['do_del']))
		{

			debug ("have bill to delete");
			$module_data['show_del_form'] = "";
			if ($priv -> has("bills", "del", "write") || $priv -> has("bills", "moderate_del", "write"))
			{
				debug ("user has admin rights, deleting bill");

				if(isset($_POST['satellite']))
					$satellite = $_POST['satellite'];
				else if (isset($_GET['satellite']))
					$satellite = $_GET['satellite'];
				else
					$satellite = 0;

				if ($satellite)
				{
					debug("deleting from satellite");
					$sat = new Satellite;
					$sat -> id = $satellite;
					$result_del = $sat -> del_element($config['bills']['table'], $_POST['id']);
					debug("result del: ".$result_del);
				}
				else if (isset($config['bbcpanel']['bbcpanel_domain']) && "" != $config['bbcpanel']['bbcpanel_domain'])
				{
					debug("deleting from control panel");
					$sat = new Satellite;
					$sat -> url = $config['bbcpanel']['bbcpanel_domain'];
					$sat -> del_element($config['bills']['table']."_".$config['bbcpanel']['bb_id'], $_POST['id']);
				}

				$sql_query = "DELETE FROM `ksh_bills` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
				exec_query($sql_query);

			}
			else
				debug ("user doesn't have admin rights");
		}

        if (isset($_GET['action']))
        {
			if (isset($_POST['do_del_category']))
			{
				debug ("deleting category");
				$cat = new Category();
				$result = $cat -> del("ksh_bills_categories", "ksh_bills", $_POST['category']);
			}

            debug ("*** action: ".$_GET['action']);

			if (!($priv -> has ("bills", $_GET['action'], "read")))
				$content .= gen_content("auth", "show_login_form", auth_show_login_form());
			else
                switch ($_GET['action'])
                {
                        default:
							if ("view_by_user" == $config['bills']['default_action'] && !$user['id'])
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
							else
							{
								$config['themes']['page_title']['action'] = "Просмотр объявлений";
								$bill = new Bill();
								$cnt = $bill -> $config['bills']['default_action']();
	            			    $content .= gen_content("bills", $config['bills']['default_action'], array_merge($module_data, $cnt));
							}
                        break;

                        case "admin":
                                $content .= gen_content("bills", "admin", bills_admin());
                        break;

						case "admin_satellite":
							if (isset($_GET['element']))
								$sat_id = $_GET['element'];
							else
								$sat_id = 0;
							$content .= gen_content("bills", "admin_satellite", bills_admin_satellite($sat_id));
						break;

						case "config_edit":
							if ($priv -> has("bills", "config_edit", "write"))
							{
								$cnf = new Config;
								$cnf -> table = "ksh_bills_config";
								$cnt = $cnf -> edit();
								$content .= gen_content("bills", "config_edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;

                        case "help":
							$config['themes']['page_title']['action'] = "Справка";
                            $content .= gen_content("bills", "help", bills_help());
                        break;

                        case "create_tables":
                                $content .= gen_content("bills", "tables_create", bills_tables_create());
                        break;

                        case "drop_tables":
                                $content .= gen_content("bills", "drop_tables", bills_tables_drop());
                        break;

                        case "update_tables":
                                $content .= gen_content("bills", "tables_update", bills_tables_update());
                        break;

						case "privileges_edit":
							$template['title'] .= " - Назначение прав";
							$priv = new Privileges();
							$cnt = $priv -> edit("bills");
							$content .= gen_content("bills", "privileges_edit", array_merge($module_data, $cnt));
						break;

						case "categories_view":
							$config['themes']['page_title']['action'] = "Категории";
							$cat = new Category();
							$cnt = $cat -> view("ksh_bills_categories");
							$content .= gen_content("bills", "categories_view", array_merge($module_data, $cnt));
						break;

                        case "categories_add":
							$config['themes']['page_title']['action'] = "Добавление категории";
							$cat = new Category();
							$cnt = $cat -> add("ksh_bills_categories");
                            $content .= gen_content("bills", "categories_add", array_merge($module_data, $cnt));
                        break;

                        case "categories_del":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Удаление категории";
							$cat = new Category();
							$cnt = $cat -> del("ksh_bills_categories", "ksh_bills", $_GET['category']);
							$content .= gen_content("bills", "categories_del", array_merge($module_data, $cnt));
                        break;

						case "categories_edit":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование категории";
							$cat = new Category();
							$cnt = $cat -> edit("ksh_bills_categories", $_GET['category']);
	                        $content .= gen_content("bills", "categories_edit", array_merge($module_data, $cnt));
                        break;


						case "view_by_category":
						/*
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$bill = new Bill();
							$cnt = $bill -> view_by_category();
						*/
							if(isset($_GET['category']))
								$category = $_GET['category'];
							else if (isset($_GET['element']))
								$category = $_GET['element'];
							else
								$category = 0;
	
							$dbo = new DataObject;
							$dbo -> categories_table = "ksh_bills_categories";
							$dbo -> elements_table = "ksh_bills";
							$dbo -> elements_on_page = $config['bills']['bills_on_page'];
							$cnt = $dbo -> view_by_category($category);
                            $content .= gen_content("bills", "view_by_category", array_merge($module_data, $cnt));
                        break;

						case "view_by_user":
							if (!$user['id'])
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
							else
							{
								$config['themes']['page_title']['action'] = "Просмотр объявлений пользователя";
								/*
								$bill = new Bill();
								$cnt = $bill -> view_by_user();
								*/
								if (isset($_GET['user']))
									$user_id = $_GET['user'];
								else
									$user_id = 0;
								$dob = new DataObject();
								$dob -> table = "ksh_bills";
								$dob -> elements_on_page = $config['bills']['bills_on_page'];
								$dob -> order_field = "date";
								$dob -> order_type = "DESC";
								$cnt = $dob -> view_by_user($user_id);
	                            $content .= gen_content("bills", "view_by_user", array_merge($module_data, $cnt));
							}
                        break;

                        case "add":
							$config['themes']['page_title']['action'] = "Добавление объявления";
							/*
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$bill = new Bill();
							$cnt = $bill -> add();
							*/
							if ($user['id'])
								$module_data['show_user_bills_link'] = "yes";
							$dob = new DataObject();
							$dob -> table = "ksh_bills";
							$dob -> categories_table = "ksh_bills_categories";
							$cnt = $dob -> add();
                            $content .= gen_content("bills", "add", array_merge($module_data, $cnt));
                        break;

                        case "del":
							$config['themes']['page_title']['action'] = "Удаление объявления";
							/*
							if (isset($_GET['element']))
								$_GET['bill'] = $_GET['element'];
							$bill = new Bill();
							$cnt = $bill -> del();
							*/
							if ($user['id'])
								$module_data['show_user_bills_link'] = "yes";
							$dob = new DataObject();
							$dob -> table = "ksh_bills";
							$cnt = $dob -> del($_GET['element']);
                            $content .= gen_content("bills", "del", array_merge($module_data, $cnt));
                        break;


                        case "edit":
							$config['themes']['page_title']['action'] = "Редактирование объявления";
							/*
							if (isset($_GET['element']))
								$_GET['bill'] = $_GET['element'];
							$bill = new Bill();
							$cnt = $bill -> edit();
							*/
							if (!$user['id'])
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
							else
							{
								$module_data['show_user_bills_link'] = "yes";

								$element = 0;
								if (isset($_GET['element']))
									$element = $_GET['element'];
								if (isset($_POST['id']))
									$element = $_POST['id'];

								$dob = new DataObject();
								$dob -> table = "ksh_bills";
								$dob -> categories_table = "ksh_bills_categories";
								$cnt = $dob -> edit($element);
	                            $content .= gen_content("bills", "edit", array_merge($module_data, $cnt));
							}
                        break;

                        case "view":
						/*
							if (isset($_GET['element']))
								$_GET['bill'] = $_GET['element'];
							$bill = new Bill();
							$cnt = $bill -> view();
						*/
							$config['themes']['page_title']['action'] = "Просмотр объявления";
							if ($user['id'])
								$module_data['show_user_bills_link'] = "yes";
							$dob = new DataObject();
							$dob -> table = "ksh_bills";
							$dob -> categories_table = "ksh_bills_categories";
							$cnt = $dob -> view($_GET['element']);
                            $content .= gen_content("bills", "view", array_merge($module_data, $cnt));
                        break;

						case "sections_update":
							bills_sections_update();
						break;

						case "sections_edit":
							$config['themes']['page_title']['action'] = "Назначение разделов";
							if ($priv -> has("bills", "sections_edit", "write"))
								$content .= gen_content("bills", "sections_edit", array_merge($module_data, bills_sections_edit()));
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;
						
						case "titles_edit":
							$config['themes']['page_title']['action'] = "Назначение специфичных названий разделов";
							if ($priv -> has("bills", "titles_edit", "write"))
								$content .= gen_content("bills", "titles_edit", array_merge($module_data, bills_titles_edit()));
							else
								$content .= gen_content("auth", "show_login_form", auth_show_login_form());
						break;


                        case "moderate_edit":
						/*
							if (isset($_GET['element']))
								$_GET['bill'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование объявления";
							$priv = new Privileges();
							if ($priv -> has("bills", "moderate_edit", "write"))
							{
								$bill = new Bill();
								$cnt = $bill -> edit();
	                            $content .= gen_content("bills", "edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", array());
						*/
							if (isset($_GET['element']))
								$_GET['bill'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование объявления";
							$priv = new Privileges();
							if ($priv -> has("bills", "moderate_edit", "write"))
							{
								$dbo = new DataObject;
								$dbo -> table = $config['bills']['table'];
								$dbo -> categories_table = $config['bills']['categories_table'];
								if (isset($_GET['satellite']))
								{
									$dbo -> table .= "_".$_GET['satellite'];
									$dbo -> categories_table .= "_".$_GET['satellite'];
								}
								debug("table: ".$dbo -> table);
								$cnt = $dbo -> edit($_GET['element']);
	                            $content .= gen_content("bills", "moderate_edit", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", array());
                        break;						

						case "moderate_del":
						/*
							if (isset($_GET['element']))
								$_GET['bill'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Удаление объявления";
							$priv = new Privileges();
							if ($priv -> has("bills", "moderate_del", "write"))
							{
								$bill = new Bill();
								$cnt = $bill -> del();
								if ($result_del)
								{
									$cnt['show_del_form'] = '';
									$cnt['result'] = "Объявление успешно удалено";
								}
	                            $content .= gen_content("bills", "del", array_merge($module_data, $cnt));
							}
							else
								$content .= gen_content("auth", "show_login_form", array());
						*/
							if (isset($_GET['element']))
								$_GET['article'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Удаление объявления";
							$priv = new Privileges();
							if ($priv -> has("bills", "moderate_del", "write"))
							{
								$dbo = new DataObject;
								$dbo -> table = $config['bills']['table'];
								$dbo -> categories_table = $config['bills']['categories_table'];
								if (isset($_GET['satellite']))
								{
									$dbo -> table .= "_".$_GET['satellite'];
									$dbo -> categories_table .= "_".$_GET['satellite'];
								}

								$cnt = $dbo -> del($_GET['element']);
								if (isset($result_del) && $result_del)
								{
									$cnt['show_del_form'] = '';
									$cnt['result'] = "Объявление успешно удалено";
								}
	                            $content .= gen_content("bills", "moderate_del", array_merge($module_data, $cnt));
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
								$table = $config['bills']['categories_table'];
								$cat = new Category();
								$cat -> create_table($table."_".$satellite);
								$cnt = $sat -> synchronize($table, "Категории");
								$content .= gen_content("base", "satellite_synchronize", array_merge($cnt, $module_data));								

								$module_data['show_heading'] = "";
								$module_data['show_satellite_link'] = "";
								$table = $config['bills']['table'];
								bills_table_create($table."_".$satellite);
								$cnt = $sat -> synchronize($table, "Объявления");
								$content .= gen_content("base", "satellite_synchronize", array_merge($cnt, $module_data));
							}
						break;
                }
        }

        else
        {
                debug ("*** action: default");
				/*

				if ("view_by_user" == $config['bills']['default_action'])
				{
					debug("action: view_by_user");
					if (!$user['id'])
						$content .= gen_content("auth", "show_login_form", auth_show_login_form());
					else
					{
						$bill = new Bill();
						$cnt = $bill -> view_by_user();
	                    $content .= gen_content("bills", "view_by_user", array_merge($module_data, $cnt));
					}
				}
				else
				{
					debug("action: view_by_category");
					$bill = new Bill();
					$cnt = $bill -> view_by_category();
                    $content .= gen_content("bills", "view_by_category", array_merge($module_data, $cnt));
				}
				*/
				if ("view_by_user" == $config['bills']['default_action'])
				{
					debug("action: view_by_user");
					if (!$user['id'])
						$content .= gen_content("auth", "show_login_form", auth_show_login_form());
					else
					{
						$config['themes']['page_title']['action'] = "Просмотр объявлений пользователя";

						if (isset($_GET['user']))
							$user_id = $_GET['user'];
						else
							$user_id = 0;
						$dob = new DataObject();
						$dob -> table = "ksh_bills";
						$dob -> elements_on_page = $config['bills']['bills_on_page'];
						$dob -> order_field = "date";
						$dob -> order_type = "DESC";
						$cnt = $dob -> view_by_user($user_id);
	                    $content .= gen_content("bills", "view_by_user", array_merge($module_data, $cnt));
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
	
					$dbo = new DataObject;
					$dbo -> categories_table = "ksh_bills_categories";
					$dbo -> elements_table = "ksh_bills";
					$dbo -> elements_on_page = $config['bills']['bills_on_page'];
					$cnt = $dbo -> view_by_category($category);
                    $content .= gen_content("bills", "view_by_category", array_merge($module_data, $cnt));
				}

        }


		// Updating data on the billboard
		/*
		if(isset($_POST['do_add_category']) || isset($_POST['do_update_category']) || isset($_POST['do_del_category']))
		{
			include_once($config['modules']['location']."bbcpanel/index.php");
			$bb = new BB();
			$bb -> sections_update_all();
		}
		*/

		debug("=== end: mod: bills ===<br>");
        return $content;

}

?>
