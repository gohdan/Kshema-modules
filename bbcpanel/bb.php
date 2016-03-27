<?php

// BillBoard class

class BB
{

function add()
{
	global $user;
	global $config;
	debug ("*** BB: add ***");
	$content = array(
		'content' => '',
		'result' => '',
		'categories_select' => '',
		'categories_checkboxes' => ''
	);

	$cat = new Category();
	$priv = new Privileges();

	if ($priv -> has ("bbcpanel", "bb_add", "write"))
	{
		if (isset($_POST['do_add']))
		{
			debug ("user is admin");
/*	
			$sections = "";

			$categories = $cat -> get_categories_list("ksh_bills_categories");
			dump($categories);
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
*/
			$sql_query = "INSERT INTO `ksh_bbcpanel_bbs` (
			`category`,
			`name`,
			`title`,
			`url`,
			`instroot`
			) VALUES (
			'".mysql_real_escape_string($_POST['category'])."',
			'".mysql_real_escape_string($_POST['name'])."',
			'".mysql_real_escape_string($_POST['title'])."',
			'".mysql_real_escape_string($_POST['url'])."',
			'".mysql_real_escape_string($_POST['instroot'])."'
			)";
			exec_query($sql_query);
			if (0 == mysql_errno())
			{
				$content['result'] = "Сателлит успешно добавлен";
				$id = mysql_insert_id();
				$this -> sections_update($id);
			}
			else
				$content['result'] = "Не удалось добавить сателлит, ошибка базы данных";
		}
	}

	if (isset($_GET['category']))
		$category = $_GET['category'];
	else
		$category = 0;

	$content['categories_select'] = $cat -> get_select("ksh_bbcpanel_categories", $category);

	$content['categories_checkboxes'] = $cat -> get_checkboxes("ksh_bills_categories", 0);

	debug ("*** end: BB: add ***");
	return $content;	
}

function edit()
{
	global $user;
	global $config;
	debug ("*** BB: edit ***");
	$content = array(
		'content' => '',
		'result' => '',
		'show_admin_link' => '',
		'id' => '',
		'title' => '',
		'name' => '',
		'url' => '',
		'sections' => '',
		'bills_per_page' => '',
		'theme' => '',
		'bill_view_mode' => '',
		'category_title' => '',
		'category' => '',
		'categories_select' => '',
		'categories_checkboxes' => '',
		'modules' => ''
	);

	$cat = new Category();
	$priv = new Privileges();
	$sat = new Satellite();


	if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

	$bb = 0;
	if (isset($_GET['bb']))
		$bb = $_GET['bb'];
	
	if (isset($_POST['id']))
		$bb = $_POST['id'];

	$sat -> id = $bb;

	if (isset($_POST['do_update']))
	{
		debug ("have bb to update");
		if ($priv -> has("bbcpanel", "bb_edit", "write"))
		{
			debug ("user has admin rights");
/*
			$sections = "";

			$categories = $cat -> get_categories_list("ksh_bills_categories");
			dump($categories);
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
*/

			$sql_query = "UPDATE `ksh_bbcpanel_bbs` SET
				`name` = '".mysql_real_escape_string($_POST['name'])."',
				`title` = '".mysql_real_escape_string($_POST['title'])."',
				`category` = '".mysql_real_escape_string($_POST['category'])."',
				`url` = '".mysql_real_escape_string($_POST['url'])."',
				`instroot` = '".mysql_real_escape_string($_POST['instroot'])."'
				WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			if (0 == mysql_errno())
			{
				$content['result'] = "Обновление успешно записано";
				$this -> sections_update($_POST['id']);
			}
			else
				$content['result'] = "Не удалось обновить запись, ошибка базы данных";
		}
		else
			debug ("user doesn't have admin rights");
	}

	$sat_open_modules = $sat -> get_open_modules();
	debug("satellite open modules:");
	dump($sat_open_modules);
	$content['modules'] = $sat_open_modules;

	$sql_query = "SELECT * FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($bb)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['category'] = stripslashes($row['category']);
	$sql_query = "SELECT `title` FROM `ksh_bbcpanel_categories` WHERE `id` = '".mysql_real_escape_string($content['category'])."'";
	$result = exec_query($sql_query);
	$row_cat = mysql_fetch_array($result);
	mysql_free_result($result);
	$content['category_title'] = stripslashes($row_cat['title']);
	$content['categories_select'] = $cat -> get_select("ksh_bbcpanel_categories", $content['category']);
/*
	$content['sections'] = stripslashes($row['sections']);
	$sections = explode("|", $content['sections']);
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
*/
	$content['id'] = stripslashes($row['id']);
	$content['title'] = stripslashes($row['title']);
	$content['name'] = stripslashes($row['name']);
	$content['url'] = stripslashes($row['url']);
	$content['instroot'] = stripslashes($row['instroot']);
	/*
	$content['bills_per_page'] = stripslashes($row['bills_per_page']);
	$content['theme'] = stripslashes($row['theme']);
	if ("0" == $content['theme'])
		$content['theme'] = "default";

	$bill_view_mode = stripslashes($row['bill_view_mode']);
	$content['bill_view_mode_selected_'.$bill_view_mode] = "yes";
*/

	return $content;	
}

function del()
{
	global $user;
	global $config;
	debug ("*** BB: del ***");
	$content = array(
		'content' => '',
		'result' => '',
		'id' => '',
		'title' => '',
		'category' => ''
	);

	$bb = $_GET['bb'];
	$sql_query = "SELECT `id`, `title`, `category` FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($bb)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($row['id']);
	$content['title'] = stripslashes($row['title']);
	$content['category'] = stripslashes($row['category']);

	debug ("*** end: BB: del ***");
	return $content;	
}

function view()
{
	global $user;
	global $config;
	debug ("*** BB: view ***");
	$content = array(
		'content' => '',
		'result' => '',
		'show_admin_link' => '',
		'id' => '',
		'title' => '',
		'name' => '',
		'url' => '',
		'sections' => '',
		'bills_per_page' => '',
		'theme' => '',
		'bill_view_mode' => '',
		'category_title' => '',
		'category' => ''
	);

	if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

	$bb = $_GET['bb'];

	$sql_query = "SELECT * FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($bb)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['category'] = stripslashes($row['category']);
	$sql_query = "SELECT `title` FROM `ksh_bbcpanel_categories` WHERE `id` = '".mysql_real_escape_string($content['category'])."'";
	$result = exec_query($sql_query);
	$cat = mysql_fetch_array($result);
	mysql_free_result($result);
	$content['category_title'] = stripslashes($cat['title']);

	$content['id'] = stripslashes($row['id']);
	$content['title'] = stripslashes($row['title']);
	$content['name'] = stripslashes($row['name']);
	$content['url'] = stripslashes($row['url']);
	$content['bills_per_page'] = stripslashes($row['bills_per_page']);
	$content['theme'] = stripslashes($row['theme']);
	if ("0" == $content['theme'])
		$content['theme'] = "default";

	$bill_view_mode = stripslashes($row['bill_view_mode']);
	switch ($bill_view_mode)
	{
		default:
			$content['bill_view_mode'] = "";
		break;
		case "1":
			$content['bill_view_mode'] = "Заголовок с текстом";
		break;
		case "2":
			$content['bill_view_mode'] = "Только заголовок";
		break;
	}


	$content['sections'] = array();
	$sections = explode("|", stripslashes($row['sections']));

	$cat = new Category();
	foreach($sections as $k => $v)
	{
		debug ("sections: ");
		dump($sections);
		if ("subcats" == substr($v, 0, 7))
		{
			$parent = substr($v, 8);
			debug ("parent: ".$parent);
			$content['sections'][]['title'] = $cat -> get_title("ksh_bills_categories", $parent)." и все подкатегории";
		}
		else
			$content['sections'][]['title'] = $cat -> get_title("ksh_bills_categories", $v);
	}

	debug ("*** end: BB: view ***");
	return $content;	
}

function view_by_category()
{
	global $user;
	global $config;
	debug ("*** BB: view_by_category ***");
	$content = array(
		'content' => '',
		'result' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'category_title' => '',
		'category_id' => '',
		'bbs' => ''
	);

	$priv = new Privileges();

	if ($priv -> has("bbcpanel", "admin", "write"))
		$content['show_admin_link'] = "yes";

	if ($priv -> has("bbcpanel", "bb_add", "write"))
		$content['show_add_link'] = "yes";

	if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

	if (isset($_GET['category']))
	{
		$category = $_GET['category'];
		$content['category_id'] = $category;
		$sql_query = "SELECT `title` FROM `ksh_bbcpanel_categories` WHERE `id` = '".$category."'";
		$result = exec_query($sql_query);
		$cat = mysql_fetch_array($result);
		mysql_free_result($result);
		$content['category_title'] = stripslashes($cat['title']);

		$config['themes']['page_title'] .= " - ".$content['category_title'];

		$sql_query = "SELECT * from `ksh_bbcpanel_bbs` WHERE `category` = '".mysql_real_escape_string($category)."'";
	}
	else
	{
		$sql_query = "SELECT * from `ksh_bbcpanel_bbs` ORDER BY `id`";
	}

	$i = 0;
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$content['bbs'][$i]['id'] = stripslashes($row['id']);
		$content['bbs'][$i]['title'] = stripslashes($row['title']);
		$content['bbs'][$i]['name'] = stripslashes($row['name']);
		$content['bbs'][$i]['url'] = stripslashes($row['url']);
		$content['bbs'][$i]['instroot'] = stripslashes($row['instroot']);
		/*
		$content['bbs'][$i]['sections'] = stripslashes($row['sections']);
		$content['bbs'][$i]['bills_per_page'] = stripslashes($row['bills_per_page']);
		$content['bbs'][$i]['theme'] = stripslashes($row['theme']);
		if ("0" == $content['bbs'][$i]['theme'])
			$content['bbs'][$i]['theme'] = "default";
*/
		$sat = new Satellite;
		$sat -> id = $content['bbs'][$i]['id'];
		$sat -> url = $sat -> get_url();
		//$state = $sat -> get_state();
		$state = 1;
		if ($state)
			$content['bbs'][$i]['state_up'] = "yes";
		else
			$content['bbs'][$i]['state_down'] = "no";

		$sql_query = "SELECT `title` FROM `ksh_bbcpanel_categories` WHERE `id` = '".$row['category']."'";
		$result_category = exec_query($sql_query);
		$row_category = mysql_fetch_array($result_category);
		mysql_free_result($result_category);
		$content['bbs'][$i]['category_title'] = stripslashes($row_category['title']);
/*
		$bill_view_mode = stripslashes($row['bill_view_mode']);
		switch ($bill_view_mode)
		{
			default:
				$content['bbs'][$i]['bill_view_mode'] = "";
			break;
			case "1":
				$content['bbs'][$i]['bill_view_mode'] = "Заголовок с текстом";
			break;
			case "2":
				$content['bbs'][$i]['bill_view_mode'] = "Только заголовок";
			break;
		}
*/

		if ($priv -> has("bbcpanel", "bb_edit", "write"))
			$content['bbs'][$i]['show_edit_link'] = "yes";
		else
			$content['bbs'][$i]['show_edit_link'] = "";

		if ($priv -> has("bbcpanel", "bb_del", "write"))
			$content['bbs'][$i]['show_del_link'] = "yes";
		else
			$content['bbs'][$i]['show_del_link'] = "";

		$i++;
	}
	mysql_free_result($result);

	debug ("*** end: BB: view_by_category ***");
	return $content;	
}

function get_select()
{
	global $user;
	global $config;
	debug ("*** BB: get_select ***");

	$bbs = array();
	
	$sql_query = "SELECT `id`, `title` FROM `ksh_bbcpanel_bbs` ORDER BY `id`";
	$result = exec_query($sql_query);
	$i = 0;
	while ($row = mysql_fetch_array($result))
	{
		$bbs[$i]['id'] = stripslashes($row['id']);
		$bbs[$i]['title'] = stripslashes($row['title']);
		$i++;
	}
	mysql_free_result($result);

	debug ("*** end: BB: get_select ***");
	return $bbs;
}

function titles_edit()
{
	global $user;
	global $config;
	debug ("*** BB: titles_edit ***");

	$content = array(
		'id' => '',
		'show_bb_select_form' => '',
		'show_titles_form' => '',
		'titles' => '',
		'bbs_select' => ''
	);

	$bb_id = 0;
	if (isset($_GET['bb']))
		$bb_id = $_GET['bb'];
	else if (isset($_POST['bb']))
		$bb_id = $_POST['bb'];
	if (!$bb_id)
	{
		debug ("don't have bb, show select form");
		$content['show_bb_select_form'] = "yes";
		$content['bbs_select'] = $this -> get_select();
	}
	else
	{
		debug ("have bb");
		$content['show_titles_form'] = "yes";
		$content['id'] = $bb_id;
		$cat = new Category();
		$content['categories_select'] = $cat -> get_select("ksh_bills_categories");

		if (isset($_POST['do_update']))
		{
			if ("" != $_POST['new_category'] && "" != $_POST['new_title'])
			{
				if ("" == $_POST['new_name'])
					$name = transliterate($_POST['new_title'], "ru", "en");
				else
					$name = $_POST['new_name'];

				$name = str_replace("/", "", $name);

				$sql_query = "INSERT INTO `ksh_bbcpanel_titles`
					(`bb`, `category`, `name`, `title`)
					VALUES (
					'".mysql_real_escape_string($bb_id)."',
					'".mysql_real_escape_string($_POST['new_category'])."',
					'".mysql_real_escape_string($name)."',
					'".mysql_real_escape_string($_POST['new_title'])."'
					)";
				exec_query($sql_query);
			}
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

					$sql_query = "UPDATE `ksh_bbcpanel_titles` SET
						`category` = '".mysql_real_escape_string($_POST['category_'.$v])."',
						`name` = '".mysql_real_escape_string($name)."',
						`title` = '".mysql_real_escape_string($_POST['title_'.$v])."'
						WHERE `id` = '".mysql_real_escape_string($v)."'";
					exec_query($sql_query);
				}
				else
				{
					$sql_query = "DELETE FROM `ksh_bbcpanel_titles` WHERE `id` = '".mysql_real_escape_string($v)."'";
					$result = exec_query($sql_query);
				}
			}
			$this -> sections_update($bb_id);				
		}

		$sql_query = "SELECT * FROM `ksh_bbcpanel_titles` WHERE `bb` = '".mysql_real_escape_string($bb_id)."' ORDER BY `id`";
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
			$categories = $cat -> get_select("ksh_bills_categories", $category);
			debug("categories list:");
			dump($categories);
			foreach($categories as $k => $v)
				$content['titles'][$i]['categories_select'] .= gen_content("base", "list_categories_select", $v);
			$i++;
		}
		mysql_free_result($result);
	}


	debug ("*** end: BB: titles_edit ***");
	return $content;
}

function tparts_edit()
{
	global $user;
	global $config;
	debug ("*** BB: tparts_edit ***");

	$content = array(
		'id' => '',
		'show_bb_select_form' => '',
		'show_tparts_form' => '',
		'tparts' => '',
		'bbs_select' => ''
	);

	$bb_id = 0;
	if (isset($_GET['bb']))
		$bb_id = $_GET['bb'];
	else if (isset($_POST['bb']))
		$bb_id = $_POST['bb'];
	if (!$bb_id)
	{
		debug ("don't have bb, show select form");
		$content['show_bb_select_form'] = "yes";
		$content['bbs_select'] = $this -> get_select();
	}
	else
	{
		debug ("have bb");
		$content['show_tparts_form'] = "yes";
		$content['id'] = $bb_id;

		if (isset($_POST['do_update']))
		{
			debug("POST:");
			dump($_POST);
			if ("" != $_POST['new_title'] && "" != $_POST['new_tpart'])
				$this -> tpart_send($bb_id, 0, $_POST['new_title'], $_POST['new_tpart']);

			foreach($_POST['entries'] as $k => $v)
				$this -> tpart_send($bb_id, $v, $_POST['title_'.$v], $_POST['tpart_'.$v]);
		}

		$content['tparts'] = $this -> get_tparts($bb_id);
	}


	debug ("*** end: BB: titles_edit ***");
	return $content;
}

function get_url($bb_id = 0)
{
	global $user;
	global $config;
	debug ("*** BB: get_url ***");
	$url = "";

	if ($bb_id)
	{
		$sql_query = "SELECT `url` FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($bb_id)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$url = stripslashes($row['url']);

		if ("http://" == substr($url, 0, 7))
			$url = substr($url, 7);

		$url = rtrim($url, "/");			
	}
	debug ("url: ".$url);

	debug ("*** end: BB: get_url ***");
	return $url;
}

function get_title($bb_id = 0)
{
	global $user;
	global $config;
	debug ("*** BB: get_title ***");
	$url = "";

	if ($bb_id)
	{
		$sql_query = "SELECT `title` FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($bb_id)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$title = stripslashes($row['title']);
	}
	debug ("title: ".$title);

	debug ("*** end: BB: get_title ***");
	return $title;
}


function sections_update($bb_id)
{
	global $user;
	global $config;
	debug ("*** BB: sections_update ***");

	$content = array(
		'result' => ''
	);

	$client =new xmlrpc_client("/modules/bills/xmlrpcserver.php", $this -> get_url($bb_id));
	$message =new xmlrpcmsg('sections_update', array(
		new xmlrpcval($config['bbcpanel']['password'])
		));
	$response =$client->send($message);


	if (!$response ->faultCode(  ))
		debug("Ответ XMLRPC сервера: ".htmlentities($response->serialize()));
	else
	    debug("Проблема: Код: " . $response->faultCode(  ) . " Причина '" .$response->faultString(  )."'");

	debug ("*** end: BB: sections_update ***");
	return $content;
}

function users_update($bb_id)
{
	global $user;
	global $config;
	debug ("*** BB: users_update ***");

	$content = array(
		'result' => ''
	);

	$client =new xmlrpc_client("/modules/users/xmlrpcserver.php", $this -> get_url($bb_id));
	$message =new xmlrpcmsg('users_update', array(
		new xmlrpcval($config['bbcpanel']['password'])
		));
	$response =$client->send($message);


	if (!$response ->faultCode(  ))
		debug("Ответ XMLRPC сервера: ".htmlentities($response->serialize()));
	else
	    debug("Проблема: Код: " . $response->faultCode(  ) . " Причина '" .$response->faultString(  )."'");

	debug ("*** end: BB: users_update ***");
	return $content;
}

function sections_update_all()
{
	global $user;
	global $config;
	debug ("*** BB: sections_update_all ***");
	$content = array(
	);

	$sql_query = "SELECT `id` FROM `ksh_bbcpanel_bbs`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$this -> sections_update(stripslashes($row['id']));
	}
	mysql_free_result($result);

	debug ("*** end: BB: sections_update_all ***");
	return $content;
}

function users_update_all()
{
	global $user;
	global $config;
	debug ("*** BB: users_update_all ***");
	$content = array(
	);

	$sql_query = "SELECT `id` FROM `ksh_bbcpanel_bbs`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$this -> users_update(stripslashes($row['id']));
	}
	mysql_free_result($result);

	debug ("*** end: BB: users_update_all ***");
	return $content;
}

function get_tparts($bb_id)
{
	global $user;
	global $config;
	debug ("*** BB: get_tparts ***");

	$sat = new Satellite;
	$sat -> id = $bb_id;

	$content = array(
	);

	$serv_path = $sat -> get_instroot()."/modules/themes/xmlrpcserver.php";
	debug("xmlrpc server path: ".$serv_path);

	$client =new xmlrpc_client($serv_path, $this -> get_url($bb_id));
	$message =new xmlrpcmsg('get_tparts', array(
		new xmlrpcval($config['bbcpanel']['password'])
		));
	$response =$client->send($message);


	if (!$response ->faultCode(  ))
	{
		debug("Ответ XMLRPC сервера: ".htmlentities($response->serialize()));
		$v=$response->value();
		for($a=0; $a<$v->arraysize(  ); $a++)
		{
			$z=$v->arraymem($a);

			$struct_id = $z -> structmem("id");
			$struct_title = $z -> structmem("title");
			$struct_tpart = $z -> structmem("tpart");

			$content[$a]['id'] = $struct_id->scalarval();
			$content[$a]['title'] = htmlspecialchars($struct_title->scalarval());
			$content[$a]['tpart'] = htmlspecialchars(base64_decode($struct_tpart->scalarval()));
		}

	}
	else
	    debug("Проблема: Код: " . $response->faultCode(  ) . " Причина '" .$response->faultString(  )."'");

	debug ("*** end: BB: get_tparts ***");
	return $content;
}

function tpart_send($bb_id, $id, $title, $tpart)
{
	global $user;
	global $config;
	debug ("*** BB: tpart_send ***");

	$content = array(
		'result' => ''
	);

	debug("bb_id: ".$bb_id);
	debug("tpart id: ".$id);
	debug("title: ".$title);

	$sat = new Satellite;
	$sat -> id = $bb_id;

	$serv_path = $sat -> get_instroot()."/modules/themes/xmlrpcserver.php";
	debug("xmlrpc server path: ".$serv_path);

	$client =new xmlrpc_client($serv_path, $this -> get_url($bb_id));
	$message =new xmlrpcmsg('tpart_receive', array(
		new xmlrpcval($config['bbcpanel']['password'], "string"),
		new xmlrpcval($id, "string"),
		new xmlrpcval($title, "string"),
		new xmlrpcval(base64_encode($tpart), "string")
		));
	$response =$client->send($message);


	if (!$response ->faultCode(  ))
		debug("Ответ XMLRPC сервера: ".htmlentities($response->serialize()));
	else
	    debug("Проблема: Код: " . $response->faultCode(  ) . " Причина '" .$response->faultString(  )."'");

	debug ("*** end: BB: tpart_send ***");
	return $content;
}

function update_all()
{
	global $user;
	global $config;
	debug ("*** BB: update_all ***");

	$content = array(
		'id' => '',
		'show_bb_select_form' => '',
		'bbs_select' => '',
		'result' => ''
	);

	$bb_id = 0;
	if (isset($_GET['bb']))
		$bb_id = $_GET['bb'];
	else if (isset($_POST['bb']))
		$bb_id = $_POST['bb'];
	if (!$bb_id)
	{
		debug ("don't have bb, show select form");
		$content['show_bb_select_form'] = "yes";
		$content['bbs_select'] = $this -> get_select();
	}
	else
	{
		debug ("have bb");
		$content['show_update_form'] = "yes";
		$content['id'] = $bb_id;

		if (isset($_POST['do_update']))
		{
			$content['show_update_form'] = "";

			$client =new xmlrpc_client("/modules/updater/xmlrpcserver.php", $this -> get_url($bb_id));
			$message =new xmlrpcmsg('update_all', array(
				new xmlrpcval($config['bbcpanel']['password'], "string"),
				));
			$response =$client->send($message);


			if (!$response ->faultCode(  ))
			{
				debug("Ответ XMLRPC сервера: ".htmlentities($response->serialize()));
				$content['result'] .= strip_tags($response->serialize());
			}
			else
			{
			    debug("Проблема: Код: " . $response->faultCode(  ) . " Причина '" .$response->faultString(  )."'");
				$content['result'] .= "Проблема: Код: " . $response->faultCode(  ) . " Причина '" .$response->faultString(  )."'";
			}


		}
	}


	debug ("*** end: BB: titles_edit ***");
	return $content;

}

function get_ctitle($bb_id, $category_id)
{
	global $user;
	global $config;
	debug ("*** BB: get_ctitle ***");

	$sql_query = "SELECT `title` FROM `ksh_bbcpanel_titles` WHERE
		`bb` = '".mysql_real_escape_string($bb_id)."' AND
		`category` = '".mysql_real_escape_string($category_id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$ctitle = stripslashes($row['title']);
	mysql_free_result($result);

	debug ("*** end: BB: get_ctitle ***");
	return $ctitle;
}

function get_cname($bb_id, $category_id)
{
	global $user;
	global $config;
	debug ("*** BB: get_name ***");

	$sql_query = "SELECT `name` FROM `ksh_bbcpanel_titles` WHERE
		`bb` = '".mysql_real_escape_string($bb_id)."' AND
		`category` = '".mysql_real_escape_string($category_id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$cname = stripslashes($row['name']);
	mysql_free_result($result);

	debug ("*** end: BB: get_cname ***");
	return $cname;
}

}
?>
