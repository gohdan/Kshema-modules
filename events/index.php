<?php

// Base functions of the "events" module


include_once ("db.php");
include_once ("events.php");


function events_admin()
{
        $content['content'] = "";
        return $content;
}

function events_frontpage()
{
    debug ("*** events_frontpage ***");
    global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'events' => '',
        'admin_link' => '',
        'result' => ''
    );
	$i = 0;

    if (1 == $user['id'])
    {
        $content['admin_link'] .= "<p><a href=\"/index.php?module=events&action=admin\">Администрировать события</a></p>";
    }

    $result = exec_query("SELECT * FROM ksh_events ORDER BY id ASC LIMIT 30");

    while ($row = mysql_fetch_array($result))
    {
        debug("show events ".$row['id']);
		if ("" != $row['image_big'])
			$content['events'][$i]['image_big'] = "<img src=\"".$row['image_big']."\">";
		else $content['events'][$i]['image_big'] = "";

		$content['events'][$i]['id'] = stripslashes($row['id']);
		$content['events'][$i]['desc_local'] = stripslashes($row['desc_local']);
		$content['events'][$i]['organiser'] = stripslashes($row['organiser']);
		$i++;
    }
    mysql_free_result($result);

    debug ("*** end: events_frontpage");
    return $content;
}


function events_default_action()
{
	global $config;
        global $user;
        $content = "";
        $nav_string = "";

        $content .= $nav_string;

        debug("<br>=== mod: events ===");

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("events", "frontpage", events_frontpage());
                        break;

                        case "admin":
                                $content .= gen_content("events", "admin", events_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("events", "install_tables", events_install_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("events", "drop_tables", events_drop_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("events", "update_tables", events_update_tables());
                        break;

						case "import_xml":
							$content .= gen_content("events", "import_xml", events_import_xml());
						break;
                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("events", "frontpage", events_frontpage());
        }

        debug("=== end: mod: events ===<br>");
        return $content;

}

?>
