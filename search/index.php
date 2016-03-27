<?php

// Base functions of the "search" module

include_once("modules.php");

function search_main()
{
	global $user;
	global $config;
    debug ("*** search_main ***");
	$content = array(
		'result' => '',
		'search_string' => '',
		'results' => '',
		'inst_root' => '',
		'site_url' => ''
	);

	$content['inst_root'] = trim($config['base']['inst_root'], "/");
	$content['site_url'] = trim($config['base']['site_url'], "/");

    if (isset($_POST['search_string']))
        $search_string = $_POST['search_string'];
    else $search_string = "";

    $content['search_string'] = $search_string;

    if (isset($_POST['do_search']))
    {
        debug ("have something to search");

        if (("" == $search_string) || (" " == $search_string))
        {
            debug ("search string is empty");
            $content['result'] .= "Задан пустой поисковый запрос";
        }
        else
        {
            debug ("search string is not empty");

			$results = array();

			if (in_array("articles", $config['modules']['installed']))
				$results = array_merge($results, search_articles($search_string));

			if (in_array("bills", $config['modules']['installed']))
				$results = array_merge($results, search_bills($search_string));

			if (in_array("news", $config['modules']['installed']))
				$results = array_merge($results, search_news($search_string));

			if (in_array("pages", $config['modules']['installed']))
				$results = array_merge($results, search_pages($search_string));

			if (in_array("shop", $config['modules']['installed']))
				$results = array_merge($results, search_shop($search_string));

			$content['results'] = $results;
        }
    }
    else
        debug ("have nothing to search!");

    debug ("*** search_main ***");
    return $content;
}

function search_default_action()
{
	debug("<br>=== mod: search ===");

	debug ("*** action: default");
	$content = gen_content("search", "main", search_main());

	debug("=== end: mod: search ===<br>");
	return $content;

}

?>
