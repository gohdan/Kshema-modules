<?php

// RSS functions of the RSS module

function rss_add($title, $link, $description, $pubdate)
{
	global $user;
	global $config;
	debug("*** rss_add ***");
/*
	$sql_query = "SELECT COUNT(*) FROM `ksh_rss` WHERE
		`title` = '".mysql_real_escape_string($title)."',
		`link` = '".mysql_real_escape_string($link)."',
		`description` = '".mysql_real_escape_string($description)."',
		`pubdate` = '".mysql_real_escape_string($pubdate)."'
		";
*/
	$sql_query = "SELECT COUNT(*) FROM `ksh_rss` WHERE
		`link` = '".mysql_real_escape_string($link)."'
		";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$qty = $row['COUNT(*)'];
	if ("0" == $qty)
	{
		$sql_query = "INSERT INTO `ksh_rss` (`title`, `link`, `description`, `pubdate`, `date`, `time`) VALUES (
		'".mysql_real_escape_string($title)."',
		'".mysql_real_escape_string($link)."',
		'".mysql_real_escape_string($description)."',
		'".mysql_real_escape_string($pubdate)."',
		CURDATE(),
		CURTIME()
		)";
		exec_query($sql_query);
	}


	debug("*** end: rss_add ***");
	return 1;
}

function rss_purge()
{
	global $user;
	global $config;
	debug("*** rss_purge ***");

	$sql_query = "SELECT `id` FROM `ksh_rss` ORDER BY `id` DESC LIMIT ".mysql_real_escape_string($config['rss']['max_items']).",1";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$id = $row['id'];
	debug("id :". $id);

	if ($id)
	{
		$sql_query = "DELETE FROM `ksh_rss` WHERE `id` <= '".mysql_real_escape_string($id)."'";
		exec_query($sql_query);
	}

	debug("*** end: rss_purge ***");
	return 1;
}

function rss_view()
{
    debug("*** rss_view ***");
    global $user;
	global $config;

    $content = array(
		'rss_items' => array()
	);
    
	$i = 0;
    $result = exec_query("SELECT * FROM `ksh_rss` ORDER BY `id` DESC"); 
	while ($row = mysql_fetch_array($result))
	{
		$content['rss_items'][$i]['id'] = stripslashes($row['id']);
		$content['rss_items'][$i]['title'] = stripslashes($row['title']);

		$content['rss_items'][$i]['link'] = htmlspecialchars(stripslashes($row['link']));

		if ("" != $row['pubDate'])
			$content['rss_items'][$i]['date'] = stripslashes($row['pubDate']);
		else
		{
			$datetime = explode(" ", stripslashes($row['date']));
			$dt = explode("-", $datetime[0]);

			switch($dt[1])
			{
				default: break;
				case "01": $mon = "Jan"; break;
				case "02": $mon = "Feb"; break;
				case "03": $mon = "Mar"; break;
				case "04": $mon = "Apr"; break;
				case "05": $mon = "May"; break;
				case "06": $mon = "Jun"; break;
				case "07": $mon = "Jul"; break;
				case "08": $mon = "Aug"; break;
				case "09": $mon = "Sep"; break;
				case "10": $mon = "Oct"; break;
				case "11": $mon = "Nov"; break;
				case "12": $mon = "Dec"; break;
			}

			$tm = explode(":", $datetime[1]);
			$content['rss_items'][$i]['date'] = $dt[2]." ".$mon." ".$dt[0]." 00:00:00 +0400";
		}


		$content['rss_items'][$i]['description'] = htmlspecialchars(strip_tags(stripslashes($row['description']), "<p><br><a>"));
		$content['rss_items'][$i]['site_url'] = $config['base']['site_url'];
		$i++;
	}
	mysql_free_result($result);


    debug("*** end: rss_view ***");
    return $content;
}


?>
