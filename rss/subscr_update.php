<?php


$f = explode("/", dirName(__FILE__));
array_pop($f);
array_pop($f);
$_SERVER['DOCUMENT_ROOT'] = implode("/", $f);
//print_r($_SERVER);

include_once("../../config.php");
//print_r($config);

$config['base']['debug_echo'] = "no";
if ("yes" == $config['base']['logs_write'])
	$config['base']['debug_file'] = "yes";

ini_set('error_reporting', $config['base']['error_reporting']);
error_reporting($config['base']['error_reporting']);
if ($config['base']['error_reporting'])
	ini_set('display_errors', 1);
else
	ini_set('display_errors', 0);

include_once("../base/index.php");
include_once("../db/index.php");
include_once("index.php");
//include_once($config['libs']['location']."lastrss/lastRSS.php");
include_once($config['libs']['location']."magpierss/rss_fetch.inc");

connect_2db ($db_user, $db_password, $db_host, $db_name);

foreach ($config['rss']['feeds'] as $feed_idx => $feed)
{
//	echo ($feed."\n");
//	$rss = new lastRSS;
//	$news = $rss->Get($feed);
	$news = fetch_rss($feed);
//	print_r($news);

	foreach($news->items as $item)
		rss_add($item['title'], $item['link'], $item['description'], $item['pubdate']);
}

rss_purge();

?>
