<?php

// SEO functions



function tires_generate_seo_data($params)
{
	global $user;
	global $config;
	//$config['base']['debug_level'] = "3";	
	debug("*** tires_generate_seo_data ***");

	debug("params:");
	dump($params);

	foreach($params as $k => $v)
		if (in_array($k, $config['tires']['own_tables']))
			$params[$k] = tires_get_own_table_title($k, $v);

	debug("changed params:");
	dump($params);

	$h1_parts = array(
		'source' => '',
		'season' => '',
		'shiny' => 'Магазины шин',
		'brand' => '',
		'radius' => '',
		'width' => '',
		'type' => ' для легковых автомобилей', //"легковая" == $params['type']
		'model' => " ".$params['model']." ",
		'tail' => ' в Тамбове'
	);

	$title_parts = array(
		'source' => '',
		'season' => '',
		'shiny' => 'Шины',
		'brand' => '',
		'radius' => '',
		'width' => '',
		'type' => ' для легковых автомобилей', //"легковая" == $params['type']
		'model' => " ".$params['model']." ",
		'tail' => ', купить в Тамбове — адреса и телефоны магазинов, цены'
	);


	if ($config['tires']['unk'] != $params['brand'])
	{
		$h1_parts['brand'] = " ".$params['brand'];
		$title_parts['brand'] = " ".$params['brand'];
	}

	if ($config['tires']['unk'] != $params['radius'])
	{
		$h1_parts['radius'] = " R".$params['radius'];
		$title_parts['radius'] = " R".$params['radius']." (диаметр)";
	}

	if ($config['tires']['unk'] != $params['width'])
	{
		$h1_parts['width'] = " шириной ".$params['width'];
		$title_parts['width'] = " шириной ".$params['width'];
	}

	if ($config['tires']['unk'] != $params['source'])
	{
		$h1_parts['shiny'] = "шины";
		$title_parts['shiny'] = "шины";

		if ("Импортная" == $params['source'])
		{
			$h1_parts['source'] = "Импортные ";
			$title_parts['source'] = "Импортные ";
		}
		else if ("Отечественная" == $params['source'])
		{
			$h1_parts['source'] = "Отечественные ";
			$title_parts['source'] = "Отечественные ";
		}
	}

	if ($config['tires']['unk'] != $params['season'])
	{
		$h1_parts['shiny'] = "шины";
		$title_parts['shiny'] = "шины (резина)";

		if ("Зимняя" == $params['season'])
		{	
			if ($config['tires']['unk'] == $params['source'])
			{
				$h1_parts['season'] = "Зимние ";
				$title_parts['season'] = "Зимние ";
			}
			else
			{
				$h1_parts['season'] = "зимние ";
				$title_parts['season'] = "зимние ";
			}
		}
		else if ("Летняя" == $params['season'])
		{
			if ($config['tires']['unk'] == $params['source'])
			{
				$h1_parts['season'] = "Летние ";
				$title_parts['season'] = "Летние ";
			}
			else
			{
				$h1_parts['season'] = "летние ";
				$title_parts['season'] = "летние ";
			}
		}
		else if ("Всесезонная" == $params['season'])
		{
			if ($config['tires']['unk'] == $params['source'])
			{
				$h1_parts['season'] = "Всесезонные ";
				$title_parts['season'] = "Всесезонные ";
			}
			else
			{
				$h1_parts['season'] = "всесезонные ";
				$title_parts['season'] = "всесезонные ";
			}
		}
	}

	if ($config['tires']['unk'] != $params['spikes'])
	{
		if ("шипы" == $params['spikes'])
		{
		
			if ("" != $h1_parts['source'] || "" != $h1_parts['season'])
			{
				$h1_parts['source'] = str_replace("ые", "ая", $h1_parts['source']);
				$h1_parts['season'] = str_replace("ие", "яя", $h1_parts['season']);
				$h1_parts['shiny'] = "шипованная резина ";
			}
			else
				$h1_parts['shiny'] = "Шипованная резина ";

			if ("" != $title_parts['source'] || "" != $title_parts['season'])
			{
				$title_parts['source'] = str_replace("ые", "ая", $title_parts['source']);
				$title_parts['season'] = str_replace("ие", "яя", $title_parts['season']);
				$title_parts['shiny'] = "шипованная резина ";
			}
			else
				$title_parts['shiny'] = "Шипованная резина ";
		}
		else if ("Зимняя" == $params['season'])
		{
			$h1_parts['shiny'] = "шины липучки ";
			$title_parts['shiny'] = "шины липучки ";
		}

	}


	if (tires_if_all_params_set($params)) // viewing tire
	{
		$h1_parts['shiny'] = 'Шины ';
		$title_parts['shiny'] = 'Шины ';

		$seo_data = array(
			'h1' => 
				$h1_parts['shiny'].
				$h1_parts['brand'].
				$h1_parts['model'].
				$h1_parts['tail'],
			'title' => 
				$h1_parts['shiny'].
				$h1_parts['brand'].
				$h1_parts['model'].
				$h1_parts['tail'],
		);
	}
	else
		$seo_data = array(
			'h1' =>
				$h1_parts['source'].
				$h1_parts['season'].
				$h1_parts['shiny'].
				$h1_parts['type'].
				$h1_parts['brand'].
				$h1_parts['radius'].
				$h1_parts['width'].
				$h1_parts['tail'],
			'title' =>
				$title_parts['source'].
				$title_parts['season'].
				$title_parts['shiny'].
				$title_parts['type'].
				$title_parts['brand'].
				$title_parts['radius'].
				$title_parts['width'].
				$title_parts['tail']
		);

	debug("seo_data:");
	dump($seo_data);

	debug("*** end: tires_generate_seo_data ***");
	//$config['base']['debug_level'] = "0";

	return $seo_data;
}

?>
