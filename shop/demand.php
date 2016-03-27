<?php
// Demand management functions of the "shop" module

function shop_demand_view()
{
	debug ("*** shop_demand_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'demands' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		if (isset($_POST['do_del']))
    	{
	        exec_query ("delete from ksh_shop_demands where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] .= "Заявка удалена";
	    }

		$content['demands'] = shop_demand_list();

	}
	else
	{
		debug ("user isn't admin");
		$content['content'] .= "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:shop_demand_view ***");
	return $content;
}

function shop_demand_add()
{
	debug ("*** shop_demand_add ***");
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

	if (isset($_POST['do_add']))
    {
		exec_query("INSERT INTO ksh_shop_demands (user, name, author, isbn, commentary) VALUES ('".mysql_real_escape_string($user['id'])."', '".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['author'])."', '".mysql_real_escape_string($_POST['isbn'])."', '".mysql_real_escape_string($_POST['commentary'])."')");
		$content['result'] .= "Ваша заявка зарегистрирована";
    }
	else
	{
	}
	
	$config['themes']['page_tpl'] = "demand_add";

	debug ("*** end:shop_demand_add ***");
	return $content;
}

function shop_demand_list()
{
    debug("*** shop_demand_list ***");
    $i = 0;
    $result = exec_query ("select * from ksh_shop_demands");
    while ($demand = mysql_fetch_array($result))
    {
        debug ("demand ".$i);
        $demands[$i]['id'] = stripslashes($demand['id']);
		$demands[$i]['user_id'] = stripslashes($demand['user']);
		debug ("user id: ".$demands[$i]['user_id']);
        $demands[$i]['user'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_users WHERE id='".mysql_real_escape_string($demand['user'])."'"), 0, 0));
        debug ("user name: ".$demands[$i]['user']);
		$demands[$i]['email'] = stripslashes(mysql_result(exec_query("SELECT login FROM ksh_users WHERE id='".mysql_real_escape_string($demand['user'])."'"), 0, 0));
        debug ("user email: ".$demands[$i]['email']);
        $demands[$i]['name'] = stripslashes($demand['name']);
		$demands[$i]['author'] = stripslashes($demand['author']);
		$demands[$i]['isbn'] = stripslashes($demand['isbn']);
		$demands[$i]['commentary'] = stripslashes($demand['commentary']);

        $i++;
    }
    mysql_free_result($result);
    debug("*** shop_demand_list ***");
    return $demands;
}

function shop_demand_del()
{
	debug ("*** shop_demand_del ***");
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

	$content['id'] = $_GET['demand'];

	debug ("*** end:shop_demand_del ***");
	return $content;
}


?>