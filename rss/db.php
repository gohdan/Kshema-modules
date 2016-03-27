<?php

// Database functions of the "rss" module

function rss_tables_create()
{
	debug ("*** rss_tables_create ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );

	$cat = new Category();
	$result =  $cat -> create_table("ksh_rss_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_rss_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_rss_access");
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

	/* Creating rss table */

	$sql_query = "CREATE TABLE IF NOT EXISTS `ksh_rss` (
		`id` int auto_increment primary key,
		`category` int,
		`date` date,
		`time` time,
		`title` tinytext,
		`link` tinytext,
		`description` tinytext,
		`pubDate` tinytext,
		`author` tinytext
	)".$charset;

	/* End: Creating rss table */

	$queries[] = $sql_query;

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}
	debug ("*** end: rss_tables_create ***");        
	return $content;
}

function rss_tables_drop()
{
	debug ("*** rss_tables_drop ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
           debug ("*** drop_db");
           unset ($_POST['do_drop']);

			if (isset($_POST['drop_rss_categories_table']))
			{
				debug ("dropping categories table");
				$cat = new Category();
				$result = $cat -> drop_table("ksh_rss_categories");
				$content['result'] .= $result['result'];
				unset($_POST['drop_rss_categories_table']);
			}
			
			if (isset($_POST['drop_privileges_table']))
			{
				debug ("dropping privileges table");
				$cat = new Privileges();
				$result = $cat -> drop_table("ksh_rss_privileges");
				$content['result'] .= $result['result'];
				unset($_POST['drop_privileges_table']);
			}

			foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
           $content['result'] .= "Таблицы БД успешно удалены";
    }
    debug ("*** end: drop_db");
	debug ("*** end: rss_tables_drop ***");
    return $content;
}

function rss_tables_update()
{
	global $user;
	global $config;
	global $db_name;

	debug ("*** rss_tables_update ***");
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

	$tables = array();
	$sql_query = "SHOW TABLES";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
		$tables[] = stripslashes($row['Tables_in_'.$db_name]);
	mysql_free_result($result);

	debug("tables:", 2);
	dump($tables);

	if (!in_array("ksh_rss_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_rss_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_rss_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_rss_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_rss_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_rss_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_rss", $tables))
		$queries[] = "create table if not exists ksh_rss (
			`id` int auto_increment primary key,
			`category` int,
			`date` date,
			`time` time,
			`title` tinytext,
			`link` tinytext,
			`description` tinytext,
			`pubDate` tinytext,
			`author` tinytext
		)".$charset;

	/* Checking fields in ksh_rss */

	$sql_query = "SHOW FIELDS IN `ksh_rss`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

//	if (!in_array("subcategory", $field_names))
//		$queries[] = "ALTER TABLE `ksh_rss` ADD `subcategory` int";




	/* End: Checking fields in ksh_rss */

    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** rss_tables_update ***");        
    return $content;
}

?>
