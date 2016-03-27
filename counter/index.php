<?php

// Base functions of the counter module

include_once ($config['modules']['location']."counter/config.php");

$config_file = $config['base']['doc_root']."/config/counter.php";
if (file_exists($config_file))
	include($config_file);

include_once($config['modules']['location']."counter/db.php");
include_once($config['modules']['location']."counter/counter.php");

function counter_admin()
{
        $content['content'] = "";
        return $content;
}



function counter_default_action()
{
	global $config;
    global $user;
        $content = "";

        debug("<br>=== mod: counter ===");

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("counter", "frontpage", counter_frontpage());
                        break;

                        case "admin":
                                $content .= gen_content("counter", "admin", counter_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("counter", "install_tables", counter_install_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("counter", "drop_tables", counter_drop_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("counter", "update_tables", counter_update_tables());
                        break;

                        case "view_last":
                                $content .= gen_content("counter", "view_last", counter_view_last());
                        break;

						case "view_month":
                                $content .= gen_content("counter", "view_month", counter_view_month());
                        break;
						
						case "view_day":
                                $content .= gen_content("counter", "view_day", counter_view_day());
                        break;

                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("counter", "frontpage", counter_frontpage());
        }

        debug("=== end: mod: counter ===<br>");
        return $content;

}

?>
