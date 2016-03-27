<?php

// Database functions of the shop module

function shop_install_tables()
{
	debug ("*** shop_install_tables ***");
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
	$cat = new Category();
	$result =  $cat -> create_table("ksh_shop_categories");
	$content['result'] .= $result['result'];

	$priv = new Privileges();
	$result =  $priv -> create_table("ksh_shop_privileges");
	$content['result'] .= $result['result'];

	$acc = new Access();
	$result =  $acc -> create_table("ksh_shop_access");
	$content['result'] .= $result['result'];

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_authors` (
                `id` int auto_increment primary key,
                `name` tinytext,
				`category` int,
				`image` tinytext,
				`descr` text,
				`if_hide` varchar(1)
		)".$charset;

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_goods` (
                `id` int auto_increment primary key,
				`code` tinytext,
                `name` tinytext,
				`title` tinytext,
                `author` int,
                `category` int,
                `image` tinytext,
                `images` text,
                `genre` tinytext,
                `original_name` tinytext,
                `format` tinytext,
                `language` tinytext,
                `year` tinytext,
                `publisher` tinytext,
                `pages_qty` tinytext,
                `new_qty` tinytext,
                `new_price` tinytext,
                `used_qty` tinytext,
                `used_price` tinytext,
                `weight` tinytext,
                `description_short` mediumtext,
                `description` text,
				`if_new` varchar(1),
				`if_popular` varchar(1),
				`if_hide` varchar(1),
				`collection` int,
				`pdf` tinytext,
				`epub` tinytext,
				`mp3` tinytext,
				`embed` mediumtext,
				`tags` tinytext,
				`if_recommended` varchar(1),
				`links` mediumtext,
				`h1` tinytext,
				`meta_keywords` tinytext,
				`meta_description` tinytext
        )".$charset;

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_requests` (
                `id` int auto_increment primary key,
                `user` int,
                `good` int,
                `qty` int
        )".$charset;

		$queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_demands` (
                `id` int auto_increment primary key,
                `user` int,
                `name` tinytext,
                `author` tinytext,
				`isbn` tinytext,
				`commentary` text
        )".$charset;

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_carts` (
                `id` int auto_increment primary key,
                `user` int,
                `good` int,
                `new_qty` int,
                `used_qty` int
        )".$charset;

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_orders` (
                `id` int auto_increment primary key,
                `user` int,
                `status` tinyint,
				`date` date
        )".$charset;

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_orders_statuses` (
                `id` tinyint auto_increment primary key,
                `status` tinytext,
                `date` date
        )".$charset;

		$queries[] = "INSERT INTO `ksh_shop_orders_statuses` (`id`, `status`) VALUES ('1', 'Заказ отправлен')";
		$queries[] = "INSERT INTO `ksh_shop_orders_statuses` (`id`, `status`) VALUES ('2', 'Заказ оплачен')";
		$queries[] = "INSERT INTO `ksh_shop_orders_statuses` (`id`, `status`) VALUES ('3', 'Возврат')";
		$queries[] = "INSERT INTO `ksh_shop_orders_statuses` (`id`, `status`) VALUES ('4', 'Отмена')";

        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_ordered_goods` (
                `id` int auto_increment primary key,
                `order_id` int,
                `good` int,
                `new_qty` int,
                `used_qty` int
        )".$charset;


        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_collections` (
                `id` int auto_increment primary key,
                `author` int,
				`category` int,
				`name` tinytext,
				`title` tinytext,
				`descr` text,
				`images` text
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

	debug ("*** end: shop_install_tables ***");
	return $content;
}

function shop_drop_tables()
{
	debug ("*** shop_drop_tables ***");
	global $config;
	global $user;
	$content = array(
		'content' => ''
	);

    if (isset($_POST['do_drop']))
    {
	    debug ("*** drop_db");
        unset ($_POST['do_drop']);
        foreach ($_POST as $k => $v) exec_query ("DROP TABLE ".mysql_real_escape_string($v));
	        $content['content'] .= "Таблицы БД успешно удалены";
		debug ("*** end: drop_db");
	}

	debug ("*** end: shop_drop_tables ***");
    return $content;
}

function shop_update_tables()
{
	debug("*** shop_update_tables ***");
	global $config;
	global $user;
	$content = array(
		'content' => '',
        'result' => '',
		'queries_qty' => '',
		'queries' => ''
	);
	$queries = array();

    // $queries[] = ""; // Write your SQL queries here
	
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

	/* Checking tables */

	$tables = db_tables_list();

	if (!in_array("ksh_shop_privileges", $tables))
	{
		$priv = new Privileges();
		$result =  $priv -> create_table("ksh_shop_privileges");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_shop_access", $tables))
	{
		$acc = new Access();
		$result = $acc -> create_table("ksh_shop_access");
		$content['result'] .= $result['result'];
	}	

	$cat = new Category();
	if (!in_array("ksh_shop_categories", $tables))
	{
		$result = $cat -> create_table("ksh_shop_categories");
		$content['result'] .= $result['result'];
	}
	else
	{
		$result = $cat -> update_table("ksh_shop_categories");
		$content['result'] .= $result['result'];
	}

	if (!in_array("ksh_shop_collections", $tables))
	{
        $queries[] = "CREATE TABLE IF NOT EXISTS `ksh_shop_collections` (
                `id` int auto_increment primary key,
                `author` int,
				`category` int,
				`name` tinytext,
				`title` tinytext,
				`descr` text,
				`images` text
        )".$charset;
	}

	/* end: Checking tables */

	/* Checking fields in ksh_shop_goods */

	$fields = db_fields_list("ksh_shop_goods");

	if (!in_array("collection", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `collection` int";
	if (!in_array("pdf", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `pdf` tinytext";
	if (!in_array("epub", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `epub` tinytext";
	if (!in_array("mp3", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `mp3` tinytext";
	if (!in_array("embed", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `embed` mediumtext";
	if (!in_array("tags", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `tags` tinytext";
	if (!in_array("if_recommended", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `if_recommended` varchar(1)";
	if (!in_array("links", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `links` mediumtext";
	if (!in_array("title", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `title` tinytext";
	if (!in_array("h1", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `h1` tinytext";
	if (!in_array("meta_keywords", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `meta_keywords` tinytext";
	if (!in_array("meta_description", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `meta_description` tinytext";
	if (!in_array("description_short", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` ADD `description_short` mediumtext";

	if (in_array("commentary", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_goods` CHANGE `commentary` `description` text";

	/* end: Checking fields in ksh_shop_goods */

	/* Checking fields in ksh_shop_authors */

	$fields = db_fields_list("ksh_shop_authors");

	if (!in_array("category", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_authors` ADD `category` int";
	if (!in_array("image", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_authors` ADD `image` tinytext";
	if (!in_array("if_hide", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_authors` ADD `if_hide` varchar(1)";
	if (!in_array("descr", $fields['names']))
		$queries[] = "ALTER TABLE `ksh_shop_authors` ADD `descr` text";

	/* end: Checking fields in ksh_shop_authors */

	$queries_qty = count($queries);
	$content['queries_qty'] = $queries_qty;

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) 
		{
			$content['queries'][]['query'] = $sql_query;
			exec_query ($sql_query);
		}
		$content['result'] .= "Запросы выполнены";
	}
	else
		$content['result'] .= "Нечего выполнять";

	debug("*** end: shop_update_tables ***");
	return $content;
}


?>
