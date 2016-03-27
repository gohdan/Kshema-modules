<?php

// Database functions of the "bbcpanel" module

function bbcpanel_tables_create()
{
	debug ("*** bbcpanel_tables_create ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );

	$cat = new Category();
	$result =  $cat -> create_table("ksh_bbcpanel_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_bbcpanel_privileges");
	$content['result'] .= $result['result'];

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

	$queries[] = "create table if not exists `ksh_bbcpanel_bbs` (
		`id` int auto_increment primary key,
		`category` int,
		`name` tinytext,
		`title` tinytext,
		`url` tinytext,
		`sections` text,
		`bills_per_page` tinyint,
		`bill_view_mode` char(1),
		`theme` int,
		`instroot` tinytext
	)".$charset;

	$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_bbcpanel_titles` (
		`id` int auto_increment primary key,
		`bb` int,
		`category` int,
		`name` tinytext,
		`title` tinytext
	)".$charset;

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}
	debug ("*** end: bbcpanel_tables_create ***");        
	return $content;
}

function bbcpanel_tables_drop()
{
	debug ("*** bbcpanel_tables_drop ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
           debug ("*** drop_db");
           unset ($_POST['do_drop']);

			if (isset($_POST['drop_bbcpanel_categories_table']))
			{
				debug ("dropping categories table");
				$cat = new Category();
				$result = $cat -> drop_table("ksh_bbcpanel_categories");
				$content['result'] .= $result['result'];
				unset($_POST['drop_bbcpanel_categories_table']);
			}

			if (isset($_POST['drop_bbcpanel_privileges_table']))
			{
				debug ("dropping privileges table");
				$cat = new Privileges();
				$result = $cat -> drop_table("ksh_bbcpanel_privileges");
				$content['result'] .= $result['result'];
				unset($_POST['drop_bbcpanel_privileges_table']);
			}

           foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
           $content['result'] .= "Таблицы БД успешно удалены";
    }
    debug ("*** end: drop_db");
	debug ("*** end: bbcpanel_tables_drop ***");
    return $content;
}

function bbcpanel_tables_update()
{
	global $user;
	global $config;
	global $db_name;


	debug ("*** bbcpanel_tables_update ***");
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );

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

    $queries = array();

	// $queries[] = ""; // Write your SQL queries here
/*
	$version = modules_get_version("bbcpanel");

	if ($version < 0.5)
	{
		$priv = new Privileges();
		$result =  $priv -> create_table("ksh_bbcpanel_privileges");
		$content['result'] .= $result['result'];
	}

	if ($version < 0.6)
		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_bbcpanel_titles` (
			`id` int auto_increment primary key,
			`bb` int,
			`category` int,
			`title` tinytext
		)".$charset;

	if ($version < 0.8)
		$queries[] = "ALTER TABLE `ksh_bbcpanel_titles` ADD `name` tinytext";


	if ($version < 0.9)
		$queries[] = "ALTER TABLE `ksh_bbcpanel_privileges` ADD `uid` int auto_increment primary key";
*/


	$tables = array();
	$sql_query = "SHOW TABLES";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
		$tables[] = stripslashes($row['Tables_in_'.$db_name]);
	mysql_free_result($result);

	debug("tables:", 2);
	dump($tables);

	if (!in_array("ksh_bbcpanel_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_bbcpanel_categories");
	}

	if (!in_array("ksh_bbcpanel_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_bbcpanel_privileges");
	}

	if (!in_array("ksh_bbcpanel_bbs", $tables))
		$queries[] = "create table if not exists `ksh_bbcpanel_bbs` (
			`id` int auto_increment primary key,
			`category` int,
			`name` tinytext,
			`title` tinytext,
			`url` tinytext,
			`sections` text,
			`bills_per_page` tinyint,
			`bill_view_mode` char(1),
			`theme` int,
			`instroot` tinytext
		)".$charset;
	else
	{
		$fields = array();
		$i = 0;
		$sql_query = "SHOW FIELDS IN `ksh_bbcpanel_bbs`";
		$result = exec_query($sql_query);
		while ($row = mysql_fetch_array($result))
		{
			$fields_names[$i] = stripslashes($row['Field']);
			$fields_types[$i] = stripslashes($row['Type']);
		}
		mysql_free_result($result);

		if (!in_array("instroot", $fields_names))
			$queries[] = "ALTER TABLE `ksh_bbcpanel_bbs` ADD `instroot` tinytext";
	}

    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** bbcpanel_tables_update ***");        
    return $content;
}

?>
