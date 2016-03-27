<?php

// Cart handling functions of the "store" module

function store_cart_add()
{
	debug ("*** store_cart_add ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		$_POST['qty'] = str_replace(",", ".", $_POST['qty']);

		if (isset($_POST['do_add']))
		{
			exec_query("INSERT INTO ksh_store_cart (good, qty) VALUES ('".mysql_real_escape_string($_POST['id'])."', '".mysql_real_escape_string($_POST['qty'])."')");
			$content['result'] = "Товар добавлен в корзину";
		}
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	$content['id'] = $_GET['goods'];

	debug ("*** end:store_cart_add ***");
	return $content;
}

function store_cart_out()
{
	debug ("*** store_cart_out ***");
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

		if (isset($_POST['do_del']))
		{
			exec_query("DELETE FROM ksh_store_cart WHERE id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Товар успешно удалён из корзины";
		}

		$result = exec_query("SELECT * FROM ksh_store_cart");
		$i = 0;
		while ($row = mysql_fetch_array($result))
		{
			$content['cart_goods'][$i]['id'] = stripslashes($row['id']);
			$content['cart_goods'][$i]['name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_store_goods WHERE id='".$row['good']."'"), 0, 0));
			$content['cart_goods'][$i]['qty'] = stripslashes($row['qty']);
			$i++;
		}
		mysql_free_result($result);

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

	debug ("*** end:store_cart_out ***");
	return $content;
}

function store_cart_del()
{
	debug ("*** store_cart_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['id'] = $_GET['goods'];

	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_cart_del ***");
	return $content;
}

?>