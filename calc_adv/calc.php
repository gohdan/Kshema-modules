<?php

// Calc functions of the calc_adv module

function calc_adv_calc()
{
    debug ("*** calc_adv_calc ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'admin_link' => '',
        'result' => '',
		'if_show_calculator' => '',
		'if_show_cities_select' => '',
		'cities_select' => '',
		'times' => '',
		'time_prices' => '',
		'noresident_checked' => '',
		'if_show_result_string' => '',
		'if_show_result_table' => '',
		'if_noresident' => '',
		'if_show_discount' => '',
		'results' => ''
    );
	$cities = array();
	$i = 0;

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
    }
   
	// Если нужно прибить какой-нибудь результат в БД   
    if (isset($_POST['do_del_result']))
    {
    	$sql_query = "DELETE FROM ksh_calc_adv_calcs WHERE id='".mysql_real_escape_string($_POST['result_id'])."'";
    	exec_query($sql_query);
    }

	// При заходе в калькулятор прибиваем все просчёты, сделанные до того
	if (isset($_POST['do_go']))
	{
		$sql_query = "DELETE FROM ksh_calc_adv_calcs WHERE user='".mysql_real_escape_string($user['id'])."'";
		exec_query ($sql_query);
	}
	
	// По-любому нам нужен будет город
    $sql_query = "SELECT * FROM ksh_calc_adv_cities";
	$result = exec_query($sql_query);
	while ($city = mysql_fetch_array($result))
	{
		stripslashes($city);
		$cities[$city['id']] = $city;
	}
	mysql_free_result($result);

	/* Сделали расчёт, записали его в базу данных */

	if (isset($_POST['do_calc']))
	{
 		debug ("do calc");

		$city_current = $cities[$_POST['city']];
		$content['city_id'] = $city_current['id'];
		$content['city_title'] = $city_current['title'];

		// $month = date("n");
		$month = $_POST['month'];
		debug ("month: ".$month);
		$content['month_name'] = base_get_month_name($month);
		
		if (isset($_POST['hron']))
			$hron = $_POST['hron'];
		else
 			$hron = 0;
 		$content['hron'] = $hron;
 			
		$prime = 0;
		$noprime = 0;
		$proc_qty = 0;
		
		debug ("calc_type: ".$city_current['calc_type']);
		if ("0" == $city_current['calc_type'])
		{
			/* Считаем стоимость по прайму */
			debug ("calc_type == 0");
			$content['if_show_result_prime'] = "yes";

			if (isset($_POST['prime']))
				$prime = $_POST['prime'];
	
			if (isset($_POST['noprime']))
				$noprime = $_POST['noprime'];

			if (isset($_POST['proc_qty']))
				$proc_qty = $_POST['proc_qty'];

			$qty_prime = round(($_POST['prime'] / 100) * $proc_qty, 0);
			$qty_noprime = round(($_POST['noprime'] / 100) * $proc_qty, 0);
			if ($qty_prime + $qty_noprime > $proc_qty)
				$qty_noprime--;

			debug ("prime qty: ".$qty_prime);
			debug ("prime price: ".$city_current['price_prime']);
			debug ("noprime qty: ".$qty_noprime);
			debug ("noprime price: ".$city_current['price_noprime']);
			$sum = $qty_prime * $city_current['price_prime'] * $hron + $qty_noprime * $city_current['price_noprime'] * $hron;

			$hron_sum = $proc_qty * $hron;
			debug ("hron sum: ".$hron_sum);

			$content['prime'] = $prime;
			$content['noprime'] = $noprime;
			$content['proc_qty'] = $proc_qty;
		}

		else
		/* Считаем стоимость по часам */
		{
			debug ("calc_type != 0");
			$content['if_show_result_time'] = "yes";

			$qty = 0;
			$times = explode("|", $city_current['times']);
			$time_prices = explode("|", $city_current['time_prices']);
			$time_qtys = array();
			
			$times_2sql = "";
			$time_qtys_2sql = "";

			$sum = 0;
			$hron_sum = 0;

			foreach ($times as $k => $v)
			{
				if (isset($_POST['time_qty_'.$k]) && "" != $_POST['time_qty_'.$k])
				{
					$time_qtys[$k] = $_POST['time_qty_'.$k];
					$qty = $qty + $time_qtys[$k];
					debug ("qty: ".$qty);
					
					$times_2sql .= $v."|";
					$time_qtys_2sql .= $time_qtys[$k]."|";
				}
				else
					$time_qtys[$k] = "";

				$content['times'][$k]['time'] = $v;
				$content['time_prices'][$k]['id'] = $k;
				$content['time_prices'][$k]['qty'] = $time_qtys[$k];
				
				

				$sum = $sum + $time_qtys[$k] * $hron * $time_prices[$k];
				debug ("sum: ".$sum);
		
			}
			
			if ("|" == substr($times_2sql, -1))
				$times_2sql = substr_replace($times_2sql, "", -1);
			if ("|" == substr($time_qtys_2sql, -1))
				$time_qtys_2sql = substr_replace($time_qtys_2sql, "", -1);

			$hron_sum = $hron * $qty;
			debug ("hron sum: ".$hron_sum);

		}
		
		
		$content['sum'] = $sum;
		debug ("sum: ".$sum);
		
		debug ("city season coef: ".$city_current['month_'.$month]);
		$season_coef = $city_current['month_'.$month];
		debug ("season coef: ".$season_coef);
		$content['season_coef'] = $season_coef;
		$sum_season = $sum * $season_coef;
		$content['sum_season'] = $sum_season;
		debug ("sum_season: ".$sum_season);

		$if_noresident_sql = "0";
		
		if (isset($_POST['noresident']))
		{
			debug ("noresident");
			$content['if_noresident'] = "yes";
			$if_noresident_sql = "1";
			$noresident_coef = $city_current['noresident_coef'];
			debug ("noresident coef: ".$noresident_coef);
			$content['noresident_coef'] = $noresident_coef;
			$sum_noresident = $sum_season * $noresident_coef;
			$content['sum_noresident'] = $sum_noresident;
			
		}
		else
		{
			debug ("resident");
			$sum_noresident = $sum_season;
		}
		debug ("sum_noresident: ".$sum_noresident);

		$discount = 0;
		if ("0" == $city_current['discount_type'])
		{
			// Скидка с времени

			$discounts = explode ("|", $city_current['discount_from']);
			$discount_prices = explode ("|", $city_current['discount']);
			$dsc = array();
			foreach ($discounts as $k => $v)
			{
				$dsc[$v] = $discount_prices[$k];
			}
			arsort($discounts);
			foreach ($dsc as $k => $v)
			{
				debug ("discount from ".$k." is ".$v);
				if ($hron_sum >= $k)
				{
					debug ("have discount");
					$discount = $v;
				}
			}
		}
		else
		{
			// Скидка со стоимости 
			$discounts = explode ("|", $city_current['discount_from']);
			$discount_prices = explode ("|", $city_current['discount']);
			$dsc = array();
			foreach ($discounts as $k => $v)
			{
				$dsc[$v] = $discount_prices[$k];
			}
			arsort($discounts);
			foreach ($dsc as $k => $v)
			{
				debug ("discount from ".$k." is ".$v);
				if ($sum_noresident >= $k)
				{
					debug ("have discount");
					$discount = $v;
				}
			}
		
		
		}
		
		if (0 != $discount)
		{
			$content['if_show_discount'] = "yes";
			$content['discount'] = $discount;
            $sum_final = $sum_noresident - ( $sum_noresident * ($discount / 100) );
		}
        else
            $sum_final = $sum_noresident;
		

		$content['sum_final'] = $sum_final;

	
		$sql_query = "INSERT INTO ksh_calc_adv_calcs (
			user,
			month,
			city,
			hron,
			calc_type,
			prime_qty,
			noprime_qty,
			qty,
			times,
			times_qty,
			sum,
			season_coef,
			sum_season,
			if_noresident,
			noresident_coef,
			sum_noresident,
			discount,
			sum_final
			) VALUES (
			'".mysql_real_escape_string($user['id'])."',
			'".mysql_real_escape_string($month)."',
			'".mysql_real_escape_string($city_current['title'])."',
			'".mysql_real_escape_string($hron)."',
			'".mysql_real_escape_string($city_current['calc_type'])."',
			'".mysql_real_escape_string($qty_prime)."',
			'".mysql_real_escape_string($qty_noprime)."',
			'".mysql_real_escape_string($qty)."',
			'".mysql_real_escape_string($times_2sql)."',
			'".mysql_real_escape_string($time_qtys_2sql)."',
			'".mysql_real_escape_string($sum)."',
			'".mysql_real_escape_string($season_coef)."',
			'".mysql_real_escape_string($sum_season)."',
			'".mysql_real_escape_string($if_noresident_sql)."',
			'".mysql_real_escape_string($noresident_coef)."',
			'".mysql_real_escape_string($sum_noresident)."',
			'".mysql_real_escape_string($discount)."',
			'".mysql_real_escape_string($sum_final)."'			
			)";
		exec_query($sql_query);

		/* Deleting all result except the 1 last */
		$last_id = mysql_result(exec_query("SELECT id FROM ksh_calc_adv_calcs WHERE user='".mysql_real_escape_string($user['id'])."' ORDER BY id DESC LIMIT 1"), 0, 0);

		$sql_query = "DELETE FROM ksh_calc_adv_calcs WHERE user='".mysql_real_escape_string($user['id'])."' AND id < ".$last_id;
		exec_query($sql_query);


	}
	
	/* Вытащили все расчёты из базы данных, показали */
		
	$sql_query = "SELECT * FROM ksh_calc_adv_calcs WHERE user='".$user['id']."' ORDER BY id DESC LIMIT 1";
	$result = exec_query($sql_query);

	$row = mysql_fetch_array($result);
	stripslashes($row);
	$content['result_id'] = $row['id'];
	$content['result_city_title'] = $row['city'];
	$content['result_month_name'] = base_get_month_name($row['month']);
	$content['result_hron'] = $row['hron'];
	if ("0" == $row['calc_type'])
	{
		// Считали по прайму
		$content['result_if_show_result_prime'] = "yes";
		$content['result_prime'] = $row['prime_qty'];
		$content['result_noprime'] = $row['noprime_qty'];
	}
	else
	{
		// Считали по времени
		$content['result_if_show_result_time'] = "yes";
		$times = explode("|", $row['times']);
		$times_qty = explode("|", $row['times_qty']);
		$content['result_times'] = "";
		$content['result_time_qtys'] = "";
		foreach($times as $k => $v)
		{
			$content['result_times'] .= "<th scope=\"col\">".$v."</th><th rowspan=\"2\" class=\"bb\" scope=\"col\"><img src=\"/themes/calc_adv/images/bb.png\" width=\"2\" height=\"131\" /></th>";
			$content['result_times_pure'] .= "<th>".$v."</th>";
			$content['result_times_csv'] .= $v.";";
			$content['result_time_qtys'] .= "<td>".$times_qty[$k]."</td>";
			$content['result_time_qtys_csv'] .= $times_qty[$k].";";
		}
			
	}
	$content['result_sum'] = $row['sum'];
	$content['result_season_coef'] = $row['season_coef'];
	$content['result_sum_season'] = $row['sum_season'];
	if ("1" == $row['if_noresident'])
	{
		$content['result_if_noresident'] = "yes";
		$content['result_noresident_coef'] = $row['noresident_coef'];
		$content['result_sum_noresident'] = $row['sum_noresident'];
	}
	if ("0" != $row['discount'] && "" != $row['discount'])
	{
		$content['result_if_show_discount'] = "yes";
		$content['result_discount'] = $row['discount'];
	}
	$content['result_sum_final'] = $row['sum_final'];

	if (isset($_POST['do_calc']))
	{
		$content['if_show_result_table'] = "yes";	

	}

	if (isset($_POST['do_select_city']))
	{
		$content['if_show_result_string'] = "yes";
		$content['month'] = $_POST['month'];
		
		// Show the result string
		// previous:
		// $sql_query = "SELECT city, sum_final FROM ksh_calc_adv_calcs WHERE user='".mysql_real_escape_string($user['id'])."' ORDER BY id DESC LIMIT 1,1";
		// current:
		$sql_query = "SELECT city, sum_final FROM ksh_calc_adv_calcs WHERE user='".mysql_real_escape_string($user['id'])."' ORDER BY id DESC LIMIT 1";	
		$result = exec_query($sql_query);
		$prev_result = mysql_fetch_array($result);
		mysql_free_result($result);
		stripslashes($prev_result);
		$content['result'] =  $prev_result['city']." - стоимость размещения Вашего ролика составляет ".$prev_result['sum_final']." руб.";

		// Write result to HTML file
		$filepath = $config['base']['doc_root']."temp/";
		$filename = "calc_".$user['id'].".html";
		debug ("HTML file: ".$filepath.$filename);
		file_put_contents ($filepath.$filename, gen_content("calc_adv", "result_html", $content));
		$content['filename_html'] = $filename;
		
		// Write result to CSV file
		$filepath = $config['base']['doc_root']."temp/";
		$filename = "calc_".$user['id'].".csv";
		debug ("CSV file: ".$filepath.$filename);
		file_put_contents ($filepath.$filename, gen_content("calc_adv", "result_csv", $content));
		$content['filename_csv'] = $filename;


	}


	
	

	/* Если город выбран, показали формочку для расчёта */
	if (isset($_POST['do_select_city']) || isset($_POST['do_go']))
	{
		/* Показываем форму калькулятора */
		$content['if_show_calculator'] = "yes";


		/*
		$month = date("n");
		*/
		$month = $_POST['month'];
		debug ("month: ".$month);
		$content['month'] = $month;
		$content['month_name'] = base_get_month_name($month);

		$city_current = $cities[$_POST['city']];
		$content['city_id'] = $city_current['id'];
		$content['city_title'] = $city_current['title'];

		


		/* Выводим форму расчёта прайм / не прайм */
		debug ("calc_type: ".$city_current['calc_type']);
		if ("0" == $city_current['calc_type'])
		{
			debug ("calc_type == 0");
			$content['if_show_prime'] = "yes";
		}

		/* Конец вывода формы расчёта прайм / не прайм */

		else
		/* Выводим форму расчёта по часам */
		{
			debug ("calc_type != 0");
			$content['if_show_time'] = "yes";

			$times = explode("|", $city_current['times']);

			foreach ($times as $k => $v)
			{
				$content['times'][$k]['time'] = $v;
				$content['time_qtys'][$k]['id'] = $k;
			}

		}
		/* Конец вывода формы расчёта по часам */


	}
	/* Если город не выбран, показали формочку для выбора города */
	else 
	{
		/* Показываем форму выбора города */

		$content['if_show_calculator'] = "";
		$content['if_show_cities_select'] = "yes";

		for ($i = 1; $i <= 12; $i++)
		{
			$content['month_select'][$i]['month_id'] = $i;
			$content['month_select'][$i]['month_name'] = base_get_month_name($i);
		}

		$sql_query = "SELECT * FROM ksh_calc_adv_cities";
		$result = exec_query($sql_query);
		while ($city = mysql_fetch_array($result))
		{
			stripslashes($city);
			$content['cities_select'][$city['id']] = $city;
		}
		mysql_free_result($result);
		
	}

    debug ("*** end: calc_adv_calc");
    return $content;
}


?>
