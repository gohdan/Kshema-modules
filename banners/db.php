<?php

// Database functions of the "banners" module

function banners_install_tables()
{
	debug ("*** banners_install_tables ***");
	global $config;
    $content = array (
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
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
	$priv -> create_table("ksh_banners_privileges");

	$acc = new Access();
	$result =  $acc -> create_table("ksh_banners_access");
	$content['result'] .= $result['result'];

        $queries[] = "create table if not exists `ksh_banners` (
                `id` int auto_increment primary key,
                `name` tinytext,
				`title` tinytext,
                `category` int,
                `file` tinytext,
				`descr` text,
				`params` tinytext,
				`alt` tinytext,
				`width` int,
				`height` int,
				`type` tinytext,
				`class` tinytext,
				`link` tinytext
        )".$charset;

        $queries[] = "create table if not exists ksh_banners_categories (
                `id` int auto_increment primary key,
                `name` tinytext,
				`title` tinytext
        )".$charset;

        $queries_qty = count($queries);

        $content['queries_qty'] .= $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
        else
        	$content['result'] .= "Запросов нет";

	debug ("*** end: banners_install_tables ***");
        return $content;
}

function banners_drop_tables()
{
    debug ("*** banners_drop_tables ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
            unset ($_POST['do_drop']);
            foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
            $content['result'] .= "Таблицы БД успешно удалены";
    }
    else
	   	$content['result'] .= "";

    debug ("*** end: banners_drop_tables ***");
    return $content;
}

function banners_update_tables()
{
	global $user;
	global $config;

	debug ("*** banners_update_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
    );

    $queries = array();

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

	if (!in_array("ksh_banners_categories", $tables))
	{
        $queries[] = "create table if not exists `ksh_banners_categories` (
                `id` int auto_increment primary key,
                `name` tinytext,
				`title` tinytext
        )".$charset;
	}

	if (!in_array("ksh_banners_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_banners_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_banners_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_banners_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_banners", $tables))
        $queries[] = "create table if not exists `ksh_banners` (
                `id` int auto_increment primary key,
                `name` tinytext,
				`title` tinytext,
                `category` int,
                `file` tinytext,
				`descr` text,
				`params` tinytext,
				`alt` tinytext,
				`width` int,
				`height` int,
				`type` tinytext,
				`class` tinytext;
        )".$charset;

	/* Checking fields in ksh_banners */

	$field_names = array();
	$field_types = array();
	$i = 0;

	$sql_query = "SHOW FIELDS IN `ksh_banners`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	if (!in_array("class", $field_names))
		$queries[] = "ALTER TABLE `ksh_banners` ADD `class` tinytext";
	if (!in_array("link", $field_names))
		$queries[] = "ALTER TABLE `ksh_banners` ADD `link` tinytext";

	/* End: Checking fields in ksh_banners */

    $queries_qty = count($queries);
    $content['queries_qty'] .= $queries_qty;

    if ($queries_qty > 0)
    {
	    foreach ($queries as $idx => $sql_query)
        	exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
    else
	  	$content['result'] .= "Нет запросов";

	debug ("*** banners_update_tables ***");
    return $content;
}


?>
