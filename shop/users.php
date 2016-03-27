<?php

function shop_users_view()
{
	debug ("*** shop_users_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'users' => '',
		'show_admin_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";

		if (isset($_POST['do_del']))
		{
			$sql_query = "DELETE FROM ksh_users WHERE id='".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			$content['result'] .= "Пользователь удалён.";
		}

		$content['users'] = users_users_list();

		foreach ($content['users'] as $k => $v)
		{
			$content['users'][$k]['orders_qty'] = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_orders WHERE user='".mysql_real_escape_string($content['users'][$k]['id'])."'"), 0, 0);

			$content['users'][$k]['queries_qty'] = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_requests WHERE user='".mysql_real_escape_string($content['users'][$k]['id'])."'"), 0, 0);

			$content['users'][$k]['cart_qty'] = "yes";
		}
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end:shop_users_view ***");
	return $content;
}


function shop_user_del()
{
	debug ("*** shop_user_del ***");
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
		debug ("authed");
		$result = exec_query("SELECT * FROM ksh_users WHERE id='".mysql_real_escape_string($_GET['user'])."'");
		$usr = mysql_fetch_array($result);
		mysql_free_result($result);
		stripslashes($usr);

		$content['id'] = $usr['id'];
		$content['name'] = $usr['name'];
		$content['login'] = $usr['login'];
		
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] .= "Пожалуйста, войдите как администратор.";
	}


	debug ("*** end: shop_user_del ***");
	return $content;
}



?>
