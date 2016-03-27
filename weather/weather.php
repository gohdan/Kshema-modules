<?php

// functions of weather module

function weather_update()
{
	global $user;
	global $config;
	debug("=== function: weather_update ===");

	$content = array(
		'success' => '',
		'error' => ''
	);

	$link = $config['weather']['link'];

	$json_string = file_get_contents($link);
	$parsed_json = json_decode($json_string);

	$location = $parsed_json->{'location'}->{'city'};
	$weather = $parsed_json->{'current_observation'}->{'weather'};
	$icon = $parsed_json->{'current_observation'}->{'icon_url'};
	$temp_f = $parsed_json->{'current_observation'}->{'temp_f'};
	$temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
	$hum = $parsed_json->{'current_observation'}->{'relative_humidity'};
	$wind = $parsed_json->{'current_observation'}->{'wind_string'};

	$sql_query = "SELECT * FROM `ksh_weather` WHERE `location` = '".mysql_real_escape_string($location)."'";
	$result = exec_query($sql_query);
	if (mysql_num_rows($result))
	{
		$sql_query = "UPDATE `ksh_weather` SET
			`weather` = '".mysql_real_escape_string($weather)."',
			`icon` = '".mysql_real_escape_string($icon)."',
			`temp_f` = '".mysql_real_escape_string($temp_f)."',
			`temp_c` = '".mysql_real_escape_string($temp_c)."',
			`hum` = '".mysql_real_escape_string($hum)."',
			`wind` = '".mysql_real_escape_string($wind)."',
			`update` = CURRENT_TIMESTAMP()
			WHERE `location` = '".mysql_real_escape_string($location)."'";
		$id = $location;
	}
	else
	{
		$sql_query = "INSERT INTO `ksh_weather` (`location`, `weather`, `icon`, `temp_f`, `temp_c`, `hum`, `wind`) VALUES (
			'".mysql_real_escape_string($location)."',
			'".mysql_real_escape_string($weather)."',
			'".mysql_real_escape_string($icon)."',
			'".mysql_real_escape_string($temp_f)."',
			'".mysql_real_escape_string($temp_c)."',
			'".mysql_real_escape_string($hum)."',
			'".mysql_real_escape_string($wind)."'
		)";
		$id = mysqli_insert_id();
	}
	exec_query($sql_query);

	$content['error'] = mysql_error();
	if ('' == $content['error'])
		$content['success'] = "yes";



	$weather = weather_get_from_db($id);
	foreach ($weather as $k => $v)
		$content[$k] = $v;

	debug("=== end: function: weather_update ===");
	return $content;
}


function weather_get_from_db($id = 0)
{
	global $user;
	global $config;
	debug("=== function: weather_get_from_db ===");

	$content = array(

	);

	$weather = array();

	if ($id)
	{
		if (is_int($id))
			$field = "id";
		else
			$field = "location";

		$sql_query = "SELECT * FROM `ksh_weather` WHERE `".$field."` = '".mysql_real_escape_string($id)."'";
	}
	else
		$sql_query = "SELECT * FROM `ksh_weather` ORDER BY `id` DESC LIMIT 1";
		
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	foreach ($row as $k => $v)
		$weather[$k] = $v;

	debug("=== end: function: weather_get_from_db ===");
	return $weather;
}

function weather_get($location = "")
{
	global $user;
	global $config;
	debug("=== function: weather_get ===");

	$content = array(

	);

	$weather = weather_get_from_db($location);

	$ts_upd = strtotime($weather['update']);
	debug("ts_upd: ".$ts_upd);
	$ts_cur = time();
	debug("ts_cur: ".$ts_cur);
	$diff = $ts_cur - $ts_upd;
	debug("diff: ".$diff);

	if ($diff >= $config['weather']['update_interval'])
	{
		debug("too much time passed, updating weather data");
		weather_update();
		$weather = weather_get_from_db($location);
	}
	else
		debug("not much time passed, using data from db");

	debug("=== end: function: weather_get ===");
	return $weather;
}


?>
