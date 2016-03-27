<?php

// Database functions of the store module

function store_install_tables()
{
	debug ("*** store_install_tables ***");
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

        $queries[] = "create table if not exists ksh_store_categories (
                id int auto_increment primary key,
				position int,
                name tinytext
        )".$charset;

        $queries[] = "create table if not exists ksh_store_goods (
                id int auto_increment primary key,
				category int,
				position int,
				name tinytext,
				measure tinytext,
				qty float,
				price int,
				status tinyint,
				commentary text
        )".$charset;

        $queries[] = "create table if not exists ksh_store_objects (
                id int auto_increment primary key,
				name tinytext,
				status tinyint,
				position int
        )".$charset;

        $queries[] = "create table if not exists ksh_store_users (
                id int auto_increment primary key,
				name tinytext,
				status tinyint,
				position int
        )".$charset;

		$queries[] = "create table if not exists ksh_store_inout (
                id int auto_increment primary key,
				way tinytext,
				good int,
				user int,
				object int,
				qty float,
            	date date,
            	time time,
				commentary text
        )".$charset;

		$queries[] = "create table if not exists ksh_store_cart (
                id int auto_increment primary key,
				good int,
				qty float
        )".$charset;


        $queries_qty = count($queries);
        $content['queries_qty'] .= $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
		else
			$content['result'] .= "Нечего выполнять";
	debug ("*** end: store_install_tables ***");
        return $content;
}

function store_drop_tables()
{
	debug ("*** store_drop_tables ***");
	global $config;
	global $user;
	$content = array(
		'content' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
        if (isset($_POST['do_drop']))
        {
                debug ("*** drop_db");
                unset ($_POST['do_drop']);
                foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
                $content['content'] .= "Таблицы БД успешно удалены";
				debug ("*** end: drop_db");
        }
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] .= "Пожалуйста, <a href=\"/index.php?module=auth&action=show_login_form\">войдите в систему как администратор</a>";
	}
	debug ("*** end: store_drop_tables ***");
    return $content;
}

function store_update_tables()
{
        debug("*** store_update_tables ***");
		global $config;
		global $user;
		$content = array(
			'content' => '',
			'queries_qty' => ''
		);
		$queries = array();

		$queries[] = "alter table ksh_store_goods change qty qty float";

		$queries[] = "alter table ksh_store_inout change qty qty float";

		$queries[] = "create table if not exists ksh_store_cart (
                id int auto_increment primary key,
				good int,
				qty float
        )".$charset;


        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['content'] .= "Запросы выполнены";
        }
		else
			$content['content'] .= "Нечего выполнять";

        debug("*** end: store_update_tables ***");
        return $content;
}


?>
