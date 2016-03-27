<?php

// Database functions of the "calc_adv" module

function calc_adv_install_tables()
{
	debug ("*** calc_adv_install_tables ***");
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


        $queries[] = "create table if not exists ksh_calc_adv_cities (
			id int auto_increment primary key,
			title tinytext,
			descr text,
			calc_type char(1),
			price_prime float default 0,
			price_noprime float default 0,
			times text,
			time_prices text,
			month_1 float,
			month_2 float,
			month_3 float,
			month_4 float,
			month_5 float,
			month_6 float,
			month_7 float,
			month_8 float,
			month_9 float,
			month_10 float,
			month_11 float,
			month_12 float,
			noresident_coef float,
			discount_type char(1),
			discount_from text,
			discount text
        )".$charset;

		$queries[] = "create table if not exists ksh_calc_adv_calcs (
			id int auto_increment primary key,
			user int,
			month tinyint,
			city tinytext,
			hron int,
			calc_type char(1),
			prime_qty int,
			noprime_qty int,
			qty int,
			times text,
			times_qty text,
			sum float,
			season_coef float,
			sum_season float,
			if_noresident char(1),
			noresident_coef float,
			sum_noresident float,
			discount float,
			sum_final float
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

	debug ("*** end: calc_adv_install_tables ***");
        return $content;
}

function calc_adv_drop_tables()
{
    debug ("*** calc_adv_drop_tables ***");
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

    debug ("*** end: calc_adv_drop_tables ***");
    return $content;
}

function calc_adv_update_tables()
{
	debug ("*** calc_adv_update_tables ***");
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

	debug ("*** calc_adv_update_tables ***");
    return $content;
}

function calc_adv_export_tables()
{
	debug ("*** calc_adv_export_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',

    );


	$table_name = "ksh_calc_adv_cities";
	$sql_query = "SELECT * FROM ".$table_name;
	$result = exec_query($sql_query);
	$i = 0;
	while ($row = mysql_fetch_array($result))
	{
		$content['export_rows'][$i]['table_name'] = $table_name;
		$content['export_rows'][$i]['fields'] = "title, descr, discount_type, discount, discount_from, noresident_coef, month_1, month_2, month_3, month_4, month_5, month_6, month_7, month_8, month_9, month_10, month_11, month_12, calc_type, price_prime, price_noprime, times, time_prices";
/*
		$content['export_rows'][$i]['title'] = stripslashes($row['title']);
		$content['export_rows'][$i]['descr'] = stripslashes($row['descr']);
		$content['export_rows'][$i]['discount_type'] = stripslashes($row['discount_type']);
		$content['export_rows'][$i]['discount'] = stripslashes($row['discount']);
		$content['export_rows'][$i]['discount_from'] = stripslashes($row['discount_from']);
		$content['export_rows'][$i]['noresident_coef'] = stripslashes($row['noresident_coef']);
		$content['export_rows'][$i]['month_1'] = stripslashes($row['month_1']);
		$content['export_rows'][$i]['month_2'] = stripslashes($row['month_2']);
		$content['export_rows'][$i]['month_3'] = stripslashes($row['month_3']);
		$content['export_rows'][$i]['month_4'] = stripslashes($row['month_4']);
		$content['export_rows'][$i]['month_5'] = stripslashes($row['month_5']);
		$content['export_rows'][$i]['month_6'] = stripslashes($row['month_6']);
		$content['export_rows'][$i]['month_7'] = stripslashes($row['month_7']);
		$content['export_rows'][$i]['month_8'] = stripslashes($row['month_8']);
		$content['export_rows'][$i]['month_9'] = stripslashes($row['month_9']);
		$content['export_rows'][$i]['month_10'] = stripslashes($row['month_10']);
		$content['export_rows'][$i]['month_11'] = stripslashes($row['month_11']);
		$content['export_rows'][$i]['month_12'] = stripslashes($row['month_12']);
		$content['export_rows'][$i]['calc_type'] = stripslashes($row['calc_type']);
		$content['export_rows'][$i]['price_prime'] = stripslashes($row['price_prime']);
		$content['export_rows'][$i]['price_noprime'] = stripslashes($row['price_noprime']);
		$content['export_rows'][$i]['times'] = stripslashes($row['times']);
		$content['export_rows'][$i]['time_prices'] = stripslashes($row['time_prices']);
*/
		$content['export_rows'][$i]['values'] = "";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['title'])."', ";
		$content['export_rows'][$i]['values'] .= "'".htmlspecialchars(stripslashes($row['descr']))."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['discount_type'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['discount'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['discount_from'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['noresident_coef'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_1'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_2'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_3'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_4'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_5'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_6'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_7'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_8'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_9'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_10'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_11'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['month_12'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['calc_type'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['price_prime'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['price_noprime'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['times'])."', ";
		$content['export_rows'][$i]['values'] .= "'".stripslashes($row['time_prices'])."'";


		$i++;
	}
	mysql_free_result($result);


    return $content;
}




?>



