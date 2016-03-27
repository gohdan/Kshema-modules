<?php

// Archive functions of the "store" module

function store_archive_create()
{
	debug ("*** store_archive_create ***");
	global $user;
	global $config;
	global $db_name;
	$content = array(
		'result' => '',
		'content' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	$date = date("Y_m_d");
	debug ("date: ".$date);

	$cur_table = "ksh_store_categories_".$date;
	debug ("current table: ".$cur_table);

	$if_table_exist = 0;
	$result = mysql_list_tables($db_name);
	while ($row = mysql_fetch_array($result))
	{
		debug ("table: ".$row[0]);
		if ($cur_table == $row[0])
		{
			debug ("archive table already exist");
			$if_table_exist = 1;
		}
	}
	mysql_free_result($result);

	if (1 == $if_table_exist)
	{
		debug ("archive table already exist, doing nothing");
	}
	else
	{
		debug ("archive table doesn't exist, copying current to archive");
		if ("yes" == $config['db']['old_engine'])
		{
			debug ("db engine is too old, don't using charsets");
			$charset = "";
		}
		else
		{
			debug ("db engine isn't too old, using charsets");
			$charset = " charset='utf8'";
		}
		$sql_query = "create table if not exists ".$cur_table." (
                id int auto_increment primary key,
				position int,
                name tinytext
        )".$charset;
		exec_query($sql_query);

		$result = exec_query("SELECT * FROM ksh_store_categories");
		while ($good = mysql_fetch_array($result))
		{
			exec_query("INSERT INTO ".$cur_table." (id, position, name) values ('".$good['id']."', '".$good['position']."', '".$good['name']."')");
		}
		mysql_free_result($result);


	}

	$cur_table = "ksh_store_goods_".$date;
	debug ("current table: ".$cur_table);

	$if_table_exist = 0;
	$result = mysql_list_tables($db_name);
	while ($row = mysql_fetch_array($result))
	{
		debug ("table: ".$row[0]);
		if ($cur_table == $row[0])
		{
			debug ("archive table already exist");
			$if_table_exist = 1;
		}
	}
	mysql_free_result($result);

	if (1 == $if_table_exist)
	{
		debug ("archive table already exist, doing nothing");
	}
	else
	{
		debug ("archive table doesn't exist, copying current to archive");
		if ("yes" == $config['db']['old_engine'])
		{
			debug ("db engine is too old, don't using charsets");
			$charset = "";
		}
		else
		{
			debug ("db engine isn't too old, using charsets");
			$charset = " charset='utf8'";
		}
		$sql_query = "create table if not exists ".$cur_table." (
			id int auto_increment primary key,
			category int,
			position int,
			name tinytext,
			measure tinytext,
			qty float,
			price int,
			status tinyint,
			commentary text
        )".$charset;
		exec_query($sql_query);

		$result = exec_query("SELECT * FROM ksh_store_goods");
		while ($good = mysql_fetch_array($result))
		{
			exec_query("INSERT INTO ".$cur_table." (id, category, position, measure, qty, price, status, commentary, name) values ('".$good['id']."', '".$good['category']."', '".$good['position']."', '".$good['measure']."', '".$good['qty']."', '".$good['price']."', '".$good['status']."', '".$good['commentary']."', '".$good['name']."')");
		}
		mysql_free_result($result);


	}



	debug ("*** end:store_archive_create ***");
	return $content;
}

function store_archive_view()
{
	debug ("*** store_archive_view ***");
    global $config;
	global $user;
	global $db_name;

	$content = array(
		'content' => '',
		'result' => '',
		'archives' => ''
	);

	$i = 0;
	$result = mysql_list_tables($db_name);
	while ($row = mysql_fetch_array($result))
	{
		debug ("table: ".$row[0]);
		$marker = $row[0]{15};
		debug ("marker: ".$marker);
		if ("_" == $marker)
		{
			debug ("date found");
			$content['archives'][$i]['date'] = substr($row[0], 16, 10);
			$i++;
		}
	}
	mysql_free_result($result);

	debug ("*** end: store_archive_view ***");
    return $content;
}

function store_archive_view_by_date()
{
	debug ("*** store_archive_view_by_date ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'categories' => '',
		'goods' => '',
		'name' => '',
		'date' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	$content['date'] = $_GET['date'];

	$categories_table = "ksh_store_categories_".$_GET['date'];
	$goods_table = "ksh_store_goods_".$_GET['date'];

	$result = exec_query("SELECT * FROM ".$categories_table." ORDER BY position ASC");
	$i = 0;
	while ($category = mysql_fetch_array($result))
	{
		$content['archive_categories'][$i]['date'] = $_GET['date'];
		$content['archive_categories'][$i]['id'] = stripslashes($category['id']);
		$content['archive_categories'][$i]['position'] = stripslashes($category['position']);
		$content['archive_categories'][$i]['name'] = stripslashes($category['name']);
		if (stripslashes($category['id']) == $_GET['categories'])
			$content['name'] = stripslashes($category['name']);
		$i++;
	}
	mysql_free_result($result);

	if (isset($_GET['categories']))
	{
		$res = exec_query("SELECT * FROM ".$goods_table." WHERE category='".$_GET['categories']."' AND status='0' ORDER BY position ASC");
		$i = 0;
		while ($good = mysql_fetch_array($res))
    	{
			$content['archive_goods'][$i]['id'] = stripslashes($good['id']);
			$content['archive_goods'][$i]['name'] = stripslashes($good['name']);
			$content['archive_goods'][$i]['qty'] = stripslashes($good['qty']);
			$content['archive_goods'][$i]['measure'] = stripslashes($good['measure']);
			$content['archive_goods'][$i]['price'] = stripslashes($good['price']);
			$content['archive_goods'][$i]['commentary'] = stripslashes($good['commentary']);
			$i++;
    	}
		mysql_free_result($res);
	}
	debug ("*** end:store_archive_view_by_date ***");
	return $content;
}



?>
