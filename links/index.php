<?php

// Base functions of the "links" module


include_once ("db.php");
include_once ("categories.php");
include_once ("links.php");



function links_admin()
{
        $content['content'] = "";
        return $content;
}

function links_frontpage()
{
        global $user;
        global $debug;
        $content['content'] = "";

		$priv = new Privileges();
		if ($priv -> has ("links", "admin", "write"))
			$content['if_show_admin_link'] = "yes";

        debug ("*** links_frontpage ***");
        debug ("*** end: links_frontpage");

        return $content;
}


function links_default_action()
{
        global $user;
        $content = "";
        $nav_string = "
        ";

        $content .= $nav_string;

        debug("<br>=== mod: links ===");

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("links", "frontpage", links_frontpage());
                        break;

                        case "admin":
                                $content .= gen_content("links", "admin", links_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("links", "install_tables", links_install_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("links", "update_tables", links_update_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("links", "drop_tables", links_drop_tables());
                        break;

                        case "view_categories":
                                $content .= gen_content("links", "categories_view", links_categories_view());
                        break;

                        case "add_category":
                                $content .= gen_content("links", "categories_add", links_categories_add());
                        break;

                        case "del_category":
                                $content .= gen_content("links", "categories_del", links_categories_del());
                        break;

                        case "add_links":
                                $content .= gen_content("links", "add", links_add());
                        break;

                        case "view_by_category":
                                $content .= gen_content("links", "view_by_category", links_view_by_category());
                        break;

                        case "edit":
                                $content .= gen_content("links", "edit", links_edit());
                        break;

                        case "del":
                                $content .= gen_content("links", "del", links_del());
                        break;

                        case "view":
                                $content .= gen_content("links", "view", links_view());
                        break;

						case "links_archive":
                                $content .= gen_content("links", "archive", links_archive());
                        break;

						case "category_edit":
                                $content .= gen_content("links", "categories_edit", links_categories_edit());
                        break;
                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("links", "frontpage", links_frontpage());
        }

        debug("=== end: mod: links ===<br>");
        return $content;

}

?>
