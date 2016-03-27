<?php

// Base functions of the "store" module

include_once ("db.php");
include_once ("categories.php");
include_once ("goods.php");
include_once ("objects.php");
include_once ("users.php");
include_once ("inouts.php");
include_once ("cart.php");
include_once ("archive.php");


function store_admin()
{
	debug ("*** store_admin ***");
    $content['content'] = "";
	debug ("*** end: store_admin ***");
    return $content;
}

function store_frontpage()
{
	debug ("*** store_frontpage ***");
    global $config;
	global $user;

	$content = store_categories_view_all();

    debug ("*** end: store_frontpage ***");
    return $content;
}



function store_default_action()
{
	global $config;
    global $user;
        $content = "";

        debug("<br>=== mod: store ===");

		store_archive_create();

        if (isset($_GET['action']))
        {
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
                                $content .= gen_content("store", "frontpage", store_frontpage());
                        break;

						case "admin":
                                $content .= gen_content("store", "admin", store_admin());
                        break;

                        case "install_tables":
                                $content .= gen_content("store", "install_tables", store_install_tables());
                        break;

                        case "drop_tables":
                                $content .= gen_content("store", "drop_tables", store_drop_tables());
                        break;

                        case "update_tables":
                                $content .= gen_content("store", "update_tables", store_update_tables());
                        break;

						case "categories_add":
                                $content .= gen_content("store", "categories_add", store_categories_add());
                        break;

						case "categories_edit":
                                $content .= gen_content("store", "categories_edit", store_categories_edit());
                        break;

						case "categories_del":
                                $content .= gen_content("store", "categories_del", store_categories_del());
                        break;

						case "categories_view_all":
                                $content .= gen_content("store", "categories_view_all", store_categories_view_all());
                        break;

						case "categories_sort":
                                $content .= gen_content("store", "categories_sort", store_categories_sort());
                        break;

						case "goods_add":
                                $content .= gen_content("store", "goods_add", store_goods_add());
                        break;

						case "goods_edit":
                                $content .= gen_content("store", "goods_edit", store_goods_edit());
                        break;

						case "goods_del":
                                $content .= gen_content("store", "goods_del", store_goods_del());
                        break;

						case "goods_out":
                                $content .= gen_content("store", "goods_out", store_goods_out());
                        break;

						case "goods_out_from_category":
                                $content .= gen_content("store", "goods_out_from_category", store_goods_out_from_category());
                        break;

						case "goods_in":
                                $content .= gen_content("store", "goods_in", store_goods_in());
                        break;

						case "goods_sort":
                                $content .= gen_content("store", "goods_sort", store_goods_sort());
                        break;

						case "goods_comment_view":
                                $content .= gen_content("store", "goods_comment_view", store_goods_comment_view());
                        break;

						case "view_by_categories":
                                $content .= gen_content("store", "view_by_categories", store_view_by_categories());
                        break;

						case "objects_view_all":
                                $content .= gen_content("store", "objects_view_all", store_objects_view_all());
                        break;

						case "objects_add":
                                $content .= gen_content("store", "objects_add", store_objects_add());
                        break;

						case "objects_edit":
                                $content .= gen_content("store", "objects_edit", store_objects_edit());
                        break;

						case "objects_del":
                                $content .= gen_content("store", "objects_del", store_objects_del());
						break;

						case "objects_sort":
                                $content .= gen_content("store", "objects_sort", store_objects_sort());
                        break;

						case "users_view_all":
                                $content .= gen_content("store", "users_view_all", store_users_view_all());
                        break;

						case "users_add":
                                $content .= gen_content("store", "users_add", store_users_add());
                        break;

						case "users_edit":
                                $content .= gen_content("store", "users_edit", store_users_edit());
                        break;

						case "users_del":
                                $content .= gen_content("store", "users_del", store_users_del());
						break;

						case "users_sort":
                                $content .= gen_content("store", "users_sort", store_users_sort());
                        break;

						case "inouts_view_all":
                                $content .= gen_content("store", "inouts_view_all", store_inouts_view_all());
						break;

						case "inouts_view":
                                $content .= gen_content("store", "inouts_view", store_inouts_view());
						break;

						case "inouts_comment_view":
                                $content .= gen_content("store", "inouts_comment_view", store_inouts_comment_view());
                        break;

						case "cart_add":
                                $content .= gen_content("store", "cart_add", store_cart_add());
                        break;

						case "cart_out":
                                $content .= gen_content("store", "cart_out", store_cart_out());
                        break;

						case "cart_del":
                                $content .= gen_content("store", "cart_del", store_cart_del());
                        break;

						case "archive_view":
                                $content .= gen_content("store", "archive_view", store_archive_view());
                        break;

						case "archive_view_by_date":
                                $content .= gen_content("store", "archive_view_by_date", store_archive_view_by_date());
                        break;


                }
        }

        else
        {
                debug ("*** action: default");
                $content = gen_content("store", "frontpage", store_frontpage());
        }

        debug("=== end: mod: store ===<br>");
        return $content;

}

?>
