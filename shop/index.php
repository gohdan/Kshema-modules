<?php

// Base functions of the "shop" module

include_once ($config['modules']['location']."shop/config.php");

$config_file = $config['base']['doc_root']."/config/shop.php";
if (file_exists($config_file))
	include($config_file);

include_once ($config['modules']['location']."shop/db.php");
include_once ($config['modules']['location']."shop/categories.php");
include_once ($config['modules']['location']."shop/authors.php");
include_once ($config['modules']['location']."shop/goods.php");
include_once ($config['modules']['location']."shop/users.php");
include_once ($config['modules']['location']."shop/cart.php");
include_once ($config['modules']['location']."shop/orders.php");
include_once ($config['modules']['location']."shop/requests.php");
include_once ($config['modules']['location']."shop/demand.php");

include_once ($config['modules']['location']."/files/index.php"); // to upload pictures

function shop_admin()
{
	debug ("*** shop_admin ***");
    $content['content'] = "";
	debug ("*** end: shop_admin ***");
    return $content;
}

function shop_frontpage()
{
    global $config;	
	global $user;
	debug ("*** shop_frontpage ***");
	$content = array(
		'content' => '',
		'goods_last' => ''
	);
   	$lastitems = $config['shop']['lastitems'];

	if ("1" == $user['id'])
		$content['show_admin_link'] = "yes";

	$i = 0;
	$content['categories_frontpage'] = array();
	$sql_query = "SELECT * FROM `ksh_shop_categories` WHERE `parent` = '0' ORDER BY `position` ASC";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		foreach($row as $k => $v)
			$content['categories_frontpage'][$i][$k] = stripslashes($v);
		$i++;
	}
	mysql_free_result($result);

	$i = 0;
	
    $result = exec_query ("SELECT `id`, `name` FROM `ksh_shop_categories`");
	if ($result && mysql_num_rows($result))
	{
	    while ($row = mysql_fetch_array($result))
			$categories[] = $row;
	    mysql_free_result ($result);

	    foreach ($categories as $ck => $cv)
    	{
			$sql_query = "SELECT COUNT(*) FROM `ksh_shop_goods` WHERE category='".$cv['id']."' AND (`if_hide` IS NULL OR `if_hide` != '1')";
			$goods_qty = mysql_result(exec_query($sql_query), 0, 0);
			debug ("goods qty: ".$goods_qty);
	        if (0 != $goods_qty)
    	    {
				debug("there are goods, processing");
				$content['goods_last'][$i]['begin_row'] = "yes";
				$content['goods_last'][$i]['lastitems_qty'] = $lastitems;
				$content['goods_last'][$i]['category_title'] = stripslashes($cv['name']);
		        unset ($goods);

				$sql_query = "SELECT `id`, `name`, `image` FROM `ksh_shop_goods` 
					WHERE category='".$cv['id']."'
					AND (`if_hide` IS NULL OR `if_hide` != '1')
					ORDER BY `id` DESC LIMIT ".$lastitems;
		        $result = exec_query ($sql_query);
		        while ($row = mysql_fetch_array($result))
					$goods[] = $row;
	        	mysql_free_result($result);

				$j = 0;
		        foreach ($goods as $k => $v)
	    	    {
					debug("processing good ".$k.", i=".$i.", j=".$j);
					$content['goods_last'][$i]['id'] = stripslashes($v['id']);
					$content['goods_last'][$i]['image'] = stripslashes($v['image']);
					$content['goods_last'][$i]['name'] = stripslashes($v['name']);
					$i++;
					$j++;
	
					if ($j == $lastitems)
					{
						$content['goods_last'][$i]['end_row'] = "yes";
				        if ("yes" == $config['shop']['show_last_goods_link'])
						{
							$content['goods_last'][$i]['show_last_goods_link'] = "yes";
							$content['goods_last'][$i]['category_id'] = stripslashes($cv['id']);
							$content['goods_last'][$i]['category_title'] = stripslashes($cv['name']);
							$content['goods_last'][$i]['lastitems_qty'] = $lastitems;
						}
					}
				}
	        }
    	}
	}

    debug ("*** end: shop_frontpage ***");
    return $content;
}

function shop_read_csv()
{
	debug ("*** shop_read_csv ***");
    global $config;
    global $user;
    $content = array(
    	'content' => '',
        'result' => '',
		'code' => '',
		'name' => '',
		'category' => '',
		'author' => ''
    );

	$file_path = $config['base']['doc_root'].$config['base']['domain_dir']."/modules/shop/data.csv";
	debug ("CSV file path: ".$file_path);
	$handle = fopen ($file_path,"r");
	while ($data = fgetcsv ($handle, 1000, ";"))
    {
        $content['id'] = $data[0];
        $content['name'] = $data[1];

		$content['category'] = 1;
		$content['author'] = 1;

        exec_query("INSERT INTO ksh_shop_goods (category, author, code, name) values ('".mysql_real_escape_string($content['category'])."', '".mysql_real_escape_string($content['author'])."', '".mysql_real_escape_string($content['code'])."', '".mysql_real_escape_string($content['name'])."')");
	}
	fclose ($handle);

    debug ("*** end: shop_read_csv ***");
    return $content;
}

function shop_hook()
{
    debug("*** shop_hook ***");
    global $user;
    global $config;
    $content = array(
		'hook' => '',
		'show_admin_link' => ''
	);

    $sql_query = "SELECT * FROM `ksh_hooks` WHERE
		`hook_module` = 'shop' AND
		`to_module` = '".mysql_real_escape_string($config['modules']['current_module'])."' AND
		`to_id` = '".mysql_real_escape_string($config['modules']['current_id']).")'";

    $result = exec_query($sql_query);

	while ($hook = mysql_fetch_array($result))
	{
		if ("category" == stripslashes($hook['hook_type']))
		{
		    $category = stripslashes($hook['hook_id']);
	    	$sql_query = "SELECT * FROM `ksh_shop_goods` WHERE `category` = '".mysql_real_escape_string($category)."' ORDER BY `id` DESC LIMIT ".$config['shop']['lastitems'];
	    	$categories = exec_query($sql_query);

			$i = 0;
	    	while ($row = mysql_fetch_array($categories))
	    	{
	        	debug("show goods ".$row['id']);
				foreach ($row as $k => $v)
					$content['hook'][$i][$k] = stripslashes($v);
				$i++;
	    	}
	    	mysql_free_result($categories);
		}
	}
    mysql_free_result($result);

    if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

    debug("*** end: shop_hook ***");
    return $content;
}


function shop_default_action()
{
	global $config;
    global $user;
	
	debug("=== mod: shop ===");
	
	$content = "";

	$module_data = array (
		'module_name' => "shop",
		'module_title' => "Магазин"
	);

	$config['shop']['page_title'] = $module_data['module_title'];
	$config['themes']['page_title']['module'] = "Магазин";

	if (isset($config['shop']['css']) && ("" != $config['shop']['css']))
		$config['template']['css'][] = $config['shop']['css'];

	$priv = new Privileges();

	if (isset($_GET['category']))
		$config['modules']['current_category'] = $_GET['category'];

	if (isset($_POST['do_del_category']))
	{
		if ($priv -> has("shop", "admin", "write"))
		{
			debug ("deleting category");
			$cat = new Category();
			$result = $cat -> del("ksh_shop_categories", "ksh_shop", $_POST['id']);
		}
	}

	if (isset($_POST['do_del']))
	{
		if ($priv -> has("shop", "admin", "write"))
		{
			debug ("user is admin, deleting good from DB");
			exec_query ("delete from `ksh_shop_goods` where `id` = '".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Товар удалён";
		}
	}

	$action = "default";
	if (isset($_GET['action']))
	{
		debug("*** have GET action");
		$action = $_GET['action'];
	}

	debug ("*** action: ".$action);

	if (in_array($action, $config['shop']['admin_actions']))
		$config['themes']['admin'] = "yes";

	if (in_array($action, $config['shop']['admin_actions']) && !($priv -> has("shop", "admin", "write")))
		$content .= gen_content("auth", "show_login_form", auth_show_login_form());
	else switch ($action)
	{
		default:
			$content .= gen_content("shop", "frontpage", shop_frontpage());
		break;

		case "admin":
			$config['themes']['page_title']['action'] = "Администрирование";
			$content .= gen_content("shop", "admin", shop_admin());
		break;

		case "install_tables":
			$config['themes']['page_title']['action'] = "Создание таблиц БД";
			$content .= gen_content("shop", "install_tables", shop_install_tables());
		break;

		case "drop_tables":
			$config['themes']['page_title']['action'] = "Удаление таблиц БД";
			$content .= gen_content("shop", "drop_tables", shop_drop_tables());
		break;

		case "update_tables":
			$config['themes']['page_title']['action'] = "Обновление таблиц БД";
			$content .= gen_content("shop", "update_tables", shop_update_tables());
		break;

		case "categories_view":
			$config['themes']['page_title']['action'] = "Категории";
			$content .= gen_content("shop", "categories_view", shop_categories_view());
		break;

		case "categories_view_adm":
			$config['themes']['page_title']['action'] = "Категории";
			$cat = new Category();
			$cnt = $cat -> view("ksh_shop_categories");
			$content .= gen_content("shop", "categories_view", array_merge($module_data, $cnt));
		break;

		case "categories_add":
			$config['themes']['page_title']['action'] = "Добавление категории";
			$cat = new Category();
			$cnt = $cat -> add("ksh_shop_categories");
			$content .= gen_content("pages", "categories_add", array_merge($module_data, $cnt));
		break;

		case "categories_edit":
			$config['themes']['page_title']['action'] = "Редактирование категории";
			if (isset($_POST['category']))
				$category = $_POST['category'];
			else if (isset($_GET['category']))
				$category = $_GET['category'];
			else if (isset($_GET['element']))
				$category = $_GET['element'];
			else
				$category = 0;

			$cat = new Category();
			$cnt = $cat -> edit("ksh_shop_categories", $category);
			$content .= gen_content("shop", "categories_edit", array_merge($module_data, $cnt));
		break;

		case "categories_del":
			$config['themes']['page_title']['action'] = "Удаление категории";
			$content .= gen_content("shop", "categories_del", shop_categories_del());
		break;

		case "authors_view":
			$config['themes']['page_title']['action'] = "Авторы";
			$content .= gen_content("shop", "authors_view", shop_authors_view());
		break;

		case "authors_view_by_category":
			$config['themes']['page_title']['action'] = "Просмотр авторов по категориям";
			$content .= gen_content("shop", "authors_view_by_category", shop_authors_view_by_category());
		break;

		case "authors_add":
			$config['themes']['page_title']['action'] = "Добавление авторов";
			$content .= gen_content("shop", "authors_add", shop_authors_add());
		break;

		case "authors_edit":
			$config['themes']['page_title']['action'] = "Редактирование авторов";
			$content .= gen_content("shop", "authors_edit", shop_authors_edit());
		break;

		case "authors_del":
			$config['themes']['page_title']['action'] = "Удаление авторов";
			$content .= gen_content("shop", "authors_del", shop_authors_del());
		break;

		case "goods_view_all":
			$config['themes']['page_title']['action'] = "Все товары";
			$content .= gen_content("shop", "goods_view_all", shop_goods_view_all());
		break;

		case "goods_view_hidden":
			$config['themes']['page_title']['action'] = "Скрытые товары";
			$content .= gen_content("shop", "goods_view_hidden", shop_goods_view_hidden());
		break;

		case "goods_add":
			$config['themes']['page_title']['action'] = "Добавление товаров";
			$content .= gen_content("shop", "goods_add", shop_goods_add());
		break;

		case "goods_edit":
			$config['themes']['page_title']['action'] = "Редактирование товара";
			$content .= gen_content("shop", "goods_edit", shop_goods_edit());
		break;

		case "goods_del":
			$config['themes']['page_title']['action'] = "Удаление товаров";
			$content .= gen_content("shop", "goods_del", shop_goods_del());
		break;

		case "view_by_categories":
			$config['themes']['page_title']['action'] = "Просмотр товаров в категории";
			$content = gen_content("shop", "view_by_categories", shop_view_by_categories());
		break;

		case "view_by_authors":
			$config['themes']['page_title']['action'] = "Просмотр товаров по авторам";
			$content = gen_content("shop", "view_by_authors", shop_view_by_authors());
		break;

		case "view_by_tag":
			$config['themes']['page_title']['action'] = "Просмотр товаров по тегу";
			$content = gen_content("shop", "view_by_tag", shop_view_by_tag());
		break;

		case "view_popular":
			$config['themes']['page_title']['action'] = "Популярные товары";
			$content = gen_content("shop", "view_popular", shop_view_popular());
		break;

		case "view_new":
			$config['themes']['page_title']['action'] = "Новые товары";
			$content = gen_content("shop", "view_new", shop_view_new());
		break;

		case "view_recommended":
			$config['themes']['page_title']['action'] = "Рекомендуемые товары";
			$content = gen_content("shop", "view_recommended", shop_view_recommended());
		break;

		case "view_good":
			$config['themes']['page_title']['action'] = "Просмотр товара";
			$content = gen_content("shop", "view_good", shop_view_good());
		break;

		case "users_view":
			$config['themes']['page_title']['action'] = "Пользователи";
			$content = gen_content("shop", "users_view", shop_users_view());
		break;

		case "user_del":
			$config['themes']['page_title']['action'] = "Удаление пользователя";
			$content = gen_content("shop", "user_del", shop_user_del());
		break;

		case "cart_add":
			$config['themes']['page_title']['action'] = "Добавление в корзину";
			$content = gen_content("shop", "cart_add", shop_cart_add());
		break;

		case "cart_add_multiple":
			$config['themes']['page_title']['action'] = "Множественное добавление в корзину";
			$content = gen_content("shop", "cart_add_multiple", shop_cart_add_multiple());
		break;

		case "cart_view":
			$config['themes']['page_title']['action'] = "Корзина";
			$content = gen_content("shop", "cart_view", shop_cart_view());
		break;

		case "cart_del":
			$config['themes']['page_title']['action'] = "Удаление из корзины";
			$content = gen_content("shop", "cart_del", shop_cart_del());
		break;

		case "order_create":
			$config['themes']['page_title']['action'] = "Оформление заказа";
			$content = gen_content("shop", "orders_create", shop_orders_create());
		break;

		case "order_send":
			$config['themes']['page_title']['action'] = "Отправка заказа";
			$content = gen_content("shop", "orders_send", shop_orders_send());
		break;

		case "orders_view_all":
			$config['themes']['page_title']['action'] = "Все заказы";
			$content = gen_content("shop", "orders_view_all", shop_orders_view_all());
		break;

		case "orders_view":
			$config['themes']['page_title']['action'] = "Просмотр заказа";
			$content = gen_content("shop", "orders_view", shop_orders_view());
		break;

		case "orders_view_by_user":
			$config['themes']['page_title']['action'] = "Просмотр заказа по пользователю";
			$content = gen_content("shop", "orders_view_by_user", shop_orders_view_by_user());
		break;

		case "orders_del":
			$config['themes']['page_title']['action'] = "Удаление заказа";
			$content = gen_content("shop", "orders_del", shop_orders_del());
		break;

		case "orders_cancel":
			$config['themes']['page_title']['action'] = "Отмена заказа";
			$content = gen_content("shop", "orders_cancel", shop_orders_cancel());
		break;

		case "requests_view":
			$config['themes']['page_title']['action'] = "Просмотр запроса";
			$content = gen_content("shop", "requests_view", shop_requests_view());
		break;

		case "requests_add":
			$config['themes']['page_title']['action'] = "Добавление запроса";
			$content = gen_content("shop", "requests_add", shop_requests_add());
		break;

		case "requests_del":
			$config['themes']['page_title']['action'] = "Удаление запроса";
			$content = gen_content("shop", "requests_del", shop_requests_del());
		break;

		case "read_csv":
			$config['themes']['page_title']['action'] = "Импорт из CSV";
			$content = gen_content("shop", "read_csv", shop_read_csv());
		break;

		case "demand_add":
			$config['themes']['page_title']['action'] = "Добавление запроса";
			$content = gen_content("shop", "demand_add", shop_demand_add());
		break;

		case "demand_view":
			$config['themes']['page_title']['action'] = "Просмотр запроса";
			$content = gen_content("shop", "demand_view", shop_demand_view());
		break;

		case "demand_del":
			$config['themes']['page_title']['action'] = "Удаление запроса";
			$content = gen_content("shop", "demand_del", shop_demand_del());
		break;

		case "view_last":
			$config['themes']['page_title']['action'] = "Последние товары";
			$content = gen_content("shop", "view_last", shop_view_last());
		break;
	}
	
debug("=== end: mod: shop ===");
return $content;

}

?>
