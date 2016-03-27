<?php

// Database functions of the "podcast" module

function podcast_table_create($table)
{
	debug("*** podcast_table_create ***");
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
            `user` int,
            `author` tinytext,
            `category` int,
			`date` date,
			`title` tinytext,
			`subtitle` tinytext,
			`summary` tinytext,
			`image` tinytext,
			`enclosure` tinytext,
			`duration` tinytext
        )".$charset;

	$queries_qty = count($queries);

    if ($queries_qty > 0)
    {
            foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
            $content['result'] .= "Запросы выполнены";
    }
    else
 	 	$content['result'] .= "Нечего выполнять";

	debug("*** end: podcast_table_create ***");
	return $content;
}

function podcast_install_tables()
{
	debug ("*** podcast_install_tables ***");
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

	$cat = new Category();
	$result =  $cat -> create_table("ksh_podcast_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_podcast_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_podcast_access");
	$content['result'] .= $result['result'];
	$queries[] = "INSERT INTO `ksh_podcast_access` (`res_type`, `res_id`, `subj_type`, `subj_id`) VALUES ('category', '1', 'group', '0|1|2')";


	$cnf = new Config();
	$cnf -> table = "ksh_podcast_config";
	$result = $cnf -> create_table();
	$content['result'] .= " ".$result['result'];
	$queries[] = "ALTER TABLE `ksh_podcast_config` CHANGE `value` `value` mediumtext";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('default_action', 'frontpage', 'Действие по умолчанию')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('title', '', 'Название подкаста')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('link', '', 'Ссылка на сайт')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('rss_link', '', 'Ссылка на RSS')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('language', '', 'Язык')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('copyright', '', 'Копирайт')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('subtitle', '', 'Подзаголовок подкаста')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('author', '', 'Автор')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('summary', '', 'Краткое описание')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('description', '', 'Подробное описание')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('owner_name', '', 'Имя владельца')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('owner_email', '', 'E-mail владельца')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('image', '', 'URL изображения-описания')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('category', '', 'Категория в iTunes')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('subcategory', '', 'Подкатегория в iTunes')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('explicit', 'Clean', 'Содержит ли контент для взрослых (если нет - Clean)')";
	$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('', '', '')";

	$res = podcast_table_create("ksh_podcast");
	$content['result'] .= $res['result'];

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content['result'] .= "Запросы выполнены";
	}
	else
		$content['result'] .= "Нечего выполнять";

    debug ("*** end: podcast_install_tables ***");
    return $content;
}

function podcast_drop_tables()
{
	debug ("*** podcast_drop_tables ***");
    global $config;
    $content = array(
    	'content' => '',
        'result' => ''
    );

        if (isset($_POST['do_drop']))
        {
                debug ("*** drop_db");
                unset ($_POST['do_drop']);

				if (isset($_POST['drop_podcast_privileges_table']))
				{
					debug ("dropping privileges table");
					$cat = new Privileges();
					$result = $cat -> drop_table("ksh_podcast_privileges");
					$content['result'] .= $result['result'];
					unset($_POST['drop_podcast_privileges_table']);
				}

                foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
                $content['result'] .= "Таблицы БД успешно удалены";
        }


        debug ("*** end: drop_db");

	debug ("*** end: podcast_drop_tables ***");
    return $content;
}

function podcast_update_tables()
{
	global $user;
	global $config;
	global $db_name;

	debug ("*** podcast_update_tables ***");
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



	$tables = array();
	$sql_query = "SHOW TABLES";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
		$tables[] = stripslashes($row['Tables_in_'.$db_name]);
	mysql_free_result($result);

	debug("tables:", 2);
	dump($tables);

	if (!in_array("ksh_podcast_categories", $tables))
	{
		$cat = new Category();
		$result =  $cat -> create_table("ksh_podcast_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_podcast_privileges", $tables))
	{
		$priv = new Privileges();
		$priv -> create_table("ksh_podcast_privileges");
	}

	if (!in_array("ksh_podcast_access", $tables))
	{
		$acc = new Access();
		$acc -> create_table("ksh_podcast_access");
		$queries[] = "INSERT INTO `ksh_podcast_access` (`res_type`, `res_id`, `subj_type`, `subj_id`) VALUES ('category', 'id', 'group', '0|1|2')";
	}

	if (!in_array("ksh_podcast_config", $tables))
	{
		$cnf = new Config;
		$cnf -> table = "ksh_podcast_config";
		$cnf -> create_table();

		$queries[] = "ALTER TABLE `ksh_podcast_config` CHANGE `value` `value` mediumtext";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('default_action', 'frontpage', 'Действие по умолчанию')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('title', '', 'Название подкаста')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('link', '', 'Ссылка на сайт')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('rss_link', '', 'Ссылка на RSS')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('language', '', 'Язык')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('copyright', '', 'Копирайт')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('subtitle', '', 'Подзаголовок подкаста')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('author', '', 'Автор')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('summary', '', 'Краткое описание')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('description', '', 'Подробное описание')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('owner_name', '', 'Имя владельца')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('owner_email', '', 'E-mail владельца')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('image', '', 'URL изображения-описания')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('category', '', 'Категория в iTunes')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('subcategory', '', 'Подкатегория в iTunes')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('explicit', 'Clean', 'Содержит ли контент для взрослых (если нет - Clean)')";
		$queries[] = "INSERT INTO `ksh_podcast_config` (`name`, `value`, `descr`) VALUES ('', '', '')";
	}


	if (!in_array("ksh_podcast", $tables))
		podcast_table_create("ksh_podcast");

	/* Checking fields in ksh_podcast_access */
	$sql_query = "SELECT * FROM `ksh_podcast_access`";
	$result = exec_query($sql_query);
	if (!mysql_num_rows($result))
		$queries[] = "INSERT INTO `ksh_podcast_access` (`res_type`, `res_id`, `subj_type`, `subj_id`) VALUES ('category', '1', 'group', '0|1|2')";

	/* End: Checking fields in ksh_podcast_access */


	/* Checking fields in ksh_podcast */

	$sql_query = "SHOW FIELDS IN `ksh_podcast`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$field_names[$i] = stripslashes($row['Field']);
		$field_types[$i] = stripslashes($row['Type']);
	}
	mysql_free_result($result);

	//if (!in_array("yt_id", $field_names))
	//	$queries[] = "ALTER TABLE `ksh_podcast` ADD `yt_id` tinytext";

	/* End: Checking fields in ksh_podcast */


        $queries_qty = count($queries);
        $content['queries_qty'] = $queries_qty;

        if ($queries_qty > 0)
        {
                foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
                $content['result'] = "Запросы выполнены";
        }
        else
        	$content['result'] = "Нечего выполнять";
	debug ("*** end: podcast_update_tables ***");
    return $content;
}


?>
