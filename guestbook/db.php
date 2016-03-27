<?php

// Database functions of the "guestbook" module

function guestbook_tables_create()
{
	debug ("*** guestbook_tables_create ***");
	global $config;
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

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_guestbook_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_guestbook_access");
	$content['result'] .= $result['result'];

	$queries[] = "create table if not exists `ksh_guestbook` (
		`id` int auto_increment primary key,
		`name` tinytext,
		`contact` tinytext,
		`text` text,
		`date` date,
		`time` time,
		`approved` enum('0','1') default '0'
	)".$charset;

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}
	debug ("*** end: guestbook_tables_create ***");        
	return $content;
}

function guestbook_tables_drop()
{
	debug ("*** guestbook_tables_drop ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
		debug ("*** drop_db");
		unset ($_POST['do_drop']);

		if (isset($_POST['drop_privileges_table']))
		{
			debug ("dropping privileges table");
			$cat = new Privileges();
			$result = $cat -> drop_table("ksh_guestbook_privileges");
			$content['result'] .= $result['result'];
			unset($_POST['drop_privileges_table']);
		}

		if (isset($_POST['drop_access_table']))
		{
			debug ("dropping access table");
			$cat = new Access();
			$result = $cat -> drop_table("ksh_guestbook_access");
			$content['result'] .= $result['result'];
			unset($_POST['drop_access_table']);
		}

        foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
        $content['result'] .= "Таблицы БД успешно удалены";
		debug ("*** end: drop_db");
    }

	debug ("*** end: guestbook_tables_drop ***");
    return $content;
}

function guestbook_tables_update()
{
	global $user;
	global $config;

	debug ("*** guestbook_tables_update ***");
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

	$tables = db_tables_list();

	if (!in_array("ksh_guestbook_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_guestbook_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_guestbook_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_guestbook_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_guestbook", $tables))
		$queries[] = "create table if not exists `ksh_guestbook` (
			`id` int auto_increment primary key,
			`name` tinytext,
			`contact` tinytext,
			`text` text,
			`date` date,
			`time` time,
			`approved` enum('0','1') default '0'
		)".$charset;

	/* Checking fields in ksh_guestbook */
	$field_names = array();
	$field_types = array();
	$i = 0;

	$sql_query = "SHOW FIELDS IN `ksh_guestbook`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	//if (!in_array("field", $field_names))
	//	$queries[] = "";

	/* End: Checking fields in ksh_guestbook */

    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** guestbook_tables_update ***");        
    return $content;
}

?>
