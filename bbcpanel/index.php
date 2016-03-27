<?php

// Base functions of the "bbcpanel" module

include_once ($config['modules']['location']."bbcpanel/config.php");

$config_file = $config['base']['doc_root']."/config/bbcpanel.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."bbcpanel/db.php");
include_once ($config['modules']['location']."bbcpanel/bb.php");

// XMLRPC functionality class
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpc.inc");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpc_wrappers.inc");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpcs.inc");


function bbcpanel_admin()
{
	debug ("*** bbcpanel_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => '',
		'show_profile_link' => '',
		'show_categories_view_link' => '',
		'show_privileges_edit_link' => '',
		'show_bbs_view_all_link' => '',
		'show_categories_view_link' => '',
		'show_view_by_user_link' => '',
		'show_admin_link' => ''
    );
    $content['heading'] = "Администрирование панели управления досками объявлений";

	if ($user['id'])
		$content['show_profile_link'] = "yes";

	$priv = new Privileges();

	if ($priv -> has("bbcpanel", "privileges_edit", "write"))
		$content['show_privileges_edit_link'] = "yes";

	if ($priv -> has("bbcpanel", "bbs_view_all", "write"))
		$content['show_bbs_view_all_link'] = "yes";

	if ($priv -> has("bbcpanel", "categories_view", "write"))
		$content['show_categories_view_link'] = "yes";

	if ($priv -> has("bbcpanel", "view_by_user", "write"))
		$content['show_view_by_user_link'] = "yes";

	if ($priv -> has("bbcpanel", "admin", "write"))
		$content['show_admin_link'] = "yes";


	debug ("*** end: bbcpanel_admin ***");
    return $content;
}

function bbcpanel_frontpage()
{
	global $user;
	global $config;
	debug ("*** bbcpanel_frontpage ***");

	$content = array(
		'show_admin_link' => '',
		'show_profile_link' => ''
	);

	$priv = new Privileges();

	if ($user['id'])
		$content['show_profile_link'] = "yes";

	if ($priv -> has("bbcpanel", "admin", "write"))
		$content['show_admin_link'] = "yes";

	if ($priv -> has("bbcpanel", "privileges_edit", "write"))
		$content['show_privileges_link'] = "yes";

	if ($priv -> has("bbcpanel", "categories_view", "write"))
		$content['show_categories_view_link'] = "yes";

	if ($priv -> has("bbcpanel", "bbs_view_all", "write"))
		$content['show_bbs_view_all_link'] = "yes";

	debug ("*** end: bbcpanel_frontpage ***");
	return $content;
}


function bbcpanel_help()
{
	debug ("*** bbcpanel_help ***");
	global $config;
	global $user;
	$content['content'] = "";

	debug ("*** end: bbcpanel_help ***");
	return $content;
}

function bbcpanel_bb_checkstate()
{
	debug ("*** bbcpanel_bb_checkstate ***");
	global $config;
	global $user;
	$content = array (
		'content' => '',
		'result' => ''
	);


	$c=new xmlrpc_client("/modules/bbclient/xmlrpcserver.php", "kshema-test.gohdan.ru");
	$f=new xmlrpcmsg('checkstate', array(new xmlrpcval("ok", "string")));
	$r=$c->send($f);
	$v=$r->value(  );

	if (!$r->faultCode(  ))
		$content['result'] .= "State is ".$v->scalarval()."<br /><hr />I got this value back<br /><pre>".htmlentities($r->serialize()). "</pre><hr />";
	else
	    $content['result'] .= "Fault: Code: " . $r->faultCode(  ) . " Reason '" .$r->faultString(  )."'";

	debug ("*** end: bbcpanel_bb_checkstate ***");
	return $content;
}

function bbcpanel_default_action()
{
        global $user;
        global $config;
		global $template;

        $content = "";

		$descr_file_path = $config['modules']['location']."bbcpanel/description.ini";
		debug ("descr_file_path: ".$descr_file_path);
		$module_data = parse_ini_file($descr_file_path);
		$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
		$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
		dump($module_data);

		if (isset($config['bbcpanel']))
			array_merge($module_data, $config['bbcpanel']);
		else
			$config['bbcpanel'] = $module_data;
		dump($config['bbcpanel']);

		$config['themes']['page_title']['module'] = $module_data['title'];
		$config['modules']['current_module'] = "bbcpanel";

        debug("<br>=== mod: bbcpanel ===");

		if (isset($_POST['do_bb_del']))
		{
			debug ("have bb to delete");
			$priv = new Privileges();
			if ($priv -> has("bbcpanel", "bb_del", "write"))
			{
				debug ("user has admin rights, deleting bb");
				$sql_query = "DELETE FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
				exec_query($sql_query);
			}
			else
				debug ("user doesn't have admin rights");
		}

        if (isset($_GET['action']))
        {
			$_GET['action'] = rtrim($_GET['action'], "/");


	        if (isset($_GET['element']))
				$_GET['element'] = rtrim($_GET['element'], "/");

	        if (isset($_GET['page']))
				$_GET['page'] = rtrim($_GET['page'], "/");
	
			if (isset($_POST['do_del_category']))
			{
				debug ("deleting category");
				$cat = new Category();
				$result = $cat -> del("ksh_bbcpanel_categories", "ksh_bbcpanel", $_POST['category']);
			}
			
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							$config['themes']['page_title']['action'] = "Главная страница";
                            $content .= gen_content("bbcpanel", "frontpage", bbcpanel_frontpage());
                        break;

                        case "admin":
							$config['themes']['page_title']['action'] = "Администрирование";
                            $content .= gen_content("bbcpanel", "admin", bbcpanel_admin());
                        break;

                        case "help":
							$config['themes']['page_title']['action'] = "Справка";
                            $content .= gen_content("bbcpanel", "help", bbcpanel_help());
                        break;

                        case "create_tables":
							$config['themes']['page_title']['action'] = "Создание таблиц";
							$content .= gen_content("bbcpanel", "tables_create", bbcpanel_tables_create());
                        break;

                        case "drop_tables":
							$config['themes']['page_title']['action'] = "Удаление таблиц";						
                            $content .= gen_content("bbcpanel", "drop_tables", bbcpanel_tables_drop());
                        break;

                        case "update_tables":
							$config['themes']['page_title']['action'] = "Обновление таблиц";						
                            $content .= gen_content("bbcpanel", "tables_update", bbcpanel_tables_update());
                        break;

						case "privileges_edit":
							$config['themes']['page_title']['action'] = "Назначение прав";						
							$template['title'] .= " - Назначение прав";
							$priv = new Privileges();
							$cnt = $priv -> edit("bbcpanel");
							$content .= gen_content("bbcpanel", "privileges_edit", array_merge($module_data, $cnt));
						break;

						case "categories_view":
							$config['themes']['page_title']['action'] = "Категории";
							$config['themes']['page_title'] .= " - Категории";
							$cat = new Category();
							$cnt = $cat -> view("ksh_bbcpanel_categories");
							$content .= gen_content("bbcpanel", "categories_view", array_merge($module_data, $cnt));
						break;

                        case "categories_add":
							$config['themes']['page_title']['action'] = "Добавление категории";
							$config['themes']['page_title'] .= " - Добавление категории";
							$cat = new Category();
							$cnt = $cat -> add("ksh_bbcpanel_categories");
                            $content .= gen_content("bbcpanel", "categories_add", array_merge($module_data, $cnt));
                        break;

                        case "categories_del":
							$config['themes']['page_title']['action'] = "Удаление категории";
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$cat = new Category();
							$cnt = $cat -> del("ksh_bbcpanel_categories", "ksh_bbcpanel", $_GET['category']);
                            $content .= gen_content("bbcpanel", "categories_del", array_merge($module_data, $cnt));
                        break;

						case "categories_edit":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование категории";
							$cat = new Category();
							$cnt = $cat -> edit("ksh_bbcpanel_categories", $_GET['category']);
	                        $content .= gen_content("bbcpanel", "categories_edit", array_merge($module_data, $cnt));
                        break;


						case "view_by_category":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Просмотр досок объявлений в категории";

							$bb = new BB();
							$cnt = array('result' => '');

							if (isset($_POST['do_action']))
							{
								debug("exec action: ".$_POST['action']);
								if (isset($_POST['bbs']) && isset($_POST['action']))
								{
									debug("have checked bbs");
									foreach($_POST['bbs'] as $k => $v)
									{
										debug("bb: ".$v);
										$sat = new Satellite();
										$sat -> id = $v;
										$sat -> url = $sat -> get_url();
										$res =  $sat -> do_action($_POST['action']);
										debug("result: ".$res);
										$result = array(
											'bb' => $bb -> get_title($v),
											'result' => $res
										);
										$cnt['result'] .= gen_content("bbcpanel", "action_result", $result);
									}
								}
								else
									debug("don't have checked bbs");
							}

							$cnt = array_merge($bb -> view_by_category(), $cnt);
                            $content .= gen_content("bbcpanel", "bb_view_by_category", array_merge($module_data, $cnt));
                        break;

						case "bbs_view_all":
							$config['themes']['page_title']['action'] = "Просмотр досок объявлений";
							$bb = new BB();
							$cnt = array('result' => '');

							if (isset($_POST['do_action']))
							{
								debug("exec action: ".$_POST['action']);
								if (isset($_POST['bbs']) && isset($_POST['action']))
								{
									debug("have checked bbs");
									foreach($_POST['bbs'] as $k => $v)
									{
										debug("bb: ".$v);
										$sat = new Satellite();
										$sat -> id = $v;
										$sat -> url = $sat -> get_url();
										$res =  $sat -> do_action($_POST['action']);
										debug("result: ".$res);
										$result = array(
											'bb' => $bb -> get_title($v),
											'result' => $res
										);
										$cnt['result'] .= gen_content("bbcpanel", "action_result", $result);
									}
								}
								else
									debug("don't have checked bbs");
							}

							$cnt = array_merge($bb -> view_by_category(), $cnt);

							$content .= gen_content("bbcpanel", "bb_view_by_category", array_merge($module_data, $cnt));
                        break;

                        case "bb_add":
							if (isset($_GET['element']))
								$_GET['category'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Добавление доски объявлений";
							$bb = new BB();
							$cnt = $bb -> add();
                            $content .= gen_content("bbcpanel", "bb_add", array_merge($module_data, $cnt));
                        break;

                        case "bb_del":
							if (isset($_GET['element']))
								$_GET['bb'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Удаление доски объявлений";
							$bb = new BB();
							$cnt = $bb -> del();
                            $content .= gen_content("bbcpanel", "bb_del", array_merge($module_data, $cnt));
                        break;


                        case "bb_edit":
							if (isset($_GET['element']))
								$_GET['bb'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование доски объявлений";
							$bb = new BB();
							$cnt = $bb -> edit();
                            $content .= gen_content("bbcpanel", "bb_edit", array_merge($module_data, $cnt));
                        break;

                        case "bb_view":
							if (isset($_GET['element']))
								$_GET['bb'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Просмотр доски объявлений";
							$bb = new BB();
							$cnt = $bb -> view();
                            $content .= gen_content("bbcpanel", "bb_view", array_merge($module_data, $cnt));
                        break;

						case "titles_edit":
							if (isset($_GET['element']))
								$_GET['bb'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Назначение специфичных названий разделов";
							$bb = new BB();
							$cnt = $bb -> titles_edit();
							$content .= gen_content("bbcpanel", "titles_edit", array_merge($module_data, $cnt));
						break;

						case "tparts_edit":
							if (isset($_GET['element']))
								$_GET['bb'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Редактирование частей шаблонов";
							$bb = new BB();
							$cnt = $bb -> tparts_edit();
							$content .= gen_content("bbcpanel", "tparts_edit", array_merge($module_data, $cnt));
						break;

						case "update_all":
							if (isset($_GET['element']))
								$_GET['bb'] = $_GET['element'];
							$config['themes']['page_title']['action'] = "Обновление программного кода";
							$bb = new BB();
							$cnt = $bb -> update_all();
							$content .= gen_content("bbcpanel", "update_all", array_merge($module_data, $cnt));
						break;
                }
        }

        else
        {
                debug ("*** action: default");
				$config['themes']['page_title']['action'] = "Главная страница";
                $content .= gen_content("bbcpanel", "frontpage", bbcpanel_frontpage());
        }

        debug("=== end: mod: bbcpanel ===<br>");
        return $content;

}

?>
