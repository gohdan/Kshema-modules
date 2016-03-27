<?php

// Base functions of the "tires" module

include_once ("functions.php");
include_once ("seo.php");


function tires_admin()
{
	debug ("*** tires_admin ***");
    global $config;
    $content = array(
    	'content' => ''
    );
	debug ("*** end: tires_admin ***");
    return $content;
}

function tires_frontpage()
{
	global $config;
    global $user;
	global $template;

	$config['tires']['unk'] = "x";
	$config['tires']['brands_cols_qty'] = "6";
	$config['tires']['tires_analogs_qty'] = 10;

	//$config['base']['debug_level'] = "3";
	//$config['base']['error_reporting'] = E_ALL;

	debug ("*** tires_frontpage ***");

    $content = array(
    	'content' => '',
		'unk' => $config['tires']['unk'],
		'brand_url' => '',
		'brand' => '',
		'source_url' => '',
		'source' => '',
		'season_url' => '',
		'season' => '',
		'spikes_url' => '',
		'spikes' => '',
		'speed_url' => '',
		'speed' => '',
		'profile_url' => '',
		'profile' => '',
		'radius_url' => '',
		'radius' => '',
		'width_url' => '',
		'width' => '',
		'season_select' => tires_get_distincts("season"),
		'radius_select' => tires_get_distincts("radius"),
		'width_select' => tires_get_distincts("width"),
		'profile_select' => tires_get_distincts("profile")
    );

    $config['tires']['params_names'] = array(
		'source',
		'season',
		'spikes',
		'brand',
		'model',
		'profile',
		'radius',
		'width'
    ); // + speed, title, rn

	$config['tires']['params_url'] = array(
		'source',
		'season'
	);

	$config['tires']['params_quickselect'] = array(
		'season',
		'radius',
		'width',
		'profile'
	);

	$config['tires']['own_tables'] = array(
		'brand',
		'model'
	);

	$params = array();

	debug("query: ".$_GET['query']);
	$query = explode("/", $_GET['query']);
	debug("query array:");
	dump($query);

	if ("" == $query[0]) // empty query really
	{
		debug("no query given really, emptying query");
		$query = array();
		debug("new query:");
		dump($query);
	}

	$query_qty = count($query);
	debug("query qty: ".$query_qty);
	$params_qty = count($config['tires']['params_names']);
	debug("params qty: ".$params_qty);

	if ($query_qty < $params_qty)
	{
		debug("not full url, redirecting");
		$url = tires_gen_url($query);
		debug("url: ".$url);
		if (!headers_sent())
			header("Location: ".$url);
	}
	else if ($query_qty > $params_qty)
	{
		debug("too many parameters");
		if (!headers_sent())
			header('HTTP/1.1 404 Not Found');
		include($config['base']['doc_root']."themes/tires/pages/404.php");
		exit;
	}

	/*
	$query_decyrillic = tires_decyrillic($query);
	$query = $query_decyrillic['query'];

	if ($query_decyrillic['if_query_has_cyrillic'])
		if (!headers_sent())
			header("Location: ".tires_gen_url($query));
	*/

	$query = tires_query_extract_values($query);

	debug("query extracted values:");
	dump($query);

	if (in_array("-", $query))
	{
		debug("not existing params in query");
		if (!headers_sent())
			header('HTTP/1.1 404 Not Found');
		include($config['base']['doc_root']."themes/tires/pages/404.php");
		exit;
	}

	if (isset($_POST['do_quick_select']))
	{
		debug("have POST quick select");
		if (!headers_sent())
			header("Location: ".tires_gen_url($query, "quickselect"));
	}

	$params = tires_params_extract($query);
	debug("params:");
	dump($params);

	$seo_data = tires_generate_seo_data($params);
	$template['title_generated'] = $seo_data['title'];
	$content['h1'] = $seo_data['h1'];

	debug("going through params");
	foreach($params as $param_name => $param_value)
	{
		debug("going through param: ".$param_name.":".$param_value);

		$content["url_no_".$param_name] = tires_gen_url($params, "cancel_filter", $param_name);

		if (($config['tires']['unk'] != $param_value) && ("" != $param_value))
		{
			debug("query is set, setting parameter");

			if (in_array($param_name, $config['tires']['own_tables']))
			{
				$content[$param_name] = tires_get_own_table_title($param_name, $param_value);
				$content[$param_name."_url"] = tires_get_urlvalue($param_name, $param_value);
			}
			else if (in_array($param_name, $config['tires']['params_url']))
			{
				$content[$param_name] = $param_value;
				$content[$param_name."_url"] = tires_get_urlvalue($param_name, $content[$param_name]);
			}
			else
			{
				$content[$param_name] = $param_value;
				$content[$param_name."_url"] = urlencode(tires_transliterate($content[$param_name], "ru", "en"));
			}

			if (isset($content[$param_name."_select"]))
				foreach($content[$param_name."_select"] as $k => $v)
					if ($params[$param_name] == $v['param'])
						$content[$param_name."_select"][$k]['selected'] = "yes";
		}
		else
		{
			debug($param_value." is empty, generating form");

			if ("brand" == $param_name)
			{
				$content['brands'] = "yes";
				$brands = tires_gen_brands($params);

				$brands_qty = count($brands['elements']);
				debug("brands qty: ".$brands_qty);

				if (0 == $brands_qty)
					$content['no_brands'] = "yes";
				else
					for ($i = 1; $i <= ($config['tires']['brands_cols_qty'] + 1); $i++)
						if (count($brands['brands_'.$i]))
							$content["brands_".$i] = gen_content("tires", "view_brands", $brands["brands_".$i]);

			}
			else
				$content[$param_name.'s'] = gen_content("tires", "view_param", tires_view_param($param_name, $params));

		}
	}

	if (tires_if_all_params_set($query)) // all params set, view exact tires
	{
		debug("all params set, viewing tire");

		$tire_data = tires_view_tire($query);

		$content['tire'] = gen_content("tires", "view_tire", $tire_data);
	}
	else
	{
		debug("no tire selected, checking params");

		$content['show_filters_cancel'] = "yes";
		$content['show_all_breadcrumbs'] = "yes";

		$no_choice = tires_check_no_choices($params);

		if ($no_choice['have_no_choice'])
		{
			debug("no choice, redirecting");
			$url = tires_gen_url($no_choice['new_params'], "by_param_name");
			debug("url: ".$url);
			if (!headers_sent())
				header("Location: ".$url);
		}

		if (tires_if_any_param_set($params))
		{
			$content['if_results'] = "yes";
			$content['results'] = gen_content("tires", "view_results", tires_get_results($params));
		}
	}

	$config['base']['debug_level'] = "0";
	$config['base']['error_reporting'] = E_ALL;

	debug ("*** end: tires_frontpage");
    return $content;
}

function tires_get_actions_list()
{
	debug ("*** tires_get_actions_list ***");
	global $user;
	global $debug;
	
	$actions_list = array(
		"admin",
		"admin_satellite",
		"config_edit",
		"install_tables",
		"drop_tables",
		"update_tables",
		"categories_view",
		"categories_add",
		"categories_del",
		"categories_edit",
		"add_tires",
		"view_by_category",
		"add",
		"edit",
		"del",
		"view",
		"tires_archive",
		"view_by_user",
		"privileges_edit",
		"sections_edit",
		"titles_edit",
		"moderate_edit",
		"moderate_del"
	);

	debug ("*** end: tires_get_actions_list ***");
	return $actions_list;
}

function tires_default_action()
{
        global $user;
		global $config;
		global $template;

        debug("<br>=== mod: tires ===");

        $content = "";

		$descr_file_path = $config['modules']['location']."tires/description.ini";
		debug ("descr_file_path: ".$descr_file_path);
		$module_data = parse_ini_file($descr_file_path);
		$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
		$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
		dump($module_data);

		if (isset($config['base']['inst_root']))
			$module_data['inst_root'] = $config['base']['inst_root'];
		else
			$module_data['inst_root'] = "";

		if (isset($config['tires']))
			array_merge($module_data, $config['tires']);
		else
			$config['tires'] = $module_data;
		dump($config['tires']);

		$config['themes']['page_title']['module'] = $module_data['title'];
		$config['modules']['current_module'] = "tires";

		if ($user['id'])
			$config['base']['use_captcha'] = "no";

		$priv = new Privileges();


		if ($priv -> has("tires", "admin", "write"))
			$module_data['show_admin_link'] = "yes";

		if ($priv -> has("tires", "add", "write"))
			$module_data['show_add_link'] = "yes";


        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("tires", "frontpage", tires_frontpage());
                        break;
                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("tires", "frontpage", tires_frontpage());
        }

        debug("=== end: mod: tires ===<br>");
        return $content;
}

?>
