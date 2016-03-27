<?php

// Database functions of the "articles" module

function articles_table_create($table)
{
	debug("*** articles_table_create ***");
	global $user;
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
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

    $queries[] = "CREATE TABLE IF NOT EXISTS `".mysql_real_escape_string($table)."` (
            `id` int auto_increment primary key,
            `name` tinytext,
			`title` tinytext,
            `user` int,
            `category` int,
            `image` tinytext,
	        `full_text` text,
            `descr` text,
		    `descr_image` tinytext,
            `date` date,
			`doc` tinytext
        )".$charset;

	$queries_qty = count($queries);

    if ($queries_qty > 0)
    {
            foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
            $content['result'] .= "Запросы выполнены";
    }
    else
 	 	$content['result'] .= "Нечего выполнять";

	debug("*** end: articles_table_create ***");
	return $content;
}

function articles_install_tables()
{
	debug ("*** articles_install_tables ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
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

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_articles_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_articles_access");
	$content['result'] .= $result['result'];

	$cnf = new Config();
	$cnf -> table = "ksh_articles_config";
	$result = $cnf -> create_table();
	$content['result'] .= " ".$result['result'];
	$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('default_action', 'view_by_user', 'Действие по умолчанию')";
	$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('elements_on_page', '20', 'Количество статей на странице')";
	$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('xmlrpc_use', '1', 'Использовать передачу данных через XMLRPC')";

	$res = articles_table_create($config['articles']['table']);
	$content['result'] .= $res['result'];

	$queries[] = "create table if not exists ksh_articles_categories (
                id int auto_increment primary key,
                name tinytext,
				parent int,
				menu_template tinytext,
				`title` tinytext,
				`template` tinytext,
				`list_template` tinytext,
				`element_template` tinytext,
				`page_template` tinytext
        )".$charset;

        $queries[] = "create table if not exists `ksh_articles_categories_titles` (
                `id` int auto_increment primary key,
				`satellite` int,
                `name` tinytext,
				`category` int,
				`title` tinytext
        )".$charset;

        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] .= "Запросы выполнены";
        }
        else
        	$content['result'] .= "Нечего выполнять";
    debug ("*** end: articles_install_tables ***");
    return $content;
}

function articles_drop_tables()
{
	debug ("*** articles_drop_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

        if (isset($_POST['do_drop']))
        {
                debug ("*** drop_db");
                unset ($_POST['do_drop']);

				if (isset($_POST['drop_articles_privileges_table']))
				{
					debug ("dropping privileges table");
					$cat = new Privileges();
					$result = $cat -> drop_table("ksh_articles_privileges");
					$content['result'] .= $result['result'];
					unset($_POST['drop_articles_privileges_table']);
				}

                foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
                $content['result'] .= "Таблицы БД успешно удалены";
        }


        debug ("*** end: drop_db");

	debug ("*** end: articles_drop_tables ***");
    return $content;
}

function articles_update_tables()
{
	global $user;
	global $config;

	debug ("*** articles_update_tables ***");
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );
    $queries = array();

    //$queries[] = ""; // Write your SQL queries here
	
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

	if (!in_array("ksh_articles_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_articles_categories");
	}

	if (!in_array("ksh_articles_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_articles_privileges");
	}

	if (!in_array("ksh_articles_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_articles_access");
	}

	if (!in_array($config['articles']['table'], $tables))
		articles_table_create($config['articles']['table']);

	if (!in_array("ksh_articles_config", $tables))
	{
		$cnf = new Config;
		$cnf -> table = "ksh_articles_config";
		$cnf -> create_table();

		$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('elements_on_page', '20', 'Статей на странице')";
		$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('default_action', 'view_by_category', 'Действие по умолчанию')";
		$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('xmlrpc_use', '1', 'Использовать ли XMLRPC')";
		$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('resemble_elements_qty', '5', 'Количество похожих элементов')";
		$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('table', 'ksh_articles', 'Название таблицы статей')";
		$queries[] = "INSERT INTO `ksh_articles_config` (`name`, `value`, `descr`) VALUES ('categories_table', 'ksh_articles_categories', 'Название таблицы категорий статей')";
	}

	if (!in_array("ksh_articles_categories_titles", $tables))
	{
		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_articles_categories_titles` (
			`id` int auto_increment primary key,
			`satellite` int,
			`category` int,
			`title` tinytext,
			`name` tinytext
		)".$charset;
	}

        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] = "Запросы выполнены";
        }
        else
        	$content['result'] = "Нечего выполнять";
	debug ("*** end: articles_update_tables ***");
    return $content;
}


?>
