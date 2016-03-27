<?php

// functions

function tires_if_all_params_set($query)
{
	global $user;
	global $config;

	debug("*** tires_if_all_params_set ***");

	debug("query:");
	dump($query);

	$result = 1;

	if (count($query) != count($config['tires']['params_names']))
		$result = 0;
	else
		foreach($query as $k => $v)
			if ($config['tires']['unk'] == $v)
				$result = 0;

	debug("result: ".$result);

	debug("*** end: tires_if_all_params_set ***");

	return $result;
}

function tires_if_any_param_set($query)
{
	global $user;
	global $config;

	debug("*** tires_if_any_param_set ***");

	debug("query:");
	dump($query);

	$result = 0;

	foreach($query as $k => $v)
		if ($config['tires']['unk'] != $v)
			$result = 1;

	debug("result: ".$result);

	debug("*** end: tires_if_any_param_set ***");

	return $result;
}

function tires_count_rows($field, $param)
{
	global $user;
	global $config;

	debug("*** tires_count_rows ***");

	debug("field: ".$field);
	debug("param: ".$param);


	$sql_query = "SELECT COUNT(*) FROM `tires` WHERE `".mysql_real_escape_string($field)."` = BINARY '".mysql_real_escape_string($param)."'";

	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$qty = stripslashes($row['COUNT(*)']);


	debug("qty: ".$qty);

	debug("*** end: tires_count_rows ***");

	return $qty;
}

function tires_get_distincts($param)
{
	global $user;
	global $config;

	debug("*** tires_get_distincts ***");

	$content = array(
	);

	debug("param: ".$param);

	$distincts = array();

	$sql_query = "SELECT DISTINCT(`".mysql_real_escape_string($param)."`) FROM `tires` ORDER BY `".mysql_real_escape_string($param)."`";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
		$distincts[]['param'] = stripslashes($row[$param]);
	mysql_free_result($result);


	debug("*** end: tires_get_distincts ***");
	return $distincts;
}


function tires_count_distincts($param, $params)
{
	global $user;
	global $config;

	debug("*** tires_count_distincts ***");

	$content = array(
		'param' => $param
	);

	$if_condition = 0;
	$qty = 0;

	debug("param: ".$param);
	dump($params);

	
	foreach($params as $k => $v)
		if ("" != $v && $config['tires']['unk'] != $v)
			$if_condition = 1;


	$sql_query = "SELECT COUNT(DISTINCT(`".mysql_real_escape_string($param)."`)) FROM `tires`";
	
	
	if ($if_condition)
	{
		$sql_query .= "WHERE ";

		foreach($params as $k => $v)
		{
			if ("" != $v && $config['tires']['unk'] != $v)
				$sql_query .= " `".mysql_real_escape_string($k)."` = '".mysql_real_escape_string($v)."' AND";
		}

		$sql_query = rtrim($sql_query, " AND");
	}

	$sql_query .= " ORDER BY `".mysql_real_escape_string($param)."` ASC";

	$result = exec_query($sql_query);

	$row = mysql_fetch_array($result);

	mysql_free_result($result);

	$qty = stripslashes($row['COUNT(DISTINCT(`'.mysql_real_escape_string($param).'`))']);

	debug("qty: ".$qty);

	debug("*** end: tires_count_distincts ***");
	return $qty;
}



function tires_view_param($param, $params)
{
	global $user;
	global $config;

	debug("*** tires_view_param ***");

	$content = array(
		'param' => $param
	);

	$if_condition = 0;

	debug("param: ".$param);
	dump($params);

	
	foreach($params as $k => $v)
		if ("" != $v && $config['tires']['unk'] != $v)
			$if_condition = 1;


	$sql_query = "SELECT DISTINCT(`".mysql_real_escape_string($param)."`) FROM `tires`";
	
	
	if ($if_condition)
	{
		$sql_query .= " WHERE ";

		foreach($params as $k => $v)
			if ("" != $v && $config['tires']['unk'] != $v)
				$sql_query .= " `".mysql_real_escape_string($k)."` = '".mysql_real_escape_string($v)."' AND";

		$sql_query = rtrim($sql_query, " AND");
	}

	$sql_query .= " ORDER BY `".mysql_real_escape_string($param)."` ASC";

	$result = exec_query($sql_query);

	if (mysql_num_rows($result))
	{
		debug("have result rows");
		$i = 0;
		while ($row = mysql_fetch_array($result))
		{
			foreach($row as $k => $v)
			{
				debug("; find url");
				foreach($params as $x => $y)
				{
					if ($config['tires']['unk'] == $y)
						$content['elements'][$i][$x.'_url'] = $y;
					else if (in_array($x, $config['tires']['own_tables']) || in_array($x, $config['tires']['params_url']))
						$content['elements'][$i][$x.'_url'] = tires_get_urlvalue($x, $y);
					else
						$content['elements'][$i][$x.'_url'] = urlencode(tires_transliterate($y, "ru", "en"));
				}

				debug("; find other");
				if (in_array($param, $config['tires']['own_tables']))
				{
					$id = stripslashes($row[$param]);
					$sql_query = "SELECT * FROM `".$param."s` WHERE `id` = '".mysql_real_escape_string($id)."'";
					$result_param = exec_query($sql_query);
					$row_param = mysql_fetch_array($result_param);
					mysql_free_result($result_param);

					$content['elements'][$i]['own_id'] = $id;
					$content['elements'][$i]['title'] = stripslashes($row_param['title']);
					$content['elements'][$i][$param] = stripslashes($row_param['title']);
					$content['elements'][$i]['url'] = stripslashes($row_param['url']);
					$content['elements'][$i][$param.'_url'] = stripslashes($row_param['url']);
				}
				else
				{
					$content['elements'][$i]['title'] = stripslashes($row[$param]);
					$content['elements'][$i][$param] = stripslashes($row[$param]);
					if (in_array($param, $config['tires']['params_url']))
					{
						$content['elements'][$i]['url'] = tires_get_urlvalue($param, $content['elements'][$i]['title']);
						$content['elements'][$i][$param.'_url'] = tires_get_urlvalue($param, $content['elements'][$i]['title']);
					}
					else
					{
						$content['elements'][$i]['url'] = urlencode(tires_transliterate($content['elements'][$i]['title'], "ru", "en"));
						$content['elements'][$i][$param.'_url'] = urlencode(tires_transliterate($content['elements'][$i]['title'], "ru", "en"));
					}
				}
			}
			$i++;
		}
	}
	else
		$content['no_result'] = "yes";

	mysql_free_result($result);

	debug("elements:");
	dump($content['elements']);

	debug("*** end: tires_view_param ***");
	return $content;
}



function tires_get_results($params)
{
	global $user;
	global $config;

	debug("*** tires_get_results ***");

	$content = array(
	);

	$if_condition = 0;

	debug("params:");
	dump($params);

	
	foreach($params as $k => $v)
		if ("" != $v && $config['tires']['unk'] != $v)
			$if_condition = 1;


	$sql_query = "SELECT * FROM `tires`";
	
	
	if ($if_condition)
	{
		$sql_query .= " WHERE ";

		foreach($params as $k => $v)
			if ("" != $v && $config['tires']['unk'] != $v)
					$sql_query .= " `".mysql_real_escape_string($k)."` = '".mysql_real_escape_string($v)."' AND";

		$sql_query = rtrim($sql_query, " AND");
	}

	$sql_query .= " ORDER BY `radius`, `width`, `profile` ASC";

	$result = exec_query($sql_query);

	if (mysql_num_rows($result))
	{
		debug("have results");
		$i = 0;
		$radius_cur = "";
		while ($row = mysql_fetch_array($result))
		{
			debug("processing result ".$i);
			dump($row);
			foreach($row as $k => $v)
			{
				debug("param: ".$k.":".$v);
				if (!is_numeric($k))
				{
					if (in_array($k, $config['tires']['own_tables']))
					{
						$content['results'][$i][$k] = tires_get_own_table_title($k, $v);
						$content['results'][$i][$k."_url"] = tires_get_urlvalue($k, $v);
					}
					else
					{
						$content['results'][$i][$k] = stripslashes($v);
	
						if (in_array($k, $config['tires']['own_tables']))
							$content['results'][$i][$k."_url"] = tires_get_urlvalue($k, $row[$k]);
						else if (in_array($k, $config['tires']['params_url']))
							$content['results'][$i][$k."_url"] = stripslashes($row[$k."_url"]);
						else
							$content['results'][$i][$k."_url"] = urlencode(tires_transliterate(stripslashes($v), "ru", "en"));
					}
				}
				else
					debug("param is numeric, don't process it");
			}

			$sps = explode(" (", $content['results'][$i]['speed']);
			$content['results'][$i]['speed_short'] = $sps[0];

			if ($radius_cur != $content['results'][$i]['radius'])
			{
				$radius_cur = $content['results'][$i]['radius'];
				$content['results'][$i]['radius_cur'] = $radius_cur;
			}

			$i++;
		}
	}
	else
		$content['no_results'] = "yes";

	mysql_free_result($result);

	debug("results:");
	dump($content['results']);

	debug("*** end: tires_get_results ***");
	return $content;
}


function tires_view_tire($params)
{
	global $user;
	global $config;

	debug("*** tires_view_tire ***");

	$content = array(
		'no_tire' => '',
		'has_tire' => '',
		'has_analogs' => ''
	);

	debug("params:");
	dump($params);

	$sql_query = "SELECT * FROM `tires` WHERE ";

	foreach($config['tires']['params_names'] as $k => $v)
		$sql_query .= "`".$v."` = '".mysql_real_escape_string($params[$k])."' AND ";

	$sql_query = rtrim($sql_query, " AND");

	$result = exec_query($sql_query);

	if (mysql_num_rows($result))
	{
		debug("has tire");
		$content['has_tire'] = "yes";
		$row = mysql_fetch_array($result);
		foreach($row as $k => $v)
		{
			if (in_array($k, $config['tires']['own_tables']))
			{
				$content[$k] = tires_get_own_table_title($k, $v);
				$content[$k."_url"] = tires_get_urlvalue($k, $v);
			}
			else
			{
				$content[$k] = stripslashes($v);

				if (!isset($row[$k."_url"]))
					$content[$k."_url"] = urlencode(tires_transliterate(stripslashes($v), "ru", "en"));
			}
		}
		$sps = explode(" (", $content['speed']);
		$content['speed_short'] = $sps[0];
	}
	else
	{
		debug("doesn't has tire");
		$content['no_tire'] = "yes";
	}

	mysql_free_result($result);

	// Get analogs
	if ("yes" == $content['has_tire'])
	{
		$sql_query = "SELECT * FROM `tires` WHERE
			`season` = '".mysql_real_escape_string($content['season'])."' AND
			`radius` = '".mysql_real_escape_string($content['radius'])."' AND
			`width` = '".mysql_real_escape_string($content['width'])."' AND
			`profile` = '".mysql_real_escape_string($content['profile'])."' AND
			`rn` != '".mysql_real_escape_string($content['rn'])."'			
			";
		$result = exec_query($sql_query);
		if (mysql_num_rows($result))
		{
			$content['has_analogs'] = "yes";
			$analogs_type = "analogs";
			$content[$analogs_type] = array();
			$i = 0;
			while ($row = mysql_fetch_array($result))
			{
				if ($i == $config['tires']['tires_analogs_qty'] && "analogs" == $analogs_type)
				{
					$content['has_analogs_hidden'] = "yes";
					$analogs_type = "analogs_hidden";
					$content[$analogs_type] = array();
					$i = 0;
				}

				foreach ($row as $k => $v)
				{
					if (in_array($k, $config['tires']['own_tables']))
					{
						$content[$analogs_type][$i][$k] = tires_get_own_table_title($k, $v);
						$content[$analogs_type][$i][$k."_url"] = tires_get_urlvalue($k, $v);
					}
					else
					{
						$content[$analogs_type][$i][$k] = stripslashes($row[$k]);

						if (in_array($k, $config['tires']['params_url']))
							$content[$analogs_type][$i][$k."_url"] = stripslashes($row[$k."_url"]);
						else
							$content[$analogs_type][$i][$k."_url"] = urlencode(tires_transliterate(stripslashes($v), "ru", "en"));
					}
				}

				$sps = explode(" (", $content[$analogs_type][$i]['speed']);
				$content[$analogs_type][$i]['speed_short'] = $sps[0];

				$i++;

			}

			if ("analogs_hidden" == $analogs_type)
				$content['analogs_hidden_qty'] = $i;
		}
		mysql_free_result($result);
	}


	debug("*** end: tires_view_tire ***");
	return $content;
}

function tires_transliterate($string, $lang_from, $lang_to)
{
	global $user;
	global $config;

	debug("*** tires_transliterate ***");

	debug("string: ".$string);
	debug("lang_from: ".$lang_from);
	debug("lang_to: ".$lang_to);

	$new_string = "";

	if ("ru" == $lang_from && "en" == $lang_to)
	{
			$new_string = strtr($string, 
				"ЮАБЦДЕФГХИЙКЛМНОПЯРСТШюабцдефгхийклмнопярстш",
			    "abvgdejziyklmnoprstufyABVGDEJZIYKLMNOPRSTUFY"
			);
			$new_string = strtr($new_string, array(
			'╦'=>"yo",    'У'=>"h",  'Ж'=>"ts",  'В'=>"ch", 'Ь'=>"sh",  
			'Ы'=>"sch",   'З'=>'*',   'Э'=>'**', 'Щ'=>"ye", 'Ч'=>"yu", 'Ъ'=>"ya",
			'╗'=>"Yo",    'у'=>"H",  'ж'=>"Ts",  'в'=>"Ch", 'ь'=>"Sh",
			'ы'=>"Sch",   'з'=>'***',   'э'=>'****', 'щ'=>"Ye", 'ч'=>"Yu", 'ъ'=>"Ya",
			'/'=>"--"
			));
	}
	else if ("en" == $lang_from && "ru" == $lang_to)
	{
			$new_string = strtr($string, array(
			'yo'=>"╦",    'h'=>"У",  'ts'=>"Ж",  'ch'=>"В", 'sh'=>"Ь",  
			'sch'=>"Ы",   '*' => "З", '**' => "Э", 'ye'=>"Щ", 'yu'=>"Ч", 'ya'=>"Ъ",
			'Yo'=>"╗",    'H'=>"у",  'Ts'=>"ж",  'Ch'=>"в", 'Sh'=>"ь",
			'Sch'=>"ы",   '***' => "з", '****' => "э", 'Ye'=>"щ", 'Yu'=>"ч", 'Ya'=>"ъ",
			'--'=>"/"
			));
			$new_string = strtr($new_string, 
			    "abvgdejziyklmnoprstufyABVGDEJZIYKLMNOPRSTUFY",
				"ЮАБЦДЕФГХИЙКЛМНОПЯРСТШюабцдефгхийклмнопярстш"

			);

	}

	debug("*** end: tires_transliterate ***");

	return $new_string;
}

function tires_if_has_cyrillic($string)
{
	global $user;
	global $config;
	
	debug("*** tires_if_has_cyrillic ***");

	debug("string: ".$string);

	$l = strlen($string);
	debug("length: ".$l);

	$if_has_cyrillic = 0;

	//$letters = "ЮАБЦДЕ╦ФГХИЙКЛМНОПЯРСТЗШЭЩЧЪюабцде╗фгхийклмнопярстзшэщчъ";
	//if (strpbrk($string, $letters))
	//	$if_has_cyrillic = 1;

	$letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-";

	for ($i = 1; $i <= $l; $i++)
	{
		if (!strpbrk($string, $letters))
		{
			debug("disallowed symbol");
			$if_has_cyrillic = 1;
		}
	}

	debug("*** end: tires_has_cyrillic ***");

	return $if_has_cyrillic;

}

function tires_deurl($param, $string)
{
	global $user;
	global $config;
	
	debug("*** tires_deurl ***");

	debug("param: ".$param);
	debug("string: ".$string);

	if (in_array($param, $config['tires']['own_tables']))
	{
		debug("param has its own table, getting id");
		$value = tires_get_own_table_id($param, $string);
	}
	else if (in_array($param, $config['tires']['params_url']))
	{
		debug("param has its own url field, getting usual field");
		$sql_query = "SELECT `".mysql_real_escape_string($param)."` FROM `tires` WHERE 
			`".mysql_real_escape_string($param."_url")."` = '".mysql_real_escape_string($string)."'
			LIMIT 1";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		$value = stripslashes($row[$param]);
		mysql_free_result($result);
	}
	else
	{
		debug("params doesn't has its own table or url field, just transliterate");
		$value = tires_transliterate(urldecode($string), "en", "ru");
	}

	debug("*** end: tires_deurl ***");

	return $value;
}

function tires_get_urlvalue($param, $string)
{
	global $user;
	global $config;
	
	debug("*** tires_get_urlvalue ***");

	debug("param: ".$param);
	debug("string: ".$string);

	if (in_array($param, $config['tires']['own_tables']))
	{
		$sql_query = "SELECT `url` FROM `".mysql_real_escape_string($param)."s` WHERE `id` = '".mysql_real_escape_string($string)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$value = stripslashes($row['url']);
	}
	else
	{
		$sql_query = "SELECT `".mysql_real_escape_string($param."_url")."` FROM `tires` WHERE 
			`".mysql_real_escape_string($param)."` = '".mysql_real_escape_string($string)."'
			LIMIT 1";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		$value = stripslashes($row[$param."_url"]);
		mysql_free_result($result);
	}

	debug("value: ".$value);

	debug("*** end: tires_get_urlvalue ***");

	return $value;
}

function tires_insert_brand_letters($brands)
{
	global $user;
	global $config;
	
	debug("*** tires_insert_brand_letters ***");

	/* inserting letters into brands */
	$brands_elements = $brands;
	unset($brands);
	$brands = array();
	$i = 0;
	$cur_letter = "";
	foreach($brands_elements as $brand_element_idx => $brand_element)
	{
		$first_letter = substr($brand_element['title'], 0, 1);
		if ($first_letter != $cur_letter)
		{
			$brands[$i]['letter'] = $first_letter;
			$cur_letter = $first_letter;
			$i++;
		}
		$brands[$i] = $brand_element;
		$i++;
	}

	debug("*** end: tires_insert_brand_letters ***");

	return $brands;
}

function tires_decyrillic($query)
{
	global $user;
	global $config;

	debug("*** tires_decyrillic ***");

	debug("query:");
	dump($query);

	$if_query_has_cyrillic = 0;
	foreach($query as $k => $v)
	{
		$query[$k] = urldecode($v);
		if (tires_if_has_cyrillic($query[$k]))
		{
			debug("has cyrillic");
			$if_query_has_cyrillic = 1;
			$query[$k] = tires_transliterate($query[$k], "ru", "en");
			debug("transliterated: ".$query[$k]);
		}
	}

	$result = array(
		'query' => $query,
		'if_query_has_cyrillic' => $if_query_has_cyrillic
	);

	debug("*** end: tires_decyrillic ***");

	return $result;
}

function tires_query_extract_values($query)
{
	global $user;
	global $config;

	debug("*** tires_query_extract_values ***");

	debug("query:");
	dump($query);

	foreach($query as $k => $v)
		if ($config['tires']['unk'] != $query[$k])
		{
			$param_name = $config['tires']['params_names'][$k];
			debug("extracting param ".$param_name);

			$value = tires_deurl($param_name, $v);

			$qty = tires_count_rows($param_name, $value);

			if ($qty)
			{
				debug("there are rows with this param");
				$query[$k] = $value;
			}
			else
			{
				debug("no rows with this param, setting it to -");
				$query[$k] = "-";
			}
		}

	debug("new query:");
	dump($query);

	debug("*** end: tires_query_extract_values ***");

	return $query;
}



function tires_gen_url($query, $mode = "simple", $param_name = "")
{
	global $user;
	global $config;
	
	debug("*** tires_gen_url ***");

	debug("query:");
	dump($query);
	debug("mode: ".$mode);
	debug("param_name: ".$param_name);

	$url = $config['base']['inst_root']."/";

	switch($mode)
	{
		default: break;

		case "simple":
			
			foreach($query as $k => $v)
				$url .= urlencode($v)."/";

			$params_qty = count($query);

			while ($params_qty < count($config['tires']['params_names']))
			{
				debug("have ".$params_qty." params, adding unk");
				$url .= $config['tires']['unk']."/";
				$params_qty++;
			}
		
		break;

		case "quickselect":

			$url_post = array();

			foreach($config['tires']['params_quickselect'] as $k => $v)
			{
				if ($config['tires']['unk'] == $_POST[$v])
					$url_post[$v] = $config['tires']['unk'];
				else if (in_array($v, $config['tires']['params_url']))
					$url_post[$v] = tires_get_urlvalue($v, $_POST[$v]);
				else
					$url_post[$v] = urlencode(tires_transliterate($_POST[$v], "ru", "en"));
			}
	
			$url = $url."x/".$url_post['season']."/x/x/x/".$url_post['profile']."/".$url_post['radius']."/".$url_post['width']."/";

		break;

		case "cancel_filter":

			$query[$param_name] = $config['tires']['unk'];

			foreach($query as $k => $v)
			{
				if ($config['tires']['unk'] == $v)
					$url .= $config['tires']['unk']."/";
				else if (in_array($k, $config['tires']['params_url']))
					$url .= tires_get_urlvalue($k, $v)."/";
				else
					$url .= urlencode(tires_transliterate($v, "ru", "en"))."/";
			}

		break;

		case "by_param_name":

			foreach($config['tires']['params_names'] as $k => $v)
			{
				debug($k.":".$v);
				if ($config['tires']['unk'] == $query[$v])
					$url .= $query[$v]."/";
				else if (in_array($config['tires']['params_names'][$k], $config['tires']['params_url']) || in_array($config['tires']['params_names'][$k], $config['tires']['own_tables']))
					$url .= tires_get_urlvalue($v, $query[$v])."/";
				else
					$url .= tires_transliterate($query[$v], "ru", "en")."/";
			}

		break;
	}

	debug("url: ".$url);

	debug("*** end: tires_gen_url ***");

	return $url;
}

function tires_params_extract($query)
{
	global $user;
	global $config;
	
	debug("*** tires_params_extract ***");

	debug("query: ".$query);

	$params = array();

	foreach($config['tires']['params_names'] as $param_idx => $param_name)
	{
		debug($param_idx.":".$param_name);
		if (isset($query[$param_idx]) && "" != $query[$param_idx] && $config['tires']['unk'] != $query[$param_idx])
		{
			debug("query is set, setting parameter");
			$params[$param_name] = $query[$param_idx];
		}
		else
		{
			debug("query is not set, setting parameter empty");
			$params[$param_name] = $config['tires']['unk'];
		}
	}

	debug("*** end: tires_params_extract ***");

	return $params;
}


function tires_check_no_choices($params)
{
	global $user;
	global $config;
	
	debug("*** tires_check_no_choices ***");

	$no_choice = 0;

	foreach($params as $k => $v)
	{
		debug($k.":".$v);
		if ("" == $v || $config['tires']['unk'] == $v)
		{
			debug($k." is empty, checking choices");
			$choices_qty = tires_count_distincts($k, $params);
			debug("choices qty: ".$choices_qty);
			if (1 == $choices_qty)
			{
				debug("have no choice, inserting param to params to build url later, param: ".$k);
				$no_choice = 1;
				$value = tires_view_param($k, $params);
				debug("value:");
				dump($value);
				if (in_array($k, $config['tires']['own_tables']))
					$params[$k] = $value['elements'][0]['own_id'];
				else
					$params[$k] = $value['elements'][0]['title'];
				debug("param: ".$params[$k]);
			}
		}
	}

	$result = array(
		'have_no_choice' => $no_choice,
		'new_params' => $params
	);

	debug("result:");
	dump($result);

	debug("*** end: tires_check_no_choices ***");

	return $result;
}

function tires_elements_sort($elements, $mode = "title")
{
	global $user;
	global $config;

	debug("*** tires_elements_sort ***");

	debug("elements:");
	dump($elements);

	$values = array();

	foreach($elements as $k => $v)
	{
		$values[$k] = $v[$mode];
	}

	debug("values:");
	dump($values);

	asort($values);

	debug("sorted values:");
	dump($values);

	$elements_new = array();
	foreach($values as $k => $v)
		$elements_new[] = $elements[$k];

	debug("*** end: tires_elements_sort ***");

	return $elements_new;
}

function tires_gen_brands($params)
{
	global $user;
	global $config;

	debug("*** tires_gen_brands ***");

	$brands = tires_view_param("brand", $params);

	$brands['elements'] = tires_elements_sort($brands['elements'], "title");

	debug("brands:");
	dump($brands);

	$brands['elements'] = tires_insert_brand_letters($brands['elements']);

	dump($brands);
	$brands_qty = count($brands['elements']);
	debug("brands qty: ".$brands_qty);

	if ($brands_qty)
	{
		$brands_in_col = ceil($brands_qty / $config['tires']['brands_cols_qty']);
		debug("brands in col: ".$brands_in_col);

		for ($i = 1; $i <= ($config['tires']['brands_cols_qty'] + 1); $i++)
			$brands['brands_'.$i]['brands'] = array();

		$i = 1;
		$j = 1;
		foreach($brands['elements'] as $k => $v)
		{
			$brands['brands_'.$i]['brands'][] = $v;
			$j++;
			if ($j > $brands_in_col)
			{ 
				if(isset($v['letter']))
				{
					array_pop($brands['brands_'.$i]['brands']);
					$i++;
					$j = 1;
					$brands['brands_'.$i]['brands'][] = $v;
					$j++;
				}
				else
				{
					$i++;
					$j = 1;
				}
			}
		}
	}

	debug("*** end: tires_gen_brands ***");

	return $brands;
}


function tires_get_own_table_id($param, $value)
{
	global $user;
	global $config;

	debug("*** tires_get_own_table_id ***");

	debug("param: ".$param);
	debug("value: ".$value);

	$id = 0;

	if (in_array($param, $config['tires']['own_tables']))
	{
		$sql_query = "SELECT `id` FROM `".mysql_real_escape_string($param)."s` WHERE
			`title` = BINARY '".mysql_real_escape_string($value)."' OR
			`url` = '".mysql_real_escape_string($value)."' OR
			`aliases` LIKE '%|".mysql_real_escape_string($value)."|%'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		$id = stripslashes($row['id']);
	}
	else
		debug("param doesn't has own table!");

	debug("id: ".$id);

	debug("*** end: tires_get_own_table_id ***");

	return $id;
}


function tires_get_own_table_title($param, $id)
{
	global $user;
	global $config;

	debug("*** tires_get_own_table_title ***");

	debug("param: ".$param);
	debug("id: ".$id);

	$title = "";

	if (in_array($param, $config['tires']['own_tables']))
	{
		$sql_query = "SELECT `title` FROM `".mysql_real_escape_string($param)."s` WHERE
			`id` = '".mysql_real_escape_string($id)."'";
		$result = exec_query($sql_query);
		$row = mysql_fetch_array($result);
		$title = stripslashes($row['title']);
	}
	else
		debug("param doesn't has own table!");

	debug("title: ".$title);

	debug("*** end: tires_get_own_table_title ***");

	return $title;
}

?>
