<?php

// Base functions of the "docs" module

function docs_default_action()
{
	global $user;
	$content = "";
	$nav_string = "
	";

	$content .= $nav_string;

	debug("<br>=== mod: docs ===");

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

	debug("=== end: mod: docs ===<br>");
	return $content;

}

?>
