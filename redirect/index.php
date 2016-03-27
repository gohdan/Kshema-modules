<?php

// Base functions of the "redirect" module

include_once ($config['modules']['location']."redirect/config.php");

$config_file = $config['base']['doc_root']."/config/redirect.php";
if (file_exists($config_file))
	include($config_file);


function redirect_links_replace($text)
{
	global $user;
	global $config;
	debug ("*** redirect_links_replace ***");

	debug("text: ".$text, 2);

	/*
    $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
    $regex .= "(\:[0-9]{2,5})?"; // Port
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 
	preg_match_all("/^$regex$/", $text, $matches);
	*/

	$pattern = "/<a\b.*href\s*=\s*[\'\"](.*)[\'\"].*>(.*)<\/a>/isU";
	preg_match_all($pattern, $text, $matches);

	debug("matches:");
	dump($matches);

	foreach($matches[1] as $idx => $link)
	{
		$encoded_link = "/index.php?module=redirect&amp;action=to&amp;element=".htmlspecialchars(urlencode($link));
		debug("replacing ".$link." with ".$encoded_link);

		$text = str_replace($link, $encoded_link, $text);
	}
		
	debug ("*** end: redirect_links_replace ***");
	return $text;
}

function redirect_to()
{
	global $user;
	global $config;
	debug ("*** redirect_to ***");
	$content = array(
		'link' => ''
	);
	debug ("server uri:".$_SERVER['REQUEST_URI']);
	debug("GET:");
	dump($_GET);

	if (isset($_GET['element']))
		$url = $_GET['element'];
	
	debug ("server uri:".$_SERVER['REQUEST_URI']);

	$url = urldecode(substr($_SERVER['REQUEST_URI'], 45));
	debug ("url: ".$url);

	$content['link'] = $url;

	if ("yes" == $config['redirect']['immediately'] && !headers_sent())
		header("Location: ".$url);
	else

	debug ("*** end: redirect_to ***");
	return $content;
}

function redirect_default_action()
{
        global $user;
        global $config;

        $content = "";

		$descr_file_path = $config['modules']['location']."redirect/description.ini";
		debug ("descr_file_path: ".$descr_file_path);
		$module_data = parse_ini_file($descr_file_path);
		$module_data['module_name'] = $module_data['name']; // added to compatibility with base categories
		$module_data['module_title'] = $module_data['title']; // added to compatibility with base categories
		dump($module_data);

		if (isset($config['redirect']))
			array_merge($module_data, $config['redirect']);
		else
			$config['redirect'] = $module_data;
		dump($config['redirect']);

		$config['themes']['page_title'] .= " - ".$module_data['title'];
		$config['modules']['current_module'] = "redirect";

        debug("<br>=== mod: redirect ===");

        if (isset($_GET['action']))
        {
			debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							$content .= gen_content("redirect", "to", redirect_to());
                        break;

						case "to":
							$content .= gen_content("redirect", "to", redirect_to());
						break;

                }
        }

        else
        {
			debug ("*** action: default ***");
			$content .= gen_content("redirect", "to", redirect_to());
        }

		debug("=== end: mod: redirect ===<br>");
        return $content;

}

?>
