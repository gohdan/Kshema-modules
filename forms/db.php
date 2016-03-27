<?php

// Database functions of the "forms" module

function forms_install_tables()
{
	debug ("*** forms_install_tables ***");
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

        $queries[] = "create table if not exists ksh_forms_categories (
                id int auto_increment primary key,
                name tinytext,
				title tinytext
        )".$charset;


        $queries[] = "create table if not exists ksh_forms (
                id int auto_increment primary key,
                name tinytext,
				title tinytext,
                flds_names text,
                flds_descrs text,
                category int,
                template text
        )".$charset;


        $queries[] = "create table if not exists ksh_forms_submitted (
        	id int auto_increment primary key,
			type int,
            name tinytext,
            flds text,
            vls text,
            date date,
            time time
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

	debug ("*** end: forms_install_tables ***");
        return $content;
}

function forms_drop_tables()
{
    debug ("*** forms_drop_tables ***");
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

    debug ("*** end: forms_drop_tables ***");
    return $content;
}

function forms_update_tables()
{
    global $config;
	global $user;
	global $db_name;
	
	debug ("*** forms_update_tables ***");
    $content = array(
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
    );

    $queries = array();

    // $queries[] = ""; // Write your SQL queries here
	/*
    $queries[] = "ALTER TABLE ksh_forms_submitted ADD date date";
    $queries[] = "ALTER TABLE ksh_forms_submitted ADD time time";
	$queries[] = "ALTER TABLE ksh_forms_submitted ADD type int";
	$queries[] = "UPDATE ksh_forms_submitted SET type = '1' WHERE name='pan_project'";
	$queries[] = "UPDATE ksh_forms_submitted SET type = '2' WHERE name='project_info'";
	$queries[] = "UPDATE ksh_forms_submitted SET type = '3' WHERE name='catalogue'";
	$queries[] = "UPDATE ksh_forms_submitted SET type = '4' WHERE name='brus_project'";
	$queries[] = "ALTER TABLE ksh_forms ADD title tinytext";
	*/

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

	if (!in_array("ksh_forms_categories", $tables))
		$queries[] = "create table if not exists ksh_forms_categories (
                id int auto_increment primary key,
                name tinytext,
				title tinytext
        )".$charset;

	if (!in_array("ksh_forms_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_forms_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_forms", $tables))
        $queries[] = "create table if not exists ksh_forms (
                id int auto_increment primary key,
                name tinytext,
				title tinytext,
                flds_names text,
                flds_descrs text,
                category int,
                template text
        )".$charset;

	if (!in_array("ksh_forms_submitted", $tables))
        $queries[] = "create table if not exists ksh_forms_submitted (
        	id int auto_increment primary key,
			type int,
            name tinytext,
            flds text,
            vls text,
            date date,
            time time
        )".$charset;


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

	debug ("*** forms_update_tables ***");
    return $content;
}


?>
