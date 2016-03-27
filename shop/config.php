<?php

$config['shop']['lastitems'] = "4";
$config['shop']['goods_on_page'] = "20";
$config['shop']['categories_goods_sort_by'] = "name";
$config['shop']['categories_goods_sort_order'] = "ASC";
$config['shop']['show_last_goods_link'] = "yes";
$config['shop']['new_goods_sort_by'] = "name";
$config['shop']['popular_goods_sort_order'] = "name";
$config['shop']['recommended_goods_sort_by'] = "id";
$config['shop']['recommended_goods_sort_order'] = "DESC";
$config['shop']['show_multiple_add_form'] = "yes";
$config['shop']['upl_dir'] = "shop/"; // must be writable to web server
$config['shop']['admin_actions'] = array(
	'admin',
	'install_tables',
	'drop_tables',
	'update_tables',
	'categories_add',
	'categories_edit',
	'categories_del',
	'authors_add',
	'authors_edit',
	'authors_del',
	'goods_view_hidden',
	'goods_add',
	'goods_edit',
	'goods_del',
	'user_del'
);

?>
