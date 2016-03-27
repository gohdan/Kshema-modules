<?php
// Requests management functions of the "shop" module

function shop_requests_view()
{
	debug ("*** shop_requests_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'requests' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		if (isset($_POST['do_del']))
    	{
	        exec_query ("delete from ksh_shop_requests where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] .= "Заявка удалена";
	    }

		$content['requests'] = shop_requests_list();

	}
	else
	{
		debug ("user isn't admin");
		$content['content'] .= "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:shop_requests_view ***");
	return $content;
}

function shop_requests_add()
{
	debug ("*** shop_requests_add ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if ($user['id'])
	{

		if (isset($_POST['do_add']))
	    {
			exec_query("INSERT INTO ksh_shop_requests (user, good, qty) VALUES ('".mysql_real_escape_string($user['id'])."', '".mysql_real_escape_string($_POST['good'])."', '".mysql_real_escape_string($_POST['qty'])."')");
			$content['result'] .= "Ваша заявка зарегистрирована";
	    }
		else
		{
			$content['result'] .= "Заявка не зарегистрирована: не выбран товар";
		}
	}
	else
		$content['result'] .= "Чтобы оставить заявку, Вы должны <a href=\"/index.php?module=auth&action=show_login_form\">войти на сайт</a>.";

	debug ("*** end:shop_requests_add ***");
	return $content;
}

function shop_requests_list()
{
    debug("*** shop_requests_list ***");
    $i = 0;
    $result = exec_query ("select id,user,good,qty from ksh_shop_requests");
    while ($request = mysql_fetch_array($result))
    {
        debug ("request ".$i);
        $requests[$i]['id'] = stripslashes($request['id']);
		$requests[$i]['user_id'] = stripslashes($request['user']);
		debug ("user id: ".$requests[$i]['user_id']);

		if (0 != $request['user'])
		{
	        $requests[$i]['user'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_users WHERE id='".mysql_real_escape_string($request['user'])."'"), 0, 0));
	        debug ("user name: ".$requests[$i]['user']);
			$requests[$i]['email'] = stripslashes(mysql_result(exec_query("SELECT login FROM ksh_users WHERE id='".mysql_real_escape_string($request['user'])."'"), 0, 0));
	        debug ("user email: ".$requests[$i]['email']);
		}
		else
		{
			$requests[$i]['user'] = "Гость";
			$requests[$i]['email'] = "-";
		}

        $requests[$i]['good'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_goods WHERE id='".mysql_real_escape_string($request['good'])."'"), 0, 0));
        $requests[$i]['good_id'] = stripslashes($request['good']);
        $requests[$i]['qty'] = stripslashes($request['qty']);
        $i++;
    }
    mysql_free_result($result);
    debug("*** shop_requests_list ***");
    return $requests;
}

function shop_requests_del()
{
	debug ("*** shop_requests_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'admin_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['admin_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	$content['id'] = $_GET['requests'];

	debug ("*** end:shop_requests_del ***");
	return $content;
}


?>
