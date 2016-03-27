<?php

function shop_categories_view()
{
	debug ("*** shop_categories_view ***");
	global $config;
	global $user;
	$content = array(
		'result' => '',
		'content' => '',
		'categories' => '',
		'show_admin_link' => '',
		'show_add_link' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (isset($_POST['do_del']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, deleting from DB");
			exec_query ("delete from ksh_shop_categories where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Категория удалена";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Категория не удалена";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$content['categories'] = shop_categories_list();


	foreach ($content['categories'] as $k => $v)
	{
		if (1 == $user['id'])
		{
			$content['categories'][$k]['show_edit_link'] = "yes";
			$content['categories'][$k]['show_del_link'] = "yes";
		}
//		if ("0" != $v['parent']) $content['categories'] .= "<ul>";
//		$content['categories'] .= "<li>".$v['name']." <a href=\"/index.php?module=shop&action=edit_categories&command=edit_category&id=".$v['id']."\">Редактировать</a> <a href=\"/index.php?module=shop&action=edit_categories&command=del_category&id=".$v['id']."\">Удалить</a></li>";
//		if ("0" != $v['parent']) $content['categories'] .= "</ul>";
	}



	debug ("*** end: shop_categories_view ***");
	return $content;
}

function shop_categories_add()
{
	debug ("*** shop_categories_add ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'categories_select' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if (isset($_POST['do_add']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, inserting into DB");
			exec_query ("insert into ksh_shop_categories (name, parent, template) values ('".mysql_real_escape_string($_POST['name'])."','".mysql_real_escape_string($_POST['parent'])."', '".mysql_real_escape_string($_POST['template'])."')");
			$content['result'] = "Категория добавлена";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Категория не добавлена";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$content['categories_select'] = shop_categories_list();


	debug ("*** end:shop_categories_add ***");
	return $content;
}

function shop_categories_edit()
{
	debug ("*** shop_categories_edit ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'categories_select' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if (isset($_POST['do_update']))
	{
		if (1 == $user['id'])
		{
			exec_query ("update ksh_shop_categories set name='".mysql_real_escape_string($_POST['name'])."', parent='".mysql_real_escape_string($_POST['parent'])."', template='".mysql_real_escape_string($_POST['template'])."' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Изменения записаны";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Изменения не записаны";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT id,name,parent,template FROM ksh_shop_categories WHERE id='".mysql_real_escape_string($_GET['categories'])."'");
	$category = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($category['id']);
	$content['name'] = stripslashes($category['name']);
	$content['template'] = stripslashes($category['template']);

	$content['categories_select'] = shop_categories_list();
	foreach ($content['categories_select'] as $k => $v)
	{
		if ($category['parent'] == $v['id'])
			$content['categories_select'][$k]['selected'] = "yes";
		if ($category['id'] == $v['id'])
			unset ($content['categories_select'][$k]);
	}

	debug ("*** end:shop_categories_edit ***");
	return $content;
}

function shop_categories_del()
{
	debug ("*** shop_categories_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'if_subcats' => ''
	);

	if (isset($_POST['categories']))
		$id = $_POST['categories'];
	else if (isset($_GET['categories']))
		$id = $_GET['categories'];
	else if (isset($_GET['element']))
		$id = $_GET['element'];
	else
		$id = 0;

	$sql_query = "SELECT * FROM `ksh_shop_categories` WHERE `id` = '".mysql_real_escape_string($id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result ($result);
	foreach($row as $k => $v)
		$content[$k] = stripslashes($v);

	$sql_query = "SELECT COUNT(*) FROM `ksh_shop_categories` WHERE `parent` = '".mysql_real_escape_string($id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$qty = stripslashes($row['COUNT(*)']);
	if ("0" != $qty)
		$content['if_subcats'] = "yes";

	debug ("*** end:shop_categories_del ***");
	return $content;
}

function shop_categories_list()
{
	debug ("*** shop_categories_list ***");
	global $config;
	$i = 0;
	$result = exec_query ("select `id`, `title`, `parent` from `ksh_shop_categories` order by `id`");
	while ($category = mysql_fetch_array($result))
	{
		foreach($category as $k => $v)
			$categories[$i][$k] = stripslashes($v);
		$i++;
	}
	mysql_free_result($result);
	debug ("*** end: shop_categories_list ***");
	return $categories;
}

function shop_dyn_top_categories()
{
	debug ("*** shop_dyn_top_categories ***");
	global $config;
	global $user;

	$content = "";

	$cat = new Category();

	$i = 0;
	$cnt['categories_top'] = array();
	$categories_top = $cat -> get_categories_level("ksh_shop_categories", 0);
	foreach($categories_top as $k => $v)
	{
		$cnt['categories_top'][$i] = $cat -> get_category("ksh_shop_categories", $v);
		$i++;
	}

	$content = gen_content("shop", "dyn_categories_top", $cnt);


	debug ("*** end: shop_dyn_top_categories ***");
	return $content;
}



?>
