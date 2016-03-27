<?php

// Database functions of the "projects" module

function projects_install_tables()
{
	debug ("*** projects_install_tables ***");
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

        $queries[] = "create table if not exists ksh_projects_categories (
                id int auto_increment primary key,
                name tinytext,
				title tinytext,
				author tinytext,
				descr text,
				descr_image tinytext,
				status tinyint,
				att_project int
        )".$charset;

        $queries[] = "create table if not exists ksh_projects (
                id int auto_increment primary key,
                name tinytext,
				title tinytext,
                author int,
                category int,
                descr text,
				descr_image tinytext,
                date date
        )".$charset;

        $queries[] = "create table if not exists ksh_projects_files (
                id int auto_increment primary key,
                number int,
				part int,
				project int,
                name tinytext,
				file_path tinytext,
                descr text,
				descr_image tinytext,
                date date
        )".$charset;

		$queries[] = "create table if not exists ksh_projects_statuses (
                id int auto_increment primary key,
                name tinytext
        )".$charset;

		$queries[] = "INSERT INTO ksh_projects_statuses (name) values ('Активные')";
		$queries[] = "INSERT INTO ksh_projects_statuses (name) values ('Завершённые')";
		$queries[] = "INSERT INTO ksh_projects_statuses (name) values ('Отдельные истории')";
		$queries[] = "INSERT INTO ksh_projects_statuses (name) values ('Будущие')";

        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
        else
        	$content['result'] .= "Нечего выполнять";
	debug ("*** end: projects_install_tables ***");
    return $content;
}

function projects_drop_tables()
{
	debug ("*** projects_drop_tables ***");
    global $config;
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
	debug ("*** end: projects_drop_tables ***");
    return $content;
}

function projects_update_tables()
{
	debug("*** projects_update_tables ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );
    $queries = array();

    //$queries[] = ""; // Write your SQL queries here

	if ($config['base']['version'] < 0.5)
	{
		$queries[] = "ALTER TABLE ksh_projects_categories ADD att_project tinytext";
        $queries[] = "ALTER TABLE ksh_projects_files ADD number int";

	}

	if ($config['base']['version'] < 0.6)
	{
        $queries[] = "ALTER TABLE ksh_projects_files ADD part int";

	}

        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
        else
        	$content['result'] .= "Нечего выполнять";
	debug("*** end: projects_update_tables ***");
    return $content;
}

?>
