<?php

// Database functions of the "counter" module

function counter_install_tables()
{
	debug ("*** counter_install_tables ***");
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


        $queries[] = "create table if not exists ksh_counter_days (
                id int auto_increment primary key,
				date date,
				time time,
				user tinytext,
				url tinytext,
				referer tinytext,
				ip tinytext
        )".$charset;

		$queries[] = "create table if not exists ksh_counter_monthes (
				month tinyint primary key,
				visits int
		)".$charset;

		$queries[] = "insert into ksh_counter_monthes (month) values ('1')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('2')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('3')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('4')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('5')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('6')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('7')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('8')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('9')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('10')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('11')";
		$queries[] = "insert into ksh_counter_monthes (month) values ('12')";

		$queries[] = "create table if not exists ksh_counter_years (
				year int primary key,
				visits int
		)".$charset;

		$queries[] = "insert into ksh_counter_years (year) values ('".date("Y")."')";


        $queries_qty = count($queries);

        $content['queries_qty'] .= $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
        else
        	$content['result'] .= "Запросов нет";

	debug ("*** end: counter_install_tables ***");
        return $content;
}

function counter_drop_tables()
{
    debug ("*** counter_drop_tables ***");
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

    debug ("*** end: counter_drop_tables ***");
    return $content;
}

function counter_update_tables()
{
	debug ("*** counter_update_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
    );

    $queries = array();

    // $queries[] = ""; // Write your SQL queries here
	$queries[] = "ALTER TABLE ksh_counter_days ADD `ip` tinytext";


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

	debug ("*** counter_update_tables ***");
    return $content;
}


?>
