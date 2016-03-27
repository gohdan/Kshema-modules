<?php

// Database functions of the "weather" module

function weather_create_table_query()
{
	global $user;
	global $config;
	debug ("*** weather_create_table_query ***");

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

	/* Creating weather table */

	$sql_query = "CREATE TABLE IF NOT EXISTS `ksh_weather` (
		`id` int auto_increment primary key,
		`location` tinytext,
		`weather` tinytext,
		`icon` tinytext,
		`temp_f` tinyint,
		`temp_c` tinyint,
		`hum` tinytext,
		`wind` tinytext,
		`update` timestamp
	)".$charset;

	/* End: Creating weather table */


	debug ("*** end: weather_create_table_query ***");
	return $sql_query;
}


function weather_tables_create()
{
	debug ("*** weather_tables_create ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );

	$cat = new Category();
	$result =  $cat -> create_table("ksh_weather_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_weather_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_weather_access");
	$content['result'] .= $result['result'];

	$queries[] = weather_create_table_query();

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}

	debug ("*** end: weather_tables_create ***");        
	return $content;
}

function weather_tables_drop()
{
	debug ("*** weather_tables_drop ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
           debug ("*** drop_db");
           unset ($_POST['do_drop']);

			if (isset($_POST['drop_weather_categories_table']))
			{
				debug ("dropping categories table");
				$cat = new Category();
				$result = $cat -> drop_table("ksh_weather_categories");
				$content['result'] .= $result['result'];
				unset($_POST['drop_weather_categories_table']);
			}
			
			if (isset($_POST['drop_privileges_table']))
			{
				debug ("dropping privileges table");
				$cat = new Privileges();
				$result = $cat -> drop_table("ksh_weather_privileges");
				$content['result'] .= $result['result'];
				unset($_POST['drop_privileges_table']);
			}

			foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
           $content['result'] .= "Таблицы БД успешно удалены";
    }
    debug ("*** end: drop_db");
	debug ("*** end: weather_tables_drop ***");
    return $content;
}

function weather_tables_update()
{
	global $user;
	global $config;
	global $db_name;

	debug ("*** weather_tables_update ***");
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

	if (!in_array("ksh_weather_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_weather_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_weather_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_weather_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_weather_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_weather_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_weather", $tables))
		$queries[] = weather_create_table_query();

	/* Checking fields in ksh_weather */

	$sql_query = "SHOW FIELDS IN `ksh_weather`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	/*
	if (!in_array("update", $field_names))
		$queries[] = "ALTER TABLE `ksh_weather` ADD `update` timestamp";
	*/

	/* End: Checking fields in ksh_weather */

    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** weather_tables_update ***");        
    return $content;
}

?>
