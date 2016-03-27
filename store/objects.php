<?php

// Objects functions of the "store" module

function store_objects_view_all()
{
	debug ("*** store_objects_view_all ***");
    global $config;
	global $user;

	$content = array(
		'content' => '',
		'result' => '',
		'objects' => ''
	);


	if (isset($_POST['do_del']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, deleting from DB");
			//exec_query ("delete from ksh_store_objects where id='".mysql_real_escape_string($_POST['id'])."'");
			exec_query ("update ksh_store_objects set status='1' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Объект завершён";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Объект не завершён";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT * FROM ksh_store_objects ORDER BY position ASC");
	$i = 0;
	while ($object = mysql_fetch_array($result))
	{
		$content['objects'][$i]['id'] = stripslashes($object['id']);
		$content['objects'][$i]['position'] = stripslashes($object['position']);
		$content['objects'][$i]['name'] = stripslashes($object['name']);
		if ("0" == stripslashes($object['status']))
			$content['objects'][$i]['status'] = "в работе";
		else
			$content['objects'][$i]['status'] = "завершён";
		$i++;
	}
	mysql_free_result($result);


    debug ("*** end: store_objects_view_all ***");
    return $content;
}



function store_objects_add()
{
	debug ("*** store_object_add ***");
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
			$position = stripslashes(mysql_result(exec_query("SELECT max(position) FROM ksh_store_objects"), 0, 0)) + 1;

			exec_query("INSERT INTO ksh_store_objects (name, position, status) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($position)."', '0')");

			$content['result'] = "Объект добавлен";
		}
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}



	debug ("*** end:store_object_add ***");
	return $content;
}

function store_objects_edit()
{
	debug ("*** store_objects_edit ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'objects_select' => ''
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
			exec_query ("update ksh_store_objects set name='".mysql_real_escape_string($_POST['name'])."', status='".mysql_real_escape_string($_POST['status'])."' where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Изменения записаны";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Изменения не записаны";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT * FROM ksh_store_objects WHERE id='".mysql_real_escape_string($_GET['objects'])."'");
	$object = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($object['id']);
	$content['name'] = stripslashes($object['name']);
	$content['position'] = stripslashes($object['position']);
	if ("0" == stripslashes($object['status']))
		$content['option_0'] = "yes";
	else
		$content['option_1'] = "yes";


	debug ("*** end:store_objects_edit ***");
	return $content;
}

function store_objects_del()
{
	debug ("*** store_objects_del ***");
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

	$result = exec_query("select name from ksh_store_objects where id='".mysql_real_escape_string($_GET['objects'])."'");
	$content['id'] = $_GET['objects'];
	$content['name'] = stripslashes(mysql_result($result, 0, 0));
	mysql_free_result ($result);

	debug ("*** end:store_objects_del ***");
	return $content;
}


function store_objects_list()
{
	debug ("*** store_objects_list ***");
	global $config;
	$i = 0;
	$result = exec_query ("select id,name,position from ksh_store_objects order by position");
	while ($object = mysql_fetch_array($result))
	{
		$objects[$i]['id'] = stripslashes($object['id']);
		$objects[$i]['name'] = stripslashes($object['name']);
		$objects[$i]['position'] = stripslashes($object['position']);
		$i++;
	}
	mysql_free_result($result);
	debug ("*** end: store_objects_list ***");
	return $objects;
}


function store_objects_sort()
{
	debug ("*** store_objects_sort ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'objects_select' => '',
		'id' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		if (isset($_POST['do_sort']))
		{
			unset ($_POST['do_sort']);

			$_POST['position'] = stripslashes(mysql_result(exec_query("SELECT position FROM ksh_store_objects WHERE id='".$_POST['position']."'"), 0, 0));

			$result = exec_query ("SELECT id, position FROM ksh_store_objects WHERE position >= '".mysql_real_escape_string($_POST['position'])."' ORDER BY position ASC");
			while ($row = mysql_fetch_array($result))
			{
				exec_query ("UPDATE ksh_store_objects SET position='".($row['position'] + 1)."' WHERE id='".$row['id']."'");
			}
			mysql_free_result($result);

			exec_query("UPDATE ksh_store_objects SET position='".$_POST['position']."' WHERE id='".mysql_real_escape_string($_POST['id'])."'");
		}

		$content['id'] = $_GET['objects'];

		$object_id = mysql_result(exec_query("SELECT id FROM ksh_store_objects WHERE id='".mysql_real_escape_string($_GET['objects'])."'"), 0, 0);

		$result = exec_query("SELECT id, name FROM ksh_store_objects WHERE id != '".mysql_real_escape_string($_GET['objects'])."' ORDER BY position ASC");

		$i = 0;
		while ($object = mysql_fetch_array($result))
		{
			$content['objects_select'][$i]['id'] = stripslashes($object['id']);
			$content['objects_select'][$i]['name'] = stripslashes($object['name']);
			$i++;
		}
		mysql_free_result($result);
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_objects_sort ***");
	return $content;
}



?>