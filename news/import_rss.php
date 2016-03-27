<?php

// News import functions of the news module


function news_import_rss()
{
	debug ("*** news_import_rss ***");
	global $user;
	global $config;
	$content = array(
		'content' => '',
		'result' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$url = $config['news']['rss_url'];
		$rss = new lastrss;
		$news = $rss->Get("$url");
		debug ("RSS title: ".$news['title']);
		$i=0;
		while (array_key_exists($i,$news['items']) )
		{
			extract($news['items'][$i]);

			debug ("item: ".$i);
			debug ("title: ".$title);
			debug ("pubDate: ".$pubDate);

			$date_exploded = explode(" ", $pubDate);
			$date = $date_exploded[3]."-".base_get_month_num($date_exploded[2], 2)."-".$date_exploded[1];
			debug ("new date: ".$date);

			debug ("link: ".$link);
			debug ("description: ".$description);

			if (isset($_GET['category']))
				$category = $_GET['category'];
			else
				$category = 1;
			debug ("category: ".$category);

			$if_exist = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_news WHERE url='".mysql_real_escape_string($link)."'"), 0, 0);
			debug ("if exist: ".$if_exist);

			if (!$if_exist)
			{
				debug ("no such link, importing");
				exec_query("INSERT INTO ksh_news (name, date, descr, url, category) values ('".mysql_real_escape_string($title)."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string(str_replace("&lt;br&gt;", "",  $description))."', '".mysql_real_escape_string($link)."', '".mysql_real_escape_string($category)."')");
			}
			else
			{
				debug ("such link already exist, don't importing");
			}

			$i++;
		}
	}

	else
	{
		debug ("user isn't admin!");
		$content['content'] .= "Пожалуйста, войдите в систему как администратор";
	}
	debug ("*** end: news_import_rss ***");
	return $content;
}

global $xml_content;
global $tag;

function startElement($parser, $name, $attrs)
{
	global $xml_content;
	global $tag;
	$tag = $name;
// 1-й параметр будет ссылкой на наш $xml_parser, 2-й - строка, содержащая имя открывающего тега, а третий - именованный массив параметров тега, т.е. если в теге есть параметр 'href', то его значение записалось в $attrs['href']. 
	debug ("opening tag: ".$name);
	return 0;
}

function endElement($parser, $name)
{
	global $xml_content;
	global $tag;
// Содержимое 1го параметра - $xml_parser, а второго - строка с именем закрывающего тега. 
	debug ("closing tag: ".$name);
	return 0;
}

function textElement($parser, $text)
{
	global $xml_content;
	global $tag;
// Содержимое 1го параметра - опять же $xml_parser, а второго - строка с полученными текстовыми данными. 
	//echo ("parser: ".$parser);
	debug ("text element: ".$text);
	$xml_content[$tag] .= $text;
	return 0;
}
 
function news_import_xml()
{
	debug ("*** news_import_xml ***");
	global $user;
	global $config;
	global $xml_content;
	$content = array(
		'content' => '',
		'result' => ''
	);
	// 1 - Создать поток чтения к нужному XML файлу, например так: 
	$fp = fopen("/home/gohdan/work/kshema/www/modules/news/p_update_short.xml", "r");
	// 2 - прочитать в строку содержиме XML, например так: 
	$datastr = fread($fp, 8192);
	//  (8192 байт - максимальное число байт из нашего "superxmlka.xml" файла, которое функция fread вернёт за 1 обращение, и если поставить бельшее число, то всё равно будут читаться только 8192 байта) 
	// 3 - создать переменную-парсер, которая будет обрабатывать нашу прочтённую строку $datastr 
	$xml_parser=xml_parser_create();
	// 4 - подумать, какие структуры нам нужно вытащить из нашего XML файла. В нашем случае это будут "элементы"(element) и "текстовая информация"(character data). 
	// 5 - назначить функции на обработку необходимых типов объектов при парсинге XML с помощью похожих команд "xml_set_..._handler": 
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	// В "xml_set_element_handler" необходимо назначить 2 функции, первая вызывается при обработке открывающего тега элемента, а вторая - при обработке закрывающего тега. 
	xml_set_character_data_handler ($xml_parser, "textElement");
	// В "xml_set_character_data_handler" необходимо назначить 1 функцию, которая будет вызываться при обработке текстовых данных. 
	// 6 - создать придуманные нами функции, учитывая, что у них должны быть определённые параметры
	// 7 - после всего этого надо дать команду на старт парсинга: 
	xml_parse( $xml_parser, $datastr, feof($fp));
	
	debug ("parsed XML:");
	foreach ($xml_content as $k => $v)
		debug ($k.":".$v);
	$content['category_name'] = $xml_content['CATEGORY_NAME'];
	$content['main_category_id'] = $xml_content['MAIN_CATEGORY_ID'];
	$content['venue_zip'] = $xml_content['VENUE_ZIP'];
	$content['date_sales_from'] = $xml_content['DATE_SALES_FROM'];
	$content['desc_local'] = $xml_content['DESC_LOCAL'];
	$content['venue_id'] = $xml_content['VENUE_ID'];
	$content['date_sales_to'] = $xml_content['DATE_SALES_TO'];
	$content['organiser_name'] = $xml_content['ORGANISER_NAME'];
	$content['venue_name'] = $xml_content['VENUE_NAME'];
	$content['desc_en'] = $xml_content['DESC_EN'];
	$content['venue_iso2_country_code'] = $xml_content['VENUE_ISO2_COUNTRY_CODE'];
	$content['url'] = $xml_content['URL'];
	$content['venue_town'] = $xml_content['VENUE_TOWN'];
	$content['event_name'] = $xml_content['EVENT_NAME'];
	$content['image_big'] = $xml_content['IMAGE_BIG'];
	$content['date_to'] = $xml_content['DATE_TO'];
	$content['date_from'] = $xml_content['DATE_FROM'];
	$content['image_small'] = $xml_content['IMAGE_SMALL'];
	$content['category_id'] = $xml_content['CATEGORY_ID'];
	$content['venue_street'] = $xml_content['VENUE_STREET'];
	$content['event_id'] = $xml_content['EVENT_ID'];
	$content['main_category_name'] = $xml_content['MAIN_CATEGORY_NAME'];
	$content['image_med'] = $xml_content['IMAGE_MED'];
	
	
	debug ("*** end: news_import_xml ***");
	return $content;
}

?>
