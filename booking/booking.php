<?php

// Booking functions of the booking module


function booking_add()
{

    debug ("*** booking_add ***");
    global $config;
    global $user;
    $content = array(
    	'content' => '',
		'category' => '',
		'show_admin_link' => '',
		'show_additional_sleepplaces' => '',
		'additional_bed_checked' => '',
		'additional_cot_checked' => '',
		'pay_type_1_checked' => '',
		'pay_type_2_checked' => '',
		'show_form' => 'yes',
		'show_result' => ''
    );

	$priv = new Privileges();

	if ($priv -> has("booking", "admin", "write"))
		$content['show_admin_link'] = "yes";

    if (isset($_POST['do_add']) && $priv -> has("booking", "add", "write"))
    {
        debug ("have data to insert into DB");

		$content['show_form'] = "";
		if (!isset($_GET['page_template']))
			$content['show_result_'.$config['base']['lang']['current']] = "yes";
		else
			$content['show_result_worker'] = "yes";

        unset ($_POST['do_add']);

		if (isset($_POST['year1']) && isset($_POST['month1']) && isset($_POST['day1']))
			$_POST['date_from'] = $_POST['year1']."-".$_POST['month1']."-".$_POST['day1'];
		if (isset($_POST['year2']) && isset($_POST['month2']) && isset($_POST['day2']))
			$_POST['date_to'] = $_POST['year2']."-".$_POST['month2']."-".$_POST['day2'];


		$fields = "`surname`,
			`name`,
			`country`,
			`phone`,
			`email`,
			`comment`,
			`date_from`,
			`date_to`,
			`variant`,
			`adults_qty`,
			`if_children`,
			`if_extra_bed`,
			`if_transfer`,
			`rooms_qty`,
			`breakfast_qty`,
			`cost`,
			`date`";

		$values = "
			'".mysql_real_escape_string($_POST['surname'])."',
			'".mysql_real_escape_string($_POST['name'])."',
			'".mysql_real_escape_string($_POST['country'])."',
			'".mysql_real_escape_string($_POST['phone'])."',
			'".mysql_real_escape_string($_POST['email'])."',
			'".mysql_real_escape_string($_POST['comment'])."',
			'".mysql_real_escape_string($_POST['date_from'])."',
			'".mysql_real_escape_string($_POST['date_to'])."',
			'".mysql_real_escape_string($_POST['variant'])."',
			'".mysql_real_escape_string($_POST['adults_qty'])."',
			'".mysql_real_escape_string($_POST['if_children'])."',
			'".mysql_real_escape_string($_POST['if_extra_bed'])."',
			'".mysql_real_escape_string($_POST['if_transfer'])."',
			'".mysql_real_escape_string($_POST['rooms_qty'])."',
			'".mysql_real_escape_string($_POST['breakfast_qty'])."',
			'".mysql_real_escape_string($_POST['cost'])."',
			CURDATE()
		";

		if(isset($_POST['room']))
		{
			$fields .= ", `room`";
			$values .= ", '".mysql_real_escape_string($_POST['room'])."'";
		}

		if(isset($_POST['passport']))
		{
			$fields .= ", `passport`";
			$values .= ", '".mysql_real_escape_string($_POST['passport'])."'";
		}

		if(isset($_POST['payment_type']))
		{
			$fields .= ", `payment_type`";
			$values .= ", '".mysql_real_escape_string($_POST['payment_type'])."'";
		}

		if(isset($_POST['prepayment']))
		{
			$fields .= ", `prepayment`";
			$values .= ", '".mysql_real_escape_string($_POST['prepayment'])."'";
		}

		if(isset($_POST['leftover']))
		{
			$fields .= ", `leftover`";
			$values .= ", '".mysql_real_escape_string($_POST['leftover'])."'";
		}

		if(isset($_POST['payment_status']))
		{
			$fields .= ", `payment_status`";
			$values .= ", '".mysql_real_escape_string($_POST['payment_status'])."'";
		}

		if(isset($_POST['manager']))
		{
			$fields .= ", `manager`";
			$values .= ", '".mysql_real_escape_string($_POST['manager'])."'";
		}

		if(isset($_POST['dealer']))
		{
			$fields .= ", `dealer`";
			$values .= ", '".mysql_real_escape_string($_POST['dealer'])."'";
		}

		if(isset($_POST['time_from']))
		{
			$fields .= ", `time_from`";
			$values .= ", '".mysql_real_escape_string($_POST['time_from'])."'";
		}

		if(isset($_POST['time_to']))
		{
			$fields .= ", `time_to`";
			$values .= ", '".mysql_real_escape_string($_POST['time_to'])."'";
		}

		if(isset($_POST['days']))
		{
			$fields .= ", `days`";
			$values .= ", '".mysql_real_escape_string($_POST['days'])."'";
		}

		if(isset($_POST['price']))
		{
			$fields .= ", `price`";
			$values .= ", '".mysql_real_escape_string($_POST['price'])."'";
		}

        $sql_query = "INSERT INTO ksh_booking (".$fields.") values (".$values.")";

		exec_query($sql_query);


		$to = "order@lawatuna.com";
		$subject = 	"Форма бронирования";
		$headers  = "Content-type: text/plain; charset=utf-8 \r\n";
		$headers .= "From: Lawatuna <order@lawatuna.com>\r\n";
		$headers .= "Bcc: gohdan@gohdan.ru\r\n"; 

		$message = "Фамилия: ".$_POST['surname']."\r\n";
		$message .= "Имя: ".$_POST['name']."\r\n";
		$message .= "Страна: ".$_POST['country']."\r\n";
		$message .= "Контактный телефон: ".$_POST['phone']."\r\n";
		$message .= "E-mail: ".$_POST['email']."\r\n";
		$message .= "Комментарий: ".$_POST['comment']."\r\n";

		$message .= "От: ".$_POST['day1'].".".$_POST['month1'].".".$_POST['year1']."\r\n";
		$message .= "До: ".$_POST['day2'].".".$_POST['month2'].".".$_POST['year2']."\r\n";

		switch($_POST['variant'])
		{
			default: break;
			case "1": $variant = "Standard"; break;
			case "2": $variant = "Deluxe"; break;
			case "3": $variant = "Apartment"; break;
		}

		$message .= "Вариант: ".$variant."\r\n";
		$message .= "Количество взрослых: ".$_POST['adults_qty']."\r\n";

		if ("1" == $_POST['if_children'])
			$children = "да";
		else
			$children = "нет";

		if ("1" == $_POST['if_extra_bed'])
			$extra_bed = "да";
		else
			$extra_bed = "нет";

		if ("1" == $_POST['if_transfer'])
			$transfer = "да";
		else
			$transfer = "нет";

		$message .= "Дети до 12-ти лет: ".$children."\r\n";
		$message .= "Доп. кровать: ".$extra_bed."\r\n";
		$message .= "Количество номеров: ".$_POST['rooms_qty']."\r\n";
		$message .= "Трансфер: ".$transfer."\r\n";
		$message .= "Завтрак: ".$_POST['breakfast_qty']."\r\n";
		$message .= "Итоговая сумма: ".$_POST['cost']."$\r\n";

		mail($to, $subject, $message, $headers);

    }
    else
        debug ("don't have data to insert into DB");

	$i = 0;
	$sql_query = "SELECT * FROM `ksh_booking_rooms` ORDER BY `floor`, `number`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$id = stripslashes($row['id']);
		$content['order_edit_rooms_select'][$i]['id'] = $id;
		$content['order_edit_rooms_select'][$i]['number'] = stripslashes($row['number']);
		if ($id == $content['room'])
			$content['order_edit_rooms_select'][$i]['selected'] = "yes";
		$i++;
	}
	mysql_free_result($result);

    debug ("*** end: booking_add ***");
    return $content;



}


function booking_list_view()
{
        debug ("*** booking_list_view ***");
        global $config;
        global $user;
        $content = array(
        	'content' => '',
            'bookings' => '',
			'show_admin_link' => ''
        );

	$priv = new Privileges();

	if ($priv -> has("booking", "list_view", "write"))
	{

		if (isset($_POST['do_del']))
		{
			$sql_query = "DELETE FROM `ksh_booking` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
			$result = exec_query($sql_query);
			$content['result'] = "Бронь удалена";
		}

		$bookings = array();

	    $result = exec_query("SELECT * FROM ksh_booking ORDER BY `id` ASC");
	    while ($row = mysql_fetch_array($result))
	        $bookings[] = $row;
	    mysql_free_result($result);

		$content['bookings'] = array();
		$i = 0;

		foreach ($bookings as $k => $v)
		{
			$content['bookings'][$i]['id'] = stripslashes($v['id']);
			$content['bookings'][$i]['date_from'] = stripslashes($v['date_from']);
			$content['bookings'][$i]['date_to'] = stripslashes($v['date_to']);
			$content['bookings'][$i]['name'] = stripslashes($v['name']);
			$content['bookings'][$i]['surname'] = stripslashes($v['surname']);
			$content['bookings'][$i]['phone'] = stripslashes($v['phone']);
			$content['bookings'][$i]['email'] = stripslashes($v['email']);
			$content['bookings'][$i]['variant'] = stripslashes($v['variant']);
			$content['bookings'][$i]['rooms_qty'] = stripslashes($v['rooms_qty']);

			switch($content['bookings'][$i]['variant'])
			{
				default: break;
				case "1": $content['bookings'][$i]['room_type'] = "Standard"; break;
				case "2": $content['bookings'][$i]['room_type'] = "Deluxe"; break;
				case "3": $content['bookings'][$i]['room_type'] = "Apartments"; break;
			}

			$i++;
		}
	}
	else
		$content['result'] = "Недостаточно прав";
		
   debug ("*** end: booking_list_view");

   return $content;
}

function booking_edit()
{
	global $user;
	global $config;
	debug("*** booking: edit ***");

	$priv = new Privileges();

	if ($priv -> has("booking", "edit", "write"))
	{
		$booking_id = $_GET['element'];

		if (isset($_POST['do_update']))
		{
			$sql_query = "UPDATE `ksh_booking` SET
				`room` = '".mysql_real_escape_string($_POST['room'])."',
				`date_from` = '".mysql_real_escape_string($_POST['date_from'])."',
				`date_to` = '".mysql_real_escape_string($_POST['date_to'])."',
				`time_from` = '".mysql_real_escape_string($_POST['time_from'])."',
				`time_to` = '".mysql_real_escape_string($_POST['time_to'])."',
				`price` = '".mysql_real_escape_string($_POST['price'])."',
				`days` = '".mysql_real_escape_string($_POST['days'])."',
				`name` = '".mysql_real_escape_string($_POST['name'])."',
				`surname` = '".mysql_real_escape_string($_POST['surname'])."',
				`phone` = '".mysql_real_escape_string($_POST['phone'])."',
				`email` = '".mysql_real_escape_string($_POST['email'])."',
				`country` = '".mysql_real_escape_string($_POST['country'])."',
				`variant` = '".mysql_real_escape_string($_POST['variant'])."',
				`rooms_qty` = '".mysql_real_escape_string($_POST['rooms_qty'])."',
				`comment` = '".mysql_real_escape_string($_POST['comment'])."',
				`adults_qty` = '".mysql_real_escape_string($_POST['adults_qty'])."',
				`if_children` = '".mysql_real_escape_string($_POST['if_children'])."',
				`if_extra_bed` = '".mysql_real_escape_string($_POST['if_extra_bed'])."',
				`if_transfer` = '".mysql_real_escape_string($_POST['if_transfer'])."',
				`breakfast_qty` = '".mysql_real_escape_string($_POST['breakfast_qty'])."',
				`cost` = '".mysql_real_escape_string($_POST['cost'])."',
				`date` = '".mysql_real_escape_string($_POST['date'])."',
				`passport` = '".mysql_real_escape_string($_POST['passport'])."',
				`payment_type` = '".mysql_real_escape_string($_POST['payment_type'])."',
				`prepayment` = '".mysql_real_escape_string($_POST['prepayment'])."',
				`leftover` = '".mysql_real_escape_string($_POST['leftover'])."',
				`payment_status` = '".mysql_real_escape_string($_POST['payment_status'])."',
				`manager` = '".mysql_real_escape_string($_POST['manager'])."',
				`dealer` = '".mysql_real_escape_string($_POST['dealer'])."'
				WHERE `id` = '".$_POST['id']."'";
			$result = exec_query($sql_query);
			$content['result'] = "Изменения записаны";
		}


		$sql_query = "SELECT * FROM `ksh_booking` WHERE `id` = '".mysql_real_escape_string($booking_id)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = stripslashes($row['id']);
		$content['room'] = stripslashes($row['room']);
		$content['date_from'] = stripslashes($row['date_from']);
		$content['date_to'] = stripslashes($row['date_to']);
		$content['time_from'] = substr(stripslashes($row['time_from']), 0, -3);
		$content['time_to'] = substr(stripslashes($row['time_to']), 0, -3);
		$content['price'] = stripslashes($row['price']);
		$content['days'] = stripslashes($row['days']);
		$content['name'] = stripslashes($row['name']);
		$content['surname'] = stripslashes($row['surname']);
		$content['phone'] = stripslashes($row['phone']);
		$content['email'] = stripslashes($row['email']);
		$content['country'] = stripslashes($row['country']);
		$content['variant'] = stripslashes($row['variant']);
		$content['rooms_qty'] = stripslashes($row['rooms_qty']);
		$content['comment'] = stripslashes($row['comment']);
		$content['adults_qty'] = stripslashes($row['adults_qty']);
		$content['if_children'] = stripslashes($row['if_children']);
		$content['if_extra_bed'] = stripslashes($row['if_extra_bed']);
		$content['if_transfer'] = stripslashes($row['if_transfer']);
		$content['breakfast_qty'] = stripslashes($row['breakfast_qty']);
		$content['cost'] = stripslashes($row['cost']);
		$content['passport'] = stripslashes($row['passport']);
		$content['payment_type'] = stripslashes($row['payment_type']);
		$content['prepayment'] = stripslashes($row['prepayment']);
		$content['leftover'] = stripslashes($row['leftover']);
		$content['payment_status'] = stripslashes($row['payment_status']);
		$content['date'] = stripslashes($row['date']);
		$content['manager'] = stripslashes($row['manager']);
		$content['dealer'] = stripslashes($row['dealer']);

		switch($content['variant'])
		{
			default: break;
			case "1":
				$content['variant_type'] = "Standard";
				$content['variant_1_selected'] = "yes";
			break;
			case "2":
				$content['variant_type'] = "Deluxe";
				$content['variant_2_selected'] = "yes";
			break;
			case "3":
				$content['variant_type'] = "Apartments";
				$content['variant_3_selected'] = "yes";
			break;
		}

		$i = 0;
		$sql_query = "SELECT * FROM `ksh_booking_rooms` ORDER BY `floor`, `number`";
		$result = exec_query($sql_query);
		while ($row = mysql_fetch_array($result))
		{
			$id = stripslashes($row['id']);
			$content['order_edit_rooms_select'][$i]['id'] = $id;
			$content['order_edit_rooms_select'][$i]['number'] = stripslashes($row['number']);
			if ($id == $content['room'])
				$content['order_edit_rooms_select'][$i]['selected'] = "yes";
			$i++;
		}
		mysql_free_result($result);



	}
	else
		$content['content'] = "Недостаточно прав";

	debug("*** booking: edit ***");
	return $content;
}

function booking_view()
{
	global $user;
	global $config;
	debug("*** booking: view ***");
	$content = array(

	);

	$priv = new Privileges();

	if ($priv -> has("booking", "view", "write"))
	{
		$booking_id = $_GET['element'];

		$sql_query = "SELECT * FROM `ksh_booking` WHERE `id` = '".mysql_real_escape_string($booking_id)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = stripslashes($row['id']);
		$content['room'] = stripslashes($row['room']);
		$content['date_from'] = stripslashes($row['date_from']);
		$content['date_to'] = stripslashes($row['date_to']);
		$content['time_from'] = substr(stripslashes($row['time_from']), 0, -3);
		$content['time_to'] = substr(stripslashes($row['time_to']), 0, -3);
		$content['price'] = stripslashes($row['price']);
		$content['days'] = stripslashes($row['days']);
		$content['name'] = stripslashes($row['name']);
		$content['surname'] = stripslashes($row['surname']);
		$content['phone'] = stripslashes($row['phone']);
		$content['email'] = stripslashes($row['email']);
		$content['country'] = stripslashes($row['country']);
		$content['variant'] = stripslashes($row['variant']);
		$content['rooms_qty'] = stripslashes($row['rooms_qty']);
		$content['comment'] = stripslashes($row['comment']);
		$content['adults_qty'] = stripslashes($row['adults_qty']);
		$content['if_children'] = stripslashes($row['if_children']);
		$content['if_extra_bed'] = stripslashes($row['if_extra_bed']);
		$content['if_extra_bed'] = stripslashes($row['if_extra_bed']);
		$content['if_transfer'] = stripslashes($row['if_transfer']);
		$content['breakfast_qty'] = stripslashes($row['breakfast_qty']);
		$content['cost'] = stripslashes($row['cost']);
		$content['passport'] = stripslashes($row['passport']);
		$content['payment_type'] = stripslashes($row['payment_type']);
		$content['prepayment'] = stripslashes($row['prepayment']);
		$content['leftover'] = stripslashes($row['leftover']);
		$content['payment_status'] = stripslashes($row['payment_status']);
		$content['date'] = stripslashes($row['date']);
		$content['manager'] = stripslashes($row['manager']);
		$content['dealer'] = stripslashes($row['dealer']);

		switch($content['variant'])
		{
			default: break;
			case "1": $content['variant_type'] = "Standard"; break;
			case "2": $content['variant_type'] = "Deluxe"; break;
			case "3": $content['variant_type'] = "Apartments"; break;
		}

		$i = 0;
		$sql_query = "SELECT * FROM `ksh_booking_rooms` WHERE `id` = '".mysql_real_escape_string($content['room'])."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['room'] = stripslashes($row['number']);
	}
	else
		$content['content'] = "Недостаточно прав";

	debug("*** booking: view ***");
	return $content;
}

function booking_del()
{
    debug ("*** booking_del ***");
    global $config;
    global $user;

    $content = array(
    	'content' => '',
        'id' => '',
		'title' => ''
    );

	$priv = new Privileges();

    if ($priv -> has("booking", "del", "write"))
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM `ksh_booking` WHERE id='".mysql_real_escape_string($_GET['element'])."'");
        $page = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($page['id']);
		$content['date'] = stripslashes($page['date']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: booking_del ***");
    return $content;
}


function booking_orders_list()
{
        debug ("*** booking_orders_list ***");
        global $config;
        global $user;
        $content = array(
        	'content' => '',
            'bookings' => '',
			'show_admin_link' => ''
        );

	$priv = new Privileges();

	if ($priv -> has("booking", "orders_list", "write"))
	{

		if (isset($_POST['do_del']))
		{
			$sql_query = "DELETE FROM `ksh_booking` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
			$result = exec_query($sql_query);
			$content['result'] = "Бронь удалена";
		}

		$bookings = array();

		$transfer_price = 100;

	    $result = exec_query("SELECT * FROM ksh_booking ORDER BY `id` ASC");
	    while ($row = mysql_fetch_array($result))
	        $bookings[] = $row;
	    mysql_free_result($result);

		$content['bookings'] = array();
		$i = 0;

		foreach ($bookings as $k => $v)
		{
			$content['orders'][$i]['id'] = stripslashes($v['id']);
			$content['orders'][$i]['room'] = stripslashes($v['room']);
			$content['orders'][$i]['name'] = stripslashes($v['name']);
			$content['orders'][$i]['surname'] = stripslashes($v['surname']);
			$content['orders'][$i]['date_from'] = stripslashes($v['date_from']);
			$content['orders'][$i]['date_to'] = stripslashes($v['date_to']);
			$content['orders'][$i]['time_from'] = substr(stripslashes($v['time_from']), 0, -3);
			$content['orders'][$i]['time_to'] = substr(stripslashes($v['time_to']), 0, -3);
			$content['orders'][$i]['price'] = stripslashes($v['price']);
			$content['orders'][$i]['days'] = stripslashes($v['days']);
			$content['orders'][$i]['passport'] = stripslashes($v['passport']);
			$content['orders'][$i]['payment_type'] = stripslashes($v['payment_type']);
			$content['orders'][$i]['breakfast_qty'] = stripslashes($v['breakfast_qty']);
			$content['orders'][$i]['if_transfer'] = stripslashes($v['if_transfer']);
			$content['orders'][$i]['cost'] = stripslashes($v['cost']);
			$content['orders'][$i]['prepayment'] = stripslashes($v['prepayment']);
			$content['orders'][$i]['leftover'] = stripslashes($v['leftover']);
			$content['orders'][$i]['payment_status'] = stripslashes($v['payment_status']);
			$content['orders'][$i]['date'] = stripslashes($v['date']);
			$content['orders'][$i]['manager'] = stripslashes($v['manager']);
			$content['orders'][$i]['if_children'] = stripslashes($v['if_children']);
			$content['orders'][$i]['if_extra_bed'] = stripslashes($v['if_extra_bed']);
			$content['orders'][$i]['phone'] = stripslashes($v['phone']);
			$content['orders'][$i]['email'] = stripslashes($v['email']);
			$content['orders'][$i]['dealer'] = stripslashes($v['dealer']);

			$content['orders'][$i]['class'] = booking_get_order_status($content['orders'][$i]['id']);

			if ("1" == $content['orders'][$i]['if_transfer'])
				$content['orders'][$i]['transfer_cost'] = $transfer_price;

			$content['orders'][$i]['notes'] = "";
			if ("1" == $content['orders'][$i]['if_children'])
				$content['orders'][$i]['notes'] .= "Есть дети до 12 лет. ";
			if ("1" == $content['orders'][$i]['if_extra_bed'])
				$content['orders'][$i]['notes'] .= "Дополнительная кровать для взрослого.";

			if ("0" == $content['orders'][$i]['breakfast_qty'])
				$content['orders'][$i]['breakfast_qty'] = "";


			$date1 = explode("-", $content['orders'][$i]['date_from']);
			$date2 = explode("-", $content['orders'][$i]['date_to']);
			$ts1 = mktime(0, 0, 0, $date1[1], $date1[2], $date1[0]);
			$ts2 = mktime(0, 0, 0, $date2[1], $date2[2], $date2[0]);
			$interval = $ts2 - $ts1;
			$ts_in_day = 86400;
			$days = $interval / $ts_in_day;
			$content['orders'][$i]['days'] = $days;

			$i++;
		}
	}
	else
		$content['result'] = "Недостаточно прав";
		
   debug ("*** end: booking_orders_list");

   return $content;
}


function booking_orders_calendar()
{
        debug ("*** booking_orders_calendar ***");
        global $config;
        global $user;
        $content = array(
        	'content' => '',
            'bookings' => '',
			'show_admin_link' => '',
			'calendar_monthes' => '',
			'show_mode_1_checked' => '',
			'show_mode_2_checked' => ''
        );
	
	if (isset($_POST['rooms_filter']))
		$content[$_POST['rooms_filter']."_selected"] = "yes";

	if (isset($_POST['show_mode']))
		switch($_POST['show_mode'])
		{
			default: break;
			case "1": $content['show_mode_1_checked'] = "yes"; break;
			case "2": $content['show_mode_2_checked'] = "yes"; break;
		}
	else
		$content['show_mode_2_checked'] = "yes";

	$priv = new Privileges();

	if ($priv -> has("booking", "orders_calendar", "write"))
	{
		/* Month and year identification */

		if (isset($_POST['month_begin']))
			$month_begin = $_POST['month_begin'];
		else
			$month_begin = date("n");

		if (isset($_POST['year_begin']))
			$year_begin = $_POST['year_begin'];
		else
			$year_begin = date("Y");

		if (isset($_POST['month_end']))
			$month_end = $_POST['month_end'];
		else
			$month_end = date("n");

		if (isset($_POST['year_end']))
			$year_end = $_POST['year_end'];
		else
			$year_end = $year_begin;

		$content["month_begin_".$month_begin."_selected"] = "yes";
		$content["month_end_".$month_end."_selected"] = "yes";
		$content['year_0'] = date("Y");
		$content['year_1'] = date("Y") + 1;

		if (!isset($_POST['year_begin']) || $_POST['year_begin'] == date("Y"))
			$content['year_begin_0_selected'] = "yes";
		else
			$content['year_begin_1_selected'] = "yes";
		if (!isset($_POST['year_end']) || $_POST['year_end'] == date("Y"))
			$content['year_end_0_selected'] = "yes";
		else
			$content['year_end_1_selected'] = "yes";

		/* End: Month and year identification */

		$monthes = booking_calendar_get_monthes ($month_begin, $year_begin, $month_end, $year_end);
		debug("monthes: ", 2);
		dump($monthes);

		$rooms = booking_rooms_get_array();
		debug("rooms: ", 2);
		dump($rooms);

		if (!isset($_POST['rooms_filter']))
			$_POST['rooms_filter'] = "all";

		$monthes_qty = count($monthes);
		$date_all_begin = $monthes[0]['year']."-".$monthes[0]['month']."-1";
		$date_all_end = $monthes[$monthes_qty - 1]['year']."-".$monthes[$monthes_qty - 1]['month']."-".$monthes[$monthes_qty - 1]['days'];
		debug ("date_all_begin: ".$date_all_begin);
		debug ("date_all_end: ".$date_all_end);
		$rooms = booking_calendar_rooms_filter($_POST['rooms_filter'], $rooms, $date_all_begin, $date_all_end);

		$orders = array();

		foreach($rooms as $room_id => $room)
		{
			$orders[$room_id]['room_number'] = $room['number'];
			$orders[$room_id]['room_floor'] = $room['floor'];
		
			foreach ($monthes as $month_id => $month)
			{
				$orders[$room_id][$month_id]['month_title'] = $month['month_title'];
				$orders[$room_id][$month_id]['month_days_cur'] = $month['days'];
				$orders[$room_id][$month_id]['year'] = $month['year'];


				$date_cur_begin = $month['year']."-".$month['month']."-1";
				$date_cur_end = $month['year']."-".$month['month']."-".$month['days'];
				debug ("date_cur_begin: ".$date_cur_begin);
				debug ("date_cur_end: ".$date_cur_end);

				$orders_filter = booking_calendar_orders_filter($_POST['rooms_filter'], $room_id, $date_cur_begin, $date_cur_end);
				$result = exec_query($orders_filter);
				if (mysql_num_rows($result))
				{
					while ($order = mysql_fetch_array($result))
					{
						debug("order id: ".stripslashes($order['id']));

						if (isset($orders[$room_id][$month_id]['cost']))
							$orders[$room_id][$month_id]['cost'] = $orders[$room_id][$month_id]['cost'] + stripslashes($order['cost']);
						else
							$orders[$room_id][$month_id]['cost'] = stripslashes($order['cost']);

						if (isset($orders[$room_id][$month_id]['prepayment']))
							$orders[$room_id][$month_id]['prepayment'] = $orders[$room_id][$month_id]['prepayment'] + stripslashes($order['prepayment']);
						else
							$orders[$room_id][$month_id]['prepayment'] = stripslashes($order['prepayment']);

						if (isset($orders[$room_id][$month_id]['leftover']))
							$orders[$room_id][$month_id]['leftover'] = $orders[$room_id][$month_id]['leftover'] + stripslashes($order['leftover']);
						else
							$orders[$room_id][$month_id]['leftover'] = stripslashes($order['leftover']);


						$breakfast_price = 5;
						unset($breakfast_cost);
						$breakfast_qty = stripslashes($order['breakfast_qty']);
						if ($breakfast_qty)
							$breakfast_cost = $breakfast_price * $breakfast_qty;
						if (isset($orders[$room_id][$month_id]['breakfast_cost']))
							$orders[$room_id][$month_id]['breakfast_cost'] = $orders[$room_id][$month_id]['breakfast_cost'] + stripslashes($order['breakfast_cost']);
						else
							$orders[$room_id][$month_id]['breakfast_cost'] = $breakfast_cost;


						$transfer_price = 100;
						$if_transfer = stripslashes($order['if_transfer']);
						if ($if_transfer)
							if (isset($orders[$room_id][$month_id]['transfer_cost']))
								$orders[$room_id][$month_id]['transfer_cost'] = $orders[$room_id][$month_id]['transfer_cost'] + $transfer_price;
							else
								$orders[$room_id][$month_id]['transfer_cost'] = $transfer_price;


						$date_from = stripslashes($order['date_from']);
						$date_to = stripslashes($order['date_to']);
						debug("date_from: ".$date_from);
						debug("date_to: ".$date_to);

						$order['time_from'] = substr($order['time_from'], 0, -3);
						$order['time_to'] = substr($order['time_to'], 0, -3);
						$order['show_edit_link'] = "yes";

						$begin = explode("-", $date_from);
						$end = explode("-", $date_to);

						if (($begin[1] < $month['month']) || ((1 == $month['month']) && (12 == $begin[1])))
							$day_begin = 1;
						else
							$day_begin = $begin[2];
						debug ("day_begin: ".$day_begin);

						if (($end[1] > $month['month']) || ((12 == $month['month']) && (1 == $end[1])))
							$day_end = $month['days'];
						else
							$day_end = $end[2];
						debug ("day_end: ".$day_end);


						for ($day = 1; $day <= $month['days']; $day++)
						{
							debug("day ".$day);
							$orders[$room_id][$month_id][$day] = array();
							if ($day >= $day_begin && $day <= $day_end)
							{
								debug("booked");
								$orders[$room_id][$month_id][$day]['booked'] = "yes";
								$orders[$room_id][$month_id][$day]['id'] = stripslashes($order['id']);
								$orders[$room_id][$month_id][$day]['class'] = booking_get_order_status($orders[$room_id][$month_id][$day]['id']);
								if (isset($orders[$room_id][$month_id][$day]['orderdata']))
									$orders[$room_id][$month_id][$day]['orderdata'] .= htmlspecialchars(gen_content("booking", "view", $order));
								else
									$orders[$room_id][$month_id][$day]['orderdata'] = htmlspecialchars(gen_content("booking", "view", $order));
							}
							else
								$orders[$room_id][$month_id][$day]['booked'] = "";
						}

					}
				}
				else
					for ($day = 1; $day <= $month['days']; $day++)
						$orders[$room_id][$month_id][$day]['booked'] = "";
				mysql_free_result($result);

			}

		}
		debug("orders: ", 2);
		dump($orders);

		if ("yes" == $content['show_mode_1_checked'])
		{
			debug("show mode 1");
			foreach ($monthes as $month_id => $month)
			{
				debug("show month ".$month_id);
				$content['calendar_monthes'] .= "<table class=\"booking_calendar_table\">";
				$content['calendar_monthes'] .= "<tr>";
				$content['calendar_monthes'] .= "<th colspan=\"2\" class=\"booking_calendar_top\">";
				$content['calendar_monthes'] .= "<th colspan=\"".$month['days']."\" class=\"booking_calendar_top\">".$month['month_title']." ".$month['year']."</th>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
				$content['calendar_monthes'] .= "</tr>";
				$content['calendar_monthes'] .= "<tr>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top2\">номер</th>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top2\">этаж</th>";
				for ($day = 1; $day <= $month['days']; $day++)
					$content['calendar_monthes'] .= "<th class=\"booking_calendar_month_num_th\">".$day."</th>";;

				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">завтрак</th>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">трансфер</th>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">итого</th>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">предоплата</th>";
				$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">остаток</th>";
				$content['calendar_monthes'] .= "</tr>";

				foreach($orders as $room_id => $room)
				{
					$content['calendar_monthes'] .= "<tr>";
					$content['calendar_monthes'] .= "<td>".$room['room_number']."</td>";
					$content['calendar_monthes'] .= "<td>".$room['room_floor']."</td>";
					for($day = 1; $day <= $month['days']; $day++)
						if ("yes" == $room[$month_id][$day]['booked'])
							$content['calendar_monthes'] .= "<td class=\"booking_".$room[$month_id][$day]['class']."\"><a href=\"/booking/edit/".$room[$month_id][$day]['id']."/page_template:empty\" target=\"_new\" orderdata=\"".$room[$month_id][$day]['orderdata']."\">&nbsp;&nbsp;&nbsp;</a></td>";
						else
							$content['calendar_monthes'] .= "<td>&nbsp;&nbsp;&nbsp;</td>";
					$content['calendar_monthes'] .= "<td>".$room[$month_id]['breakfast_cost']."</td>";
					$content['calendar_monthes'] .= "<td>".$room[$month_id]['transfer_cost']."</td>";
					$content['calendar_monthes'] .= "<td>".$room[$month_id]['cost']."</td>";
					$content['calendar_monthes'] .= "<td>".$room[$month_id]['prepayment']."</td>";
					$content['calendar_monthes'] .= "<td>".$room[$month_id]['leftover']."</td>";
					$content['calendar_monthes'] .= "</tr>";
				}


				$content['calendar_monthes'] .= "</table>";
				debug("calendar_monthes: ".$content['calendar_monthes'], 3);
			}
		}
		else if ("yes" == $content['show_mode_2_checked'])
		{
			debug("show mode 2");

			$content['calendar_monthes'] .= "";
			$content['calendar_monthes'] .= "";
			$content['calendar_monthes'] .= "";
			$content['calendar_monthes'] .= "<table class=\"booking_calendar_table\">";
			$content['calendar_monthes'] .= "<tr>";
			$content['calendar_monthes'] .= "<th colspan=\"2\" class=\"booking_calendar_top\">";
			foreach($monthes as $month_id => $month)
				$content['calendar_monthes'] .= "<th colspan=\"".$month['days']."\" class=\"booking_calendar_top\">".$month['month_title']." ".$month['year']."</th>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top\">";
			$content['calendar_monthes'] .= "</tr>";

			$content['calendar_monthes'] .= "<tr>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top2\">номер</th>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top2\">этаж</th>";
			foreach($monthes as $month_id => $month)
				for ($day = 1; $day <= $month['days']; $day++)
					$content['calendar_monthes'] .= "<th class=\"booking_calendar_month_num_th\">".$day."</th>";;
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">завтрак</th>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">трансфер</th>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">итого</th>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">предоплата</th>";
			$content['calendar_monthes'] .= "<th class=\"booking_calendar_top3\">остаток</th>";
			$content['calendar_monthes'] .= "</tr>";

			foreach($orders as $room_id => $room)
			{
				$breakfast_cost = 0;
				$transfer_cost = 0;
				$cost = 0;
				$prepayment = 0;
				$leftover = 0;

				$content['calendar_monthes'] .= "<tr>";
				$content['calendar_monthes'] .= "<td>".$room['room_number']."</td>";
				$content['calendar_monthes'] .= "<td>".$room['room_floor']."</td>";
				foreach($monthes as $month_id => $month)
				{
					for($day = 1; $day <= $month['days']; $day++)
						if ("yes" == $room[$month_id][$day]['booked'])
							$content['calendar_monthes'] .= "<td class=\"booking_".$room[$month_id][$day]['class']."\"><a href=\"/booking/edit/".$room[$month_id][$day]['id']."/page_template:empty\" target=\"_new\" orderdata=\"".$room[$month_id][$day]['orderdata']."\">&nbsp;&nbsp;&nbsp;</a></td>";
						else
							$content['calendar_monthes'] .= "<td>&nbsp;&nbsp;&nbsp;</td>";
					$breakfast_cost = $breakfast_cost + $room[$month_id]['breakfast_cost'];
					$transfer_cost = $transfer_cost + $room[$month_id]['transfer_cost'];
					$cost = $cost + $room[$month_id]['cost'];
					$prepayment = $prepayment + $room[$month_id]['prepayment'];
					$leftover = $leftover + $room[$month_id]['leftover'];
				}

				if (0 == $breakfast_cost)
					$breakfast_cost = "";
				if (0 == $transfer_cost)
					$transfer_cost = "";
				if (0 == $cost)
					$cost = "";
				if (0 == $prepayment)
					$prepayment = "";
				if (0 == $leftover)
					$leftover = "";

				$content['calendar_monthes'] .= "<td>".$breakfast_cost."</td>";
				$content['calendar_monthes'] .= "<td>".$transfer_cost."</td>";
				$content['calendar_monthes'] .= "<td>".$cost."</td>";
				$content['calendar_monthes'] .= "<td>".$prepayment."</td>";
				$content['calendar_monthes'] .= "<td>".$leftover."</td>";
				$content['calendar_monthes'] .= "</tr>";
			}


			$content['calendar_monthes'] .= "</table>";
			debug("calendar_monthes: ".$content['calendar_monthes'], 3);
		}




	}
	else
		$content['result'] = "Недостаточно прав";
		
   debug ("*** end: booking_orders_calendar");

   return $content;
}

function booking_get_order_status ($id)
{
	global $user;
	global $config;
	debug("*** booking_get_order_status ***");

	$sql_query = "SELECT `room`, `prepayment`, `payment_status`, `leftover` FROM `ksh_booking` WHERE `id` = '".mysql_real_escape_string($id)."'";
	$result = exec_query($sql_query);
	if (mysql_num_rows($result))
		$row = mysql_fetch_array($result);
	else
		$row = array();
	mysql_free_result($result);

	$room = stripslashes($row['room']);
	$prepayment = stripslashes($row['prepayment']);
	$payment_status = stripslashes($row['payment_status']);
	$leftover = stripslashes($row['leftover']);

	$status = "";
	if ("" == $room)
		$status = "new";
	else
		$status = "booked";
	if ("" != $prepayment && "0" != $prepayment)
		$status = "prepaid";
	if ("" != $room && "" != $payment_status && ("" == $leftover || "0" == $leftover))
		$status = "paid";

	debug("*** end: booking_get_order_status ***");
	return $status;
}

function booking_calendar_get_monthes($month_begin, $year_begin, $month_end, $year_end)
{
	global $user;
	global $config;
	debug("*** booking_calendar_get_monthes ***");

	debug("month begin: ".$month_begin);
	debug("year begin: ".$year_begin);
	debug("month end: ".$month_end);
	debug("year end: ".$year_end);

	$month_cur = $month_begin;
	$year_cur = $year_begin;
	debug("month_cur: ".$month_cur.", year_cur: ".$year_cur);
	$monthes = array();
	$i = 0;
	while (($year_cur < $year_end) || ($year_cur == $year_end && $month_cur <= $month_end))
	{
		debug("month_cur: ".$month_cur.", year_cur: ".$year_cur);

		$monthes[$i]['year'] = $year_cur;
		$monthes[$i]['month'] = $month_cur;
		$monthes[$i]['month_title'] = base_get_month_name($month_cur);
		$monthes[$i]['days'] = base_get_month_days($month_cur);


		if (12 == $month_cur)
		{
			$month_cur = 1;
			$year_cur++;
		}
		else
			$month_cur++;
		$i++;
	}

	debug("*** end: booking_calendar_get_monthes ***");

	return ($monthes);

}

function booking_calendar_orders_filter($orders_filter, $room_id, $date_cur_begin, $date_cur_end)
{
	global $user;
	global $config;
	debug("*** booking_calendar_rooms_filter ***");


	$room_id = mysql_real_escape_string($room_id);
	$date_cur_begin = mysql_real_escape_string($date_cur_begin);
	$date_cur_end = mysql_real_escape_string($date_cur_end);

	switch($orders_filter)
	{
		default:
		case "all":
			$sql_query = "SELECT * FROM `ksh_booking` WHERE
				`room` = '".$room_id."' AND
				`date_from` <= '".$date_cur_end."'
				AND `date_to` >= '".$date_cur_begin."'
				";
		break;

		case "busy":
			$sql_query = "SELECT * FROM `ksh_booking` WHERE
				`room` = '".$room_id."' AND
				`date_from` <= '".$date_cur_end."'
				AND `date_to` >= '".$date_cur_begin."'
				";
		break;

		case "paid":
			$sql_query = "SELECT * FROM `ksh_booking` WHERE
				`room` = '".$room_id."' AND
				`date_from` <= '".$date_cur_end."'
				AND `date_to` >= '".$date_cur_begin."'
				AND `payment_status` != ''
				AND `payment_status` IS NOT NULL
				";
		break;

		case "unpaid":
			$sql_query = "SELECT * FROM `ksh_booking` WHERE
				`room` = '".$room_id."' AND
				`date_from` <= '".$date_cur_end."'
				AND `date_to` >= '".$date_cur_begin."'
				AND (`payment_status` = ''
				OR `payment_status` IS NULL)
				";
		break;

		case "prepaid":
			$sql_query = "SELECT * FROM `ksh_booking` WHERE
				`room` = '".$room_id."' AND
				`date_from` <= '".$date_cur_end."'
				AND `date_to` >= '".$date_cur_begin."'
				AND `prepayment` != ''
				AND `prepayment` IS NOT NULL
				";
		break;
	}

	debug("*** end: booking_calendar_orders_filter ***");
	return $sql_query;
}

function booking_calendar_rooms_filter($orders_filter, $rooms, $date_begin, $date_end)
{
	global $user;
	global $config;
	debug("*** booking_calendar_rooms_filter ***");


	$date_begin = mysql_real_escape_string($date_begin);
	$date_end = mysql_real_escape_string($date_end);

	foreach($rooms as $room_id => $room)
	{

		switch($orders_filter)
		{
			default:
			case "all":
				$sql_query = "SELECT COUNT(*) FROM `ksh_booking`";
			break;

			case "busy":
				$sql_query = "SELECT COUNT(*) FROM `ksh_booking` WHERE
					`room` = '".$room_id."' AND
					`date_from` <= '".$date_end."'
					AND `date_to` >= '".$date_begin."'
					";
			break;

			case "paid":
				$sql_query = "SELECT COUNT(*) FROM `ksh_booking` WHERE
					`room` = '".$room_id."' AND
					`date_from` <= '".$date_end."'
					AND `date_to` >= '".$date_begin."'
					AND `payment_status` != ''
					AND `payment_status` IS NOT NULL
					";
			break;

			case "unpaid":
				$sql_query = "SELECT COUNT(*) FROM `ksh_booking` WHERE
					`room` = '".$room_id."' AND
					`date_from` <= '".$date_end."'
					AND `date_to` >= '".$date_begin."'
					AND (`payment_status` = ''
					OR `payment_status` IS NULL)
					";
			break;

			case "prepaid":
				$sql_query = "SELECT COUNT(*) FROM `ksh_booking` WHERE
					`room` = '".$room_id."' AND
					`date_from` <= '".$date_end."'
					AND `date_to` >= '".$date_begin."'
					AND `prepayment` != ''
					AND `prepayment` IS NOT NULL
					";
			break;
		}

		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		$orders_qty = stripslashes($row['COUNT(*)']);
		debug("orders qty: ".$orders_qty);
		if ("0" == $orders_qty)
		{
			debug("room ".$room_id." did not pass the filter, unsetting it");
			unset($rooms[$room_id]);
		}
	}

	debug("*** end: booking_calendar_rooms_filter ***");
	return $rooms;
}


?>
