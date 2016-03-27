<?php

// Orders handling functions of the "shop" module

function shop_orders_create()
{
	debug ("*** shop_orders_create ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'show_form' => '',
		'sur_name' => '',
		'first_name' => '',
		'second_name' => '',
		'country' => '',
		'post_code' => '',
		'area' => '',
		'city' => '',
		'address' => '',
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	$result = exec_query("SELECT * FROM ksh_users WHERE id = '".mysql_real_escape_string($user['id'])."'");
	$user_data = mysql_fetch_array($result);
    mysql_free_result($result);

	if ("" == $user_data['first_name'] || "" == $user_data['second_name'] || "" == $user_data['sur_name'] || "" == $user_data['country'] || "" == $user_data['post_code'] || "" == $user_data['area'] || "" == $user_data['city'] || "" == $user_data['address'])
    {
        debug ("address doesn't exist!");
        $content['content'] .= "Прежде, чем оформлять заказ, пожалуйста, <a href=\"/index.php?module=users&action=profile_edit\">укажите данные для доставки</a>.";
    }
    else
    {
        debug ("address exist");
		$content['show_form'] = "yes";
		$content['first_name'] = stripslashes($user_data['first_name']);
		$content['second_name'] = stripslashes($user_data['second_name']);
		$content['sur_name'] = stripslashes($user_data['sur_name']);
		$content['country'] = stripslashes($user_data['country']);
		$content['post_code'] = stripslashes($user_data['post_code']);
		$content['area'] = stripslashes($user_data['area']);
		$content['city'] = stripslashes($user_data['city']);
		$content['address'] = stripslashes($user_data['address']);
	}

	debug ("*** end:shop_orders_create ***");
	return $content;
}

function shop_orders_send()
{
	debug ("*** shop_orders_send ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if (0 == $_SESSION['authed'])
	{
		$content['content'] .= "Пожалуйста, сначала войдите на сервер.";
	}
	else
	{
		$result = exec_query("SELECT count(*) FROM ksh_shop_carts WHERE user='".mysql_real_escape_string($user['id'])."'");
		$items_qty = mysql_result($result, 0, 0);
		mysql_free_result($result);

		if (0 == $items_qty)
		{
			debug ("no items in the cart");
			$content['content'] .= "Ваша корзина пуста. Пожалуйста, прежде чем делать заказ, добавьте в неё товары.";
		}
		else
		{
			exec_query("INSERT INTO ksh_shop_orders (user) values ('".$user['id']."')");
			$order_id = mysql_insert_id();
			debug ("order id: ".$order_id);

			$result = exec_query("SELECT * FROM ksh_shop_carts WHERE user='".mysql_real_escape_string($user['id'])."'");

			$goods = "";
			while($row = mysql_fetch_array($result))
			{
				exec_query ("INSERT INTO ksh_shop_ordered_goods (order_id, good, new_qty) values ('".mysql_real_escape_string($order_id)."', '".mysql_real_escape_string($row['good'])."', '".mysql_real_escape_string($row['new_qty'])."')");
				exec_query("DELETE FROM ksh_shop_carts WHERE id='".mysql_real_escape_string($row['id'])."'");

				$good_name = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_goods WHERE id='".$row['good']."'"), 0, 0));
				$goods .= $good_name." - ".$row['new_qty']." экз.\r\n";


			}
			mysql_free_result($result);
			$content['result'] .= "Ваш заказ отправлен на обработку.";

			$result = exec_query("SELECT login, name FROM ksh_users WHERE id='".mysql_real_escape_string($user['id'])."'");
			$usr = mysql_fetch_array($result);

			$mail['subject'] = "Новый заказ в магазине ".$config['base']['site_name'];
			$mail['headers'] = "Content-type: text/plain; charset=utf-8 \r\n";
			$mail['body'] = "В магазине ".$config['base']['site_name']." оформлен новый заказ.\r\n";
			$mail['body'] .= "Номер заказа - ".$order_id."\r\n";
			$mail['body'] .= "Пользователь - ".stripslashes($usr['name'])." (".stripslashes($usr['login']).")\r\n";
			$mail['body'] .= "<a href=\"".$config['base']['site_url']."/index.php?module=shop&action=orders_view&order=".$order_id."\">Просмотреть заказ</a> (или воспользуйтесь ссылкой ".$config['base']['site_url']."/index.php?module=shop&action=orders_view&order=".$order_id.")\r\n";
			$mail['body'] .= "Товары: \r\n".$goods;

			debug ($mail['body']);

			mail ($config['base']['admin_email'], $mail['subject'], $mail['body'], $mail['headers']);
			mail ($config['base']['webmaster_email'], $mail['subject'], $mail['body'], $mail['headers']);

		}

	}

	debug ("*** end:shop_orders_send ***");
	return $content;
}

function shop_orders_view_all()
{
	debug ("*** shop_orders_view_all ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'orders' => ''
	);

	if (1 != $user['id'])
	{
		debug ("user isn't admin!");
		$content['content'] .= "Пожалуйста, войдите в систему как администратор.";
	}
	else
	{
		debug ("user is admin");

		if (isset($_POST['do_del']))
        {
            {
                debug ("deleting an order");
                exec_query("DELETE FROM ksh_shop_orders WHERE id='".mysql_real_escape_string($_POST['id'])."'");
                exec_query("DELETE FROM ksh_shop_ordered_goods WHERE order_id='".mysql_real_escape_string($_POST['id'])."'");
            }
        }

		$result = exec_query("SELECT * FROM ksh_shop_orders_statuses");
		while ($row = mysql_fetch_array($result))
		{
			$content['filters'] .= "<a href=\"/index.php?module=shop&action=orders_view_all&status=".stripslashes($row['id'])."\">".stripslashes($row['status'])."</a> | ";
		}
		mysql_free_result($result);

		if (isset($_GET['status']))
		{
			$show_status = $_GET['status'];
			$result = exec_query("SELECT * FROM ksh_shop_orders WHERE status='".$show_status."' ORDER BY id DESC");
		}
		else
		{
			$show_status = "NULL";
			$result = exec_query("SELECT * FROM ksh_shop_orders WHERE status is NULL ORDER BY id DESC");
		}



		$i = 0;
		while ($row = mysql_fetch_array($result))
		{
			debug ("showing order ".$row['id']);

			$content['orders'][$i]['id'] = stripslashes($row['id']);
			$content['orders'][$i]['status'] = stripslashes($row['status']);
			$content['orders'][$i]['date'] = stripslashes($row['date']);

			// Определяем статус заказа
			if ("" != $row['status'])
				$content['orders'][$i]['order_status'] = stripslashes(mysql_result(exec_query("SELECT status FROM ksh_shop_orders_statuses WHERE id='".mysql_real_escape_string($row['status'])."'"), 0, 0));
			else
				$content['orders'][$i]['order_status'] = "В обработке";

			$content['orders'][$i]['user_email'] = stripslashes(mysql_result(mysql_query("SELECT login FROM ksh_users WHERE id='".mysql_real_escape_string($row['user'])."'"), 0, 0));
			$content['orders'][$i]['user_name'] = stripslashes(mysql_result(mysql_query("SELECT name FROM ksh_users WHERE id='".mysql_real_escape_string($row['user'])."'"), 0, 0));
			$i++;

		}
		mysql_free_result($result);
	}

	debug ("*** end:shop_orders_view_all ***");
	return $content;
}

function shop_orders_view_by_user()
{
	debug ("*** shop_orders_view_by_user ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'orders_by_user' => '',
		'user_id' => '',
		'filters' => '',
		'user_name' => '',
		'user_email' => ''
	);

	if (isset($_GET['user']))
		$user_id = $_GET['user'];
	else
		$user_id = $user['id'];
	$content['user_id'] = $user_id;

	$sql_query = "SELECT name, login FROM ksh_users WHERE id='".mysql_real_escape_string($user_id)."'";
	$result = exec_query($sql_query);
	$usr = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['user_name'] = stripslashes($usr['name']);
	$content['user_email'] = stripslashes($usr['login']);

	if (1 == $user['id'] || $user_id == $user['id'])
	{
		debug ("user has rights!");

		if (1 == $user['id'])
		{
			if (isset($_POST['do_del']))
        	{
               	debug ("deleting an order");
               	exec_query("DELETE FROM ksh_shop_orders WHERE id='".mysql_real_escape_string($_POST['id'])."'");
               	exec_query("DELETE FROM ksh_shop_ordered_goods WHERE order_id='".mysql_real_escape_string($_POST['id'])."'");
        	}
		}

		$result = exec_query("SELECT * FROM ksh_shop_orders_statuses");
		while ($row = mysql_fetch_array($result))
		{
			$content['filters'] .= "<a href=\"/index.php?module=shop&action=orders_view_by_user&user=".$user_id."&status=".stripslashes($row['id'])."\">".stripslashes($row['status'])."</a> | ";
		}
		mysql_free_result($result);

		if (isset($_GET['status']))
		{
			$show_status = $_GET['status'];
			$result = exec_query("SELECT * FROM ksh_shop_orders WHERE status='".$show_status."' AND user='".mysql_real_escape_string($user_id)."' ORDER BY id DESC");
		}
		else
		{
			$show_status = "NULL";
			$result = exec_query("SELECT * FROM ksh_shop_orders WHERE status is NULL AND user='".mysql_real_escape_string($user_id)."' ORDER BY id DESC");
		}



		$i = 0;
		while ($row = mysql_fetch_array($result))
		{
			debug ("showing order ".$row['id']);

			$content['orders_by_user'][$i]['id'] = stripslashes($row['id']);
			$content['orders_by_user'][$i]['status'] = stripslashes($row['status']);
			$content['orders_by_user'][$i]['date'] = stripslashes($row['date']);

			// Определяем статус заказа
			if ("" != $row['status'])
				$content['orders_by_user'][$i]['order_status'] = stripslashes(mysql_result(exec_query("SELECT status FROM ksh_shop_orders_statuses WHERE id='".mysql_real_escape_string($row['status'])."'"), 0, 0));
			else
				$content['orders_by_user'][$i]['order_status'] = "В обработке";
			$i++;

		}
		mysql_free_result($result);

	}
	else
	{
		debug ("user doesn't have rights");
		$content['content'] .= "Извините, Вы не можете смотреть заказы этого пользователя.";
	}

	debug ("*** end:shop_orders_view_by_user ***");
	return $content;
}


function shop_orders_view()
{
	debug ("*** shop_orders_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'statuses' => '',
		'sum_qty' => '',
		'sum_price' => '',
		'sum_delivery' => '',
		'sum_cost' => '',
		'sum_weight' => '',
		'cancel' => '',
		'delete' => '',
		'cart_goods' => '',
		'admin_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['delete'] = "yes";
		$content['admin_link'] = "yes";

		// Если нужно сменить статус, изменяем
        if (isset($_POST['do_change_status']))
        {
                debug ("changing status of an order");
                exec_query("UPDATE ksh_shop_orders SET status='".mysql_real_escape_string($_POST['status'])."', date=CURDATE() WHERE id='".mysql_real_escape_string($_POST['id'])."'");
        }

	}
	else
		debug ("user isn't admin");

	debug("have an order to display");
	$order['id'] = $_GET['order'];
	$content['id'] = $order['id'];

	$result = exec_query ("SELECT * FROM ksh_shop_orders WHERE id='".mysql_real_escape_string($order['id'])."'");
	$row = mysql_fetch_array($result);
	mysql_free_result ($result);

	if (($row['user'] != $user['id']) && (1 != $user['id']))
	{
		debug ("user doesn't have enough rights!");
		$content['content'] .= "Пожалуйста, войдите в систему как соответствующий пользователь или администратор.";
	}
	else
	{
		debug ("user does have enough rights");

		// Если нужно отменить заказ, отменяем
        if (isset($_POST['do_cancel']))
        {
                debug ("canceling an order");
                exec_query("UPDATE ksh_shop_orders SET status='4', date=CURDATE() WHERE id='".mysql_real_escape_string($_POST['id'])."'");
                $content['result'] .= "Ваш заказ отменён. Через некоторое время администратор удалит его.";
                $_GET['order'] = $_POST['id'];
				$row['status'] = 4;

				$result = exec_query("SELECT login, name FROM ksh_users WHERE id='".mysql_real_escape_string($user['id'])."'");
				$usr = mysql_fetch_array($result);

				$mail['subject'] = "Отмена заказа в магазине ".$config['base']['site_name'];
				$mail['headers'] = "Content-type: text/plain; charset=windows-1251 \r\n";
				$mail['body'] = "В магазине ".$config['base']['site_name']." отменён заказ.\r\n";
				$mail['body'] .= "Номер заказа - ".$_POST['id']."\r\n";
				$mail['body'] .= "Пользователь - ".stripslashes($usr['name'])." (".stripslashes($usr['login']).")\r\n";
				mail ($config['base']['admin_email'], $mail['subject'], $mail['body'], $mail['headers']);
				mail ($config['base']['webmaster_email'], $mail['subject'], $mail['body'], $mail['headers']);


        }
		else
			$content['cancel'] = "yes";

		if ("" != $row['status'])
			$order_status = stripslashes(mysql_result(exec_query("SELECT status FROM ksh_shop_orders_statuses WHERE id='".mysql_real_escape_string($row['status'])."'"), 0, 0));
		else
			$order_status = "В обработке";

		debug ("showing order ".$order['id']);
		if ("" == $row['date'])
			$content['date'] = "нет даты";
		else
			$content['date'] = stripslashes($row['date']);
		$content['id'] = stripslashes($row['id']);
		$content['order_status'] = stripslashes($order_status);

		$order['user'] = $row['user'];

		if (1 != $user['id'])
		{
			debug ("user isn't admin!");
		}
		else
		{
			debug ("user is admin");

			$statuses = exec_query("SELECT * FROM ksh_shop_orders_statuses");
			while ($status = mysql_fetch_array($statuses))
			{
				$content['statuses'] .= "<option value=\"".$status['id']."\"";
				if ($status['status'] == $order_status) $content['statuses'] .= " selected";
				$content['statuses'] .= ">".$status['status']."</option>";
			}
			mysql_free_result($statuses);

			$content['change'] = "yes";
		}

		$i = 0;
		$result = exec_query("SELECT * FROM ksh_shop_ordered_goods WHERE order_id='".mysql_real_escape_string($order['id'])."'");
        while ($row = mysql_fetch_array($result))
		{
			$res = exec_query("SELECT name,image,new_price,weight FROM ksh_shop_goods WHERE id='".$row['good']."'");
			$good = mysql_fetch_array($res);
			mysql_free_result($res);
			$content['cart_goods'][$i]['id'] = stripslashes($row['good']);
			$content['cart_goods'][$i]['name'] = stripslashes($good['name']);
			$content['cart_goods'][$i]['image'] = stripslashes($good['image']);
			$content['cart_goods'][$i]['new_price'] = stripslashes($good['new_price']);
			$content['cart_goods'][$i]['new_qty'] = stripslashes($row['new_qty']);
			$content['cart_goods'][$i]['weight'] = stripslashes($good['weight']);

			$i++;

			$sum_qty = $sum_qty + stripslashes($row['new_qty']);
			$sum_weight = $sum_weight + $good['weight'] * $row['new_qty'];
			$sum_price = $sum_price + $row['new_qty'] * $good['new_price'];
		}
		mysql_free_result($result);

		$weight = $sum_weight;
		debug ("weight: $weight");

		$delivery_formula = $config['shop']['delivery_formula'];
		$eval_string = "\$sum_delivery = $delivery_formula";
		debug ("eval_string: ".$eval_string);
		eval($eval_string);
		$sum_delivery = round($sum_delivery + $sum_delivery * $config['shop']['tax'], $config['shop']['round_value']);
		debug ("sum_delivery: $sum_delivery");

		$sum_plus_delivery = $sum_price + $sum_delivery;
		debug ("sum_plus_delivery: $sum_plus_delivery");

		$insurance = $sum_plus_delivery * $config['shop']['delivery_insurance'];
		$insurance = round($insurance, $config['shop']['round_value']);
		debug ("insurance: ".$insurance);
			
			
		$sum_cost_formula = $config['shop']['sum_cost_formula'];
		$eval_string = "\$sum_cost = $sum_cost_formula";
		debug ("eval_string: ".$eval_string);
		eval($eval_string);
		debug ("sum_cost: ".$sum_cost);

		$content['sum_qty'] = $sum_qty;
		$content['sum_price'] = $sum_price;
		$content['sum_weight'] = $sum_weight;
		$content['sum_delivery'] = $sum_delivery;
		$content['sum_plus_delivery'] = $sum_plus_delivery;
		$content['insurance'] = $insurance;
		$content['packing_cost'] = $config['shop']['packing_cost'];
		$content['sum_cost'] = $sum_cost;


		$result = exec_query("SELECT * FROM ksh_users WHERE id='".mysql_real_escape_string($order['user'])."'");
		$user_data = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['first_name'] = stripslashes($user_data['first_name']);
		$content['second_name'] = stripslashes($user_data['second_name']);
		$content['sur_name'] = stripslashes($user_data['sur_name']);
		$content['post_code'] = stripslashes($user_data['post_code']);
		$content['area'] = stripslashes($user_data['area']);
		$content['city'] = stripslashes($user_data['city']);
		$content['address'] = stripslashes($user_data['address']);

		if ("Отмена" != $order_status) $content['cancel'] = "yes";

	}


	debug ("*** end:shop_orders_view ***");
	return $content;
}

function shop_orders_del()
{
	debug ("*** shop_orders_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'admin_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['admin_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	$content['id'] = $_GET['orders'];

	debug ("*** end:shop_orders_del ***");
	return $content;
}

function shop_orders_cancel()
{
	debug ("*** shop_orders_cancel ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'admin_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['admin_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	$content['id'] = $_GET['orders'];

	debug ("*** end:shop_orders_cancel ***");
	return $content;
}


?>
