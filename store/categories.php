<?php

// Categories functions of the "store" module

function store_categories_view_all()
{
	debug ("*** store_categories_view_all ***");
    global $config;
	global $user;

	$content = array(
		'content' => '',
		'result' => '',
		'categories' => '',
		'goods' => '',
		'name' => ''
	);


	if (isset($_POST['do_del']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, deleting from DB");
			exec_query ("delete from ksh_store_categories where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Категория удалена";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Категория не удалена";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	if (isset($_GET['category_move']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, moving from DB");
			if ("up" == $_GET['category_move'])
			{
				$operator = "<";
				$order = "DESC";
			}
			else if ("down" == $_GET['category_move'])
			{
				$operator = ">";
				$order = "ASC";
			}
			else
			{
				$operator = "=";
				$order = "ASC";
			}

			$id = $_GET['category'];
			debug ("id: ".$id);
			$position = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_categories WHERE id='".mysql_real_escape_string($id)."'"), 0, 0));
			debug ("position: ".$position);

			$next_qty = stripslashes(mysql_query(exec_query("SELECT count(*) FROM ksh_store_categories WHERE position ".mysql_real_escape_string($operator)." '".mysql_real_escape_string($position)."'"), 0, 0));
			if ("0" != $next_qty)
			{

				$result = exec_query("SELECT id, position FROM ksh_store_categories WHERE position ".mysql_real_escape_string($operator)." '".mysql_real_escape_string($position)."' ORDER BY position ".mysql_real_escape_string($order)." LIMIT 1");
				$next_category = mysql_fetch_array($result);
				$next_id = stripslashes($next_category['id']);
				debug ("next id: ".$next_id);
				$next_position = stripslashes($next_category['position']);
				debug ("next position: ".$next_position);
				exec_query("UPDATE ksh_store_categories SET position='".mysql_real_escape_string($next_position)."' WHERE id='".mysql_real_escape_string($id)."'");
				exec_query("UPDATE ksh_store_categories SET position='".mysql_real_escape_string($position)."' WHERE id='".mysql_real_escape_string($next_id)."'");
				$content['result'] = "Категория подвинута";
			}
			else
				$content['result'] = "Некуда двигать";


		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Не могу подвинуть";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	if (isset($_POST['do_cart_out']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, updating DB");

			$date = date("Y-m-d");
    		debug ("date: ".$date);
    		$time = date("H:i:s");
    		debug ("time: ".$time);

			$result = exec_query("SELECT * FROM ksh_store_cart");
			while ($good = mysql_fetch_array($result))
			{

				exec_query ("INSERT INTO ksh_store_inout (way, good, object, user, qty, date, time, commentary) VALUES ('out', '".mysql_real_escape_string($good['good'])."', '".mysql_real_escape_string($_POST['object'])."', '".mysql_real_escape_string($_POST['user'])."', '".mysql_real_escape_string($good['qty'])."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string($time)."', '".mysql_real_escape_string($_POST['commentary'])."')");

				$good_qty = stripslashes(mysql_result(exec_query("SELECT qty FROM ksh_store_goods WHERE id='".mysql_real_escape_string($good['good'])."'"), 0, 0));
				exec_query ("UPDATE ksh_store_goods SET qty='".mysql_real_escape_string($good_qty - $good['qty'])."' WHERE id='".mysql_real_escape_string($good['good'])."'");

				exec_query ("DELETE FROM ksh_store_cart WHERE id='".$good['id']."'");
			}
			$content['result'] = "Товары выданы";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Товары не выданы";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}


	if (isset($_POST['do_out']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, updating DB");

			$date = date("Y-m-d");
    		debug ("date: ".$date);
    		$time = date("H:i:s");
    		debug ("time: ".$time);

			exec_query ("INSERT INTO ksh_store_inout (way, good, object, user, qty, date, time, commentary) VALUES ('out', '".mysql_real_escape_string($_POST['good'])."', '".mysql_real_escape_string($_POST['object'])."', '".mysql_real_escape_string($_POST['user'])."', '".mysql_real_escape_string($_POST['qty'])."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string($time)."', '".mysql_real_escape_string($_POST['commentary'])."')");

			$good_qty = stripslashes(mysql_result(exec_query("SELECT qty FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_POST['good'])."'"), 0, 0));
			exec_query ("UPDATE ksh_store_goods SET qty='".mysql_real_escape_string($good_qty - $_POST['qty'])."' WHERE id='".mysql_real_escape_string($_POST['good'])."'");

			$content['result'] = "Товар выдан";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Товар не выдан";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	if (isset($_POST['do_out_from_category']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, updating DB");

			$date = date("Y-m-d");
    		debug ("date: ".$date);
    		$time = date("H:i:s");
    		debug ("time: ".$time);

			exec_query ("INSERT INTO ksh_store_inout (way, good, object, user, qty, date, time, commentary) VALUES ('out', '".mysql_real_escape_string($_POST['good'])."', '".mysql_real_escape_string($_POST['object'])."', '".mysql_real_escape_string($_POST['user'])."', '".mysql_real_escape_string($_POST['qty'])."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string($time)."', '".mysql_real_escape_string($_POST['commentary'])."')");

			$good_qty = stripslashes(mysql_result(exec_query("SELECT qty FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_POST['good'])."'"), 0, 0));
			exec_query ("UPDATE ksh_store_goods SET qty='".mysql_real_escape_string($good_qty - $_POST['qty'])."' WHERE id='".mysql_real_escape_string($_POST['good'])."'");

			$content['result'] = "Товар выдан";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Товар не выдан";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}


	if (isset($_POST['do_in']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, updating DB");

			$date = date("Y-m-d");
    		debug ("date: ".$date);
    		$time = date("H:i:s");
    		debug ("time: ".$time);

			exec_query ("INSERT INTO ksh_store_inout (way, good, object, user, qty, date, time, commentary) VALUES ('in', '".mysql_real_escape_string($_POST['good'])."', '".mysql_real_escape_string($_POST['object'])."', '".mysql_real_escape_string($_POST['user'])."', '".mysql_real_escape_string($_POST['qty'])."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string($time)."', '".mysql_real_escape_string($_POST['commentary'])."')");

			$good_qty = stripslashes(mysql_result(exec_query("SELECT qty FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_POST['good'])."'"), 0, 0));
			exec_query ("UPDATE ksh_store_goods SET qty='".mysql_real_escape_string($good_qty + $_POST['qty'])."' WHERE id='".mysql_real_escape_string($_POST['good'])."'");

			$content['result'] = "Товар получен";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Товар не получен";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}



	$result = exec_query("SELECT * FROM ksh_store_categories ORDER BY position ASC");
	$i = 0;
	while ($category = mysql_fetch_array($result))
	{
		$content['categories'][$i]['id'] = stripslashes($category['id']);
		$content['categories'][$i]['position'] = stripslashes($category['position']);
		$content['categories'][$i]['name'] = stripslashes($category['name']);
		if (stripslashes($category['id']) == $_GET['categories'])
			$content['name'] = stripslashes($category['name']);
		$i++;
	}
	mysql_free_result($result);

	if (isset($_GET['categories']))
	{
		$res = exec_query("SELECT * FROM ksh_store_goods WHERE category='".$_GET['categories']."' AND status='0' ORDER BY position ASC");
		$i = 0;
		while ($good = mysql_fetch_array($res))
    	{
			$content['goods'][$i]['id'] = stripslashes($good['id']);
			$content['goods'][$i]['name'] = stripslashes($good['name']);
			$content['goods'][$i]['qty'] = stripslashes($good['qty']);
			$content['goods'][$i]['measure'] = stripslashes($good['measure']);
			$content['goods'][$i]['price'] = stripslashes($good['price']);
			$content['goods'][$i]['commentary'] = stripslashes($good['commentary']);
			$i++;
    	}
		mysql_free_result($res);
	}


    debug ("*** end: store_categories_view_all ***");
    return $content;
}



function store_categories_add()
{
	debug ("*** store_category_add ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		if (isset($_POST['do_add']))
		{
			$position = stripslashes(mysql_result(exec_query("SELECT max(position) FROM ksh_store_categories"), 0, 0)) + 1;

			exec_query("INSERT INTO ksh_store_categories (name, position) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($position)."')");

			$content['result'] = "Категория добавлена";
		}
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}



	debug ("*** end:store_category_add ***");
	return $content;
}

function store_categories_edit()
{
	debug ("*** store_categories_edit ***");
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
			exec_query ("update ksh_store_categories set name='".mysql_real_escape_string($_POST['name'])."' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Изменения записаны";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Изменения не записаны";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT id,name,position FROM ksh_store_categories WHERE id='".mysql_real_escape_string($_GET['categories'])."'");
	$category = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($category['id']);
	$content['name'] = stripslashes($category['name']);
	$content['position'] = stripslashes($category['position']);


	debug ("*** end:store_categories_edit ***");
	return $content;
}

function store_categories_del()
{
	debug ("*** store_categories_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	$result = exec_query("select name from ksh_store_categories where id='".mysql_real_escape_string($_GET['categories'])."'");
	$content['id'] = $_GET['categories'];
	$content['name'] = stripslashes(mysql_result($result, 0, 0));
	mysql_free_result ($result);

	$result = exec_query("select count(*) from ksh_store_goods where category='".mysql_real_escape_string($_GET['categories'])."'");
	$goods_qty = mysql_result($result, 0, 0);
	debug ("goods qty: ".$goods_qty);
	mysql_free_result($result);
	if ("0" != $goods_qty)
		$content['content'] = "Внимание! Категория <b>".$content['name']."</b> содержит в себе товары!";

	debug ("*** end:store_categories_del ***");
	return $content;
}


function store_categories_list()
{
	debug ("*** store_categories_list ***");
	global $config;
	$i = 0;
	$result = exec_query ("select id,name,position from ksh_store_categories order by position");
	while ($category = mysql_fetch_array($result))
	{
		$categories[$i]['id'] = stripslashes($category['id']);
		$categories[$i]['name'] = stripslashes($category['name']);
		$categories[$i]['position'] = stripslashes($category['position']);
		$i++;
	}
	mysql_free_result($result);
	debug ("*** end: store_categories_list ***");
	return $categories;
}


function store_categories_sort()
{
	debug ("*** store_categories_sort ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'categories_select' => '',
		'id' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		if (isset($_POST['do_sort']))
		{
			unset ($_POST['do_sort']);

			$_POST['position'] = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_categories WHERE id='".$_POST['position']."'"), 0, 0));

			$result = exec_query ("SELECT id, position FROM ksh_store_categories WHERE position >= '".mysql_real_escape_string($_POST['position'])."' ORDER BY position ASC");
			while ($row = mysql_fetch_array($result))
			{
				exec_query ("UPDATE ksh_store_categories SET position='".($row['position'] + 1)."' WHERE id='".$row['id']."'");
			}
			mysql_free_result($result);

			exec_query("UPDATE ksh_store_categories SET position='".$_POST['position']."' WHERE id='".mysql_real_escape_string($_POST['id'])."'");
		}

		$content['id'] = $_GET['categories'];

		$category_id = mysql_result(exec_query("SELECT id FROM ksh_store_categories WHERE id='".mysql_real_escape_string($_GET['categories'])."'"), 0, 0);

		$result = exec_query("SELECT id, name FROM ksh_store_categories WHERE id != '".mysql_real_escape_string($_GET['categories'])."' ORDER BY position ASC");

		$i = 0;
		while ($category = mysql_fetch_array($result))
		{
			$content['categories_select'][$i]['id'] = stripslashes($category['id']);
			$content['categories_select'][$i]['name'] = stripslashes($category['name']);
			$i++;
		}
		mysql_free_result($result);
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_categories_sort ***");
	return $content;
}


?>