<?php

function calc_adv_city_info()
{
    debug ("*** calc_adv_city_info ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'admin_link' => '',
        'result' => '',
        'city_title' => '',
        'city_descr' => ''
    );
	

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
    }

    if ( isset( $_GET['city'] ) )
    {
        $city_id = $_GET['city'];
    }
    else
    {
        $city_id = 0;
    }
	$sql_query = "SELECT title, descr FROM ksh_calc_adv_cities WHERE id='".mysql_real_escape_string($city_id)."'";
    $result = exec_query( $sql_query );
    $city = mysql_fetch_array( $result );
    mysql_free_result( $result);
    stripslashes( $city );

    $content['city_title'] = $city['title'];
    $content['city_descr'] = $city['descr'];

    debug ("*** end: calc_adv_city_info");
    return $content;
}



function calc_adv_view_cities()
{
    debug ("*** calc_adv_view_cities ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'admin_link' => '',
        'result' => '',
		'cities' => ''
    );
	$i = 0;

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
		$sql_query = "SELECT id, title FROM ksh_calc_adv_cities";
		$result = exec_query($sql_query);
		$i = 0;
		while ($city = mysql_fetch_array($result))
		{
			stripslashes($city);
			$content['cities'][$i]['id'] = $city['id'];
			$content['cities'][$i]['title'] = $city['title'];
			debug ("i: ".$i);
			debug ("id: ".$content['cities'][$i]['id']);
			debug ("title: ".$content['cities'][$i]['title']);
			$i++;
		}
    }
	else
	{
		$content['result'] = "Пожалуйста, зарегистрируйтесь как администратор";
	}
	


    debug ("*** end: calc_adv_view_cities");
    return $content;
}

function calc_adv_city_add()
{
    debug ("*** calc_adv_city_add ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'admin_link' => '',
        'result' => ''
    );
	$i = 0;

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
		if (isset($_POST['do_add']))
		{
            /*
			$times = $_POST['time_1']."|".$_POST['time_2'];
			$time_prices = str_replace(",", ".", $_POST['time_price_1']."|".$_POST['time_price_2']);
            */
            $times = "";
			$time_prices = "";
			for ($i = 0; $i <= 11; $i++)
			{
				debug ("i: ".$i);
				$time = $_POST['time_'.$i];
				$price = str_replace(",", ".", $_POST['time_price_'.$i]);
				debug ("time_".$i.": ".$time);
				debug ("time_price_".$i.": ".$price);
				if ("" != $time && "" != $price)
				{
					$times .= $time."|";
					$time_prices .= $price."|";
				}
			}
			if ("|" == substr($times, -1))
				$times = substr_replace($times, "", -1);
			if ("|" == substr($time_prices, -1))
				$time_prices = substr_replace($time_prices, "", -1);
			debug ("times: ".$times);
			debug ("prices: ".$time_prices);

            $discounts = "";
			$discount_prices = "";
			for ($i = 0; $i <= 10; $i++)
			{
				debug ("i: ".$i);
				$discount = $_POST['discount_'.$i];
				$price = str_replace(",", ".", $_POST['discount_price_'.$i]);
				debug ("discount_".$i.": ".$discount);
				debug ("discount_price_".$i.": ".$price);
				if ("" != $discount && "" != $price)
				{
					$discounts .= $discount."|";
					$discount_prices .= $price."|";
				}
			}
			if ("|" == substr($discounts, -1))
				$discounts = substr_replace($discounts, "", -1);
			if ("|" == substr($discount_prices, -1))
				$discount_prices = substr_replace($discount_prices, "", -1);
			debug ("discounts: ".$discounts);
			debug ("prices: ".$discount_prices);


			$sql_query = "INSERT INTO ksh_calc_adv_cities 
				(
				title, 
				descr, 
				calc_type, 
				price_prime, 
				price_noprime, 
				times, 
				time_prices, 
				month_1, 
				month_2, 
				month_3, 
				month_4, 
				month_5, 
				month_6, 
				month_7, 
				month_8, 
				month_9, 
				month_10, 
				month_11, 
				month_12, 
				noresident_coef, 
				discount_type, 
				discount_from, 
				discount
				) 
				VALUES (
				'".mysql_real_escape_string($_POST['title'])."', 
				'".mysql_real_escape_string($_POST['descr'])."', 
				'".mysql_real_escape_string($_POST['calc_type'])."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['price_prime']))."',
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['price_noprime']))."',  
				'".mysql_real_escape_string($times)."', 
				'".mysql_real_escape_string($time_prices)."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_1']))."',
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_2']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_3']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_4']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_5']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_6']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_7']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_8']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_9']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_10']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_11']))."', 
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['month_12']))."',  
				'".mysql_real_escape_string(str_replace(",", ".", $_POST['noresident_coef']))."', 
				'".mysql_real_escape_string($_POST['discount_type'])."', 
				'".mysql_real_escape_string($discounts)."', 
				'".mysql_real_escape_string($discount_prices)."'
				)";
			exec_query ($sql_query);
		}

		for ($i = 1; $i <= 12; $i++)
		{
			$content['month_coefs_form'][$i]['id'] = $i;
			$content['month_coefs_form'][$i]['name'] = base_get_month_name($i);
		}

    }
	else
	{
		$content['result'] = "Пожалуйста, зарегистрируйтесь как администратор";
	}


    debug ("*** end: calc_adv_city_add");
    return $content;
}

function calc_adv_city_edit()
{
    debug ("*** calc_adv_city_edit ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'admin_link' => '',
        'result' => '',
        'season_coefs_form' => '',
        'noresident_coef' => '',
        'discount_time_checked' => '',
        'discount_price_checked' => ''
    );
	$i = 0;

    if (1 == $user['id'])
    {
        debug ("user has admin rights");

		$city_id = $_GET['city'];

		if (isset($_POST['do_save']))
		{
			$times = "";
			$time_prices = "";
			for ($i = 0; $i <= 11; $i++)
			{
				debug ("i: ".$i);
				$time = $_POST['time_'.$i];
				$price = str_replace(",", ".", $_POST['time_price_'.$i]);
				debug ("time_".$i.": ".$time);
				debug ("time_price_".$i.": ".$price);
				if ("" != $time && "" != $price)
				{
					$times .= $time."|";
					$time_prices .= $price."|";
				}
			}
			if ("|" == substr($times, -1))
				$times = substr_replace($times, "", -1);
			if ("|" == substr($time_prices, -1))
				$time_prices = substr_replace($time_prices, "", -1);
			debug ("times: ".$times);
			debug ("prices: ".$time_prices);

			/*
			$times = $_POST['time_1']."|".$_POST['time_2'];
			$time_prices = str_replace(",", ".", $_POST['time_price_1']."|".$_POST['time_price_2']);
			*/


			$discounts = "";
			$discount_prices = "";
			for ($i = 0; $i <= 10; $i++)
			{
				debug ("i: ".$i);
				$discount = $_POST['discount_'.$i];
				$price = str_replace(",", ".", $_POST['discount_price_'.$i]);
				debug ("discount_".$i.": ".$discount);
				debug ("discount_price_".$i.": ".$price);
				if ("" != $discount && "" != $price)
				{
					$discounts .= $discount."|";
					$discount_prices .= $price."|";
				}
			}
			if ("|" == substr($discounts, -1))
				$discounts = substr_replace($discounts, "", -1);
			if ("|" == substr($discount_prices, -1))
				$discount_prices = substr_replace($discount_prices, "", -1);
			debug ("discounts: ".$discounts);
			debug ("prices: ".$discount_prices);
	

			$sql_query = "UPDATE ksh_calc_adv_cities set 
				title='".mysql_real_escape_string($_POST['title'])."', 
				descr='".mysql_real_escape_string($_POST['descr'])."', 
				calc_type='".mysql_real_escape_string($_POST['calc_type'])."', 
				price_prime='".mysql_real_escape_string(str_replace(",", ".", $_POST['price_prime']))."', 
				price_noprime='".mysql_real_escape_string(str_replace(",", ".", $_POST['price_noprime']))."', 
				times='".mysql_real_escape_string($times)."', 
				time_prices='".mysql_real_escape_string($time_prices)."', 
				month_1='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_1']))."',
				month_2='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_2']))."', 
				month_3='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_3']))."', 
				month_4='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_4']))."', 
				month_5='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_5']))."', 
				month_6='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_6']))."', 
				month_7='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_7']))."', 
				month_8='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_8']))."', 
				month_9='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_9']))."', 
				month_10='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_10']))."', 
				month_11='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_11']))."', 
				month_12='".mysql_real_escape_string(str_replace(",", ".", $_POST['month_12']))."', 
				noresident_coef='".mysql_real_escape_string(str_replace(",", ".", $_POST['noresident_coef']))."', 
				discount_type='".mysql_real_escape_string($_POST['discount_type'])."', 
				discount_from='".mysql_real_escape_string($discounts)."', 
				discount='".mysql_real_escape_string($discount_prices)."' 
				WHERE id='".mysql_real_escape_string($city_id)."'";
			exec_query ($sql_query);
		}


		$sql_query = "SELECT * FROM ksh_calc_adv_cities WHERE id='".mysql_real_escape_string($city_id)."'";
		$result = exec_query($sql_query);
		$city = mysql_fetch_array($result);
		stripslashes($city);

		$times = explode("|", $city['times']); 
		$time_prices = explode("|", $city['time_prices']);
		
		foreach ($times as $k => $v)
		{
			$content['times_'.$k] = $v;
			$content['time_prices_'.$k] = $time_prices[$k];
		}


        for ($i = 1; $i <= 12; $i++)
        {
            $content['season_coefs_form'][$i]['month_id'] = $i;
            $content['season_coefs_form'][$i]['month_name'] = base_get_month_name( $i );
            $content['season_coefs_form'][$i]['coef'] = $city['month_'.$i];
        }

        $content['noresident_coef'] = $city['noresident_coef'];

        if ( "0" == $city['discount_type'] )
            $content['discount_time_checked'] = "checked";
        else
            $content['discount_price_checked'] = "checked";
        


		$discounts = explode("|", $city['discount_from']); 
		$discount_prices = explode("|", $city['discount']);
		
		foreach ( $discounts as $k => $v )
        {
            $content['discount_'.$k] = $v;
            $content['discount_price_'.$k] = $discount_prices[$k];
        }

		$discount_time_checked = "";
		$discount_price_checked = "";

		if ("1" == $city['discount_type'])
			$discount_price_checked = "checked";
		else
			$discount_time_checked = "checked";
	

		if ("1" == $city['calc_type'])
			$calc_time_checked = "checked";
		else
			$calc_prime_checked = "checked";

		$content['id'] = $city['id'];
		$content['title'] = $city['title'];
		$content['descr'] = $city['descr'];
		$content['calc_prime_checked'] = $calc_prime_checked;
		$content['calc_time_checked'] = $calc_time_checked;
		$content['price_prime'] = $city['price_prime'];
		$content['price_noprime'] = $city['price_noprime'];
		

    }
	else
	{
		$content['result'] = "Пожалуйста, зарегистрируйтесь как администратор";
	}


    debug ("*** end: calc_adv_city_edit");
    return $content;
}



?>
