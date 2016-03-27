<?php

// prices functions of booking module

function booking_prices_edit()
{
global $user;
global $config;
debug ("*** booking_prices_edit ***");

$content = array(
	'prices' => '',
	'result' => ''
);

$table = "ksh_booking_prices";

if (isset($_POST['do_update']))
{
	if ("" != $_POST['new_price'])
	{
		$sql_query = "INSERT INTO `".mysql_real_escape_string($table)."`
			(`date_from`, `date_to`, `type`, `price`)
			VALUES (
			'".mysql_real_escape_string($_POST['new_date_from'])."',
			'".mysql_real_escape_string($_POST['new_date_to'])."',
			'".mysql_real_escape_string($_POST['new_type'])."',
			'".mysql_real_escape_string($_POST['new_price'])."'
			)";
		exec_query($sql_query);
	}
	if (isset($_POST['entries']))
		foreach($_POST['entries'] as $k => $v)
		{
			debug("updating entry ".$v);
			if (("" != $_POST['price_'.$v]) && ("" != $_POST['type_'.$v]))
			{
				$sql_query = "UPDATE `".mysql_real_escape_string($table)."` SET
					`date_from` = '".mysql_real_escape_string($_POST['date_from_'.$v])."',
					`date_to` = '".mysql_real_escape_string($_POST['date_to_'.$v])."',
					`type` = '".mysql_real_escape_string($_POST['type_'.$v])."',
					`price` = '".mysql_real_escape_string($_POST['price_'.$v])."'
					WHERE `id` = '".mysql_real_escape_string($v)."'";
				exec_query($sql_query);
			}
			else
			{
				$sql_query = "DELETE FROM `".mysql_real_escape_string($table)."` WHERE `id` = '".mysql_real_escape_string($v)."'";
				$result = exec_query($sql_query);
			}
		}
}

$sql_query = "SELECT * FROM `".mysql_real_escape_string($table)."` ORDER BY `date_from`, `id`";
$result = exec_query($sql_query);
$i = 0;
while ($row = mysql_fetch_array($result))
{
	debug ("processing element ".$i);
	$content['prices'][$i]['id'] = stripslashes($row['id']);
	$content['prices'][$i]['date_from'] = stripslashes($row['date_from']);
	$content['prices'][$i]['date_to'] = stripslashes($row['date_to']);

	if ("0000-00-00" == $content['prices'][$i]['date_from'])
		$content['prices'][$i]['date_from'] = "";
	if ("0000-00-00" == $content['prices'][$i]['date_to'])
		$content['prices'][$i]['date_to'] = "";

	$content['prices'][$i]['type'] = stripslashes($row['type']);
	$content['prices'][$i]['selected_type_'.stripslashes($row['type'])] = "yes";
	$content['prices'][$i]['price'] = stripslashes($row['price']);
	$i++;
}
mysql_free_result($result);

debug ("*** end: booking_prices_edit ***");
return $content;
}

function booking_prices_get($day = 0, $type = 0)
{
global $user;
global $config;
debug ("*** booking_prices_get ***");

$content = array(
	'result' => '',
	'id' => '',
	'price' => ''
);

$table = "ksh_booking_prices";

if (!$day)
	if (isset($_GET['day']))
		$day = $_GET['day'];
	else if (isset($_GET['element']))
		$day = $_GET['element'];

if (!$type)
	if (isset($_GET['type']))
		$type = $_GET['type'];

debug("day: ".$day);
debug("type: ".$type);

/*

date_default_timezone_set("UTC");

if (strstr($day, "-"))
{
	$day_ar = explode("-", $day);
	$day_ts = mktime(0, 0, 0, $day_ar[1], $day_ar[2], $day_ar[0]);
}
else
{
	$day_ts = $day;
}

debug("day_ts: ".$day_ts);
*/

$table = "ksh_booking_prices";
$prices = array();
$sql_query = "SELECT * FROM `".mysql_real_escape_string($table)."` WHERE
	`date_from` <= '".mysql_real_escape_string($day)."' AND
	`date_to` >= '".mysql_real_escape_string($day)."' AND
	`type` = '".mysql_real_escape_string($type)."'
	";
$result = exec_query($sql_query);
if (mysql_num_rows($result))
{
	$row = mysql_fetch_array($result);

	$id = stripslashes($row['id']);
	$price = stripslashes($row['price']);

	debug("element: ".$id);
	debug("price: ".$price);

	mysql_free_result($result);
}
else
{
	$sql_query = "SELECT * FROM `".mysql_real_escape_string($table)."` WHERE
		`date_from` = '0000-00-00' AND
		`date_to` >= '0000-00-00' AND
		`type` = '".mysql_real_escape_string($type)."'
		";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);

	$id = stripslashes($row['id']);
	$price = stripslashes($row['price']);

	debug("element: ".$id);
	debug("price: ".$price);

	mysql_free_result($result);
}



$content['id'] = $id;
$content['price'] = $price;

debug ("*** end: booking_prices_get ***");
return $content;
}


?>
