<?php

// Shopping cart functions of the "shop" module

function shop_cart_add()
{
	debug ("*** shop_cart_add ***");
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
		if (isset($_POST['do_add']))
        {
            debug ("there is good to add");
			$if_exist = mysql_result(exec_query("SELECT count(*) FROM ksh_shop_carts WHERE user='".$user['id']."' and good='".mysql_real_escape_string($_POST['id'])."'"), 0, 0);
			if ("0" != $if_exist)
            {
            	debug ("such good is in the cart yet");
				//$result = exec_query("SELECT new_qty,used_qty FROM carts WHERE user='".$user['id']."' AND good='".mysql_real_escape_string($_POST['id'])."'");
				$result = exec_query("SELECT new_qty FROM ksh_shop_carts WHERE user='".$user['id']."' AND good='".mysql_real_escape_string($_POST['id'])."'");
				$qty = mysql_fetch_array($result);
				mysql_free_result($result);
				//exec_query("UPDATE carts SET new_qty='".($qty['new_qty'] + $_POST['new_qty'])."', used_qty='".($qty['used_qty'] + $_POST['used_qty'])."' WHERE user='".$user['id']."' AND good='".mysql_real_escape_string($_POST['id'])."'");
				exec_query("UPDATE ksh_shop_carts SET new_qty='".($qty['new_qty'] + $_POST['new_qty'])."' WHERE user='".$user['id']."' AND good='".mysql_real_escape_string($_POST['id'])."'");
				$content['result'] = "Товар добавлен в Вашу <a href=\"/index.php?module=shop&action=cart_view\">корзину</a>.";
			}
			else
			{
				debug ("no such good is in the cart");
				//exec_query("INSERT INTO carts (user, good, new_qty, used_qty) VALUES ('".mysql_real_escape_string($user['id'])."', '".mysql_real_escape_string($_POST['id'])."', '".mysql_real_escape_string($_POST['new_qty'])."', '".mysql_real_escape_string($_POST['used_qty'])."')");
				exec_query("INSERT INTO ksh_shop_carts (user, good, new_qty) VALUES ('".mysql_real_escape_string($user['id'])."', '".mysql_real_escape_string($_POST['id'])."', '".mysql_real_escape_string($_POST['new_qty'])."')");
				$content['result'] = "Товар добавлен в Вашу <a href=\"/index.php?module=shop&action=cart_view\">корзину</a>.";
			}

		}
        else
			debug ("there is no good to add");
	}


	debug ("*** end:shop_cart_add ***");
	return $content;
}

function shop_cart_view()
{
	debug ("*** shop_cart_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'cart_goods' => '',
		'sum_qty' => '',
		'sum_price' => '',
		'sum_weight' => '',
		'sum_delivery' => '',
		'sum_packing' => '',
		'sum_cost' => '',
		'show_cart' => ''
	);

	$sum_price = 0;
	$sum_qty = 0;
	$sum_weight = 0;

	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if (0 == $_SESSION['authed'])
	{
		$content['content'] = "Пожалуйста, сначала войдите на сервер.";
	}
	else
	{
		if (isset($_POST['do_del']))
		{
			debug ("deleting item from cart");
			exec_query ("DELETE FROM ksh_shop_carts WHERE id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] .= "Товар удалён из корзины";
		}

		debug ("checking items in the cart");
		$result = exec_query("SELECT * FROM `ksh_shop_carts` WHERE `user`='".mysql_real_escape_string($user['id'])."'");
		while ($row = mysql_fetch_array($result))
		{
			$if_del_good = 0;
			$good_qty = mysql_result(exec_query("SELECT count(*) FROM `ksh_shop_goods` WHERE id='".mysql_real_escape_string($row['good'])."'"), 0, 0);
			debug ("good entries qty: ".$good_qty);
			if ("0" == $good_qty)
			{
				debug ("deleting item from chart");
				$if_del_good = 1;
			}
			$if_hide = mysql_result(exec_query("SELECT `if_hide` FROM `ksh_shop_goods` WHERE id='".mysql_real_escape_string($row['good'])."'"), 0, 0);
			debug ("if hide: ".$if_hide);
			if ("1" == $if_hide)
			{
				debug ("deleting item from chart");
				$if_del_good = 1;
			}
			if ($if_del_good)
			{
				$sql_query = "DELETE FROM `ksh_shop_carts` WHERE `id` = '".$row['id']."'";
				exec_query($sql_query);
				$content['result'] = "<p>Некоторые товары были удалены из Вашей корзины, так как их нет в наличии или нет в базе данных</p>";
			}
		}
		mysql_free_result($result);


		debug ("showing items in the cart");

		$result = exec_query("SELECT count(*) FROM ksh_shop_carts WHERE user='".mysql_real_escape_string($user['id'])."'");
		$items_qty = mysql_result($result, 0, 0);
		mysql_free_result($result);

		if (0 == $items_qty)
		{
			debug ("no items in the cart");
			$content['content'] .= "Корзина пуста.";
		}
		else
		{
			debug ("there are items in the cart");
			$content['show_cart'] = "yes";

			$result = exec_query("SELECT * FROM ksh_shop_carts WHERE user='".mysql_real_escape_string($user['id'])."'");
			$i = 0;
			while ($row = mysql_fetch_array($result))
			{
				//$res = exec_query("SELECT name,new_price,used_price,weight FROM goods WHERE id='".$row['good']."'");
				$res = exec_query("SELECT id,name,image,new_price,new_qty,weight FROM ksh_shop_goods WHERE id='".$row['good']."'");
				$good = mysql_fetch_array($res);
				mysql_free_result($res);

				$content['cart_goods'][$i]['id'] = stripslashes($good['id']);
				$content['cart_goods'][$i]['name'] = stripslashes($good['name']);
				$content['cart_goods'][$i]['image'] = stripslashes($good['image']);
				$content['cart_goods'][$i]['new_price'] = stripslashes($good['new_price']);
				$content['cart_goods'][$i]['new_qty'] = stripslashes(mysql_result(exec_query("SELECT new_qty FROM ksh_shop_carts WHERE id='".$row['id']."'"), 0, 0));
				debug ("new qty: ".$content['cart_goods'][$i]['new_qty']);
				$content['cart_goods'][$i]['weight'] = stripslashes($good['weight']);
				$content['cart_goods'][$i]['images_dir'] = "/themes/".$config['themes']['current']."/images";
				$content['cart_goods'][$i]['can_del'] = "yes";

				$sum_qty = $sum_qty + stripslashes($row['new_qty']);
				$sum_weight = $sum_weight + $good['weight'] * $row['new_qty'];
				$sum_price = $sum_price + $row['new_qty'] * $good['new_price'];
				$i++;
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

		}
	}

	debug ("*** end:shop_cart_view ***");
	return $content;
}

function shop_cart_del()
{
	debug ("*** shop_cart_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if (0 == $_SESSION['authed'])
	{
		debug ("not authed!");
		$content['content'] .= "Пожалуйста, сначала войдите на сервер.";
	}
	else
	{
		debug ("authed");
		$result = exec_query("SELECT * FROM ksh_shop_carts WHERE good='".mysql_real_escape_string($_GET['good'])."' and user='".$user['id']."'");
		$item = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = stripslashes($item['id']);
		$content['name'] = mysql_result(exec_query("SELECT name FROM ksh_shop_goods WHERE id='".mysql_real_escape_string($item['good'])."'"), 0, 0);
	}


	debug ("*** end:shop_cart_del ***");
	return $content;
}

/* Old functions */

function shop_cart_add_multiple()
{
        debug ("*** module: shop; function: shop_cart_add_multiple");
        global $user;
        $content = array(
			'result' => '',
			'content' => ''
		);
        $res = 0;

        if (0 == $_SESSION['authed'])
        {
            debug ("not authed!");
			$content['result'] .= "Товары не добавлены";
			$content['content'] .= "Пожалуйста, сначала войдите на сайт";
        }
        else
        {
        	if (isset($_POST['do_add']))
        	{
	            debug ("there are goods to add");
            	unset ($_POST['do_add']);
            	foreach ($_POST as $k => $v)
            	{
	                debug ($k.":".$v);

                	$if_exist = mysql_result(exec_query("SELECT count(*) FROM ksh_shop_carts WHERE user='".$user['id']."' and good='".mysql_real_escape_string($v)."'"), 0, 0);
                	if ("0" != $if_exist)
                	{
                        debug ("such good is in the cart yet");
                        $result = exec_query("SELECT new_qty FROM ksh_shop_carts WHERE user='".$user['id']."' AND good='".mysql_real_escape_string($v)."'");
                        $qty = mysql_fetch_array($result);
                        mysql_free_result($result);
                        exec_query("UPDATE ksh_shop_carts SET new_qty='".($qty['new_qty'] + 1)."' WHERE user='".$user['id']."' AND good='".mysql_real_escape_string($v)."'");
                        $res = 1;
                	}
                	else
                	{
                        debug ("no such good is in the cart");
                        exec_query("INSERT INTO ksh_shop_carts (user, good, new_qty) VALUES ('".mysql_real_escape_string($user['id'])."', '".mysql_real_escape_string($v)."', '".mysql_real_escape_string(1)."')");
                        $res = 1;
                	}

            	}

        	}
        	else debug ("there are no goods to add");
        }

        if (1 == $res) $content['content'] .= "Товары добавлены в Вашу <a href=\"/index.php?module=shop&action=cart_view\">корзину</a>.";

        debug ("*** end: module: shop; function: shop_cart_add_multiple");
        return $content;
}




function shop_cart_view_short()
{
        debug ("*** module: shop; function: shop_cart_view_short");
        global $user;

        $content['content'] = "";
        $sum_price = 0;

        if (0 == $_SESSION['authed'])
        {
                $content['content'] .= "<p>Пожалуйста, сначала войдите на сервер.</p>";
        }
        else
        {
                $result = exec_query("SELECT count(*) FROM carts WHERE user='".mysql_real_escape_string($user['id'])."'");
                $items_qty = mysql_result($result, 0, 0);
                mysql_free_result($result);

                if (0 == $items_qty)
                {
                        debug ("no items in the cart");
                        $content['content'] .= "<p align=\"center\">Корзина пуста</p>";
                }
                else
                {

                $content['content'] .= "<p>";
                $result = exec_query("SELECT * FROM carts WHERE user='".mysql_real_escape_string($user['id'])."'");
                while ($row = mysql_fetch_array($result))
                {
                        //$res = exec_query("SELECT id,name,new_price,used_price,weight FROM goods WHERE id='".$row['good']."'");
                        $res = exec_query("SELECT id,name,new_price,weight FROM goods WHERE id='".$row['good']."'");
                        $good = mysql_fetch_array($res);
                        mysql_free_result($res);

                        $content['content'] .= "<span class=\"lst_title\"><a href=\"/index.php?module=shop&action=view_good&good=".$good['id']."\">".$good['name']."</a></span><br>";

                        if (("" != $row['new_qty']) && ("0" != $row['new_qty']))
                        {
                                //$content['content'] .= "новый: ".$good['new_price']." руб.";
                                $content['content'] .= "стоимость: ".$good['new_price']." руб.";
                                if ("1" != $row['new_qty']) $content['content'] .= " (x".$row['new_qty'].")";
                                $content['content'] .= "<br>";
                                $sum_price = $sum_price + $good['new_price'] * $row['new_qty'];
                        }
                        /*
                        if (("" != $row['used_qty']) && ("0" != $row['used_qty']))
                        {
                                $content['content'] .= "б/у: ".$good['used_price']." руб.";
                                if ("1" != $row['used_qty']) $content['content'] .= " (x".$row['used_qty'].")";
                                $content['content'] .= "<br>";
                                $sum_price = $sum_price + $good['used_price'] * $row['used_qty'];
                        }
                        */
                }
                mysql_free_result($result);
                $content['content'] .= "<hr><b>Итого: ".$sum_price." руб.</b><br><a href=\"/index.php?module=shop&action=cart_view\">Оформить</a></p>";
                }
        }

        return $content;
        debug ("*** end: module: shop; function: shop_cart_view_short");
}




?>
