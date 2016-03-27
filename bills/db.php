<?php

// Database functions of the "bills" module


function bills_table_create($table)
{
	debug ("*** bills_table_create ***");
	global $config;
    $content = array(
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
	
	$queries[] = "create table if not exists `".mysql_real_escape_string($table)."` (
		`id` int auto_increment primary key,
		`category` int,
		`name` tinytext,
		`title` tinytext,
		`full_text` text,
		`bbs` text,
		`user` int,
		`date` date
	)".$charset;

	// Updating privileges
	
		$sql_query = "SELECT * FROM `ksh_bills_privileges` 
			WHERE `action` = 'default'
			AND `type` = 'group'
			AND `id` = '0'
			";
		$result = exec_query($sql_query);
		if (mysql_num_rows($result))
			$queries[] = "UPDATE `ksh_bills_privileges` SET
				`read` = '0' AND `write` = '0'
				WHERE `action` = 'default'
				AND `type` = 'group'
				AND `id` = '0'
				";
		else
			$queries[] = "INSERT INTO `ksh_bills_privileges` (
				`action`, `type`, `id`, `read`, `write`, `uid`
				) VALUES (
				'default', 'group', '0', '0', '0', '3'
				)";

		$actions = array();
		$actions[] = "view_by_category";
		$actions[] = "edit";
		$actions[] = "view";

		foreach ($actions as $k => $v)
		{
			$sql_query = "SELECT * FROM `ksh_bills_privileges` 
				WHERE `action` = '".mysql_real_escape_string($v)."'
				AND `type` = 'group'
				AND `id` = '0'
				";
			$result = exec_query($sql_query);
			if (mysql_num_rows($result))
				$queries[] = "UPDATE `ksh_bills_privileges` SET
					`read` = '1' AND `write` = '0'
					WHERE `action` = '".mysql_real_escape_string($v)."'
					AND `type` = 'group'
					AND `id` = '0'
					";
			else
				$queries[] = "INSERT INTO `ksh_bills_privileges` (
					`action`, `type`, `id`, `read`, `write`
					) VALUES (
					'".mysql_real_escape_string($v)."', 'group', '0', '1', '0'
					)";
		}


	$cnf = new Config;
	$cnf -> table = "ksh_bills_config";
	$cnf -> create_table();

	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('bills_on_page', '5', 'Объявлений на странице')";
	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('resemble_bills_qty', '5', 'Похожих объявлений')";
	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('use_captcha', 'yes', 'Использовать ли CAPTCHA')";
	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('table', 'ksh_bills', 'Название таблицы объявлений')";
	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('categories_table', 'ksh_bills_categories', 'Название таблицы категорий')";
	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('default_action', 'view_by_category', 'Действие по умолчанию')";
	$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('sections', '', 'Разделы')";


	$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_bills_categories_titles` (
		`id` int auto_increment primary key,
		`satellite` int,
		`category` int,
		`title` tinytext,
		`name` tinytext
	)".$charset;


	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query)
			exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}

	return $content;
}

function bills_tables_create()
{
	debug ("*** bills_tables_create ***");
	global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'queries_qty' => ''
    );

	$cat = new Category();
	$result =  $cat -> create_table("ksh_bills_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_bills_privileges");

	$content['result'] .= $result['result'];

	$content = array_merge($content, bills_table_create($config['bills']['table']));

	debug ("*** end: bills_tables_create ***");        
	return $content;
}

function bills_tables_drop()
{
	debug ("*** bills_tables_drop ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

    if (isset($_POST['do_drop']))
    {
           debug ("*** drop_db");
           unset ($_POST['do_drop']);

			if (isset($_POST['drop_categories_table']))
			{
				debug ("dropping categories table");
				$cat = new Category();
				$result = $cat -> drop_table("ksh_bills_categories");
				$content['result'] .= $result['result'];
				unset($_POST['drop_categories_table']);
			}

			if (isset($_POST['drop_privileges_table']))
			{
				debug ("dropping privileges table");
				$cat = new Privileges();
				$result = $cat -> drop_table("ksh_bills_privileges");
				$content['result'] .= $result['result'];
				unset($_POST['drop_privileges_table']);
			}

           foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
           $content['result'] .= "Таблицы БД успешно удалены";
    }
    debug ("*** end: drop_db");
	debug ("*** end: bills_tables_drop ***");
    return $content;
}

function bills_tables_update()
{
	global $user;
	global $config;
	global $db_name;

	debug ("*** bills_tables_update ***");
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

/*

	$version = modules_get_version("bills");

 	if ($version < 0.6)
	{
		$priv = new Privileges();
		$result =  $priv -> create_table("ksh_bills_privileges");
		$content['result'] .= $result['result'];
	}

	if ($version < 0.8)
		$queries[] = "ALTER TABLE `ksh_bills_privileges` ADD `uid` int auto_increment primary key";

	if ($version < 0.9)
		$queries[] = "ALTER TABLE `ksh_bills` ADD `name` tinytext";

	if ($version < 1.0)
	{
		$sql_query = "SELECT * FROM `ksh_bills_privileges` 
			WHERE `action` = 'default'
			AND `type` = 'group'
			AND `id` = '0'
			";
		$result = exec_query($sql_query);
		if (mysql_num_rows($result))
			$queries[] = "UPDATE `ksh_bills_privileges` SET
				`read` = '0' AND `write` = '0'
				WHERE `action` = 'default'
				AND `type` = 'group'
				AND `id` = '0'
				";
		else
			$queries[] = "INSERT INTO `ksh_bills_privileges` (
				`action`, `type`, `id`, `read`, `write`, `uid`
				) VALUES (
				'default', 'group', '0', '0', '0', '3'
				)";

		$actions = array();
		$actions[] = "view_by_category";
		$actions[] = "edit";
		$actions[] = "view";

		foreach ($actions as $k => $v)
		{
			$sql_query = "SELECT * FROM `ksh_bills_privileges` 
				WHERE `action` = '".mysql_real_escape_string($v)."'
				AND `type` = 'group'
				AND `id` = '0'
				";
			$result = exec_query($sql_query);
			if (mysql_num_rows($result))
				$queries[] = "UPDATE `ksh_bills_privileges` SET
					`read` = '1' AND `write` = '0'
					WHERE `action` = '".mysql_real_escape_string($v)."'
					AND `type` = 'group'
					AND `id` = '0'
					";
			else
				$queries[] = "INSERT INTO `ksh_bills_privileges` (
					`action`, `type`, `id`, `read`, `write`
					) VALUES (
					'".mysql_real_escape_string($v)."', 'group', '0', '1', '0'
					)";
		}
				
	}
*/

	$tables = array();
	$sql_query = "SHOW TABLES";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
		$tables[] = stripslashes($row['Tables_in_'.$db_name]);
	mysql_free_result($result);

	debug("tables:", 2);
	dump($tables);

	if (!in_array("ksh_bills_categories", $tables))
	{
		$cat = new Category();
		$cat -> create_table("ksh_bills_categories");
	}

	if (!in_array("ksh_bills_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_bills_privileges");
	}

	if (!in_array($config['bills']['table'], $tables))
		bills_table_create($config['bills']['table']);

	if (!in_array("ksh_bills_config", $tables))
	{
		$cnf = new Config;
		$cnf -> table = "ksh_bills_config";
		$cnf -> create_table();

		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('bills_on_page', '5', 'Объявлений на странице')";
		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('resemble_bills_qty', '5', 'Похожих объявлений')";
		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('use_captcha', 'yes', 'Использовать ли CAPTCHA')";
		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('table', 'ksh_bills', 'Название таблицы объявлений')";
		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('categories_table', 'ksh_bills_categories', 'Название таблицы категорий')";
		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('default_action', 'view_by_category', 'Действие по умолчанию')";
		$queries[] = "INSERT INTO `ksh_bills_config` (`name`, `value`, `descr`) VALUES ('sections', '', 'Разделы')";
	}

	if (!in_array("ksh_bills_categories_titles", $tables))
	{
		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_bills_categories_titles` (
			`id` int auto_increment primary key,
			`satellite` int,
			`category` int,
			`title` tinytext,
			`name` tinytext
		)".$charset;
	}

	/*

	$field_names = array();
	$field_types = array();
	$i = 0;

	$sql_query = "SHOW FIELDS IN `ksh_auto_models`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	if (!in_array("image", $field_names))
		$queries[] = "ALTER TABLE `ksh_auto_models` ADD `image` tinytext";
*/


    $queries_qty = count($queries);
    $content['queries_qty'] = $queries_qty;

    if ($queries_qty > 0)
    {
        foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
        $content['result'] .= "Запросы выполнены";
    }
	debug ("*** bills_tables_update ***");        
    return $content;
}

?>
