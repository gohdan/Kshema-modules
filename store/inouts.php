<?php

// Goods movements functions of the "store" module

function store_inouts_view_all()
{
	debug ("*** store_inouts_view_all ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'dates' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$result = exec_query("SELECT distinct(date) FROM ksh_store_inout ORDER BY id ASC");
		$i = 0;
		while ($date = mysql_fetch_array($result))
		{
			$content['dates'][$i]['date'] = stripslashes($date['date']);
			$i++;
		}
		mysql_free_result($result);
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_inouts_view_all ***");
	return $content;
}

function store_inouts_view()
{
	debug ("*** store_inouts_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'string' => '',
		'inouts_in' => '',
		'inouts_out' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");

		switch ($_GET['type'])
		{
			default:
				$content['string'] = ", ".$_GET['date'];
				$type = "date";
				$value = $_GET['date'];
			break;

			case "date":
				$content['date'] = $_GET['date'];
				$type = "date";
				$value = $_GET['date'];
			break;

			case "object":
				$content['string'] = " по объекту ".stripslashes(mysql_result(exec_query("SELECT name FROM ksh_store_objects WHERE id='".$_GET['object']."'"), 0, 0));
				$type = "object";
				$value = $_GET['object'];
			break;

			case "user":
				$content['string'] = " по сотруднику ".stripslashes(mysql_result(exec_query("SELECT name FROM ksh_store_users WHERE id='".$_GET['user']."'"), 0, 0));
				$type = "user";
				$value = $_GET['user'];
			break;
		}

		$content['inouts_in'] = store_inouts_list($type, $value, "in");
		$content['inouts_out'] = store_inouts_list($type, $value, "out");



	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end:store_inouts_view ***");
	return $content;
}


function store_inouts_list($type, $value, $way)
{
	debug ("*** store_inouts_list ***");
	global $user;
	global $config;

	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	switch ($type)
	{
		default: $sql_query = "SELECT * FROM ksh_store_inout WHERE date='".mysql_real_escape_string($value)."' and way='".mysql_real_escape_string($way)."'"; break;

		case "date": $sql_query = "SELECT * FROM ksh_store_inout WHERE date='".mysql_real_escape_string($value)."' and way='".mysql_real_escape_string($way)."'"; break;

		case "object": $sql_query = "SELECT * FROM ksh_store_inout WHERE object='".mysql_real_escape_string($value)."' and way='".mysql_real_escape_string($way)."'"; break;

		case "user": $sql_query = "SELECT * FROM ksh_store_inout WHERE user='".mysql_real_escape_string($value)."' and way='".mysql_real_escape_string($way)."'"; break;

	}

	$result = exec_query($sql_query);
	$i = 0;
	while ($inout = mysql_fetch_array($result))
	{
		$content[$i]['id'] = stripslashes($inout['id']);

		$way = stripslashes($inout['way']);
		if ("in" == $way)
			$content[$i]['way'] = "Получено";
		else if ("out" == $way)
			$content[$i]['way'] = "Выдано";

		$res = exec_query("SELECT name, measure FROM ksh_store_goods WHERE id='".$inout['good']."'");
		$good = mysql_fetch_array($res);
		mysql_free_result($res);

		$content[$i]['good'] = stripslashes($good['name']);
		$content[$i]['measure'] = stripslashes($good['measure']);

		$content[$i]['object'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_store_objects WHERE id='".$inout['object']."'"), 0, 0));

		$content[$i]['user'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_store_users WHERE id='".$inout['user']."'"), 0, 0));

		$content[$i]['qty'] = stripslashes($inout['qty']);
		$content[$i]['date'] = stripslashes($inout['date']);
		$content[$i]['time'] = stripslashes($inout['time']);
		$content[$i]['commentary'] = stripslashes($inout['commentary']);

		$i++;
	}
	mysql_free_result($result);

	debug ("*** end:store_inouts_list ***");
	return $content;
}


function store_inouts_comment_view()
{
	debug ("*** store_inouts_comment_view ***");
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
	$content['commentary'] = stripslashes(mysql_result(exec_query("SELECT commentary FROM ksh_store_inout WHERE id='".$_GET['inouts']."'"), 0, 0));

	debug ("*** end:store_inouts_comment_view ***");
	return $content;
}

?>