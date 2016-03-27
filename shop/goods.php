<?php

// Goods function of the "shop" module


function shop_goods_view_all()
{
	debug ("*** shop_goods_view_all ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'all_goods' => '',
		'show_admin_link' => '',
		'show_add_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	$content['all_goods'] = shop_goods_list();

	foreach ($content['all_goods'] as $k => $v)
	{
		if (1 == $user['id'])
		{
			$content['all_goods'][$k]['show_edit_link'] = "yes";
			$content['all_goods'][$k]['show_del_link'] = "yes";
		}
	}

	debug ("*** end:shop_goods_view_all ***");
	return $content;
}

function shop_goods_add()
{
	debug ("*** shop_goods_add ***");
	global $user;
	global $config;

	$content = array(
		'result' => '',
		'content' => '',
		'authors' => array(),
		'categories' => ''
	);

	$upls = array(
		'image',
		'images',
		'pdf',
		'epub',
		'mp3'
	);

	$fl = new File();

	foreach ($upls as $upl)
	{
		$uploaded = $fl -> upload($upl);
		if ("" != $uploaded)
			$_POST[$upl] = $uploaded;
	}

	if (isset($_POST['image_url']))
	{
		if ("" != $_POST['image_url'])
		{
			$_POST['image'] = $fl -> download($_POST['image_url']);
			debug("using downloaded image: ".$_POST['image']);
		}
		unset($_POST['image_url']);
	}

	$category = 0;
	if (isset($_POST['category']))
		$category = $_POST['category'];
	else if (isset($_GET['category']))
		$category = $_GET['category'];
	else if (isset($_GET['element']))
		$category = $_GET['element'];
	$content['category'] = $category;

	$cat = new Category();
	$content['categories_select'] = $cat -> get_select("ksh_shop_categories", $category);

	if (isset($_POST['do_add']))
	{
        unset ($_POST['do_add']);

		if (isset($_POST['new_link_title']) && ("" != $_POST['new_link_title']) && ("" != $_POST['new_link_url']))
			$_POST['links'] .= $_POST['new_link_title'] . "|" . $_POST['new_link_img']. "|" . $_POST['new_link_url'];
		unset($_POST['new_link_title']);
		unset($_POST['new_link_img']);
		unset($_POST['new_link_url']);


		if (!isset($_POST['name']) || ("" == $_POST['name']))
		{
			$dbo = new DataObject();
			$_POST['name'] = $dbo -> generate_unique_name("ksh_shop_goods", $_POST['title']);
		}

		$fields = "";
		$values = "";
       	foreach ($_POST as $k => $v)
       	{
            $fields .= "`".mysql_real_escape_string($k)."`, ";
           	$values .= "'".mysql_real_escape_string($v)."', ";
       	}
		$fields = rtrim($fields, ", ");
		$values = rtrim($values, ", ");

       	$sql_query = "INSERT INTO `ksh_shop_goods` (".$fields.") VALUES (".$values.")";

       	exec_query ($sql_query);

		$content['result'] = "Товар добавлен";

		if (in_array("rss", $config['modules']['installed']) && "yes" == $config['rss']['use'])
		{
			include_once($config['modules']['location']."/rss/index.php");
			rss_add($_POST['name'], $config['base']['site_url']."/shop/view_good/good:".mysql_insert_id(), $_POST['description'], date("Y-m-d"));
		}
	}

	$i = 0;
	$authors = shop_authors_list();
	foreach ($authors as $k => $v)
	{
		$content['authors_select'][$i]['id'] = $v['id'];
		$content['authors_select'][$i]['name'] = $v['name'];
		if (isset($_POST['author']) && ($v['id'] == $_POST['author']))
			$content['authors_select'][$i]['selected'] = "yes";
		$i++;
	}

	debug ("*** end:shop_goods_add ***");

	return $content;
}

function shop_goods_edit()
{
	debug ("*** shop_goods_edit ***");

	global $user;
	global $config;

	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => '',
		'image' => '',
		'images' => '',
		'authors' => '',
		'categories' => '',
		'genre' => '',
		'original_name' => '',
		'format' => '',
		'language' => '',
		'year' => '',
		'publisher' => '',
		'pages_qty' => '',
		'weight' => '',
		'new_qty' => '',
		'new_price' => '',
		'used_qty' => '',
		'used_price' => '',
		'description' => '',
		'description_short' => '',
		'if_new' => '',
		'if_popular' => '',
		'if_recommended' => '',
		'if_hide' => '',
		'pdf' => '',
		'epub' => '',
		'mp3' => '',
		'embed' => '',
		'tags' => ''
	);


	if (isset($_POST['images_del']))
	{
		$_POST['images'] = "";
		unset($_POST['images_del']);
	}
	if (isset($_POST['pdf_del']))
	{
		$_POST['pdf'] = "";
		unset($_POST['pdf_del']);
	}
	if (isset($_POST['epub_del']))
	{
		$_POST['epub'] = "";
		unset($_POST['epub_del']);
	}
	if (isset($_POST['mp3_del']))
	{
		$_POST['mp3'] = "";
		unset($_POST['mp3_del']);
	}

	$upls = array(
		'image',
		'images',
		'pdf',
		'epub',
		'mp3'
	);

	$fl = new File();

	foreach ($upls as $upl)
	{
		$uploaded = $fl -> upload($upl);
		if ("" != $uploaded)
			$_POST[$upl] = $uploaded;
	}

	if (isset($_POST['image_url']))
	{
		if ("" != $_POST['image_url'])
		{
			$_POST['image'] = $fl -> download($_POST['image_url']);
			unset($_POST['image_url']);
			debug("using downloaded image: ".$_POST['image']);
		}
		unset($_POST['image_url']);
	}

//	if (!isset($_POST['image']) || ("" == $_POST['image']))
//		$_POST['image'] = $_POST['

	if (isset($_POST['id']))
		$id = mysql_real_escape_string($_POST['id']);
	else if (isset($_GET['element']))
		$id = mysql_real_escape_string($_GET['element']);

	if (isset($_POST['do_update']))
	{
       	unset ($_POST['do_update']);
       	unset ($_POST['id']);

		/* Links processing */

		$links = "";
		if (isset($_POST['links']))
			foreach($_POST['links'] as $k => $v)
			{
				if (("" != $_POST['link_title_'.$v]) && ("" != $_POST['link_url_'.$v]))
					$links .= $_POST['link_title_'.$v] . "|" . $_POST['link_img_'.$v] .  "|" . $_POST['link_url_'.$v] . "|";
				unset($_POST['link_title_'.$v]);
				unset($_POST['link_img_'.$v]);
				unset($_POST['link_url_'.$v]);
			}

		debug ("links: ".$links);

		if (isset($_POST['new_link_title']) && ("" != $_POST['new_link_title']) && ("" != $_POST['new_link_url']))
			$links .= $_POST['new_link_title'] . "|" . $_POST['new_link_img'] . "|" . $_POST['new_link_url'] . "|";
		debug ("links: ".$links);

		$_POST['links'] = substr($links, 0, -1);
		debug ("POST links: ".$_POST['links']);
		unset($_POST['new_link_title']);
		unset($_POST['new_link_img']);
		unset($_POST['new_link_url']);

		/* End: Links processing */

       	$sql_query = "UPDATE `ksh_shop_goods` SET ";
       	foreach ($_POST as $k => $v)
			$sql_query .= "`".mysql_real_escape_string($k)."` = '".mysql_real_escape_string($v)."', ";
       	$sql_query = rtrim($sql_query, ", ")." WHERE `id` = '".$id."'";

       	exec_query ($sql_query);
		$content['result'] = "Изменения записаны";
	}

	$result = exec_query("SELECT * FROM `ksh_shop_goods` WHERE `id` = '".mysql_real_escape_string($id)."'");
	$good = mysql_fetch_array($result);
	mysql_free_result($result);

	foreach($good as $k => $v)
		$content[$k] = stripslashes($v);

	$cat = new Category();
	$content['categories_select'] = $cat -> get_select("ksh_shop_categories", $content['category']);

	$content['links_edit'] = shop_goods_links_extract(stripslashes($good['links']));
	debug("links_edit:", 2);
	dump($content['links_edit']);

	if ("1" == stripslashes($good['if_new']))
		$content['if_new'] = "yes";

	if ("1" == stripslashes($good['if_popular']))
		$content['if_popular'] = "yes";

	if ("1" == stripslashes($good['if_hide']))
		$content['if_hide'] = "yes";

	if ("1" == stripslashes($good['if_recommended']))
		$content['if_recommended'] = "yes";

	$authors = shop_authors_list();
	foreach ($authors as $k => $v)
	{
		$content['authors'] .= "<option value=\"".$v['id']."\"";
		if ($good['author'] == $v['id']) $content['authors'] .= " selected";
		$content['authors'] .= ">".$v['name']."</option>";
	}

	debug ("*** end:shop_goods_edit ***");

	return $content;
}

function shop_goods_del()
{
	debug ("*** shop_goods_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => ''
	);

	if (isset($_GET['goods']))
		$id = $_GET['goods'];
	else if (isset($_GET['element']))
		$id = $_GET['element'];
	else
		$id = 0;


	$sql_query = "SELECT `title`, `category` from `ksh_shop_goods` where `id` = '".mysql_real_escape_string($id)."'";
	$result = exec_query($sql_query);
	if ($result && mysql_num_rows($result))
	{
		$row = mysql_fetch_array($result);
		$content['id'] = $id;
		$content['title'] = stripslashes($row['title']);
		$content['category'] = stripslashes($row['category']);
		mysql_free_result ($result);
	}

	debug ("*** end:shop_goods_del ***");
	return $content;
}

function shop_goods_list()
{
	debug ("*** shop_goods_list ***");
	$goods = array();
    $i = 0;
    $result = exec_query ("select id,name,author,category from ksh_shop_goods order by id");
    while ($good = mysql_fetch_array($result))
    {
        $goods[$i]['id'] = stripslashes($good['id']);
        $goods[$i]['name'] = stripslashes($good['name']);
        $goods[$i]['author'] = stripslashes($good['author']);
        $goods[$i]['category'] = stripslashes($good['category']);
        $goods[$i]['category_name'] = mysql_result(exec_query("SELECT `name` FROM `ksh_shop_categories` WHERE `id` = '".stripslashes($good['category'])."'"), 0, 0);
		$i++;
    }
    mysql_free_result($result);
	debug ("*** end: shop_goods_list ***");
    return $goods;
}

function shop_view_by_categories()
{
	debug ("*** shop_view_by_categories ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'category_name' => '',
		'category_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'pages' => '',
		'goods_by_category' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (isset($_GET['categories']))
		$id = $_GET['categories'];
	else if (isset($_POST['categories']))
		$id = $_POST['categories'];
	else if (isset($_GET['element']))
		$id = $_GET['element'];
	else
		$id = 0;

	$cat = new Category();

	$content['category_id'] = $id;

	$sql_query = "SELECT * FROM `ksh_shop_categories` WHERE `id` = '".mysql_real_escape_string($id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['category_name'] = stripslashes($row['name']);

	$content['category_title'] = stripslashes($row['title']);
	$content['h1'] = stripslashes($row['h1']);
	if ("" == $content['h1'])
		$content['h1'] = $content['category_title'];

	$content['description'] = stripslashes($row['description']);
	$config['themes']['page_title']['element'] = $content['category_title'];
	$config['themes']['meta_keywords'] = stripslashes($row['meta_keywords']);
	$config['themes']['meta_description'] = stripslashes($row['meta_description']);


	$category_template = stripslashes($row['page_template']);

	if ("" != $category_template)
		$config['themes']['page_tpl'] = $category_template;

	$i = 0;
	$content['categories_top'] = array();
	$categories_top = $cat -> get_categories_level("ksh_shop_categories", 0);
	foreach($categories_top as $k => $v)
	{
		$content['categories_top'][$i] = $cat -> get_category("ksh_shop_categories", $v);

		if ($id == $content['categories_top'][$i]['id'])
			$content['categories_top'][$i]['active'] = "yes";

		if (in_array($content['categories_top'][$i]['id'], $cat -> get_parents_list("ksh_shop_categories", $id)))
			$content['categories_top'][$i]['parent_active'] = "yes";

		$i++;
	}

	/* Show subcategories */
	$i = 0;
	$sql_query = "SELECT * FROM `ksh_shop_categories` WHERE `parent` = '".mysql_real_escape_string($id)."' ORDER BY `position`, `id` ASC";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$subcat_id = stripslashes($row['id']);
		foreach($row as $k => $v)
			$content['subcategories'][$i][$k] = stripslashes($v);

		$last_subcat_goods = shop_view_last($subcat_id);
		$content['subcategories'][$i]['subcategories_last_goods'] = gen_content("shop", "list_subcategories_last_goods", $last_subcat_goods);

		$i++;
	}
	mysql_free_result($result);



	/* End: Show subcategories */


	$goods_on_page = $config['shop']['goods_on_page'];

	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		$start_page = $_GET['page'];
    else
		$start_page = 1; // Need to determine correct LIMIT
	
	debug("start page: ".$start_page);

	$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_goods WHERE category='".$id."' AND (`if_hide` IS NULL OR `if_hide` != '1')"), 0, 0);
    debug ("goods qty: ".$goods_qty);
    $pages_qty = ceil($goods_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	if (0 != $goods_qty)
	{
		if ("yes" == $config['shop']['show_multiple_add_form'])
			$content['show_multiple_add_form'] = "yes";
		else
			$content['show_multiple_add_form'] = "";
	}
    else
		$content['content'] = "";

	$sql_query = "SELECT * FROM ksh_shop_goods WHERE category='".mysql_real_escape_string($id)."' AND (`if_hide` IS NULL OR `if_hide` != '1')
		ORDER BY ".mysql_real_escape_string($config['shop']['categories_goods_sort_by'])."
		".mysql_real_escape_string($config['shop']['categories_goods_sort_order'])."
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page;
	$result = exec_query($sql_query);

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		foreach($good as $k => $v)
			$content['goods_by_category'][$i][$k] = stripslashes($v);


		$content['goods_by_category'][$i]['author_name'] = shop_authors_get_name($content['goods_by_category'][$i]['author']);

		if ("1" == $good['if_new'])
			$content['goods_by_category'][$i]['is_new'] = "yes";
		else
			$content['goods_by_category'][$i]['is_new'] = "";
		
		if ("1" == $good['if_popular'])
			$content['goods_by_category'][$i]['is_popular'] = "yes";
		else
			$content['goods_by_category'][$i]['is_popular'] = "";
			
        if ("" != $good['new_qty'] && 0 != $good['new_qty'])
		{
			$content['goods_by_category'][$i]['show_request_link'] = "";
			$content['goods_by_category'][$i]['presence'] = "";
		}
		else
		{
			$content['goods_by_category'][$i]['presence'] = "Нет в наличии";
			$content['goods_by_category'][$i]['show_request_link'] = "yes";
		}

		if (1 == $user['id'])
		{
			$content['goods_by_category'][$i]['show_edit_link'] = "yes";
			$content['goods_by_category'][$i]['show_del_link'] = "yes";
		}
		$i++;
    }

    mysql_free_result($result);


    // Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
            if ((!isset($_GET['page']) && ($i == 1)) || ($i == $_GET['page']))
                $content['pages'] .= " | ".$i;
            else
                $content['pages'] .= " | <a href=\"/shop/view_by_categories/".$id."/page:".$i."\">".$i."</a>";
        }
    }
	else
		$content['pages'] = "| 1 ";
    // End: Pages counting


	debug ("*** end:shop_view_by_categories ***");
	return $content;
}

function shop_view_by_authors($id = 0)
{
	debug ("*** shop_view_by_authors ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'author_name' => '',
		'author_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'pages' => '',
		'goods_by_author' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (!$id)
	{
		if (isset($_GET['authors']))
			$id = $_GET['authors'];
		else if (isset($_POST['authors']))
			$id = $_POST['authors'];
	}

	$content['author_id'] = $id;
	$sql_query = "SELECT * FROM `ksh_shop_authors` WHERE `id` = '".$id."'";
	$result = exec_query($sql_query);
	$row_author = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['author_name'] = stripslashes($row_author['name']);
	$content['author_image'] = stripslashes($row_author['image']);
	$content['author_descr'] = stripslashes($row_author['descr']);

	$goods_on_page = $config['shop']['goods_on_page'];

	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		$start_page = $_GET['page'];
    else
		$start_page = 1; // Need to determine correct LIMIT

	$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_goods WHERE author='".$id."'"), 0, 0);
    debug ("goods qty: ".$goods_qty);
    $pages_qty = ceil($goods_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	if (0 != $goods_qty)
		if ("yes" == $config['shop']['show_multiple_add_form'])
			$content['show_multiple_add_form'] = "yes";
		else
			$content['show_multiple_add_form'] = "";
    else
		$content['content'] = "Извините, товаров этого автора пока нет, следите за обновлениями.";

	$result = exec_query("SELECT * FROM ksh_shop_goods WHERE
		author='".mysql_real_escape_string($id)."'
		ORDER BY ".mysql_real_escape_string($config['shop']['categories_goods_sort_by'])."
		".mysql_real_escape_string($config['shop']['categories_goods_sort_order'])."
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page);

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		$content['goods_by_author'][$i]['category'] = stripslashes($good['category']);
		$content['goods_by_author'][$i]['category_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_categories WHERE id='".$good['category']."'"),0,0));
		$content['goods_by_author'][$i]['id'] = stripslashes($good['id']);
		$content['goods_by_author'][$i]['name'] = stripslashes($good['name']);
		$content['goods_by_author'][$i]['image'] = stripslashes($good['image']);
		$content['goods_by_author'][$i]['new_qty'] = stripslashes($good['new_qty']);
		$content['goods_by_author'][$i]['new_price'] = stripslashes($good['new_price']);
		
		if ("1" == $good['if_new'])
			$content['goods_by_author'][$i]['is_new'] = "yes";
		else
			$content['goods_by_author'][$i]['is_new'] = "";
		
		if ("1" == $good['if_popular'])
			$content['goods_by_author'][$i]['is_popular'] = "yes";
		else
			$content['goods_by_author'][$i]['is_popular'] = "";

        if ("" != $good['new_qty'] && 0 != $good['new_qty'])
		{
			$content['goods_by_author'][$i]['presence'] = "";
			$content['goods_by_author'][$i]['show_request_link'] = "";
		}
		else
		{
			$content['goods_by_author'][$i]['presence'] = "Нет в наличии";
			$content['goods_by_author'][$i]['show_request_link'] = "yes";
		}
		$i++;
    }

    mysql_free_result($result);


    // Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
            if ((!isset($_GET['page']) && ($i == 1)) || ($i == $_GET['page']))
                $content['pages'] .= " | ".$i;
            else
                $content['pages'] .= " | <a href=\"/index.php?module=shop&action=view_by_authors&authors=".$id."&page=".$i."\">".$i."</a>";
        }
    }
	else
		$content['pages'] = "| 1 ";
    // End: Pages counting

	$tpl = new Templater();
	$content['goods_by_author'] = $tpl->colonize($content['goods_by_author'], $config['shop']['lastitems']);

	debug ("*** end:shop_view_by_authors ***");
	return $content;
}

function shop_view_by_tag()
{
	debug ("*** shop_view_by_tag ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'show_admin_link' => '',
		'pages' => '',
		'goods_by_tag' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (isset($_GET['element']))
		$tag = $_GET['element'];
	else if (isset($_GET['tag']))
		$tag = $_GET['tag'];
	else
		$tag = "";
	
	$content['tag'] = $tag;

	$goods_on_page = $config['shop']['goods_on_page'];

	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		$start_page = $_GET['page'];
    else
		$start_page = 1; // Need to determine correct LIMIT

	$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_goods WHERE `tags` LIKE '%".mysql_real_escape_string($tag)."%'"), 0, 0);
    debug ("goods qty: ".$goods_qty);
    $pages_qty = ceil($goods_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	if (0 != $goods_qty)
		if ("yes" == $config['shop']['show_multiple_add_form'])
			$content['show_multiple_add_form'] = "yes";
		else
			$content['show_multiple_add_form'] = "";
    else
		$content['content'] = "Извините, товаров по этой метке пока нет, следите за обновлениями.";

	$result = exec_query("SELECT * FROM `ksh_shop_goods` WHERE `tags` LIKE '%".mysql_real_escape_string($tag)."%'
		ORDER BY ".mysql_real_escape_string($config['shop']['categories_goods_sort_by'])."
		".mysql_real_escape_string($config['shop']['categories_goods_sort_order'])."
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page
		);

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		$content['goods_by_tag'][$i]['category'] = stripslashes($good['category']);
		$content['goods_by_tag'][$i]['category_name'] = stripslashes(mysql_result(exec_query("SELECT `name` FROM `ksh_shop_categories` WHERE id='".$good['category']."'"),0,0));
		$content['goods_by_tag'][$i]['id'] = stripslashes($good['id']);
		$content['goods_by_tag'][$i]['name'] = stripslashes($good['name']);
		$content['goods_by_tag'][$i]['image'] = stripslashes($good['image']);
		$content['goods_by_tag'][$i]['new_qty'] = stripslashes($good['new_qty']);
		$content['goods_by_tag'][$i]['new_price'] = stripslashes($good['new_price']);
		
		if ("1" == $good['if_new'])
			$content['goods_by_tag'][$i]['is_new'] = "yes";
		else
			$content['goods_by_tag'][$i]['is_new'] = "";
		
		if ("1" == $good['if_popular'])
			$content['goods_by_tag'][$i]['is_popular'] = "yes";
		else
			$content['goods_by_tag'][$i]['is_popular'] = "";

        if ("" != $good['new_qty'] && 0 != $good['new_qty'])
		{
			$content['goods_by_tag'][$i]['presence'] = "";
			$content['goods_by_tag'][$i]['show_request_link'] = "";
		}
		else
		{
			$content['goods_by_tag'][$i]['presence'] = "Нет в наличии";
			$content['goods_by_tag'][$i]['show_request_link'] = "yes";
		}
		$i++;
    }

    mysql_free_result($result);


    // Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
            if ((!isset($_GET['page']) && ($i == 1)) || ($i == $_GET['page']))
                $content['pages'] .= " | ".$i;
            else
                $content['pages'] .= " | <a href=\"/shop/view_by_tag/".$tag."/page:".$i."\">".$i."</a>";
        }
    }
	else
		$content['pages'] = "| 1 ";
    // End: Pages counting


	debug ("*** end:shop_view_by_tag ***");
	return $content;
}


function shop_view_popular()
{
	debug ("*** shop_view_popular ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'category_name' => '',
		'category_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'pages' => '',
		'goods_by_category' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");


	$goods_on_page = $config['shop']['goods_on_page'];

	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		$start_page = $_GET['page'];
    else
		$start_page = 1; // Need to determine correct LIMIT

	$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_goods WHERE if_popular='1'"), 0, 0);
    debug ("goods qty: ".$goods_qty);
    $pages_qty = ceil($goods_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	if (0 != $goods_qty)
		if ("yes" == $config['shop']['show_multiple_add_form'])
			$content['show_multiple_add_form'] = "yes";
		else
			$content['show_multiple_add_form'] = "";
    else
		$content['content'] = "Извините, в этой категории товаров пока нет, следите за обновлениями.";

	$result = exec_query("SELECT * FROM ksh_shop_goods WHERE if_popular='1' ORDER BY ".mysql_real_escape_string($config['shop']['popular_goods_sort_by'])." ".mysql_real_escape_string($config['shop']['popular_goods_sort_order'])." LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page);

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		$content['goods_popular'][$i]['author'] = stripslashes($good['author']);
		$content['goods_popular'][$i]['author_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_authors WHERE id='".$good['author']."'"),0,0));
		$content['goods_popular'][$i]['category'] = stripslashes($good['category']);
		$content['goods_popular'][$i]['category_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_categories WHERE id='".$good['category']."'"),0,0));
		$content['goods_popular'][$i]['id'] = stripslashes($good['id']);
		$content['goods_popular'][$i]['name'] = stripslashes($good['name']);
		$content['goods_popular'][$i]['image'] = stripslashes($good['image']);
		$content['goods_popular'][$i]['new_qty'] = stripslashes($good['new_qty']);
		$content['goods_popular'][$i]['new_price'] = stripslashes($good['new_price']);

		if ("1" == $good['if_new'])
			$content['goods_popular'][$i]['is_new'] = "yes";
		else
			$content['goods_popular'][$i]['is_new'] = "";
		
		if ("1" == $good['if_popular'])
			$content['goods_popular'][$i]['is_popular'] = "yes";
		else
			$content['goods_popular'][$i]['is_popular'] = "";
			
        if ("" != $good['new_qty'] && 0 != $good['new_qty'])
		{
			$content['goods_popular'][$i]['show_request_link'] = "";
			$content['goods_popular'][$i]['presence'] = "";
		}
		else
		{
			$content['goods_popular'][$i]['presence'] = "Нет в наличии";
			$content['goods_popular'][$i]['show_request_link'] = "yes";
		}
		$i++;
    }

    mysql_free_result($result);

	$tpl = new Templater();
	$content['goods_popular'] = $tpl->colonize($content['goods_popular'], $config['shop']['lastitems']);

    // Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
            if ((!isset($_GET['page']) && ($i == 1)) || ($i == $_GET['page']))
                $content['pages'] .= " | ".$i;
            else
                $content['pages'] .= " | <a href=\"/index.php?module=shop&action=view_popular&page=".$i."\">".$i."</a>";
        }
    }
	else
		$content['pages'] = "| 1 ";
    // End: Pages counting


	debug ("*** end:shop_view_popular ***");
	return $content;
}

function shop_view_new()
{
	debug ("*** shop_view_new ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'category_name' => '',
		'category_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'pages' => '',
		'goods_by_category' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");


	$goods_on_page = $config['shop']['goods_on_page'];

	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		$start_page = $_GET['page'];
    else
		$start_page = 1; // Need to determine correct LIMIT

	$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_goods WHERE if_new='1'"), 0, 0);
    debug ("goods qty: ".$goods_qty);
    $pages_qty = ceil($goods_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	if (0 != $goods_qty)
		if ("yes" == $config['shop']['show_multiple_add_form'])
			$content['show_multiple_add_form'] = "yes";
		else
			$content['show_multiple_add_form'] = "";
    else
		$content['content'] = "Извините, в этой категории товаров пока нет, следите за обновлениями.";

	$result = exec_query("SELECT * FROM ksh_shop_goods WHERE if_new='1' ORDER BY ".mysql_real_escape_string($config['shop']['new_goods_sort_by'])." ".mysql_real_escape_string($config['shop']['new_goods_sort_order'])." LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page);

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		$content['goods_new'][$i]['author'] = stripslashes($good['author']);
		$content['goods_new'][$i]['author_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_authors WHERE id='".$good['author']."'"),0,0));
		$content['goods_new'][$i]['category'] = stripslashes($good['category']);
		$content['goods_new'][$i]['category_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_categories WHERE id='".$good['category']."'"),0,0));
		$content['goods_new'][$i]['id'] = stripslashes($good['id']);
		$content['goods_new'][$i]['name'] = stripslashes($good['name']);
		$content['goods_new'][$i]['image'] = stripslashes($good['image']);
		$content['goods_new'][$i]['new_qty'] = stripslashes($good['new_qty']);
		$content['goods_new'][$i]['new_price'] = stripslashes($good['new_price']);

		if ("1" == $good['if_new'])
			$content['goods_new'][$i]['is_new'] = "yes";
		else
			$content['goods_new'][$i]['is_new'] = "";
		
		if ("1" == $good['if_new'])
			$content['goods_new'][$i]['is_new'] = "yes";
		else
			$content['goods_new'][$i]['is_new'] = "";
			
        if ("" != $good['new_qty'] && 0 != $good['new_qty'])
		{
			$content['goods_new'][$i]['show_request_link'] = "";
			$content['goods_new'][$i]['presence'] = "";
		}
		else
		{
			$content['goods_new'][$i]['presence'] = "Нет в наличии";
			$content['goods_new'][$i]['show_request_link'] = "yes";
		}
		$i++;
    }

    mysql_free_result($result);

	$tpl = new Templater();
	$content['goods_new'] = $tpl->colonize($content['goods_new'], $config['shop']['lastitems']);

    // Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
            if ((!isset($_GET['page']) && ($i == 1)) || ($i == $_GET['page']))
                $content['pages'] .= " | ".$i;
            else
                $content['pages'] .= " | <a href=\"/index.php?module=shop&action=view_new&page=".$i."\">".$i."</a>";
        }
    }
	else
		$content['pages'] = "| 1 ";
    // End: Pages counting


	debug ("*** end:shop_view_new ***");
	return $content;
}

function shop_view_recommended()
{
	debug ("*** shop_view_recommended ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'category_name' => '',
		'category_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'pages' => '',
		'goods_by_category' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");


	$goods_on_page = $config['shop']['goods_on_page'];

	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
		$start_page = $_GET['page'];
    else
		$start_page = 1; // Need to determine correct LIMIT

	$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_shop_goods WHERE `if_recommended` = '1'"), 0, 0);
    debug ("goods qty: ".$goods_qty);
    $pages_qty = ceil($goods_qty / $goods_on_page);
    debug ("pages qty: ".$pages_qty);

	if (0 != $goods_qty)
		if ("yes" == $config['shop']['show_multiple_add_form'])
			$content['show_multiple_add_form'] = "yes";
		else
			$content['show_multiple_add_form'] = "";
    else
		$content['content'] = "Извините, в этой категории товаров пока нет, следите за обновлениями.";

	$result = exec_query("SELECT * FROM ksh_shop_goods
		WHERE `if_recommended` = '1'
		ORDER BY `".mysql_real_escape_string($config['shop']['recommended_goods_sort_by'])."`
		".mysql_real_escape_string($config['shop']['recommended_goods_sort_order'])."
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page);

	$i = 0;
    while ($good = mysql_fetch_array($result))
    {
		$content['goods_recommended'][$i]['author'] = stripslashes($good['author']);
		$content['goods_recommended'][$i]['author_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_authors WHERE id='".$good['author']."'"),0,0));
		$content['goods_recommended'][$i]['category'] = stripslashes($good['category']);
		$content['goods_recommended'][$i]['category_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_categories WHERE id='".$good['category']."'"),0,0));
		$content['goods_recommended'][$i]['id'] = stripslashes($good['id']);
		$content['goods_recommended'][$i]['name'] = stripslashes($good['name']);
		$content['goods_recommended'][$i]['image'] = stripslashes($good['image']);
		$content['goods_recommended'][$i]['new_qty'] = stripslashes($good['new_qty']);
		$content['goods_recommended'][$i]['new_price'] = stripslashes($good['new_price']);

		if ("1" == $good['if_new'])
			$content['goods_recommended'][$i]['is_new'] = "yes";
		else
			$content['goods_recommended'][$i]['is_new'] = "";
		
		if ("1" == $good['if_popular'])
			$content['goods_recommended'][$i]['is_popular'] = "yes";
		else
			$content['goods_recommended'][$i]['is_popular'] = "";
			
        if ("" != $good['new_qty'] && 0 != $good['new_qty'])
		{
			$content['goods_recommended'][$i]['show_request_link'] = "";
			$content['goods_recommended'][$i]['presence'] = "";
		}
		else
		{
			$content['goods_recommended'][$i]['presence'] = "Нет в наличии";
			$content['goods_recommended'][$i]['show_request_link'] = "yes";
		}
		$i++;
    }

    mysql_free_result($result);

	$tpl = new Templater();
	$content['goods_recommended'] = $tpl->colonize($content['goods_recommended'], $config['shop']['lastitems']);

    // Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
            if ((!isset($_GET['page']) && ($i == 1)) || ($i == $_GET['page']))
                $content['pages'] .= " | ".$i;
            else
                $content['pages'] .= " | <a href=\"/index.php?module=shop&action=view_new&page=".$i."\">".$i."</a>";
        }
    }
	else
		$content['pages'] = "| 1 ";
    // End: Pages counting


	debug ("*** end:shop_view_recommeded ***");
	return $content;
}


function shop_view_good()
{
	debug ("*** shop_view_good ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'name' => '',
		'image' => '',
		'publisher' => '',
		'year' => '',
		'genre' => '',
		'original_name' => '',
		'format' => '',
		'pages_qty' => '',
		'description' => '',
		'images' => '',
		'show_order_form' => '',
		'show_query_form' => '',
		'new_price' => '',
		'qty_select' => '',
		'show_admin_link' => '',
		'pdf' => '',
		'epub' => '',
		'mp3' => '',
		'embed' => '',
		'tags' => ''
	);

	global $home; // to determine files' size

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (isset($_GET['good']))
		$id = $_GET['good'];
	else if (isset($_GET['element']))
		$id = $_GET['element'];
	else
		$id = 0;

	$config['modules']['current_id'] = $id;

	$result = exec_query("SELECT * FROM `ksh_shop_goods` WHERE id='".mysql_real_escape_string($id)."'");
	$good = mysql_fetch_array($result);
    mysql_free_result($result);

	foreach($good as $k => $v)
		$content[$k] = stripslashes($v);

	$content['category_name'] = mysql_result(exec_query("SELECT name FROM ksh_shop_categories WHERE id='".$good['category']."'"),0,0);
    $content['author_name'] = shop_authors_get_name($content['author']);
	$config['modules']['current_category'] = $content['category'];

	$content['links'] = shop_goods_links_extract(stripslashes($good['links']));

	$tags_list = stripslashes($good['tags']);
	if ("" != $tags_list && " " != $tags_list)
	{
		$tags = explode(",", $tags_list);
		foreach($tags as $k => $v)
		{
			if ("" != $v && " " != $v)
			{
				$content['tags'][$k]['tag'] = trim($v);
				$content['tags'][$k]['url'] = urlencode($content['tags'][$k]['tag']);
				$content['tags'][$k]['not_last'] = "yes";
			}
		}
		$content['tags'][$k]['not_last'] = "";
	}

	if ("" != $content['pdf'])
		$content['pdf_size'] = format_bytes(filesize($home.$content['pdf']));

	if ("" != $content['epub'])
		$content['epub_size'] = format_bytes(filesize($home.$content['epub']));


	if ("" != $content['mp3'])
		$content['mp3_size'] = format_bytes(filesize($home.$content['mp3']));

	$image_path = $config['base']['doc_root'].$content['images'];
	if ("" != $content['images'] && file_exists($image_path))
		list($content['images_width'],$content['images_height']) = getimagesize($image_path);
	else
	{
		$content['images_width'] = "";
		$content['images_height'] = "";
	}

	for ($i = 1; $i <= $good['new_qty']; $i++)
	{
		$content['qty_select'] .= "<option value=\"".$i."\"";
		if (1 == $i) $content['qty_select'] .= " selected";
		$content['qty_select'] .= ">".$i."</option>";
	}


	if (("0" == $good['new_qty']) || ("" == $good['new_qty']))
		$content['show_query_form'] = "yes";
	else
		$content['show_order_form'] = "yes";

	$sql_query = "SELECT `image` FROM `ksh_shop_authors` WHERE `id` = '".mysql_real_escape_string($content['author'])."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$content['author_image'] = stripslashes($row['image']);

	$content['goods_by_author_row'] = array();
	$i = 0;
	$sql_query = "SELECT * FROM `ksh_shop_goods` WHERE `author` = '".mysql_real_escape_string($content['author'])."' ORDER BY `id` DESC";
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		$content['goods_by_author_row'][$i]['id'] = stripslashes($row['id']);
		$content['goods_by_author_row'][$i]['image'] = stripslashes($row['image']);
		$content['goods_by_author_row'][$i]['name'] = stripslashes($row['name']);
		$i++;
	}
	mysql_free_result($result);

	$tpl = new Templater();
	$content['goods_by_author_row'] = $tpl->colonize($content['goods_by_author_row'], $config['shop']['lastitems']);
	debug("goods_by_author_row", 3);
	dump($content['goods_by_author_row']);

	$i = 0;
	$content['categories_top'] = array();
	$cat = new Category();
	$categories_top = $cat -> get_categories_level("ksh_shop_categories", 0);
	foreach($categories_top as $k => $v)
	{
		$content['categories_top'][$i] = $cat -> get_category("ksh_shop_categories", $v);

		if ($id == $content['categories_top'][$i]['id'])
			$content['categories_top'][$i]['active'] = "yes";

		if (in_array($content['categories_top'][$i]['id'], $cat -> get_parents_list("ksh_shop_categories", $id)))
			$content['categories_top'][$i]['parent_active'] = "yes";

		$i++;
	}

	if ("" == $content['h1'])
		$content['h1'] = $content['title'];

	$config['themes']['meta_keywords'] = $content['meta_keywords'];
	$config['themes']['meta_description'] = $content['meta_description'];
	$config['themes']['page_title']['element'] = $content['title'];

	debug ("*** end:shop_view_good ***");
	return $content;
}

/* Old functions */

function shop_view_last($category = 0, $mode = "category")
{
	global $config;
	global $user;

	debug ("*** shop_view_last ***");

	$content = array(
		'result' => '',
		'content' => '',
		'category_name' => '',
		'category_id' => '',
		'show_multiple_add_form' => '',
		'show_admin_link' => '',
		'show_add_link' => '',
		'goods_by_category' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if ($category)
		$id = $category;
	else if (isset($_GET['categories']))
		$id = $_GET['categories'];
	else
		$id = 0;

	if ($id)
	{
		if ("category" == $mode)
		{
			$content['category_id'] = $id;
			$content['category_name'] = mysql_result(exec_query("SELECT `name` FROM `ksh_shop_categories` WHERE `id` = '".mysql_real_escape_string($id)."'"),0,0);
		}
		else  if ("author" == $mode)
		{
			$content['category_id'] = $id;
			$content['category_name'] = mysql_result(exec_query("SELECT `name` FROM `ksh_shop_authors` WHERE `id` = '".mysql_real_escape_string($id)."'"),0,0);
		}

		$last_goods_qty = $config['shop']['lastitems'];

		$goods_qty = mysql_result(exec_query("SELECT COUNT(*) FROM `ksh_shop_goods` WHERE `".mysql_real_escape_string($mode)."` = '".mysql_real_escape_string($id)."'"), 0, 0);
	    debug ("goods qty: ".$goods_qty);

		if (0 != $goods_qty)
		{
			if ("yes" == $config['shop']['show_multiple_add_form'])
				$content['show_multiple_add_form'] = "yes";
			else
				$content['show_multiple_add_form'] = "";
		}
	    else
			$content['content'] = "Извините, в этой категории товаров пока нет, следите за обновлениями.";

		$result = exec_query("SELECT * FROM `ksh_shop_goods` WHERE `".mysql_real_escape_string($mode)."` = '".mysql_real_escape_string($id)."' ORDER BY `id` DESC LIMIT ".mysql_real_escape_string($last_goods_qty));

		$i = 0;
    	while ($good = mysql_fetch_array($result))
    	{
			$content['goods_by_category'][$i]['author'] = stripslashes($good['author']);
			$content['goods_by_category'][$i]['author_name'] = shop_authors_get_name($content['goods_by_category'][$i]['author']);
			$content['goods_by_category'][$i]['id'] = stripslashes($good['id']);
			$content['goods_by_category'][$i]['name'] = stripslashes($good['name']);
			$content['goods_by_category'][$i]['image'] = stripslashes($good['image']);
			$content['goods_by_category'][$i]['new_qty'] = stripslashes($good['new_qty']);
			$content['goods_by_category'][$i]['new_price'] = stripslashes($good['new_price']);

        	if ("" != $good['new_qty'] && 0 != $good['new_qty'])
				$content['goods_by_category'][$i]['presence'] = "";
			else
				$content['goods_by_category'][$i]['presence'] = "Нет в наличии";
			$i++;
    	}

    	mysql_free_result($result);
	}
	else
	{
		$content['content'] .= "Не указана категория";
	}


    debug ("*** end: shop_view_last");
    return $content;
}

function shop_goods_view_hidden()
{
	debug ("*** shop_goods_view_hidden ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'all_goods' => '',
		'show_admin_link' => '',
		'show_add_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";

	    $sql_query =  "SELECT `id`,`name`,`author`,`category` FROM `ksh_shop_goods` WHERE `if_hide` = '1' ORDER BY `id`";
		$result = exec_query($sql_query);
		$i = 0;
	    while ($good = mysql_fetch_array($result))
	    {
	        $content['all_goods'][$i]['id'] = stripslashes($good['id']);
	        $content['all_goods'][$i]['name'] = stripslashes($good['name']);
	        $content['all_goods'][$i]['author'] = stripslashes($good['author']);
	        $content['all_goods'][$i]['category'] = stripslashes($good['category']);
	        $content['all_goods'][$i]['category_name'] = mysql_result(exec_query("SELECT `name` FROM `ksh_shop_categories` WHERE `id` = '".stripslashes($good['category'])."'"), 0, 0);
	        $i++;
	    }
	    mysql_free_result($result);

		foreach ($content['all_goods'] as $k => $v)
		{
				$content['all_goods'][$k]['show_edit_link'] = "yes";
				$content['all_goods'][$k]['show_del_link'] = "yes";
		}
	}
	else
		debug ("user isn't admin");

	debug ("*** end:shop_goods_view_hidden ***");
	return $content;
}

function shop_goods_links_extract($links_string)
{
	global $user;
	global $config;
	debug("*** shop_goods_links_extract ***");

	$links = array();

	if ("" != $links_string)
	{

		$links_array = explode("|", $links_string);
		debug("links_array:", 2);
		dump($links_array);

		$i = 0;
		$j = 1;
		foreach($links_array as $k => $v)
		{
			debug("i: ".$i.", j: ".$j."; links array element ".$k.": ".$v);
			switch($j)
			{
				default: break;
				case "1":
					$links[$i]['title'] = $v;
				break;
				case "2":
					$links[$i]['img'] = $v;
				break;
				case "3":
					$links[$i]['url'] = $v;
					$links[$i]['id'] = $i;
					$i++;
					$j = 0;
				break;
			}
			$j++;
		}
	}
	debug("links:", 2);
	dump($links);

	debug("*** end: shop_goods_links_extract ***");
	return ($links);
}

?>
