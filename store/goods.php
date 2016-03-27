<?php

// Goods administration functions of the "store" module

function store_goods_add()
{
	debug ("*** store_goods_add ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'categories_select' => '',
		'category_id' => '',
		'goods_select' => ''
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

	        unset ($_POST['do_add']);
			$object = $_POST['object'];
			unset ($_POST['object']);
			$user = $_POST['user'];
			unset ($_POST['user']);

			$_POST['qty'] = str_replace(",", ".", $_POST['qty']);
			$_POST['status'] = "0";

			if ("0" == $_POST['position'])
			{
				$result = exec_query("SELECT MAX(position) FROM ksh_store_goods");
				$max_position = stripslashes(mysql_result($result, 0, 0));
				mysql_free_result($result);
				$_POST['position'] = $max_position + 1;
			}
			else
			{

				$_POST['position'] = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_goods WHERE id='".$_POST['position']."'"), 0, 0));

				$result = exec_query ("SELECT id, position FROM ksh_store_goods WHERE position >= '".mysql_real_escape_string($_POST['position'])."' ORDER BY position ASC");
				while ($row = mysql_fetch_array($result))
				{
					exec_query ("UPDATE ksh_store_goods SET position='".($row['position'] + 1)."' WHERE id='".$row['id']."'");
				}
				mysql_free_result($result);
			}

        	foreach ($_POST as $k => $v)
        	{
	            $fields .= $k.",";
            	$values .= "'".mysql_real_escape_string($v)."',";
        	}

        	$sql_query = "INSERT INTO ksh_store_goods (".ereg_replace(",$","",$fields).") values (".ereg_replace(",$","",$values).")";

        	exec_query ($sql_query);

			$content['result'] = "Товар добавлен";

			$date = date("Y-m-d");
    		debug ("date: ".$date);
    		$time = date("H:i:s");
    		debug ("time: ".$time);
			$good = mysql_insert_id();
			debug ("good: ".$good);

			exec_query ("INSERT INTO ksh_store_inout (way, good, object, user, qty, date, time, commentary) VALUES ('in', '".mysql_real_escape_string($good)."', '".mysql_real_escape_string($object)."', '".mysql_real_escape_string($user)."', '".mysql_real_escape_string($_POST['qty'])."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string($time)."', '".mysql_real_escape_string($_POST['commentary'])."')");
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Товар не добавлен";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}


	$content['category_id'] = $_GET['category'];

	$categories = store_categories_list();
    foreach ($categories as $k => $v)
	{
		$content['categories_select'][$k]['id'] = $v['id'];
		$content['categories_select'][$k]['name'] = $v['name'];
		if ($v['id'] == $content['category_id'])
			$content['categories_select'][$k]['selected'] = "yes";
	}

	$result = exec_query("SELECT id, name FROM ksh_store_goods WHERE category='".mysql_real_escape_string($_GET['category'])."' ORDER BY position ASC");
	$i = 0;
	while ($good = mysql_fetch_array($result))
	{
		$content['goods_select'][$i]['id'] = stripslashes($good['id']);
		$content['goods_select'][$i]['name'] = stripslashes($good['name']);
		$i++;
	}
	mysql_free_result($result);
	$content['goods_select'][$i]['id'] = 0;
	$content['goods_select'][$i]['name'] = "В самый конец";
	$content['goods_select'][$i]['selected'] = "yes";
	$i++;

	$result = exec_query("SELECT * FROM ksh_store_objects WHERE status='0'");
	$i = 0;
	while ($object = mysql_fetch_array($result))
	{
		$content['objects_select'][$i]['id'] = stripslashes($object['id']);
		$content['objects_select'][$i]['name'] = stripslashes($object['name']);
		$i++;
	}
	mysql_free_result($result);

	$result = exec_query("SELECT * FROM ksh_store_users WHERE status='0'");
	$i = 0;
	while ($user = mysql_fetch_array($result))
	{
		$content['users_select'][$i]['id'] = stripslashes($user['id']);
		$content['users_select'][$i]['name'] = stripslashes($user['name']);
		$i++;
	}
	mysql_free_result($result);

	debug ("*** end:store_goods_add ***");
	return $content;
}

function store_view_by_categories()
{
	debug ("*** store_view_by_categories ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'category_name' => '',
		'category_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'pages' => '',
		'goods_by_category' => ''
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
			//exec_query ("delete from ksh_store_goods where id='".mysql_real_escape_string($_POST['id'])."'");
			exec_query ("update ksh_store_goods set status='1' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Товар удалён";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Товар не удалён";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}



	if (isset($_GET['good_move']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, moving from DB");
			if ("up" == $_GET['good_move'])
			{
				$operator = "<";
				$order = "DESC";
			}
			else if ("down" == $_GET['good_move'])
			{
				$operator = ">";
				$order = "ASC";
			}
			else
			{
				$operator = "=";
				$order = "ASC";
			}

			$id = $_GET['good'];
			debug ("id: ".$id);
			$position = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_goods WHERE id='".mysql_real_escape_string($id)."'"), 0, 0));
			debug ("position: ".$position);

			$category_id = stripslashes(mysql_result(exec_query("SELECT category FROM ksh_store_goods WHERE id='".mysql_real_escape_string($id)."'"), 0, 0));
			debug ("category_id: ".$category_id);

			$next_qty = stripslashes(mysql_query(exec_query("SELECT count(*) FROM ksh_store_goods WHERE category = '".$category_id."' AND position ".mysql_real_escape_string($operator)." '".mysql_real_escape_string($position)."'"), 0, 0));
			if ("0" != $next_qty)
			{

				$result = exec_query("SELECT id, position FROM ksh_store_goods WHERE category = '".$category_id."' AND position ".mysql_real_escape_string($operator)." '".mysql_real_escape_string($position)."' ORDER BY position ".mysql_real_escape_string($order)." LIMIT 1");
				$next_good = mysql_fetch_array($result);
				$next_id = stripslashes($next_good['id']);
				debug ("next id: ".$next_id);
				$next_position = stripslashes($next_good['position']);
				debug ("next position: ".$next_position);
				exec_query("UPDATE ksh_store_goods SET position='".mysql_real_escape_string($next_position)."' WHERE id='".mysql_real_escape_string($id)."'");
				exec_query("UPDATE ksh_store_goods SET position='".mysql_real_escape_string($position)."' WHERE id='".mysql_real_escape_string($next_id)."'");
				$content['result'] = "Товар подвинут";
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

	if (isset($_GET['categories']))
		$id = $_GET['categories'];
	else if (isset($_POST['categories']))
		$id = $_POST['categories'];
	else
		$id = 0;

	$content['category_id'] = $id;
	$content['category_name'] = mysql_result(exec_query("SELECT name FROM ksh_store_categories WHERE id='".$id."'"),0,0);


	$result = exec_query("SELECT * FROM ksh_store_goods WHERE category='".mysql_real_escape_string($id)."' ORDER BY position ASC");

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		$content['goods_by_category'][$i]['id'] = stripslashes($good['id']);
		$content['goods_by_category'][$i]['name'] = stripslashes($good['name']);
		$content['goods_by_category'][$i]['qty'] = stripslashes($good['qty']);
		$content['goods_by_category'][$i]['category'] = stripslashes($good['category']);
		$content['goods_by_category'][$i]['price'] = stripslashes($good['price']);
		$content['goods_by_category'][$i]['measure'] = stripslashes($good['measure']);
		$content['goods_by_category'][$i]['commentary'] = stripslashes($good['commentary']);
		if ("0" == stripslashes($good['status']))
			$content['goods_by_category'][$i]['status'] = "в наличии";
		else
			$content['goods_by_category'][$i]['status'] = "удалён";
		$content['goods_by_category'][$i]['show_edit_link'] = "yes";
		$content['goods_by_category'][$i]['show_del_link'] = "yes";
		$content['goods_by_category'][$i]['show_in_link'] = "yes";
		$content['goods_by_category'][$i]['show_out_link'] = "yes";
		$i++;
    }

    mysql_free_result($result);



	debug ("*** end:store_view_by_categories ***");
	return $content;
}

function store_goods_del()
{
	debug ("*** store_goods_del ***");
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

	$result = exec_query("select name,category from ksh_store_goods where id='".mysql_real_escape_string($_GET['goods'])."'");
	$good = mysql_fetch_array($result);
	mysql_free_result ($result);
	$content['id'] = $_GET['goods'];
	$content['name'] = stripslashes($good['name']);
	$content['category'] = stripslashes($good['category']);


	debug ("*** end:store_goods_del ***");
	return $content;
}

function store_goods_edit()
{
	debug ("*** store_goods_edit ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'image' => '',
		'images' => '',
		'authors' => '',
		'categories' => '',
		'genre' => '',
		'original_name' => '',
		'format' => '',
		'language' => '',
		'year' => '',
		'publisher' => '',
		'pages_qty' => '',
		'weight' => '',
		'new_qty' => '',
		'new_price' => '',
		'used_qty' => '',
		'used_price' => '',
		'commentary' => ''
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
			$id = mysql_real_escape_string($_POST['id']);
        	unset ($_POST['do_update']);
        	unset ($_POST['id']);

        	$sql_query = "UPDATE ksh_store_goods SET ";
        	foreach ($_POST as $k => $v) $sql_query .= $k."='".mysql_real_escape_string($v)."', ";
        	$sql_query = ereg_replace(", $","",$sql_query)." WHERE id='".$id."'";

        	exec_query ($sql_query);
			$content['result'] = "Изменения записаны";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Изменения не записаны";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT * FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_GET['goods'])."'");
	$good = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($good['id']);
	$content['category'] = stripslashes($good['category']);
	$content['name'] = stripslashes($good['name']);
	$content['qty'] = stripslashes($good['qty']);
	$content['price'] = stripslashes($good['price']);
	$content['commentary'] = stripslashes($good['commentary']);
	$content['measure'] = stripslashes($good['measure']);
	if ("0" == stripslashes($good['status']))
		$content['status_0'] = "yes";
	else
		$content['status_1'] = "yes";


	$categories = store_categories_list();
	foreach ($categories as $k => $v)
	{
		$content['categories'] .= "<option value=\"".$v['id']."\"";
		if ($good['category'] == $v['id']) $content['categories'] .= " selected";
		$content['categories'] .= ">".$v['name']."</option>";
	}


	debug ("*** end:store_goods_edit ***");
	return $content;
}

function store_goods_in()
{
	debug ("*** store_goods_in ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'objects_select' => '',
		'users_select' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		$result = exec_query("SELECT * FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_GET['goods'])."'");
		$good = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = stripslashes($good['id']);
		$content['name'] = stripslashes($good['name']);
		$content['price'] = stripslashes($good['price']);
		$content['measure'] = stripslashes($good['measure']);
		$content['commentary'] = stripslashes($good['commentary']);
		$content['qty'] = stripslashes($good['qty']);

		if ("0" == stripslashes($good['status']))
			$content['status'] = "в наличии";
		else
			$content['status'] = "удалён";

		$result = exec_query("SELECT * FROM ksh_store_objects WHERE status='0'");
		$i = 0;
		while ($object = mysql_fetch_array($result))
		{
			$content['objects_select'][$i]['id'] = stripslashes($object['id']);
			$content['objects_select'][$i]['name'] = stripslashes($object['name']);
			$i++;
		}
		mysql_free_result($result);

		$result = exec_query("SELECT * FROM ksh_store_users WHERE status='0'");
		$i = 0;
		while ($user = mysql_fetch_array($result))
		{
			$content['users_select'][$i]['id'] = stripslashes($user['id']);
			$content['users_select'][$i]['name'] = stripslashes($user['name']);
			$i++;
		}
		mysql_free_result($result);
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_goods_in ***");
	return $content;
}

function store_goods_out()
{
	debug ("*** store_goods_out ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'name' => '',
		'price' => '',
		'measure' => '',
		'commentary' => '',
		'qty' => '',
		'status' => '',
		'objects_select' => '',
		'users_select' => '',
		'qtys' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		$result = exec_query("SELECT * FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_GET['goods'])."'");
		$good = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = stripslashes($good['id']);
		$content['name'] = stripslashes($good['name']);
		$content['price'] = stripslashes($good['price']);
		$content['qty'] = stripslashes($good['qty']);
		$content['measure'] = stripslashes($good['measure']);
		$content['commentary'] = stripslashes($good['commentary']);
		$qty = stripslashes($good['qty']);
		for ($i = 1; $i <= $qty; $i++)
			$content['qtys'][$i]['qty'] = $i;
		if ("0" == stripslashes($good['status']))
			$content['status'] = "в наличии";
		else
			$content['status'] = "удалён";

		$result = exec_query("SELECT * FROM ksh_store_objects WHERE status='0'");
		$i = 0;
		while ($object = mysql_fetch_array($result))
		{
			$content['objects_select'][$i]['id'] = stripslashes($object['id']);
			$content['objects_select'][$i]['name'] = stripslashes($object['name']);
			$i++;
		}
		mysql_free_result($result);

		$result = exec_query("SELECT * FROM ksh_store_users WHERE status='0'");
		$i = 0;
		while ($user = mysql_fetch_array($result))
		{
			$content['users_select'][$i]['id'] = stripslashes($user['id']);
			$content['users_select'][$i]['name'] = stripslashes($user['name']);
			$i++;
		}
		mysql_free_result($result);



	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_goods_out ***");
	return $content;
}

function store_goods_out_from_category()
{
	debug ("*** store_goods_out ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'goods_select' => '',
		'objects_select' => '',
		'users_select' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		$result = exec_query("SELECT * FROM ksh_store_goods WHERE category='".mysql_real_escape_string($_GET['category'])."' AND status='0' ORDER BY position ASC");
		$i = 0;
		while ($good = mysql_fetch_array($result))
		{
			$content['goods_select'][$i]['id'] = stripslashes($good['id']);
			$content['goods_select'][$i]['name'] = stripslashes($good['name']);
			$i++;
		}
		mysql_free_result($result);


		$result = exec_query("SELECT * FROM ksh_store_objects WHERE status='0' ORDER BY position ASC");
		$i = 0;
		while ($object = mysql_fetch_array($result))
		{
			$content['objects_select'][$i]['id'] = stripslashes($object['id']);
			$content['objects_select'][$i]['name'] = stripslashes($object['name']);
			$i++;
		}
		mysql_free_result($result);

		$result = exec_query("SELECT * FROM ksh_store_users WHERE status='0' ORDER BY position ASC");
		$i = 0;
		while ($user = mysql_fetch_array($result))
		{
			$content['users_select'][$i]['id'] = stripslashes($user['id']);
			$content['users_select'][$i]['name'] = stripslashes($user['name']);
			$i++;
		}
		mysql_free_result($result);



	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_goods_out ***");
	return $content;
}


function store_goods_sort()
{
	debug ("*** store_goods_sort ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'goods_select' => '',
		'id' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		if (isset($_POST['do_sort']))
		{
			unset ($_POST['do_sort']);

			$_POST['position'] = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_goods WHERE id='".$_POST['position']."'"), 0, 0));

			$result = exec_query ("SELECT id, position FROM ksh_store_goods WHERE position >= '".mysql_real_escape_string($_POST['position'])."' ORDER BY position ASC");
			while ($row = mysql_fetch_array($result))
			{
				exec_query ("UPDATE ksh_store_goods SET position='".($row['position'] + 1)."' WHERE id='".$row['id']."'");
			}
			mysql_free_result($result);

			exec_query("UPDATE ksh_store_goods SET position='".$_POST['position']."' WHERE id='".mysql_real_escape_string($_POST['id'])."'");
		}

		$content['id'] = $_GET['goods'];

		$category_id = mysql_result(exec_query("SELECT category FROM ksh_store_goods WHERE id='".mysql_real_escape_string($_GET['goods'])."'"), 0, 0);

		$result = exec_query("SELECT id, name FROM ksh_store_goods WHERE category='".$category_id."' and id != '".mysql_real_escape_string($_GET['goods'])."' ORDER BY position ASC");

		$i = 0;
		while ($good = mysql_fetch_array($result))
		{
			$content['goods_select'][$i]['id'] = stripslashes($good['id']);
			$content['goods_select'][$i]['name'] = stripslashes($good['name']);
			$i++;
		}
		mysql_free_result($result);
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_goods_sort ***");
	return $content;
}

function store_goods_comment_view()
{
	debug ("*** store_goods_comment_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'commentary' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
	}
	$content['commentary'] = stripslashes(mysql_result(exec_query("SELECT commentary FROM ksh_store_goods WHERE id='".$_GET['goods']."'"), 0, 0));

	debug ("*** end:store_goods_comment_view ***");
	return $content;
}
?>