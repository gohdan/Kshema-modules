<?php

// Database functions of the "portfolio" module

function portfolio_gen_create_table_query()
{
	global $user;
	global $config;
	debug ("*** portfolio_gen_create_table_query ***");

	if ("yes" == $config['db']['old_engine'])
	{
		debug ("db engine is too old, don't using charsets");
		$charset = "";
	}
	else
	{
		debug ("db engine isn't too old, using charsets");
		$charset = " charset='".$config['db']['charset']."'";
	}

    $sql_query = "CREATE TABLE IF NOT EXISTS `ksh_portfolio` (
            `id` int auto_increment primary key,
            `name` tinytext,
			`title` tinytext,
			`order` mediumint,
            `date` date,
			`year` tinytext,
            `category` int,
			`image` tinytext,
			`descr` mediumtext,
			`full_text` mediumtext,
			`images` mediumtext,
			`tags` mediumtext
    )".$charset;


	debug ("*** end: portfolio_gen_create_table_query ***");
	return $sql_query;
}


function portfolio_install_tables()
{
	debug ("*** portfolio_install_tables ***");
	global $config;
    $content = array (
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
    );



	$cat = new Category();
	$result =  $cat -> create_table("ksh_portfolio_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_portfolio_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_portfolio_access");
	$content['result'] .= $result['result'];

	$queries[] = portfolio_gen_create_table_query();

    $queries_qty = count($queries);

    $content['queries_qty'] .= $queries_qty;

    if ($queries_qty > 0)
    {
            foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
            $content['result'] .= "Запросы выполнены";
    }
    else
    	$content['result'] .= "Запросов нет";

	debug ("*** end: portfolio_install_tables ***");
    return $content;
}

function portfolio_drop_tables()
{
    debug ("*** portfolio_drop_tables ***");
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

    debug ("*** end: portfolio_drop_tables ***");
    return $content;
}

function portfolio_update_tables()
{
	debug ("*** portfolio_update_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'queries_qty' => '',
        'result' => ''
    );

    $queries = array();

	$tables = db_tables_list();

	if (!in_array("ksh_portfolio_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_portfolio_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_portfolio_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_portfolio_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_portfolio_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_portfolio_access");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_portfolio", $tables))
		$queries[] = portfolio_gen_create_table_query();

	/* Checking fields in ksh_portfolio */

	$i = 0;
	$sql_query = "SHOW FIELDS IN `ksh_portfolio`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
		$i++;
	}
	mysql_free_result($result);

	if (!in_array("order", $field_names))
		$queries[] = "ALTER TABLE `ksh_portfolio` ADD `order` mediumint";

	/* End: Checking fields in ksh_portfolio */

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

	debug ("*** portfolio_update_tables ***");
    return $content;
}


?>
