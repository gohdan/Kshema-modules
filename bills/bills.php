<?php

// Bill class

class Bill
{

function add()
{
	global $user;
	global $config;
	debug ("*** Bill: add ***");
	$content = array(
		'content' => '',
		'result' => '',
		'categories_select' => '',
		'show_admin_link' => '',
		'category' => '',
		'session_name' => '',
		'session_id' => '',
		'title' => '',
		'full_text' => '',
		'module' => '',
		'action' => '',
		'name' => ''
	);

	$cat = new Category();
	$priv = new Privileges();

	if (isset($_POST['category']))
		$category_id = $_POST['category'];
	else if (isset($_GET['category']))
		$category_id = $_GET['category'];
	else
		$category_id = 0;

	if ("yes" == $config['bills']['use_captcha'])
	{
		$content['session_name'] = session_name();
		$content['session_id'] = session_id();
	}

	if ("bills" != $config['modules']['default_module'])
		$content['module'] = "/bills";
	if ("view_by_category" != $config['bills']['default_action'])
		$content['action'] = "/view_by_category";
	

	$content['category'] = $category_id;
	$content['categories_select'] = $cat -> get_select("ksh_bills_categories", $category_id);

	if ($priv -> has("bills", "admin", "write"))
		$content['show_admin_link'] = "yes";

	if ($priv -> has("bills", "add", "write"))
	{
		debug ("user has rights");
		if (isset($_POST['do_add']))
		{
			if ("yes" == $config['bills']['use_captcha'])
			{
				if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring'])
					$result = 1;
				else
				{
					$result = 0;
					$content['result'] .= "Неправильно введено проверочное слово.";
				}
			}
			else
				$result = 1;

			if ("" == $_POST['title'] || "" == $_POST['full_text'])
			{
				$result = 0;
				$content['result'] .= " Заполнены не все необходимые поля.";
			}

			if ($result)
			{
				if ("" == $_POST['name'])
				{
					$dob = new Dataobject();
					$name = $dob -> generate_unique_name("ksh_bills", $_POST['title']);
				}
				else
					$name = $_POST['name'];

				$name = str_replace("/", "", $name);
					
				$sql_query = "INSERT INTO `ksh_bills` (
					`category`,
					`name`,
					`title`,
					`full_text`,
					`user`,
					`date`
					) VALUES (
					'".mysql_real_escape_string($_POST['category'])."',
					'".mysql_real_escape_string($name)."',
					'".mysql_real_escape_string($_POST['title'])."',
					'".mysql_real_escape_string($_POST['full_text'])."',
					'".mysql_real_escape_string($user['id'])."',
					'".mysql_real_escape_string(date("Y-m-d"))."'
				)";
				exec_query($sql_query);
				if (0 == mysql_errno())
				{
					$content['result'] = "Объявление успешно добавлено";
	
					if (isset($config['bbcpanel']['bbcpanel_domain']) && "" != $config['bbcpanel']['bbcpanel_domain'])
					{
						$this -> inform_moderators(mysql_insert_id());

						debug("sending data to control panel");
						$data_desc = array();
						$data = array();
						
						unset($_POST['keystring']);
						unset($_POST['do_add']);

						$_POST['id'] = mysql_insert_id();
						$_POST['name'] = $name;
						$_POST['date'] = date("Y-m-d");
						$_POST['user'] = $user['id'];

						foreach($_POST as $k => $v)
						{
							$data_desc[$k] = "string";
							$data[$k] = $v;
						}

						$sat = new Satellite;
						$sat -> url = $config['bbcpanel']['bbcpanel_domain'];
						$sat -> send_element($config['bills']['table']."_".$config['bbcpanel']['bb_id'], "insert", $data, $data_desc);
					}
				}
				else
					$content['result'] = "Не удалось добавить объявление, ошибка базы данных";
			}
			else
			{
				unset($_SESSION['captcha_keystring']);
				$content['name'] = $_POST['name'];
				$content['title'] = $_POST['title'];
				$content['full_text'] = $_POST['full_text'];
			}
		}
	}
	else
		$content['result'] = "Недостаточно прав";

	debug ("*** end: Bill: add ***");
	return $content;	
}

function edit()
{
	global $user;
	global $config;
	debug ("*** Bill: edit ***");
	$content = array(
		'content' => '',
		'result' => '',
		'show_admin_link' => '',
		'id' => '',
		'title' => '',
		'full_text' => '',
		'bills' => '',
		'category_title' => '',
		'category' => '',
		'bbs' => '',
		'show_my_bills_link' => '',
		'name' => '',
		'satellite' => '',
		'action' => $_GET['action']
	);

	$cat = new Category();
	$priv = new Privileges();


	if ($priv -> has("bills", "admin", "write"))
		$content['show_admin_link'] = "yes";

	if (in_array("bbcpanel", $config['modules']['installed']))
		$content['show_my_bills_link'] = "yes";

	$bill = 0;
	if (isset($_GET['bill']))
		$bill = $_GET['bill'];
	
	if (isset($_POST['id']))
		$bill = $_POST['id'];

	if(isset($_GET['satellite']))
		$satellite = $_GET['satellite'];
	else if (isset($_POST['satellite']))
	{
		$satellite = $_POST['satellite'];
		unset($_POST['satellite']);
	}
	else
		$satellite = 0;
		

	if (isset($_POST['do_update']))
	{
		debug ("have bill to update");
		unset($_POST['do_update']);
		if ($priv -> has("bills", "edit", "write") || $priv -> has("bills", "moderate_edit", "write"))
		{
			debug ("user has admin rights");

			if ("" != $_POST['name'])
				$name = $_POST['name'];
			else
			{
				$dob = new Dataobject();
				$name = $dob -> generate_unique_name("ksh_bills", $_POST['title']);
			}

			$name = str_replace("/", "", $name);

			if($satellite)
			{
				debug("sending data to satellite");
				$data_desc = array();
				$data = array();

				$_POST['name'] = $name;

				foreach($_POST as $k => $v)
				{
					$data_desc[$k] = "string";
					$data[$k] = $v;
				}

				$sat = new Satellite();
				$sat -> id = $satellite;
				$sat -> send_element($config['bills']['table'], "update", $data, $data_desc);
			}
			else if (isset($config['bbcpanel']['bbcpanel_domain']) && "" != $config['bbcpanel']['bbcpanel_domain'])
			{
				debug("sending data to control panel");
				$data_desc = array();
				$data = array();

				$_POST['name'] = $name;

				foreach($_POST as $k => $v)
				{
					$data_desc[$k] = "string";
					$data[$k] = $v;
				}

				$sat = new Satellite();
				$sat -> url = $config['bbcpanel']['bbcpanel_domain'];
				$sat -> send_element($config['bills']['table']."_".$config['bbcpanel']['bb_id'], "update", $data, $data_desc);
			}

			$sql_query = "UPDATE `ksh_bills` SET
				`title` = '".mysql_real_escape_string($_POST['title'])."',
				`name` = '".mysql_real_escape_string($name)."',
				`category` = '".mysql_real_escape_string($_POST['category'])."',
				`full_text` = '".mysql_real_escape_string($_POST['full_text'])."'
				WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			if (0 == mysql_errno())
				$content['result'] = "Обновление успешно записано";
			else
				$content['result'] = "Не удалось обновить запись, ошибка базы данных";

		}
		else
			debug ("user doesn't have admin rights");
	}

	if($satellite)
	{
		$content['satellite'] = $satellite;
		$sat = new Satellite;
		$sat -> id = $satellite;
		$content = array_merge($content, $sat -> get_element($config['bills']['table'], $bill));
	}
	else
	{
		$content = array_merge($content, $this -> get($bill));
		$content['category_title'] = $cat -> get_title("ksh_bills_categories", $content['category']);
		$content['categories_select'] = $cat -> get_select("ksh_bills_categories", $content['category']);
	}

	debug ("*** end: Bill: edit ***");
	return $content;	
}

function del()
{
	global $user;
	global $config;
	debug ("*** Bill: del ***");
	$content = array(
		'content' => '',
		'id' => '',
		'title' => '',
		'category' => '',
		'satellite' => '',
		'action' => '',
		'show_del_form' => 'yes'
	);

	$bill = $_GET['bill'];

	if (isset($_POST['satellite']))
		$satellite = $_POST['satellite'];
	else if(isset($_GET['satellite']))
		$satellite = $_GET['satellite'];
	
	if ($satellite)
	{
		$content['satellite'] = $satellite;
		$content['action'] = "moderate_del";
		$sat = new Satellite;
		$sat -> id = $satellite;
		$content = array_merge($content, $sat -> get_element($config['bills']['table'], $bill));
	}
	else
	{
		$content['action'] = $config['bills']['default_action'];
		$content = array_merge($content, $this -> get($bill));
	}

	if ("view_by_category" != $content['action'])
		$content['category'] = $content['id'];
	
	debug ("*** end: Bill: del ***");
	return $content;	
}

function get($id)
{
	global $user;
	global $config;
	debug ("*** Bill: get ***");

	$bill = array();

	$sql_query = "SELECT * FROM `ksh_bills` WHERE `id` = '".mysql_real_escape_string($id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$bill['id'] = stripslashes($row['id']);
	$bill['category'] = stripslashes($row['category']);
	$bill['name'] = stripslashes($row['name']);
	$bill['title'] = stripslashes($row['title']);
	$bill['date'] = stripslashes($row['date']);
	$bill['user'] = stripslashes($row['user']);
	$bill['text'] = stripslashes($row['full_text']);

	$bill['full_text'] = $bill['text'];

	debug ("*** end: Bill: get ***");
	return $bill;
}

function view()
{
	global $user;
	global $config;
	global $template;

	debug ("*** Bill: view ***");
	$content = array(
		'content' => '',
		'result' => '',
		'show_admin_link' => '',
		'id' => '',
		'title' => '',
		'full_text' => '',
		'bills' => '',
		'category_title' => '',
		'category' => '',
		'date' => '',
		'user' => '',
		'resemble_bills' => '',
		'module' => '',
		'action' => ''
	);

	if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

	$cat = new Category();

	if ("bills" != $config['modules']['default_module'])
		$content['module'] = "/bills";
	if ("view_by_category" != $config['bills']['default_action'])
		$content['action'] = "/view_by_category";
	

	//$bill = $_GET['bill'];
	if(is_numeric($_GET['bill']))
		$bill = $_GET['bill'];
	else
	{
		$sql_query = "SELECT `id` FROM `ksh_bills` WHERE `name` = '".mysql_real_escape_string($_GET['bill'])."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$bill = stripslashes($row['id']);
	}

	$sql_query = "SELECT * FROM `ksh_bills` WHERE `id` = '".mysql_real_escape_string($bill)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$sql_query = "SELECT `name`, `title` FROM `ksh_bills_categories` WHERE `id` = '".$row['category']."'";
	$result = exec_query($sql_query);
	$category = mysql_fetch_array($result);
	mysql_free_result($result);
	$content['category_title'] = stripslashes($category['title']);

	$category_name = stripslashes($category['name']);
	if ("" != $category_name && NULL != $category_name)
		$content['category'] = $category_name;
	else
		$content['category'] = stripslashes($row['category']);

	$content['id'] = stripslashes($row['id']);
	$content['title'] = stripslashes($row['title']);
	$content['full_text'] = stripslashes($row['full_text']);
	$content['date'] = stripslashes($row['date']);
	$content['user'] = stripslashes($row['user']);

	$config['themes']['page_title']['element'] = $content['title'];
	$parents_list = $cat -> get_parents_list("ksh_bills_categories", stripslashes($row['category']));
	foreach ($parents_list as $k => $v)
		if ($v)
			$config['themes']['page_title']['categories_title'][]['title'] = $cat -> get_title("ksh_bills_categories", $v);
	$config['themes']['page_title']['categories_title'][]['title'] = $content['category_title'];

	// Get resemble bills
	$sql_query = "SELECT COUNT(*) FROM `ksh_bills` 
		WHERE `category` = '".$row['category']."' 
		AND `id` < '".$row['id']."'
		ORDER BY `id` DESC";
	$result = exec_query($sql_query);
	$count_row = mysql_fetch_array($result);
	mysql_free_result($result);
	$bills_before_qty = stripslashes($count_row['COUNT(*)']);
	debug("bills before: ".$bills_before_qty);

	if ($bills_before_qty < $config['bills']['resemble_bills_qty'])
		$bills_after_qty = $config['bills']['resemble_bills_qty'] - $bills_before_qty;
	else
		$bills_after_qty = 0;
	debug("bills after: ".$bills_after_qty);
	
	
	$sql_query = "SELECT * FROM `ksh_bills` 
		WHERE `category` = '".$row['category']."' 
		AND `id` < '".$row['id']."'
		ORDER BY `id` DESC LIMIT ".$config['bills']['resemble_bills_qty'];
	$result = exec_query($sql_query);
	$i = 0;
	while ($bill = mysql_fetch_array($result))
	{
		$content['resemble_bills'][$i]['id'] = stripslashes($bill['id']);
		if ("" != $bill['name'])
			$content['resemble_bills'][$i]['name'] = stripslashes($bill['name']);
		else
			$content['resemble_bills'][$i]['name'] = $content['resemble_bills'][$i]['id'];
		$content['resemble_bills'][$i]['title'] = stripslashes($bill['title']);
		$content['resemble_bills'][$i]['date'] = stripslashes($bill['date']);
		$content['resemble_bills'][$i]['full_text'] = stripslashes($bill['full_text']);
		if ("bills" != $config['modules']['default_module'])
			$content['resemble_bills'][$i]['module'] = "/bills";
		$i++;
	}
	mysql_free_result($result);

	if ($bills_after_qty)
	{
		$sql_query = "SELECT * FROM `ksh_bills` 
			WHERE `category` = '".$row['category']."' 
			AND `id` > '".$row['id']."'
			ORDER BY `id` ASC LIMIT ".$config['bills']['resemble_bills_qty'];
		$result = exec_query($sql_query);
		while ($bill = mysql_fetch_array($result))
		{
			$content['resemble_bills'][$i]['id'] = stripslashes($bill['id']);
			$content['resemble_bills'][$i]['title'] = stripslashes($bill['title']);
			$content['resemble_bills'][$i]['date'] = stripslashes($bill['date']);
			$content['resemble_bills'][$i]['full_text'] = stripslashes($bill['full_text']);
			$i++;
		}
		mysql_free_result($result);
	}
	
	debug ("*** end: Bill: view ***");
	return $content;	
}

function view_by_category()
{
	global $user;
	global $config;
	global $template;
	debug ("*** Bill: view_by_category ***");
	$content = array(
		'content' => '',
		'heading' => '',
		'result' => '',
		'subcategories' => '',
		'show_admin_link' => '',
		'category_title' => '',
		'category_id' => '',
		'bills' => '',
		'show_link_on_main' => 'yes',
		'parent_link' => '',
		'category_pages' => '',
		'parents' => '',
		'module_name' => '',
		'action' => ''
	);

	$cat = new Category();

	if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

	$content['heading'] = "Просмотр объявлений в категории";

	if ("bills" != $config['modules']['default_module'])
		$content['module_name'] = "/bills";
	if ("view_by_category" != $config['bills']['default_action'])
		$content['action'] = "/view_by_category";

	if(isset($_GET['category']))
	{
		$category = $_GET['category'];
		if (!is_numeric($category))
		{
			$sql_query = "SELECT `id` FROM `ksh_bills_categories` WHERE `name` = '".mysql_real_escape_string($category)."'";
			$result = exec_query($sql_query);
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			$category = stripslashes($row['id']);
		}
	}
	else
	{
		$category = 0;
		$content['show_link_on_main'] = "";
	}
	debug ("category: ".$category);

	if ($category)
	{
		// Get category info
	
		$sql_query = "SELECT * FROM `ksh_bills_categories` WHERE `id` = '".mysql_real_escape_string($category)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$content['category_title'] = stripslashes($row['title']);
		$template['title'] .= " - ".$content['category_title'];
		$content['category_id'] = stripslashes($row['id']);

		$parent = stripslashes($row['parent']);
		$sql_query = "SELECT `name` FROM `ksh_bills_categories` WHERE `id` = '".mysql_real_escape_string($parent)."'";
		$result_parent = exec_query($sql_query);
		$row_parent = mysql_fetch_array($result_parent);
		mysql_free_result($result_parent);
		$parent_name = stripslashes($row_parent['name']);
		if ("" != $parent_name && NULL != $parent_name)
			$content['parent_link'] = $parent_name;
		else
			$content['parent_link'] = $parent;

		$parents_list = $cat -> get_parents_list("ksh_bills_categories", $category);
		foreach($parents_list as $k => $v)
			if ($v)
				$config['themes']['page_title']['categories_title'][]['title'] = $cat -> get_title("ksh_bills_categories", $v);

		$config['themes']['page_title']['element'] = $content['category_title'];		

		// Get pages
		if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		{
			$start_page = $_GET['page'];
			$content['page'] = $_GET['page'];
		}
	    else
			$start_page = 1; // Need to determine correct LIMIT
		$goods_on_page = $config['bills']['bills_on_page'];

		$bills_qty = mysql_result(exec_query("SELECT COUNT(*) FROM `ksh_bills` WHERE `category` = '".$category."'"), 0, 0);
	    debug ("bills qty: ".$bills_qty);
	    $pages_qty = ceil($bills_qty / $goods_on_page);
	    debug ("pages qty: ".$pages_qty);

		// Pages counting

	    if ($pages_qty > 1)
	    {
	        for ($i = 1; $i <= $pages_qty; $i++)
	        {
				$content['category_pages'][$i]['id'] = $i;

				$sql_query = "SELECT `name` FROM `ksh_bills_categories` WHERE `id` = '".mysql_real_escape_string($category)."'";
				$result_cat = exec_query($sql_query);
				$row_cat = mysql_fetch_array($result_cat);
				mysql_free_result($result_cat);
				$cat_name = stripslashes($row_cat['name']);
				if ("" != $cat_name && NULL != $cat_name)
					$content['category_pages'][$i]['category'] = $cat_name;
				else
					$content['category_pages'][$i]['category'] = $category;

				if ("bills" != $config['modules']['default_module'])
					$content['category_pages'][$i]['module'] = "/bills";
				if ("view_by_category" != $config['bills']['default_action'])
					$content['category_pages'][$i]['action'] = "/view_by_category";


				if ((!isset($_GET['page']) && ($i == 1)) || (isset($_GET['page']) && $i == $_GET['page']))
					$content['category_pages'][$i]['show_link'] = "";
	            else
	                $content['category_pages'][$i]['show_link'] = "yes";
	        }
	    }
	    // End: Pages counting



		// Get bills
		$sql_query = "SELECT * from `ksh_bills`
			WHERE `category` = '".mysql_real_escape_string($category)."'
			ORDER BY `id` DESC 
			LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page;
		$i = 0;
		$result = exec_query($sql_query);
		while ($row = mysql_fetch_array($result))
		{
			$content['bills'][$i]['id'] = stripslashes($row['id']);
			if ("" != $row['name'])
				$content['bills'][$i]['name'] = stripslashes($row['name']);
			else
				$content['bills'][$i]['name'] = $content['bills'][$i]['id'];
			$content['bills'][$i]['title'] = stripslashes($row['title']);
			$content['bills'][$i]['bbs'] = stripslashes($row['bbs']);
			$content['bills'][$i]['date'] = stripslashes($row['date']);
			$content['bills'][$i]['user'] = stripslashes($row['user']);

			if ("bills" != $config['modules']['default_module'])
				$content['bills'][$i]['module'] = "/bills";

			if ("yes" == $config['base']['ext_links_redirect'])
			{
				include_once($config['modules']['location']."redirect/index.php");
				$content['bills'][$i]['full_text'] = redirect_links_replace(stripslashes($row['full_text']));
			}
			else
				$content['bills'][$i]['full_text'] = stripslashes($row['full_text']);

			if (($user['id']) && (1 == $user['id'] || $user['id'] == stripslashes($row['user'])))
				$content['bills'][$i]['show_admin_link'] = "yes";
			$i++;
		}
		mysql_free_result($result);
	}

	// Get subcategories
	$i = 0;
	$sql_query = "SELECT * FROM `ksh_bills_categories` WHERE `parent` = '".mysql_real_escape_string($category)."'";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$content['subcategories'][$i]['id'] = stripslashes($row['id']);
		$content['subcategories'][$i]['parent'] = stripslashes($row['parent']);
		$content['subcategories'][$i]['name'] = stripslashes($row['name']);
		$content['subcategories'][$i]['title'] = stripslashes($row['title']);
		if ("bills" != $config['modules']['default_module'])
			$content['subcategories'][$i]['module_name'] = "/bills";
		if ("view_by_category" != $config['bills']['default_action'])
			$content['subcategories'][$i]['action'] = "/view_by_category";

		if ("" != $content['subcategories'][$i]['name'] && NULL != $content['subcategories'][$i]['name'])
			$content['subcategories'][$i]['category_link'] = $content['subcategories'][$i]['name'];
		else
			$content['subcategories'][$i]['category_link'] = $content['subcategories'][$i]['id'];
		$i++;
	}
	mysql_free_result($result);

	// Get parents
	$cat = new Category();
	$parents = $cat -> get_parents_list("ksh_bills_categories", $category);
	debug("parents:");
	dump($parents);
	$i = 0;
	foreach ($parents as $k => $v)
	{
		if ($v)
		{
			$title = $cat -> get_title("ksh_bills_categories", $v);
			$name = $cat -> get_name("ksh_bills_categories", $v);
			if ("" != $name && NULL != $name)
				$id = $name;
			else
				$id = $v;

			$content['parents'][$i]['id'] = $id;
			$content['parents'][$i]['title'] = $title;
			if ("bills" != $config['modules']['default_module'])
				$content['parents'][$i]['module'] = "/bills";
			if ("view_by_category" != $config['bills']['default_action'])
				$content['parents'][$i]['action'] = "/view_by_category";
			$i++;
		}
	}
	
	debug ("*** end: Bill: view_by_category ***");
	return $content;	
}

function view_by_user()
{
	global $user;
	global $config;
	debug ("*** Bill: view_by_user ***");
	$content = array(
		'content' => '',
		'heading' => '',
		'result' => '',
		'show_add_link' => '',
		'show_admin_link' => '',
		'bills' => '',
		'page' => '',
		'pages' => '',
		'categories' => '',
		'sections' => '',
		'bbs' => '',
		'show_send_form' => '',
		'all_categories_selected' => '',
		'all_sections_selected' => '',
		'categories_select' => '',
		'sections_select' => ''
	);

	dump ($_POST);

	$cat = new Category();

	$priv = new Privileges();

	if ($priv -> has("bills", "admin", "write"))
		$content['show_admin_link'] = "yes";

	if ($priv -> has("bills", "add", "write"))
		$content['show_add_link'] = "yes";

	$content['heading'] = "Просмотр объявлений пользователя";

	if(isset($_GET['user']))
		$user_id = $_GET['user'];
	else
	{
		$user_id = $user['id'];
	}
	debug ("user id: ".$user_id);

	if (isset($_POST['categories']))
		$post_categories = $_POST['categories'];
	else
		$post_categories = array();

	if (isset($_POST['sections']))
		$post_sections = $_POST['sections'];
	else
		$post_sections = array();

	// Get pages
	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
	{
		$start_page = $_GET['page'];
		$content['page'] = $_GET['page'];
	}
    else
		$start_page = 1; // Need to determine correct LIMIT
	$goods_on_page = $config['bills']['bills_on_page'];

	$bills_qty = mysql_result(exec_query("SELECT COUNT(*) FROM `ksh_bills` WHERE `user` = '".$user_id."'"), 0, 0);
    debug ("bills qty: ".$bills_qty);
    $pages_qty = ceil($bills_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	// Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
			$content['pages'][$i]['id'] = $i;
			if ((!isset($_GET['page']) && ($i == 1)) || (isset($_GET['page']) && $i == $_GET['page']))
				$content['pages'][$i]['show_link'] = "";
            else
                $content['pages'][$i]['show_link'] = "yes";
        }
    }
    // End: Pages counting
	

	// Get bills
	$sql_query = "SELECT * from `ksh_bills`
		WHERE `user` = '".mysql_real_escape_string($user_id)."'
		ORDER BY `date` DESC 
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page;
	$i = 0;
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$content['bills'][$i]['id'] = stripslashes($row['id']);
		$content['bills'][$i]['title'] = stripslashes($row['title']);
		$content['bills'][$i]['full_text'] = stripslashes($row['full_text']);
		$content['bills'][$i]['bbs'] = stripslashes($row['bbs']);
		$content['bills'][$i]['date'] = stripslashes($row['date']);

		if (isset($_POST['bill_'.stripslashes($row['id'])]))
			$content['bills'][$i]['show_checked_checkbox'] = "yes";
		else
			$content['bills'][$i]['show_checkbox'] = "yes";
		
		if (1 == $user['id'] || $user['id'] == $user_id)
			$content['bills'][$i]['show_admin_link'] = "yes";

		$i++;
	}
	mysql_free_result($result);

	if ($i)
	{
		$content['categories_select'] = $cat -> get_select("ksh_bbcpanel_categories", $post_categories);
		$content['sections_select'] = $cat -> get_select("ksh_bills_categories", $post_sections);
	}

	if (isset($_POST['do_search']))
	{
		$content['show_send_form'] = "yes";

		if (!isset($post_sections[0]) || "0" == $post_sections[0])
		{
			$content['all_sections_selected'] = "yes";
			$all_sections = $cat -> get_ids("ksh_bills_categories");
			$post_sections = array_merge($post_sections, $all_sections);
		}
		
		$sql_query = "SELECT `id`, `title`, `sections` FROM `ksh_bbcpanel_bbs`";
		if (isset($post_categories[0]) && "0" != $post_categories[0])
		{
			$categories_qty = count($post_categories);
			if (1 == $categories_qty)
				$sql_query .= " WHERE `category` = '".mysql_real_escape_string($post_categories[0])."'";
			else
				foreach ($post_categories as $k => $v)
				{
					if (0 == $k)
						$sql_query .= " WHERE `category` = '".mysql_real_escape_string($post_categories[0])."'";
					else
						$sql_query .= " OR `category` = '".mysql_real_escape_string($post_categories[$k])."'";
				}
		}
		else
			$content['all_categories_selected'] = "yes";
		$sql_query .= " ORDER BY `id`";


		$result = exec_query($sql_query);
		$i = 0;
		while ($row = mysql_fetch_array($result))
		{
			$sections = explode("|", stripslashes($row['sections']));
			$bb_allsections = array();
			foreach ($sections as $k => $v)
			{
				if ("subcats" == substr($v, 0, 7))
				{
					$parent = substr($v, 8);
					$bb_allsections[] = $parent;
					$subsections = $cat -> get_categories_list("ksh_bills_categories", $parent);
					$bb_allsections = array_merge($bb_allsections, $subsections);
				}
				else
					$bb_allsections[] = $v;
			}

			$intersect = array_intersect($bb_allsections, $post_sections);
			if (count($intersect) > 0)
			{
				$content['bbs'][$i]['id'] = stripslashes($row['id']);
				$content['bbs'][$i]['title'] = stripslashes($row['title']);
				$content['bbs'][$i]['sects'] = stripslashes($row['sections']);
				$content['bbs'][$i]['theme'] = "/themes/".$config['themes']['current'];

				$content['bbs'][$i]['sections'] = "";

				$sections = explode("|", stripslashes($row['sections']));
				foreach ($sections as $k => $v)
				{
					debug ("proceeding section ".$v);
					if ("subcats" == substr($v, 0, 7))
					{
						$parent = substr($v, 8);

						$parent_info = $cat -> get_category("ksh_bills_categories", $parent);
						if(in_array($parent, $post_sections))
							$parent_info['checked'] = "checked";
						$parent_info['bb_id'] = stripslashes($row['id']);
						debug("parent_info:");
						dump($parent_info);
					
						$content['bbs'][$i]['sections'] .= gen_content("bills", "list_sections_checkboxes", $parent_info);
						$cboxes = $cat -> get_checkboxes("ksh_bills_categories", $post_sections, $parent);
						foreach ($cboxes as $idx => $value)
						{
							$value['bb_id'] = stripslashes($row['id']);
							$content['bbs'][$i]['sections'] .= gen_content("bills", "list_sections_checkboxes", $value);
						}

					}
					else
					{
						$section_info = $cat -> get_category("ksh_bills_categories", $v);
						if(in_array($v, $post_sections))
							$section_info['checked'] = "checked";
						$section_info['bb_id'] = stripslashes($row['id']);
						debug("section_info:");
						dump($section_info);
					
						$content['bbs'][$i]['sections'] .= gen_content("bills", "list_sections_checkboxes", $section_info);

					}
				}
				$i++;
			}
		}
		mysql_free_result($result);
	}

	debug ("POST:");
	dump($_POST);
	if(isset($_POST['do_send']))
	{
		debug("trying to send bills");
		$bbs = array();
		$bills = array();
		debug("POST:");
		dump($_POST);
		foreach($_POST as $k => $v)
		{
			if ("bb_" == substr($k, 0, 3))
			{
				$bb_data = explode("_", substr($k, 3));
				debug ("bb & section:");
				dump($bb_data);
				$bbs[$bb_data[0]][] = $bb_data[1];
			}

			if ("bill_" == substr($k, 0, 5))
			{
				$bills[] = substr($k, 5);
			}
		}
		debug("BBs and sections to send bills:");
		dump($bbs);
		debug("Bills to send:");
		dump($bills);

		$bills_qty = count($bills);
		debug("bills qty: ".$bills_qty);
		$i = 0;

		switch($_POST['send_type'])
		{
			default: break;

			case "1":
				// Все объявления на всех досках (1 объявление на доску в первый по списку раздел)
				debug("sending by type 1");
				foreach ($bbs as $bb => $sections)
				{
					debug("bb: ".$bb);
					debug("sections:");
					dump($sections);
					$bill = $this -> get($bills[$i]);
					$bill['category'] = $sections[0];
					debug("bill:");
					dump($bill);

					bills_bill_send($bb, $bill);

					$i++;
					if ($i == $bills_qty)
						$i = 0;
				}

			break;

			case "2":
				// Все объявления на всех разделах всех досок
				debug("sending by type 2");
				foreach ($bbs as $bb => $sections)
				{
					debug("bb: ".$bb);
					debug("sections:");
					dump($sections);

					foreach ($sections as $sections_k => $section)
					{
						foreach ($bills as $bills_k => $bill_id)
						{
							$bill = $this -> get($bill_id);
							$bill['category'] = $section;
							debug("bill:");
							dump($bill);
							bills_bill_send($bb, $bill);
						}
					}
				}

			break;

			case "3":
				// Ротация по разделам
				$i = 0;
				foreach ($bbs as $bb => $sections)
				{
					debug("bb: ".$bb);
					debug("sections:");
					dump($sections);

					foreach ($sections as $sections_k => $section)
					{
						$bill = $this -> get($bills[$i]);
						$bill['category'] = $section;
						debug("bill:");
						dump($bill);
						bills_bill_send($bb, $bill);

						$i++;
						if ($i == $bills_qty)
							$i = 0;
					}
				}
			break;

			case "4":
				// Ротация по доскам
				$i = 0;
				foreach ($bbs as $bb => $sections)
				{
					debug("bb: ".$bb);
					debug("sections:");
					dump($sections);

					$bill = $this -> get($bills[$i]);

					foreach ($sections as $sections_k => $section)
					{
						$bill['category'] = $section;
						debug("bill:");
						dump($bill);
						bills_bill_send($bb, $bill);
					}

					$i++;
					if ($i == $bills_qty)
						$i = 0;
				}
			break;
		}

	}

	debug ("*** end: Bill: view_by_user ***");
	return $content;	
}

function inform_moderators($bill_id)
{
	global $user;
	global $config;
	debug("*** inform_moderators ***");

	debug("bill id: ".$bill_id);

	$sql_query = "SELECT * FROM `ksh_bills` WHERE `id` = '".mysql_real_escape_string($bill_id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$cat = new Category();

	$cnt = array(
		'site_name' => $config['base']['site_name'],
		'site_url' => $config['base']['site_url'],
		'site_id' => $config['bbcpanel']['bb_id'],
		'panel_url' => "http://".$config['bbcpanel']['bbcpanel_domain'],
		'bill_id' => $bill_id,
		'title' => stripslashes($row['title']),
		'text' => stripslashes($row['full_text']),
		'section' => $cat -> get_title("ksh_bills_categories", stripslashes($row['category']))
	);

	$groups = array();
	$addresses = array();

	$sql_query = "SELECT `id` FROM `ksh_bills_privileges` WHERE 
		(`action` = 'moderate_edit' OR `action` = 'moderate_del') AND
		(`write` = '1') AND
		(`type` = 'group')
		";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$groups[] = stripslashes($row['id']);
	}
	mysql_free_result($result);

	$groups = array_unique($groups);

	foreach($groups as $k => $v)
	{
		$sql_query = "SELECT `email` FROM `ksh_users` WHERE `group` = '".$v."'";
		$result = exec_query($sql_query);
		while ($row = mysql_fetch_array($result))
			$addresses[] = stripslashes($row['email']);
		mysql_free_result($result);
	}

	$addresses = array_unique($addresses);

	if (count($addresses) > 0)
	{
		debug("have addresses, sending mail");
		$subj = "Новое объявление";
		$headers = "Content-type: text/plain; charset=utf-8 \r\n";
	
		$message = gen_content("bills", "email_moderators_add", $cnt);

		include_once ($config['libs']['location']."phpmailer/class.phpmailer.php");

		$mail = new PHPMailer();

		$mail->IsSMTP();                                      // set mailer to use SMTP

		$mail->Host = $config['base']['mail']['host'];  // specify main and backup server
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->Username = $config['base']['mail']['username'];  // SMTP username
		$mail->Password = $config['base']['mail']['password']; // SMTP password

		$mail->From = $config['base']['mail']['from_address'];
		$mail->FromName = $config['base']['mail']['from_address'];
		$mail->AddAddress($config['base']['admin_email'], "Admin");
		foreach ($addresses as $k => $v)
			$mail->AddAddress($v);

		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(false);                                  // set email format to HTML

		$mail->Subject = $subj;

		$mail->Body = $message;

		if($mail->Send())
			debug("Ваш запрос отправлен");
		else
			debug("Невозможно отправить запрос. </p><p>Ошибка почты: " . $mail->ErrorInfo);

	}

	debug("*** end: inform_moderators ***");
	return 1;
}


}

?>
