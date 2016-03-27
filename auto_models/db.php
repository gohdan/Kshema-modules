<?php

// Database functions of the "auto_models" module

function auto_models_tables_create()
{
	debug ("*** auto_models_tables_create ***");
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
	$result =  $cat -> create_table("ksh_auto_models_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_auto_models_privileges");
	$content['result'] .= $result['result'];


        $queries[] = "create table if not exists ksh_auto_models (
                id int auto_increment primary key,
				`category` int,
                name tinytext,
                title tinytext,
				full_text text,
				template tinytext,
				`image` tinytext,
				`link` tinytext
        )".$charset;

        $queries[] = "create table if not exists ksh_auto_models_equipment (
                id int auto_increment primary key,
				model int,
                title tinytext,
				image tinytext,
				full_text text
        )".$charset;

        $queries[] = "create table if not exists ksh_auto_models_characteristics (
                id int auto_increment primary key,
				model int,
				full_text mediumtext
        )".$charset;
		
        $queries[] = "create table if not exists ksh_auto_models_prices (
                id int auto_increment primary key,
				model int,
				full_text mediumtext
        )".$charset;

        $queries[] = "create table if not exists ksh_auto_models_colors (
                id int auto_increment primary key,
				model int,
				image tinytext,
				title tinytext,
				code tinytext
        )".$charset;

        $queries[] = "create table if not exists ksh_auto_models_images (
                id int auto_increment primary key,
				title tinytext,
				model int,
				image tinytext,
				descr text
        )".$charset;

        $queries[] = "create table if not exists ksh_auto_models_videos (
                id int auto_increment primary key,
				title tinytext,
				model int,
				video text,
				descr text
        )".$charset;

		$queries[] = "create table if not exists ksh_auto_models_present (
                `id` int auto_increment primary key,
				`category` int,
                `name` tinytext,
				`title` tinytext,
				`year` int,
				`model` int,
				`price` tinytext,
				`date` date,
				`if_with_equip` varchar(1),
				`image` tinytext,
				`thumb` tinytext,
				`engine` text,
				`salon` tinytext,
				`producer` tinytext,
				`complectation` tinytext
        )".$charset;

		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_auto_models_preowned` (
			`id` int auto_increment primary key,
			`category` int,
			`name` tinytext,
			`title` tinytext,
			`model` tinytext,
			`color` tinytext,
			`engine` tinytext,
			`transmission` tinytext,
			`chassis` tinytext,
			`year` tinytext,
			`runout` tinytext,
			`drive` tinytext,
			`complectation` text,
			`info` text,
			`price` tinytext,
			`price_new` tinytext,
			`image` tinytext,
			`thumb` tinytext
		)".$charset;

        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
	debug ("*** end: auto_models_tables_create ***");        
        return $content;
}

function auto_models_tables_drop()
{
	debug ("*** auto_models_tables_drop ***");
    global $config;
	global $user;
    $content = array(
    	'content' => '',
        'result' => ''
    );

	if ("1" == $user['id'])
	{
	    if (isset($_POST['do_drop']))
	    {
           debug ("*** drop_db");
           unset ($_POST['do_drop']);
           foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
           $content['result'] .= "Таблицы БД успешно удалены";
	    }
	}
	else
		$content['result'] .= "Недостаточно прав";

    debug ("*** end: drop_db");
	debug ("*** end: auto_models_tables_drop ***");
    return $content;
}

function auto_models_tables_update()
{
	debug ("*** auto_models_tables_update ***");
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );
    
    $queries = array();
    
    // $queries[] = ""; // Write your SQL queries here
	
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

	$tables = array();
	$sql_query = "SHOW TABLES";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
		$tables[] = stripslashes($row['Tables_in_'.$db_name]);
	mysql_free_result($result);

	debug("tables:", 2);
	dump($tables);

	if (!in_array("ksh_auto_models_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_auto_models_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_auto_models_privileges", $tables))
	{
		$priv = new Privileges();
		$result =  $priv -> create_table("ksh_auto_models_privileges");
	}

	if (!in_array("ksh_auto_models_present", $tables))
		$queries[] = "create table if not exists ksh_auto_models_present (
                `id` int auto_increment primary key,
				`category` int,
                `name` tinytext,
				`title` tinytext,
				`year` int,
				`model` int,
				`price` int,
				`date` date,
				`if_with_equip` varchar(1),
				`image` tinytext,
				`thumb` tinytext,
				`engine` text,
				`salon` tinytext,
				`producer` tinytext,
				`complectation` tinytext
        )".$charset;


	if (!in_array("ksh_auto_models_preowned", $tables))
		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_auto_models_preowned` (
			`id` int auto_increment primary key,
			`category` int,
			`name` tinytext,
			`title` tinytext,
			`model` tinytext,
			`color` tinytext,
			`engine` tinytext,
			`transmission` tinytext,
			`chassis` tinytext,
			`year` tinytext,
			`runout` tinytext,
			`drive` tinytext,
			`complectation` text,
			`info` text,
			`price` tinytext,
			`price_new` tinytext,
			`image` tinytext,
			`thumb` tinytext
		)".$charset;


	$field_names = array();
	$field_types = array();
	$i = 0;

	/* Checking fields in ksh_auto_models */

	$sql_query = "SHOW FIELDS IN `ksh_auto_models`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	if (!in_array("image", $field_names))
		$queries[] = "ALTER TABLE `ksh_auto_models` ADD `image` tinytext";

	if (!in_array("category", $field_names))
		$queries[] = "ALTER TABLE `ksh_auto_models` ADD `category` int";

	if (!in_array("link", $field_names))
		$queries[] = "ALTER TABLE `ksh_auto_models` ADD `link` tinytext";

	/* end: Checking fields in ksh_auto_models */

    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** auto_models_tables_update ***");        
    return $content;
}

?>
