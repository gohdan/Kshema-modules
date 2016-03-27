<?php

// Database functions of the photos module

function photos_install_tables()
{
	debug ("*** photos_install_tables ***");
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

	$cat = new Category();
	$result =  $cat -> create_table("ksh_photos_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$priv -> create_table("ksh_photos_privileges");

	$acc = new Access();
	$result =  $acc -> create_table("ksh_photos_access");
	$content['result'] .= $result['result'];

	$queries[] = "create table if not exists ksh_photos (
		`id` int auto_increment primary key,
		`category` int,
		`date` date,
		`title` tinytext,
		`image` tinytext,
		`descr` text
	)".$charset;

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;
        
	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] = "Запросы выполнены";
	}
	else
		$content['result'] = "Нечего выполнять";
	debug ("*** end: photos_install_tables ***");
    return $content;
}

function photos_drop_tables()
{
	debug ("*** photos_drop_tables ***");
	$content = array(
		'content' => '',
		'result' => ''
	);
        
	if (isset($_POST['do_drop']))
	{
		debug ("*** drop_db");
		unset ($_POST['do_drop']);
		foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
			$content['result'] .= "Таблицы БД успешно удалены";
		debug ("*** end: drop_db");
	}

	debug ("*** photos_drop_tables ***");
    return $content;
}

function photos_update_tables()
{
	debug ("*** photos_update_tables ***");
    global $config;
	$content = array(
		'content' => '',
		'result' => '',
		'queries_qty' => ''
	);
	$queries = array();
        
	//$queries[] = ""; // Write your SQL queries here
    
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

	$tables = db_tables_list();

	if (!in_array("ksh_photos_categories", $tables))
	{
		$cat = new Category();
		$result = $cat -> create_table("ksh_photos_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_photos_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_photos_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_photos_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_photos_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_photos", $tables))
        $queries[] = "create table if not exists ksh_photos (
                `id` int auto_increment primary key,
				`category` int,
                `date` date,
                `title` tinytext,
                `image` tinytext,
                `descr` text
        )".$charset;

	/* Checking fields in ksh_photos */
	$field_names = array();
	$field_types = array();
	$i = 0;

	$sql_query = "SHOW FIELDS IN `ksh_photos`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	//if (!in_array("field", $field_names))
	//	$queries[] = "";

	/* End: Checking fields in ksh_photos */

        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
		else
			$content['result'] .= "Нечего выполнять";

	debug ("*** end: photos_update_tables ***");
    return $content;
}

?>
