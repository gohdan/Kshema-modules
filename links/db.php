<?php

// Database functions of the links module

function links_install_tables()
{
	global $config;
        $content['content'] = "";
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

        $queries[] = "create table if not exists ksh_links (
                id int auto_increment primary key,
                name tinytext,
                author int,
                category int,
                image tinytext,
                descr text,
                date date,
				url tinytext
        )".$charset;

        $queries[] = "create table if not exists ksh_links_categories (
                id int auto_increment primary key,
                name tinytext
        )".$charset;

		$priv = new Privileges();
		$result =  $priv -> create_table("ksh_links_privileges");
		$content['result'] .= $result['result'];

        $queries_qty = count($queries);
        $content['content'] .= "<p>Количество запросов к БД: ".$queries_qty."</p>";

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['content'] .= "<p>Запросы выполнены</p>";
        }
        return $content;
}

function links_drop_tables()
{
        $content['content'] = "";

        if (isset($_POST['do_drop']))
        {
                debug ("*** drop_db");
                unset ($_POST['do_drop']);
                foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
                $content['content'] .= "<p>Таблицы БД успешно удалены</p>";
        }


        debug ("*** end: drop_db");



        return $content;
}

function links_update_tables()
{
	global $user;
	global $config;
	global $db_name;

	debug ("*** links_update_tables ***");
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

	if (!in_array("ksh_links_categories", $tables))
	{
        $queries[] = "create table if not exists ksh_links_categories (
                id int auto_increment primary key,
                name tinytext
        )".$charset;
	}

	if (!in_array("ksh_links_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_links_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_links", $tables))
        $queries[] = "create table if not exists ksh_links (
                id int auto_increment primary key,
                name tinytext,
                author int,
                category int,
                image tinytext,
                descr text,
                date date,
				url tinytext
        )".$charset;


    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** pages_tables_update ***");        
    return $content;
}

function links_backup_tables()
{
        $content = "";
        return $content;
}

function links_restore_tables()
{
        $content = "";
        return $content;
}

?>
