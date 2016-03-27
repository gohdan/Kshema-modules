<?php

// News administration functions of the news module

include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function news($category)
{
    debug("*** news_news ***");
    global $user;
    $content = "";
    $news = 2;

    debug ("category name: ".$category);
    $category_id = mysql_result(exec_query("SELECT id FROM ksh_news_categories WHERE name='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category id: ".$category_id);
    $result = exec_query("SELECT * FROM ksh_news WHERE category='".mysql_real_escape_string($category_id)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($news)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show news ".$row['id']);
        $content .= "<tr><td>
        ";

        if ("" != $row['descr_image']) $content .= "<img src=\"".$row['descr_image']."\" style=\"clear: right; float: left; margin-right: 5px\">";

        $content .= "
                    <a href=\"/news/view/".$row['id'].".html\">".$row['date']."</a><br>
                    <a href=\"/news/view/".$row['id'].".html\">".$row['name']."</a><br>
        ";

        if ("" != $row['descr']) $content .= stripslashes($row['descr']);
        else $content .= substr(stripslashes($row['full_text'], 0, 200))."...";

        $content .= "<br>
                    <span class=\"more\"><a href=\"/news/view/".$row['id'].".html\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/news/admin/\">Администрирование</a></p>";

    return $content;
    debug("*** end: news_news ***");
}

function lastnews($category)
{
    debug("*** lastnews ***");
    global $user;
    $content = "";
    $news = 3;

    debug ("category name: ".$category);
    $result = exec_query("SELECT * FROM ksh_news ORDER BY id DESC LIMIT ".mysql_real_escape_string($news)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show news ".$row['id']);
        $content .= "<tr><td>
        ";

        if ("" != $row['descr_image']) $content .= "<img src=\"".$row['descr_image']."\" style=\"clear: right; float: left; margin-right: 5px\">";

        $content .= "
                    <a href=\"/news/view/".$row['id'].".html\">".$row['date']."</a><br>
                    <a href=\"/news/view/".$row['id'].".html\">".$row['name']."</a><br>
        ";

        if ("" != $row['descr']) $content .= stripslashes($row['descr']);
        else $content .= substr(stripslashes($row['full_text']), 0, 200)."...";

        $content .= "<br>
                    <span class=\"more\"><a href=\"/news/view/".$row['id'].".html\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/news/admin/\">Администрирование</a></p>";

    return $content;
    debug("*** end: lastnews ***");
}

function news_hook()
{
    debug("*** news_hook ***");
    global $user;
    global $config;
    $content = array(
		'hook' => '',
		'show_admin_link' => ''
	);
    $news = 3;

    $result = exec_query("SELECT * FROM ksh_hooks WHERE hook_module='news' AND to_module='".mysql_real_escape_string($config['modules']['current_module'])."' AND to_id='".mysql_real_escape_string($config['modules']['current_id'])."'");
	while ($hook = mysql_fetch_array($result))
	{
		if ("category" == stripslashes($hook['hook_type']))
		{
		    $category = stripslashes($hook['hook_id']);

	    	$categories = exec_query("SELECT * FROM ksh_news WHERE category='".mysql_real_escape_string($category)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($news)."");

			$i = 0;
	    	while ($row = mysql_fetch_array($categories))
	    	{
	        	debug("show news ".$row['id']);
				$content['hook'][$i]['id'] = stripslashes($row['id']);
				$content['hook'][$i]['name'] = stripslashes($row['name']);
				$content['hook'][$i]['date'] = stripslashes($row['date']);
				$content['hook'][$i]['page_template'] = $config['themes']['page_tpl'];
				$content['hook'][$i]['image'] = stripslashes($row['descr_image']);
				$content['hook'][$i]['descr'] = stripslashes($row['descr']);
				$content['hook'][$i]['short_descr'] = stripslashes($row['short_descr']);
				$i++;
	    	}
	    	mysql_free_result($categories);
		}
		else if ("news" == stripslashes($hook['hook_type']))
		{
		    $id = stripslashes($hook['hook_id']);

	    	$categories = exec_query("SELECT * FROM ksh_news WHERE id='".mysql_real_escape_string($id)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($news)."");

			$i = 0;
	    	while ($row = mysql_fetch_array($categories))
	    	{
	        	debug("show news ".$row['id']);
				$content['hook'][$i]['id'] = stripslashes($row['id']);
				$content['hook'][$i]['name'] = stripslashes($row['name']);
				$content['hook'][$i]['date'] = stripslashes($row['date']);
				$content['hook'][$i]['page_template'] = $config['themes']['page_tpl'];
				$content['hook'][$i]['image'] = stripslashes($row['descr_image']);
				$content['hook'][$i]['descr'] = stripslashes($row['descr']);
				$content['hook'][$i]['short_descr'] = stripslashes($row['short_descr']);
				$i++;
	    	}
	    	mysql_free_result($categories);
		}

	}
    mysql_free_result($result);

    if (1 == $user['id'])
		$content['show_admin_link'] = "yes";

    debug("*** end: news_hook ***");
    return $content;
}


function news_view_by_category()
{
    debug("*** news_view_by_category ***");
    global $user;
	global $page_title;
    global $config;
	
	$i = 0;

    $category = $_GET['category'];
	debug ("category: ".$category);

	$config['modules']['current_category'] = $category;
	$config['modules']['current_id'] = $category;
	
	$result = exec_query ("SELECT * FROM ksh_news_categories WHERE id='".mysql_real_escape_string($category)."'");
	$cat = mysql_fetch_array($result);

    $content['category'] = stripslashes($cat['title']);
	if ("" != $cat['template'])
		$config['news']['category_template'] = stripslashes($cat['template']);
	if ("" != $cat['list_template'])
		$config['news']['newslist_template'] = stripslashes($cat['list_template']);
	if ("" != $cat['page_template'])
		$config['themes']['page_tpl'] = stripslashes($cat['page_template']);
	if ("" != $cat['menu_template'])
		$config['themes']['menu_tpl'] = $cat['menu_template'];


	debug ("page template: ".$config['themes']['page_tpl']);
	debug ("category template: ".$config['news']['category_template']);
	debug ("news list template: ".$config['news']['newslist_template']);	
	debug ("menu template: ".$config['themes']['menu_tpl']);	

    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        $config['news']['newslist_template'] => '',
        'admin_link' => '',
        'edit_link' => '',
        'descr' => ''
    );

	
    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have news to delete");
            exec_query("DELETE FROM ksh_news WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Новость успешно удалена";
        }
        else
        {
            debug ("don't have news to delete");
        }

        $content['admin_link'] .= "<a href=\"/news/admin/\">Администрирование</a><br><a href=\"/news/view_categories/\">Просмотр всех категорий</a><br><a href=\"/news/import_rss/".$category."\">Импортировать новости из RSS</a><br><a href=\"/news/add_news/".$category."\">Добавить новость</a>";
		
    }

	// FIXME: Check if there are categories; else user has a warning
    debug ("category name: ".$content['category']);
    $result = exec_query("SELECT * FROM `ksh_news` WHERE `category`='".mysql_real_escape_string($category)."' ORDER BY `date` DESC, `id` DESC");

    while ($row = mysql_fetch_array($result))
    {
        debug("show news ".$row['id']);

		$content['titles'][$i]['id'] = stripslashes($row['id']);
		$content['titles'][$i]['title'] = stripslashes($row['name']);

		if ("" != $row['descr_image'])
			$content[$config['news']['newslist_template']][$i]['descr_image'] = $row['descr_image'];
		else
			$content[$config['news']['newslist_template']][$i]['descr_image'] = "";

		if (isset($_GET['news']))
		{
			if (stripslashes($row['id']) == $_GET['news'])
	        	$content[$config['news']['newslist_template']][$i]['first'] = "yes";
			else
	    	    $content[$config['news']['newslist_template']][$i]['not_first'] = "yes";
		}
		else
		{
			if (!$i)
		        $content[$config['news']['newslist_template']][$i]['first'] = "yes";
			else
	    	    $content[$config['news']['newslist_template']][$i]['not_first'] = "yes";
		}

		$dt = explode("-", $row['date']);
        $content[$config['news']['newslist_template']][$i]['date'] = $dt[2].".".$dt[1].".".$dt[0];
		$content[$config['news']['newslist_template']][$i]['full_text'] = stripslashes($row['full_text']);
		$descr = stripslashes($row['descr']);
		if ("" == $descr)
		{
			$sentences = explode(".", strip_tags($content[$config['news']['newslist_template']][$i]['full_text']));
			for ($j = 0; $j <3; $j++)
				if (isset($sentences[$j]))
					$descr .= $sentences[$j].". ";
			$descr = rtrim($descr, " ");
		}
		$content[$config['news']['newslist_template']][$i]['descr'] = $descr;

        $content[$config['news']['newslist_template']][$i]['id'] = $row['id'];
		$content[$config['news']['newslist_template']][$i]['name'] = stripslashes($row['name']);
		if ("" == $row['url'])
			$content[$config['news']['newslist_template']][$i]['url'] = "	/news/view/".$row['id'].".html";
		else
			$content[$config['news']['newslist_template']][$i]['url'] = stripslashes($row['url']);


        if (1 == $user['id'])
        {
            $content[$config['news']['newslist_template']][$i]['edit_link'] = "ID: ".$row['id'].". <a href=\"/news/edit/".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/news/del/".$row['id']."\">Удалить</a>";
        }
        else
        {
        	$content[$config['news']['newslist_template']][$i]['edit_link'] = "";
        }
        $i++;
    }
    mysql_free_result($result);

	$page_title .= " | ".$content['category'];

    return $content;
    debug("*** end: news_view_by_category ***");
}


function news_add()
{
    debug ("*** news_add ***");
    global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

    $content = array (
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'date' => ''
    );

    $content['date'] = date("Y-m-d");

    $i = 0;
    $result = exec_query("SELECT * FROM ksh_news_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = $category['id'];
        $content['categories_select'][$i]['name'] = $category['name'];
        $content['categories_select'][$i]['title'] = $category['title'];
        if (((isset($_GET['category'])) && ($category['id'] == $_GET['category'])) || ((isset($_POST['category'])) && ($category['id'] == $_POST['category'])))
			$content['categories_select'][$i]['selected'] = " selected";
        else
			$content['categories_select'][$i]['selected'] = "";
        $i++;
    }
    mysql_free_result($result);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

                if ((isset($image)) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."news/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."news/",$if_file_exists);
                    debug ("size: ".filesize($home.$file_path));

                    if (filesize($home.$file_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content .= "<p>Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт</p>";
                        if (unlink ($home.$file_path)) debug ("file deleted");
                        else debug ("can't delete file!");
                        $file_path = "";
                    }

                    $_POST['image'] = $file_path;

                }
                else
                {
                    debug ("no image to upload");
                    if (isset($_POST['image']))
                    	$file_path = $_POST['image'];
                    else
                    	$file_path = "";
                }


        if (isset($_POST['do_add']))
        {
            debug ("have data to add");
            if ("" != $_POST['name'])
            {
                debug ("news name isn't empty");
                exec_query("INSERT INTO ksh_news (name, category, short_descr, descr, descr_image, full_text, date) VALUES ('".mysql_real_escape_string($_POST['name'])."','".mysql_real_escape_string($_POST['category'])."','".mysql_real_escape_string($_POST['short_descr'])."','".mysql_real_escape_string($_POST['descr'])."','".mysql_real_escape_string($file_path)."','".mysql_real_escape_string($_POST['full_text'])."', '".mysql_real_escape_string($_POST['date'])."')");
                $content['result'] .= "Новость добавлена";
            }
            else
            {
                debug ("news name is empty");
                $content['result'] .= "Пожалуйста, задайте название новости";
            }
        }
        else
        {
            debug ("no data to add");
        }
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_add ***");
    return $content;
}

function news_edit()
{
    debug ("*** news_edit ***");
    global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
        'result' => '',
        'categories' => '',
        'id' => '',
        'name' => '',
        'date' => '',
		'short_descr' => '',
        'descr' => '',
        'full_text' => '',
        'image' => ''
    );

    if (isset($_FILES['image']))
    {
        debug ("have an image!");
        $image = $_FILES['image'];
    }
    else debug ("don't have an image!");
    $if_file_exists = 0;
    $file_path = "";

    if (isset($_GET['news'])) $news_id =$_GET['news'];
    else if (isset($_POST['id'])) $news_id =$_POST['id'];
    else $news_id =0;
    debug ("news id: ".$news_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");
        if (isset($_POST['do_update']))
        {

                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."news/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."news/",$if_file_exists);
                    debug ("size: ".filesize($home.$file_path));

                    if (filesize($home.$file_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content['content'] .= "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
                        if (unlink ($home.$file_path)) debug ("file deleted");
                        else debug ("can't delete file!");
                        $file_path = $_POST['old_image'];
                    }

                    $_POST['image'] = $file_path;

                }
                else
                {
                    debug ("no image to upload");
                    $file_path = $_POST['old_image'];
                }

        if (isset($_POST['image'])) debug ("POST image: ".$_POST['image']);
        debug ("file path: ".$file_path);

            debug ("have data to update");
            if ("" != $_POST['name'])
            {
                debug ("news name isn't empty");
				$sql_query = "UPDATE ksh_news set
					`name` = '".mysql_real_escape_string($_POST['name'])."',
					`date` = '".mysql_real_escape_string($_POST['date'])."',
					`category` = '".mysql_real_escape_string($_POST['category'])."',";
				if (isset($_POST['short_descr']))
					$sql_query .= "`short_descr` = '".mysql_real_escape_string($_POST['short_descr'])."',";

				$sql_query .= "`descr` = '".mysql_real_escape_string($_POST['descr'])."',
					`descr_image` = '".mysql_real_escape_string($file_path)."',
					`full_text` = '".mysql_real_escape_string($_POST['full_text'])."'
					WHERE `id` = '".mysql_real_escape_string($news_id)."'";
                exec_query($sql_query);
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("news name is empty");
                $content['result'] .= "Пожалуйста, задайте название новости";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_news WHERE id='".mysql_real_escape_string($news_id)."'");
            $news = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['name'] = htmlspecialchars(stripslashes($news['name']));
            $content['date'] = stripslashes($news['date']);
			$content['short_descr'] = stripslashes($news['short_descr']);
            $content['descr'] = stripslashes($news['descr']);
            $content['image'] = stripslashes($news['descr_image']);
            $content['full_text'] = htmlspecialchars(stripslashes($news['full_text']));
            $content['id'] = stripslashes($news['id']);

            $result = exec_query("SELECT * FROM ksh_news_categories");

            $i = 0;
            while ($category = mysql_fetch_array($result))
            {
		        debug ("show category ".$category['id']);
		        $content['categories_select'][$i]['id'] = $category['id'];
		        $content['categories_select'][$i]['name'] = $category['name'];
		        $content['categories_select'][$i]['title'] = $category['title'];
		        if ($category['id'] == $news['category'])
                	$content['categories_select'][$i]['selected'] = " selected";
		        else
                	$content['categories_select'][$i]['selected'] = "";
		        $i++;
            }
            mysql_free_result($result);

    }
    else
    {
        debug ("user isn't admin");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_edit ***");
    return $content;
}

function news_del()
{
    debug ("*** news_del ***");
    global $config;
    global $user;

    $content = array(
    	'content' => '',
        'id' => '',
        'name' => '',
        'category_id' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_news WHERE id='".mysql_real_escape_string($_GET['news'])."'");
        $news = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($news['id']);
        $content['name'] = stripslashes($news['name']);
        $content['category_id'] = stripslashes($news['category']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_del ***");
    return $content;
}

function news_view()
{
    debug ("*** news_view ***");
	global $user;
	global $config;

    $content = array(
    	'name' => '',
        'date' => '',
        'descr' => '',
        'full_text' => '',
        'category' => '',
        'category_id' => '',
		'show_previous_news_link' => '',
		'show_next_news_link' => '',
		'if_show_admin_link' => ''
    );

	if (1 == $user['id'])
	{
		$content['if_show_admin_link'] = "yes";
	}

    $result = exec_query("SELECT * FROM ksh_news WHERE id='".mysql_real_escape_string($_GET['news'])."'");
    $news = mysql_fetch_array($result);
    mysql_free_result($result);

    $content['news_qty'] = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_news WHERE category='".$news['category']."'"), 0, 0);

    $content['id'] = stripslashes($news['id']);
    $content['name'] = stripslashes($news['name']);
    $content['date'] = format_date(stripslashes($news['date']), "ru");
    $content['descr_image'] = stripslashes($news['descr_image']);
    $content['descr'] = stripslashes($news['descr']);
    $content['full_text'] = stripslashes($news['full_text']);
	if ("" == $content['full_text'])
		$content['full_text'] = $content['descr'];

    $content['category'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_news_categories WHERE id='".mysql_real_escape_string($news['category'])."'"), 0, 0));
    $content['category_id'] = stripslashes($news['category']);
	
	$previous_news_qty = stripslashes(mysql_result(exec_query("SELECT count(*) FROM ksh_news WHERE category='".$news['category']."' and id < '".$news['id']."'"), 0, 0));
	if ($previous_news_qty > 0)
	{
		$content['show_previous_news_link'] = "yes";
		$content['previous_news_id'] = stripslashes(mysql_result(exec_query("SELECT id FROM ksh_news WHERE category='".$news['category']."' and id < '".$news['id']."' ORDER BY id DESC LIMIT 1"), 0, 0));
		
	}
		
	$next_news_qty = stripslashes(mysql_result(exec_query("SELECT count(*) FROM ksh_news WHERE category='".$news['category']."' and id > '".$news['id']."'"), 0, 0));
	if ($next_news_qty > 0)
	{
		$content['show_next_news_link'] = "yes";
		$content['next_news_id'] = stripslashes(mysql_result(exec_query("SELECT id FROM ksh_news WHERE category='".$news['category']."' and id > '".$news['id']."' ORDER BY id ASC LIMIT 1"), 0, 0));
	}

    $config['modules']['current_id'] = $news['id'];
    $config['modules']['current_category'] = $news['category'];
    $config['modules']['current_title'] = $content['name'];
	
	

	$config['pages']['page_title'] = $content['name'];
	$config['themes']['page_title']['element'] = $content['name'];

	$result = exec_query("SELECT * FROM ksh_news_categories WHERE id='".$news['category']."'");
	$category = mysql_fetch_array($result);
	$news_template = stripslashes($category['news_template']);
	debug ("news template: ".$config['news']['news_template']);
	if ("" != $category['page_template'])
		$config['themes']['page_tpl'] = stripslashes($category['page_template']);
	debug ("page template: ".$config['themes']['page_tpl']);
	
	if ("" != $category['news_template'])
		$config['news']['news_template'] = stripslashes($category['news_template']);
	debug ("news view template: ".$config['news']['news_template']);

	if ("" != $category['menu_template'])
		$config['themes']['menu_tpl'] = stripslashes($category['menu_template']);
	debug ("news menu template: ".$config['themes']['menu_tpl']);
	
    debug ("*** end: news_view ***");
    return $content;
}

function news_archive()
{
    debug("*** news_archive ***");
    global $config;
    global $user;
	global $page_title;

	$page_title .= " | Архив новостей";
    $content = array(
    	'content' => '',
        'news' => '',
        'admin_link' => ''
    );

    $result = exec_query("SELECT * FROM ksh_news ORDER BY id DESC");

    $i = 0;
    while ($row = mysql_fetch_array($result))
    {
        debug("show news ".$row['id']);
        $content['news'][$i]['id'] = $row['id'];
        $content['news'][$i]['date'] = $row['date'];
        $content['news'][$i]['name'] = $row['name'];

        if ("" != $row['descr']) $content['news'][$i]['descr'] = stripslashes($row['descr']);
        else $content['news'][$i]['descr'] = substr($row['full_text'], 0, 100)."...";

        $content['news'][$i]['full_text'] = stripslashes($row['full_text']);

		if ("" == $row['url'])
			$content['news'][$i]['url'] = "	/news/view/".$row['id'].".html";
		else
			$content['news'][$i]['url'] = stripslashes($row['url']);

        $content['news'][$i]['more'] = "<span class=\"more\"><a href=\"".$content['news'][$i]['url']."\">Подробнее...</a></span>";
        $content['news'][$i]['edit_link'] = "";
        $content['news'][$i]['descr_image'] = "";
        $i++;
    }
    mysql_free_result($result);

    if (1 == $user['id']) $content['admin_link'] .= "<a href=\"/news/admin/\">Администрирование</a>";

    return $content;
    debug("*** end: news_archive ***");
}

function news_rss()
{
    debug("*** news_rss ***");
    global $user;
	global $config;

    $content = array(
		'rss_items' => array()
	);
    
	$news_qty = $config['news']['rss_qty'];

	$i = 0;
    $result = exec_query("SELECT * FROM `ksh_news` ORDER BY `date` DESC LIMIT ".mysql_real_escape_string($news_qty)."");
	while ($row = mysql_fetch_array($result))
	{
		$content['rss_items'][$i]['id'] = stripslashes($row['id']);
		$content['rss_items'][$i]['title'] = stripslashes($row['name']);

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


		$content['rss_items'][$i]['date'] = $dt[2]." ".$mon." ".$dt[0]." 00:00:00 +0300";
		$content['rss_items'][$i]['description'] = strip_tags(stripslashes($row['descr']), "<p><br><a>");
		$content['rss_items'][$i]['site_url'] = $config['base']['site_url'];
		$i++;
	}
	mysql_free_result($result);


    debug("*** end: news_rss ***");
    return $content;
}

?>
