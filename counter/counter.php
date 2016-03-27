<?php

// Counter functions of the counter module


function counter_add()
{
	global $config;
	global $user;


	$sql_query = "INSERT INTO ksh_counter_days (
		`date`,
		`time`,
		`user`,
		`url`,
		`referer`,
		`ip`
		) VALUES (
		'".mysql_real_escape_string(date("Y-m-d"))."',
		'".mysql_real_escape_string(date("H:i:s"))."',
		'".mysql_real_escape_string(users_get_name($user['id']))."',
		'".mysql_real_escape_string($_SERVER['REQUEST_URI'])."',
		'".mysql_real_escape_string($_SERVER['HTTP_REFERER'])."',
		'".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."'
		)";
	exec_query($sql_query);



}

function counter_view_last()
{
	global $config;
	global $user;
	$content = array(
		'visits' => ''
	);

	$sql_query = "SELECT * FROM ksh_counter_days ORDER BY id DESC LIMIT 20";
	$result = exec_query($sql_query);
	$i = 0;
	while ($visit = mysql_fetch_array($result))
	{
		$content['visits'][$i]['date'] = stripslashes($visit['date']);
		$content['visits'][$i]['time'] = stripslashes($visit['time']);
		$content['visits'][$i]['user'] = stripslashes($visit['user']);
		$content['visits'][$i]['url'] = stripslashes($visit['url']);
		$content['visits'][$i]['referer'] = stripslashes($visit['referer']);
		$content['visits'][$i]['ip'] = stripslashes($visit['ip']);
		$i++;
	}
	mysql_free_result($result);
	return $content;
}


function counter_view_month()
{
	global $config;
	global $user;
	$content = array(
		'visits_month' => ''
	);

	for ($i = 1; $i <= 31; $i++)
	{
		if ($i < 10)
			$day = "0".$i;
		else
			$day = $i;

		$month = date("m");
		$year = date("Y");

		$sql_query = "SELECT COUNT(*) FROM ksh_counter_days WHERE `date` LIKE '".mysql_real_escape_string($year)."-".mysql_real_escape_string($month)."-".mysql_real_escape_string($day)."'";
		$result = exec_query($sql_query);
		$visits = stripslashes(mysql_result($result, 0, 0));
		mysql_free_result($result);

		$sql_query = "SELECT COUNT(distinct `ip`) FROM ksh_counter_days WHERE `date` LIKE '".mysql_real_escape_string($year)."-".mysql_real_escape_string($month)."-".mysql_real_escape_string($day)."'";
		$result = exec_query($sql_query);
		$unique = stripslashes(mysql_result($result, 0, 0));
		mysql_free_result($result);
		if (0 != $visits && 0 != $unique)
		{
			$content['visits_month'][$i]['day'] = $i;
			$content['visits_month'][$i]['visits'] = stripslashes($visits);
			$content['visits_month'][$i]['unique'] = stripslashes($unique);
		}

	}
	
	return $content;
}


function counter_view_day()
{
	global $config;
	global $user;
	$content = array(
		'visits' => '',
		'date' => ''
	);

	if ($_GET['day'] < 10)
		$day = "0".$_GET['day'];
	else
		$day = $_GET['day'];

	$month = date("m");
	$year = date("Y");

	$content['date'] = $day.".".$month.".".$year;

	$sql_query = "SELECT * FROM ksh_counter_days WHERE `date` LIKE '".mysql_real_escape_string($year)."-".mysql_real_escape_string($month)."-".mysql_real_escape_string($day)."' ORDER BY id DESC";
	$result = exec_query($sql_query);
	$i = 0;
	while ($visit = mysql_fetch_array($result))
	{
		$content['visits_day'][$i]['time'] = stripslashes($visit['time']);
		$content['visits_day'][$i]['user'] = stripslashes($visit['user']);
		$content['visits_day'][$i]['url'] = stripslashes($visit['url']);
		$content['visits_day'][$i]['referer'] = stripslashes($visit['referer']);
		$content['visits_day'][$i]['ip'] = stripslashes($visit['ip']);
		$i++;
	}
	mysql_free_result($result);
	return $content;
}

?>

