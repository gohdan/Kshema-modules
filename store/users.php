<?php

// users functions of the "store" module

function store_users_view_all()
{
	debug ("*** store_users_view_all ***");
    global $config;
	global $user;

	$content = array(
		'content' => '',
		'result' => '',
		'users' => ''
	);


	if (isset($_POST['do_del']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, deleting from DB");
			//exec_query ("delete from ksh_store_users where id='".mysql_real_escape_string($_POST['id'])."'");
			exec_query ("update ksh_store_users set status='1' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Сотрудник уволен";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Сотрудник не уволен";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT * FROM ksh_store_users ORDER BY position ASC");
	$i = 0;
	while ($user = mysql_fetch_array($result))
	{
		$content['users'][$i]['id'] = stripslashes($user['id']);
		$content['users'][$i]['position'] = stripslashes($user['position']);
		$content['users'][$i]['name'] = stripslashes($user['name']);
		if ("0" == stripslashes($user['status']))
			$content['users'][$i]['status'] = "работает";
		else
			$content['users'][$i]['status'] = "не работает";
		$i++;
	}
	mysql_free_result($result);


    debug ("*** end: store_users_view_all ***");
    return $content;
}



function store_users_add()
{
	debug ("*** store_user_add ***");
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
			$position = stripslashes(mysql_result(exec_query("SELECT max(position) FROM ksh_store_users"), 0, 0)) + 1;

			exec_query("INSERT INTO ksh_store_users (name, position, status) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($position)."', '0')");

			$content['result'] = "Сотрудник добавлен";
		}
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}



	debug ("*** end:store_user_add ***");
	return $content;
}

function store_users_edit()
{
	debug ("*** store_users_edit ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'users_select' => ''
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
			exec_query ("update ksh_store_users set name='".mysql_real_escape_string($_POST['name'])."', status='".mysql_real_escape_string($_POST['status'])."' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Изменения записаны";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Изменения не записаны";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT * FROM ksh_store_users WHERE id='".mysql_real_escape_string($_GET['users'])."'");
	$user = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($user['id']);
	$content['name'] = stripslashes($user['name']);
	$content['position'] = stripslashes($user['position']);
	if ("0" == stripslashes($user['status']))
		$content['option_0'] = "yes";
	else
		$content['option_1'] = "yes";


	debug ("*** end:store_users_edit ***");
	return $content;
}

function store_users_del()
{
	debug ("*** store_users_del ***");
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

	$result = exec_query("select name from ksh_store_users where id='".mysql_real_escape_string($_GET['users'])."'");
	$content['id'] = $_GET['users'];
	$content['name'] = stripslashes(mysql_result($result, 0, 0));
	mysql_free_result ($result);

	debug ("*** end:store_users_del ***");
	return $content;
}


function store_users_list()
{
	debug ("*** store_users_list ***");
	global $config;
	$i = 0;
	$result = exec_query ("select id,name,position from ksh_store_users order by position");
	while ($user = mysql_fetch_array($result))
	{
		$users[$i]['id'] = stripslashes($user['id']);
		$users[$i]['name'] = stripslashes($user['name']);
		$users[$i]['position'] = stripslashes($user['position']);
		$i++;
	}
	mysql_free_result($result);
	debug ("*** end: store_users_list ***");
	return $users;
}


function store_users_sort()
{
	debug ("*** store_users_sort ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'users_select' => '',
		'id' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		if (isset($_POST['do_sort']))
		{
			unset ($_POST['do_sort']);

			$_POST['position'] = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_users WHERE id='".$_POST['position']."'"), 0, 0));

			$result = exec_query ("SELECT id, position FROM ksh_store_users WHERE position >= '".mysql_real_escape_string($_POST['position'])."' ORDER BY position ASC");
			while ($row = mysql_fetch_array($result))
			{
				exec_query ("UPDATE ksh_store_users SET position='".($row['position'] + 1)."' WHERE id='".$row['id']."'");
			}
			mysql_free_result($result);

			exec_query("UPDATE ksh_store_users SET position='".$_POST['position']."' WHERE id='".mysql_real_escape_string($_POST['id'])."'");
		}

		$content['id'] = $_GET['users'];

		$user_id = mysql_result(exec_query("SELECT id FROM ksh_store_users WHERE id='".mysql_real_escape_string($_GET['users'])."'"), 0, 0);

		$result = exec_query("SELECT id, name FROM ksh_store_users WHERE id != '".mysql_real_escape_string($_GET['users'])."' ORDER BY position ASC");

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

	debug ("*** end:store_users_sort ***");
	return $content;
}



?>