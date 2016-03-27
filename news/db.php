<?php

// Database functions of the "news" module

function news_install_tables()
{
	debug ("*** news_install_tables ***");
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
	$result =  $priv -> create_table("ksh_news_privileges");
	$content['result'] .= $result['result'];

        $queries[] = "create table if not exists ksh_news (
                id int auto_increment primary key,
                name tinytext,
                author int,
                category int,
                image tinytext,
				descr_image tinytext,
				descr text,
				short_descr text,
                full_text text,
                date date,
				url tinytext
        )".$charset;

        $queries[] = "create table if not exists ksh_news_categories (
                id int auto_increment primary key,
                name tinytext,
				title tinytext,
				template tinytext,
				list_template tinytext,
				news_template tinytext,
				page_template tinytext,
				menu_template tinytext
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

	debug ("*** end: news_install_tables ***");
        return $content;
}

function news_drop_tables()
{
    debug ("*** news_drop_tables ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
            unset ($_POST['do_drop']);
			
			if (isset($_POST['drop_news_privileges_table']))
			{
				debug ("dropping privileges table");
				$cat = new Privileges();
				$result = $cat -> drop_table("ksh_news_privileges");
				$content['result'] .= $result['result'];
				unset($_POST['drop_news_privileges_table']);
			}

            foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
            $content['result'] .= "Таблицы БД успешно удалены";
    }
    else
	   	$content['result'] .= "";

    debug ("*** end: news_drop_tables ***");
    return $content;
}

function news_update_tables()
{
	global $user;
	global $config;

	debug ("*** news_update_tables ***");

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

	if (!in_array("ksh_news_categories", $tables))
		$queries[] = "create table if not exists ksh_news_categories (
                id int auto_increment primary key,
                name tinytext,
				title tinytext,
				template tinytext,
				list_template tinytext,
				news_template tinytext,
				page_template tinytext,
				menu_template tinytext
        )".$charset;

	if (!in_array("ksh_news_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_news_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_news", $tables))
        $queries[] = "create table if not exists ksh_news (
                id int auto_increment primary key,
                name tinytext,
                author int,
                category int,
                image tinytext,
				descr_image tinytext,
				descr text,
				short_descr text,
                full_text text,
                date date,
				url tinytext
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

	debug ("*** news_update_tables ***");
    return $content;
}


?>
