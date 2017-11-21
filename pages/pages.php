<?php

// Pages functions of the "pages" module

function pages_add()
{
    debug ("*** pages_add ***");
    global $config;
    global $user;
    $content = array(
    	'content' => '',
		'category' => ''
    );

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

	if (isset($_POST['category']))
		$category_id = $_POST['category'];
	else if (isset($_GET['element']))
		$category_id = $_GET['element'];
	else
		$category_id = 0;

	$content['category'] = $category_id;

	$field_title = "title";
	$field_full_text = "full_text";
	$field_meta_keywords = "meta_keywords";
	$field_meta_description = "meta_description";


	if (isset($config['base']['lang']['current']))
	{
		$content['lang'] = $config['base']['lang']['current'];
		$field_title .= "_".$config['base']['lang']['current'];
		$field_full_text .= "_".$config['base']['lang']['current'];
		$field_meta_keywords .= "_".$config['base']['lang']['current'];
		$field_meta_description .= "_".$config['base']['lang']['current'];
	}

    if (isset($_POST['do_add']))
    {

                if ((isset($image)) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."pages/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."pages/",$if_file_exists);
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

        debug ("have data to insert into DB");
        unset ($_POST['do_add']);
        exec_query("INSERT INTO `ksh_pages` (
			`name`,
			`category`,
			`subcategory`,
			`position`,
			`image`,
			`".$field_title."`,
			`".$field_meta_keywords."`,
			`".$field_meta_description."`,
			`template`,
			`menu_template`,
			`css`,
			`".$field_full_text."`
			) VALUES (
			'".mysql_real_escape_string($_POST['name'])."',
			'".mysql_real_escape_string($_POST['category'])."',
			'".mysql_real_escape_string($_POST['subcategory'])."',
			'".mysql_real_escape_string($_POST['position'])."',
			'".mysql_real_escape_string($file_path)."',
			'".mysql_real_escape_string($_POST['title'])."',
			'".mysql_real_escape_string($_POST['meta_keywords'])."',
			'".mysql_real_escape_string($_POST['meta_description'])."',
			'".mysql_real_escape_string($_POST['template'])."',
			'".mysql_real_escape_string($_POST['menu_template'])."',
			'".mysql_real_escape_string($_POST['css'])."',
			'".mysql_real_escape_string($_POST['full_text'])."'
			)");
    }
    else
        debug ("don't have data to insert into DB");

	$cat = new Category();
	$content['categories_select'] = $cat -> get_select("ksh_pages_categories", $category_id);
	$content['subcategories_select'] = $cat -> get_select("ksh_pages_categories");

    debug ("*** end: pages_add ***");
    return $content;
}

function pages_del()
{
    debug ("*** pages_del ***");
    global $config;
    global $user;

    $content = array(
    	'content' => '',
        'id' => '',
		'title' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_pages WHERE id='".mysql_real_escape_string($_GET['page'])."'");
        $page = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($page['id']);
		$content['title'] = stripslashes($page['title'.'_'.$config['base']['lang']['current']]);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: pages_del ***");
    return $content;
}

function pages_edit()
{
    debug ("*** pages_edit ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'id' => '',
        'name' => '',
        'title' => '',
        'full_text' => '',
        'template' => '',
		'category' => '',
		'meta_keywords' => '',
		'meta_description' => '',
		'menu_template' => '',
		'lang' => ''
    );


    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image']))
    {
        debug ("have an image!");
        $image = $_FILES['image'];
    }
    else debug ("don't have an image!");
    $if_file_exists = 0;
    $file_path = "";

	$field_title = "title";
	$field_full_text = "full_text";
	$field_meta_keywords = "meta_keywords";
	$field_meta_description = "meta_description";


	if (isset($config['base']['lang']['current']))
	{
		$content['lang'] = $config['base']['lang']['current'];
		$field_title .= "_".$config['base']['lang']['current'];
		$field_full_text .= "_".$config['base']['lang']['current'];
		$field_meta_keywords .= "_".$config['base']['lang']['current'];
		$field_meta_description .= "_".$config['base']['lang']['current'];
	}

    if (1 == $user['id'])
    {

        if (isset($_POST['id'])) $page_id = $_POST['id'];
        else if (isset($_GET['page'])) $page_id = $_GET['page'];
        else $page_id = 0;

        if (isset($_POST['do_update']))
        {

                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."pages/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."pages/",$if_file_exists);
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


            debug ("have data to insert into DB");
            unset ($_POST['do_update']);
            exec_query("UPDATE `ksh_pages` set 
				`name` = '".mysql_real_escape_string($_POST['name'])."',
				`subcategory` = '".mysql_real_escape_string($_POST['subcategory'])."',
				`position` = '".mysql_real_escape_string($_POST['position'])."',
				`category` = '".mysql_real_escape_string($_POST['category'])."',
				`image` = '".mysql_real_escape_string($file_path)."',
				`template` = '".mysql_real_escape_string($_POST['template'])."',
				`menu_template` = '".mysql_real_escape_string($_POST['menu_template'])."',
				`css` = '".mysql_real_escape_string($_POST['css'])."',
				`".$field_title."` = '".mysql_real_escape_string($_POST['title'])."',
				`".$field_full_text."` = '".mysql_real_escape_string($_POST['full_text'])."',
				`".$field_meta_keywords."` = '".mysql_real_escape_string($_POST['meta_keywords'])."',
				`".$field_meta_description."` = '".mysql_real_escape_string($_POST['meta_description'])."'
				WHERE id='".$page_id."'");
        }
        else
        {
            debug ("don't have data to insert into DB");
        }

        $result = exec_query("SELECT * FROM `ksh_pages` WHERE id='".mysql_real_escape_string($page_id)."'");
        $page = mysql_fetch_array($result);
        mysql_free_result($result);

		foreach($page as $k => $v)
			$content[$k] = stripslashes($v);

        $content['title'] .= htmlspecialchars(stripslashes($page[$field_title]));
        $content['meta_keywords'] .= stripslashes($page[$field_meta_keywords]);
        $content['meta_description'] .= stripslashes($page[$field_meta_description]);
        $content['full_text'] .= htmlspecialchars(stripslashes($page[$field_full_text]));

		$cat = new Category();
		$content['categories_select'] = $cat -> get_select("ksh_pages_categories", $page['category']);
		$content['subcategories_select'] = $cat -> get_select("ksh_pages_categories", $page['subcategory']);

    }
    else
    {
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    return $content;
    debug ("*** end: pages_edit ***");
}

function pages_list()
{
	debug ("*** pages_list ***");
    global $config;
    $pages = array();

    $result = exec_query("SELECT * FROM ksh_pages ORDER BY ".mysql_real_escape_string($config['pages']['sort_list_by'])." ASC");
    while ($row = mysql_fetch_array($result))
        $pages[] = $row;
    mysql_free_result($result);
    debug ("*** end: pages_list ***");
    return $pages;
}

function pages_view($page)
{
    debug ("*** pages_view ***");
    global $config;
	global $user;
    $content = array(
     	'content' => '',
        'id' => '',
        'title' => '',
        'full_text' => '',
        'template' => '',
		'edit_link' => '',
		'lang' => ''
    );

	$field_title = "title";
	$field_full_text = "full_text";
	$field_meta_keywords = "meta_keywords";
	$field_meta_description = "meta_description";

	if (isset($config['base']['lang']['current']))
	{
		$content['lang'] = $config['base']['lang']['current'];
		$field_title .= "_".$config['base']['lang']['current'];
		$field_full_text .= "_".$config['base']['lang']['current'];
		$field_meta_keywords .= "_".$config['base']['lang']['current'];
		$field_meta_description .= "_".$config['base']['lang']['current'];
	}

		$result = exec_query ("SELECT * FROM `ksh_pages` WHERE `name`='".$page."'");
		if (mysql_num_rows($result))
		{
	        $page = mysql_fetch_array($result);
	        mysql_free_result($result);

			foreach($page as $k => $v)
				$page[$k] = stripslashes($v);

			foreach($page as $k => $v)
				$content[$k] = $v;

			$config['modules']['current_category'] = $page['category'];
			$config['modules']['current_id'] = $page['id'];


	        $content['title'] = $page[$field_title];
			$content['meta_keywords'] = $page[$field_meta_keywords];
			$content['meta_description'] = $page[$field_meta_description];
	        $content['full_text'] = $page[$field_full_text];

			if (1 == $user['id'])
				$content['show_edit_link'] = "yes";

			$config['pages']['page_title'] = $content['title'];
			$config['pages']['page_name'] = $content['name'];
			$config['pages']['meta_keywords'] = $content['meta_keywords'];
			$config['pages']['meta_description'] = $content['meta_description'];
			$config['template']['css'][] = $content['css'];

			$config['themes']['page_title']['element'] = $content['title'];

			$category = array();
			if ("" != $page['category'])
			{
				$sql_query = "SELECT `page_template`, `menu_template` FROM `ksh_pages_categories` WHERE `id` = '".mysql_real_escape_string($page['category'])."'";
				$result = exec_query($sql_query);
				$category = mysql_fetch_array($result);
				mysql_free_result($result);
				foreach($category as $k => $v)
					$category[$k] = stripslashes($v);
			}

			if (("" == $page['template']) && isset($category['page_template']))
			{
				debug ("page-specific template is set: ".$page['template']);
				$config['themes']['page_tpl'] = $category['page_template'];
			}
			else
				$config['themes']['page_tpl'] = $page['template'];
			debug ("page_tpl: ".$config['themes']['page_tpl']);

			if (("" == $page['menu_template']) && isset($category['menu_template']))
			{
				debug ("page-specific menu template is set: ".$page['menu_template']);
				$config['themes']['menu_tpl'] = $category['menu_template'];
			}
			else
				$config['themes']['menu_tpl'] = $page['menu_template'];
			debug ("menu_tpl: ".$config['themes']['menu_tpl']);

		}
		else
		{
			if (!headers_sent())
				header('HTTP/1.1 404 Not Found');
			$config['pages']['page_title'] = "Страница не найдена";
			$config['themes']['page_tpl'] = $config['themes']['page_404'];
		}

        debug ("*** end: pages_view ***");

        return $content;
}

function pages_list_view()
{
        debug ("*** pages_list_view ***");
        global $config;
        global $user;
        $content = array(
        	'content' => '',
            'pages' => '',
			'show_admin_link' => ''
        );
        $i = 0;

		if (1 == $user['id'])
		{
			debug ("user has admin rights");
			$content['show_admin_link'] = "yes";
			if (isset($_POST['do_del']))
            {
            	exec_query ("DELETE FROM ksh_pages WHERE id='".stripslashes($_POST['id'])."'");
				$content['content'] .= "Страница успешно удалена";
            }
		}

        $pages = pages_list();

	$field_title = "title";
	$field_full_text = "full_text";
	$field_meta_keywords = "meta_keywords";
	$field_meta_description = "meta_description";

	if (isset($config['base']['lang']['current']))
	{
		$content['lang'] = $config['base']['lang']['current'];
		$field_title .= "_".$config['base']['lang']['current'];
		$field_full_text .= "_".$config['base']['lang']['current'];
		$field_meta_keywords .= "_".$config['base']['lang']['current'];
		$field_meta_description .= "_".$config['base']['lang']['current'];
	}

		if (0 == count($pages))
			$content['content'] .= "Разделов нет";
		else
		{
        	foreach ($pages as $k => $v)
        	{
            	$content['pages'][$i]['id'] = stripslashes($v['id']);
                $content['pages'][$i]['name'] = stripslashes($v['name']);
                $content['pages'][$i]['title'] = stripslashes($v[$field_title]);

				foreach($config['base']['lang']['list'] as $lang_id => $lang_name)
				{
					$cnt['view_lang_links'][$lang_id]['lang'] = $lang_name;
					$cnt['view_lang_links'][$lang_id]['id'] = $v['id'];
					$cnt['view_lang_links'][$lang_id]['name'] = $v['name'];

					$content['pages'][$i]['view_lang_links'] = gen_content("pages", "view_lang_links", $cnt);
				}

                if (1 == $user['id'])
                {
					$content['pages'][$i]['show_admin_link'] = "yes";
                	$content['pages'][$i]['edit_link'] = "<a href=\"/index.php?module=pages&action=edit&page=".$v['id']."\">";
                    $content['pages'][$i]['edit_link_end'] = "</a>";
                    $content['pages'][$i]['add_hook'] = "<a href=\"/index.php?module=hooks&action=add&to_module=pages&to_type=page&to_id=".stripslashes($v['id'])."&hook_module=news&hook_type=category\">Привязать категорию новостей</a>";
					$content['pages'][$i]['del_link'] = "<a href=\"/index.php?module=pages&action=del&page=".stripslashes($v['id'])."\">Удалить</a>";

					foreach($config['base']['lang']['list'] as $lang_id => $lang_name)
					{
						$cnt['edit_lang_links'][$lang_id]['lang'] = $lang_name;
						$cnt['edit_lang_links'][$lang_id]['id'] = $v['id'];
						$cnt['view_lang_links'][$lang_id]['name'] = $v['name'];

						$content['pages'][$i]['edit_lang_links'] = gen_content("pages", "edit_lang_links", $cnt);
					}

                }
                else
                {
                	$content['pages'][$i]['edit_link'] = "";
                    $content['pages'][$i]['edit_link_end'] = "";
                    $content['pages'][$i]['add_hook'] = "";
					$content['pages'][$i]['del_link'] = "";
                }
				$i++;
        	}
		}

        debug ("*** end: pages_list_view");

        return $content;
}

function pages_view_by_category()
{
    debug("*** pages_view_by_category ***");
    global $user;
	global $page_title;
    global $config;
	
    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        'show_admin_link' => '',
        'pages' => '',
		'category_id' => ''
    );

	$field_title = "title";
	$field_full_text = "full_text";
	$field_meta_keywords = "meta_keywords";
	$field_meta_description = "meta_description";

	if (isset($config['base']['lang']['current']))
	{
		$content['lang'] = $config['base']['lang']['current'];
		$field_title .= "_".$config['base']['lang']['current'];
		$field_full_text .= "_".$config['base']['lang']['current'];
		$field_meta_keywords .= "_".$config['base']['lang']['current'];
		$field_meta_description .= "_".$config['base']['lang']['current'];
	}



	$i = 0;

	if (isset($_GET['category']))
	    $category = $_GET['category'];
	else if (isset($_GET['element']))
		$category = $_GET['element'];
	else
		$category = 0;

	$content['category_id'] = $category;
	
	$result = exec_query ("SELECT * FROM ksh_pages_categories WHERE id='".mysql_real_escape_string($category)."'");
	$cat = mysql_fetch_array($result);
    $content['category'] = stripslashes($cat['title']);	
	mysql_free_result($result);

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
		$content['show_admin_link'] = "yes";

        if (isset($_POST['do_del']))
        {
            debug ("have pages to delete");
            exec_query("DELETE FROM ksh_pages WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Страница успешно удалена";
        }
        else
        {
            debug ("don't have pages to delete");
        }


    }

	// FIXME: Check if there are categories; else user has a warning
    debug ("category title: ".$content['category']);
    $result = exec_query("SELECT * FROM ksh_pages WHERE category='".mysql_real_escape_string($category)."' ORDER BY `name` ASC");
	$i = 0;
    while ($row = mysql_fetch_array($result))
    {
        debug("show pages ".$row['id']);
        $content['pages'][$i]['id'] = $row['id'];
		$content['pages'][$i]['name'] = stripslashes($row['name']);
		$content['pages'][$i]['title'] = stripslashes($row[$field_title]);

		foreach($config['base']['lang']['list'] as $lang_id => $lang_name)
		{
			$cnt['view_lang_links'][$lang_id]['lang'] = $lang_name;
			$cnt['view_lang_links'][$lang_id]['id'] = $row['id'];
			$cnt['view_lang_links'][$lang_id]['name'] = stripslashes($row['name']);

			$content['pages'][$i]['view_lang_links'] = gen_content("pages", "view_lang_links", $cnt);
		}



		if (1 == $user['id'])
		{
			$content['pages'][$i]['show_admin_link'] = "yes";

			foreach($config['base']['lang']['list'] as $lang_id => $lang_name)
			{
				$cnt['edit_lang_links'][$lang_id]['lang'] = $lang_name;
				$cnt['edit_lang_links'][$lang_id]['id'] = $row['id'];

				$content['pages'][$i]['edit_lang_links'] = gen_content("pages", "edit_lang_links", $cnt);
			}
		}
		else
		{
			$content['pages'][$i]['show_admin_link'] = "";
		}


		$i++;
    }
    mysql_free_result($result);

    return $content;
    debug("*** end: pages_view_by_category ***");
}

function pages_transfer()
{
		debug ("*** pages_transfer ***");
		global $user;
		global $config;

		$content = array(
			'content' => ''
		);

		$sql_query = "SELECT * FROM `ksh_pages`";
		$result = exec_query($sql_query);

		while ($row = mysql_fetch_array($result))
		{
			$content['content'] .= "UPDATE `ksh_pages` SET 
				`template` = '".$row['template']."',
				`category` = '".$row['category']."',
				`menu_template` = '".$row['menu_template']."',
				`meta_keywords` = '".$row['meta_keywords']."',
				`meta_description` = '".$row['meta_description']."'
				WHERE `id` = '".$row['id']."';<br>\n";

		}
		mysql_free_result($result);

		debug ("*** end: pages_transfer ***");
		return $content;
}

?>
