<?php

// Database functions of the "booking" module

function booking_tables_create()
{
	debug ("*** booking_tables_create ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );


	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_booking_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_booking_access");
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

	/* Creating booking table */

	$sql_query = "create table if not exists `ksh_booking` (
		`id` int auto_increment primary key,
		`surname` tinytext,
		`name` tinytext,
		`country` tinytext,
		`phone` tinytext,
		`email` tinytext,
		`comment` text,
		`date_from` date,
		`time_from` time,
		`date_to` date,
		`time_to` time,
		`variant` varchar(1),
		`adults_qty` tinyint,
		`if_children` varchar(1),
		`if_extra_bed` varchar(1),
		`if_transfer` varchar(1),
		`rooms_qty` tinyint,
		`breakfast_qty` tinyint,
		`cost` int,
		`passport` text,
		`payment_type` tinytext,
		`prepayment` tinytext,
		`leftover` tinytext,
		`payment_status` tinytext,
		`date` date,
		`manager` tinytext,
		`dealer` tinytext,
		`room` int,
		`days` int,
		`price` float
	)".$charset;

	$sql_query = "CREATE TABLE IF NOT EXISTS `ksh_booking_rooms` (
		`id` int auto_increment primary key,
		`number` int,
		`type` varchar(1),
		`floor` int
	)".$charset;

	$sql_query = "CREATE TABLE IF NOT EXISTS `ksh_booking_prices` (
		`id` int auto_increment primary key,
		`date_from` date,
		`date_to` date,
		`type` varchar(1),
		`price` int
	)".$charset;

	$sql_query = "CREATE TABLE IF NOT EXISTS `ksh_booking_transfer` (
		`id` int auto_increment primary key,
		`from` tinytext,
		`to` tinytext,
		`date` date,
		`time` time,
		`flight` tinytext,
		`airline` tinytext,
		`reference` tinytext,
		`driver` tinytext,
		`customer` tinytext
	)".$charset;

	/* End: Creating booking table */

	$queries[] = $sql_query;

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}
	debug ("*** end: booking_tables_create ***");        
	return $content;
}

function booking_tables_drop()
{
	debug ("*** booking_tables_drop ***");
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
				$result = $cat -> drop_table("ksh_booking_privileges");
				$content['result'] .= $result['result'];
				unset($_POST['drop_privileges_table']);
			}

			foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
           $content['result'] .= "Таблицы БД успешно удалены";
    }
    debug ("*** end: drop_db");
	debug ("*** end: booking_tables_drop ***");
    return $content;
}

function booking_tables_update()
{
	global $user;
	global $config;
	global $db_name;

	debug ("*** booking_tables_update ***");
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



	if (!in_array("ksh_booking_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_booking_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_booking_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_booking_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_booking", $tables))
		$queries[] = "create table if not exists `ksh_booking` (
		`id` int auto_increment primary key,
		`surname` tinytext,
		`name` tinytext,
		`country` tinytext,
		`phone` tinytext,
		`email` tinytext,
		`comment` text,
		`date_from` date,
		`time_from` time,
		`date_to` date,
		`time_from` time,
		`variant` varchar(1),
		`adults_qty` tinyint,
		`if_children` varchar(1),
		`if_extra_bed` varchar(1),
		`if_transfer` varchar(1),
		`rooms_qty` tinyint,
		`breakfast_qty tinyint,
		`cost` int,
		`days` int,
		`price` float
	)".$charset;

	if (!in_array("ksh_booking_rooms", $tables))
		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_booking_rooms` (
		`id` int auto_increment primary key,
		`number` int,
		`type` varchar(1),
		`floor` int
	)".$charset;

	if (!in_array("ksh_booking_prices", $tables))
		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_booking_prices` (
			`id` int auto_increment primary key,
			`date_from` date,
			`date_to` date,
			`type` varchar(1),
			`price` int
		)".$charset;

	if (!in_array("ksh_booking_transfer", $tables))
		$queries[] = $sql_query = "CREATE TABLE IF NOT EXISTS `ksh_booking_transfer` (
			`id` int auto_increment primary key,
			`from` tinytext,
			`to` tinytext,
			`date` date,
			`time` time,
			`flight` tinytext,
			`airline` tinytext,
			`reference` tinytext,
			`driver` tinytext,
			`customer` tinytext
		)".$charset;

	/* Checking fields in ksh_booking */

	$sql_query = "SHOW FIELDS IN `ksh_booking`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	if (!in_array("surname", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `surname` tinytext";
	if (!in_array("name", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `name` tinytext";
	if (!in_array("country", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `country` tinytext";
	if (!in_array("phone", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `phone` tinytext";
	if (!in_array("email", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `email` tinytext";
	if (!in_array("comment", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `comment` text";
	if (!in_array("date_from", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `date_from` date";
	if (!in_array("date_to", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `date_to` date";
	if (!in_array("variant", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `variant` varchar(1)";
	if (!in_array("adults_qty", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `adults_qty` tinyint";
	if (!in_array("if_children", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `if_children` varchar(1)";
	if (!in_array("if_extra_bed", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `if_extra_bed` varchar(1)";
	if (!in_array("if_transfer", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `if_transfer` varchar(1)";
	if (!in_array("rooms_qty", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `rooms_qty` tinyint";
	if (!in_array("breakfast_qty", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `breakfast_qty` tinyint";
	if (!in_array("cost", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `cost` int";

	if (!in_array("passport", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `passport` text";
	if (!in_array("payment_type", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `payment_type` tinytext";
	if (!in_array("prepayment", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `prepayment` tinytext";
	if (!in_array("leftover", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `leftover` tinytext";
	if (!in_array("payment_status", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `payment_status` tinytext";
	if (!in_array("date", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `date` date";
	if (!in_array("manager", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `manager` tinytext";
	if (!in_array("dealer", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `dealer` tinytext";
	if (!in_array("room", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `room` int";
	if (!in_array("time_from", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `time_from` time";
	if (!in_array("time_to", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `time_to` time";
	if (!in_array("days", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `days` int";
	if (!in_array("price", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking` ADD `price` float";

	/* End: Checking fields in ksh_booking */


	/* Checking fields in ksh_booking_rooms */

	$sql_query = "SHOW FIELDS IN `ksh_booking_rooms`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	if (!in_array("id", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking_rooms` ADD `id` int auto_increment primary key";
	if (!in_array("number", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking_rooms` ADD `number` int";
	if (!in_array("type", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking_rooms` ADD `type` varchar(1)";
	if (!in_array("floor", $field_names))
		$queries[] = "ALTER TABLE `ksh_booking_rooms` ADD `floor` int";

	/* End: Checking fields in ksh_booking_rooms */


    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** booking_tables_update ***");        
    return $content;
}

?>
