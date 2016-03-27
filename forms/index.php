<?php

// Base functions of the "forms" module

include_once ("db.php");
include_once ("forms.php");

function forms_admin()
{
        $content['content'] = "";
        return $content;
}

function forms_frontpage()
{
        debug ("*** forms_frontpage ***");
        global $config;
        $content = array(
        	'content' => ''
        );
        $content['content'] = "";
        debug ("*** end: forms_frontpage ***");
        return $content;
}

function forms_default_action()
{
        debug("<br>=== mod: forms ===");
		global $user;
        global $config;

        $content = "";

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                //$content .= gen_content("forms", "frontpage", forms_frontpage());
                                $content .= gen_content("forms", "admin", forms_admin());
                        break;

                        case "send":
							if (isset($_POST['send']))
                                $content .= gen_content("forms", "send", forms_send());
							else
								$content .= gen_content("forms", "submit", forms_submit());
                        break;

                        case "submit":
                            if (isset($_POST['send']))
                                $content .= gen_content("forms", "send", forms_send());
							else
								$content .= gen_content("forms", "submit", forms_submit());
                        break;

                        case "view_submitted_forms":
                                $content .= gen_content("forms", "view_submitted_forms", forms_view_submitted_forms());
                        break;

                        case "admin":
                                $content .= gen_content("forms", "admin", forms_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("forms", "install_tables", forms_install_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("forms", "drop_tables", forms_drop_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("forms", "update_tables", forms_update_tables());
                        break;

                        case "add":
                                $content .= gen_content("forms", "add", forms_add());
                        break;

                        case "edit":
                        	$content .= gen_content("forms", "edit", forms_edit());
                        break;

                        case "list":
                        	$content .= gen_content("forms", "list", forms_list());
                        break;

						case "view_submitted":
                        	$content .= gen_content("forms", "view_submitted", forms_view_submitted());
                        break;

						case "del_submitted":
                        	$content .= gen_content("forms", "del_submitted", forms_del_submitted());
                        break;

                }
        }

        else
        {
                debug ("*** action: default");
                // $content = gen_content("forms", "frontpage", forms_frontpage());
                $content .= gen_content("forms", "admin", forms_admin());
        }

        debug("=== end: mod: forms ===<br>");
        return $content;

}

?>

