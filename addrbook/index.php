<?php

// Base functions of the "addrbook" module

function addrbook_default_action()
{
	global $user;
	$content = "";
	$nav_string = "
	";

	$content .= $nav_string;

	debug("<br>=== mod: addrbook ===");

	if (isset($_GET['action']))
	{
		debug ("action: ".$_GET['action']);
		switch ($_GET['action'])
		{
			default: break;
		}
	}

	else
	{
		debug ("*** default");

	}

	debug("=== end: mod: addrbook ===<br>");
	return $content;

}

?>
