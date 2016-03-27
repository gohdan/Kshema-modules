<?php

// Database functions of the "events" module

function events_install_tables()
{
	debug ("*** events_install_tables ***");
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


        $queries[] = "create table if not exists ksh_events (
                id int auto_increment primary key,
				category_name tinytext,
				main_category_id tinytext,
				venue_zip tinytext,
				date_sales_from tinytext,
				desc_local text,
				venue_id tinytext,
				date_sales_to tinytext,
				organiser_name tinytext,
				venue_name tinytext,
				desc_en text,
				venue_iso2_country_code tinytext,
				url tinytext,
				venue_town tinytext,
				event_name tinytext,
				image_big tinytext,
				date_to tinytext,
				date_from tinytext,
				image_small tinytext,
				category_id tinytext,
				venue_street tinytext,
				event_id tinytext,
				main_category_name tinytext,
				image_med tinytext
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

	debug ("*** end: events_install_tables ***");
        return $content;
}

function events_drop_tables()
{
    debug ("*** events_drop_tables ***");
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

    debug ("*** end: events_drop_tables ***");
    return $content;
}

function events_update_tables()
{
	debug ("*** events_update_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
    );

    $queries = array();

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

	debug ("*** events_update_tables ***");
    return $content;
}


?>
