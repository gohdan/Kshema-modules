<?php

// rooms functions of booking module

function booking_rooms_edit()
{
global $user;
global $config;
debug ("*** booking_rooms_edit ***");

$content = array(
	'rooms' => '',
	'result' => ''
);

$table = "ksh_booking_rooms";

if (isset($_POST['do_update']))
{
	if ("" != $_POST['new_room_number'])
	{
		$sql_query = "INSERT INTO `".mysql_real_escape_string($table)."`
			(`floor`, `number`, `type`)
			VALUES (
			'".mysql_real_escape_string($_POST['new_floor'])."',
			'".mysql_real_escape_string($_POST['new_room_number'])."',
			'".mysql_real_escape_string($_POST['new_room_type'])."'
			)";
		exec_query($sql_query);
	}
	if (isset($_POST['entries']))
		foreach($_POST['entries'] as $k => $v)
		{
			debug("updating entry ".$v);
			if (("" != $_POST['number_'.$v]) && ("" != $_POST['type_'.$v]))
			{
				$sql_query = "UPDATE `".mysql_real_escape_string($table)."` SET
					`floor` = '".mysql_real_escape_string($_POST['floor_'.$v])."',
					`number` = '".mysql_real_escape_string($_POST['number_'.$v])."',
					`type` = '".mysql_real_escape_string($_POST['type_'.$v])."'
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

$sql_query = "SELECT * FROM `".mysql_real_escape_string($table)."` ORDER BY `floor`, `number`";
$result = exec_query($sql_query);
$i = 0;
while ($row = mysql_fetch_array($result))
{
	debug ("processing element ".$i);
	$content['rooms'][$i]['id'] = stripslashes($row['id']);
	$content['rooms'][$i]['floor'] = stripslashes($row['floor']);
	$content['rooms'][$i]['number'] = stripslashes($row['number']);
	$content['rooms'][$i]['type'] = stripslashes($row['type']);
	$content['rooms'][$i]['selected_floor_'.stripslashes($row['floor'])] = "yes";
	$content['rooms'][$i]['selected_type_'.stripslashes($row['type'])] = "yes";
	$i++;
}
mysql_free_result($result);

debug ("*** end: booking_rooms_edit ***");
return $content;
}

function booking_rooms_get($id = 0)
{
global $user;
global $config;
debug ("*** booking_rooms_get ***");

$content = array(
	'rooms' => '',
	'result' => ''
);

$table = "ksh_booking_rooms";

$sql_query = "SELECT * FROM `".mysql_real_escape_string($table)."` WHERE `id` = '".mysql_real_escape_string($id)."'";
$result = exec_query($sql_query);
$row = mysql_fetch_array($result);
mysql_free_result($result);

$content['id'] = stripslashes($row['id']);
$content['floor'] = stripslashes($row['floor']);
$content['number'] = stripslashes($row['number']);
$content['type'] = stripslashes($row['type']);

debug("id: ".$content['id']);
debug("type: ".$content['type']);

debug ("*** end: booking_rooms_get ***");
return $content;
}

function booking_rooms_get_array()
{
global $user;
global $config;
debug ("*** booking_rooms_get_array ***");

$rooms = array();
$sql_query = "SELECT * FROM `ksh_booking_rooms` ORDER BY `floor`, `number`";
$result = exec_query($sql_query);
while ($row = mysql_fetch_array($result))
{
	$id = stripslashes($row['id']);
	$rooms[$id]['floor'] = stripslashes($row['floor']);
	$rooms[$id]['number'] = stripslashes($row['number']);
}
mysql_free_result($result);

debug ("*** end: booking_rooms_get_array ***");
return $rooms;
}


?>
