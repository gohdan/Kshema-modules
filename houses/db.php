<?php

// Database functions of the "houses" module

function houses_install_tables()
{
	debug ("*** houses_install_tables ***");
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

	
        $queries[] = "create table if not exists ksh_houses (
                id int auto_increment primary key,
                name tinytext,
                category int,
                image tinytext,
				descr_image tinytext,
                floors tinyint,
				3d tinytext,
				fasad tinytext,
				1floor_t tinytext,
                1floor tinytext,
				2floor_t tinytext,
                2floor tinytext,
				pdf tinytext,
				sq_common int,
				sq_balcones int,
				sq_living int,
                price int,
				composition text,
				if_show char(3)
        )".$charset;

        $queries[] = "create table if not exists ksh_houses_categories (
                id int auto_increment primary key,
                name tinytext,
								title tinytext
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

	debug ("*** end: houses_install_tables ***");
        return $content;
}

function houses_drop_tables()
{
    debug ("*** houses_drop_tables ***");
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

    debug ("*** end: houses_drop_tables ***");
    return $content;
}

function houses_update_tables()
{
	debug ("*** houses_update_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'queries_qty' => '',
        'result' => '',
		'current_version' => '',
		'new_version' => ''
    );
    
    $queries = array();

	$content['current_version'] = $config['houses']['version'];

	if ($config['houses']['version'] < 0.3)
	{
		$content['new_version'] = "0.3";
		$queries[] = "ALTER TABLE `ksh_houses` ADD `if_show` char(3)";
		$queries[] = "UPDATE `ksh_houses` SET `if_show` = 'yes'";
	}

    // $queries[] = ""; // Write your SQL queries here

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
        
	debug ("*** houses_update_tables ***");
    return $content;
}


?>
