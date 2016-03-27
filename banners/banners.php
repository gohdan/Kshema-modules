<?php

// banners administration functions of the banners module

include_once ($config['modules']['location']."files/index.php"); // to upload pictures


function banners_hook($type, $id = 0)
{
    debug("=== banners_hook ===");
    global $user;
    global $config;
    $content = "";

	debug ("type: ".$type);
	debug ("id: ".$id);

	switch ($type)
	{
		default: break;

		case "element":
			$sql_query = "SELECT * FROM `ksh_banners` WHERE `id` = '".mysql_real_escape_string($id)."'";
			$result = exec_query($sql_query);
			$row = mysql_fetch_array($result);
			mysql_free_result($result);

			foreach($row as $k => $v)
				$banner[$k] = stripslashes($v);

			dump($banner);

			$content = gen_content("banners", $banner['type'], $banner);

		break;

		case "category":

			if (0 == $id)
			{
				$sql_query = "SELECT `hook_id` FROM `ksh_hooks` WHERE
					`hook_module` = 'banners' AND
					`hook_type` = 'category' AND
					`to_module` = '".mysql_real_escape_string($config['modules']['current_module'])."' AND
					(
						(`to_type` = 'category' AND	`to_id` = '".$config['modules']['current_category']."')	OR
						(`to_type` = 'element' AND `to_id` = '".$config['modules']['current_id']."')
					)";
				$result = exec_query($sql_query);
				$row = mysql_fetch_array($result);
				$id = stripslashes($row['hook_id']);
				mysql_free_result($result);
				debug("category id: ".$id);
			}

			// Determining templates
			$sql_query = "SELECT `name`	FROM `ksh_banners_categories` WHERE `id` = '".mysql_real_escape_string($id)."'";
			$result = exec_query($sql_query);
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			$category_name = stripslashes($row['name']);

			if ("" != templater_find_template("banners", $category_name))
			{
				$template_file = $category_name;
				$banners_list = $category_name."_banners";
			}
			else
			{
				$template_file = "hook_category";
				$banners_list = "banners_hooked";
			}

			debug("template_file: ".$template_file);
			debug("banners_list: ".$banners_list);

			$i = 0;

			$sql_query = "SELECT * FROM `ksh_banners` WHERE `category` = '".mysql_real_escape_string($id)."'";
			$result = exec_query($sql_query);
			while ($row = mysql_fetch_array($result))
			{
				foreach ($row as $k => $v)
					$cnt[$banners_list][$i][$k] = stripslashes($v);

				$cnt[$banners_list][$i]['i'] = $i;
				$cnt[$banners_list][$i]['number'] = $i+1;

				$i++;
			}
			mysql_free_result($result);

			$cnt['banners_qty'] = $i;

			if ("1" == $user['id'])
				$cnt['show_admin_link'] = "yes";


			$content = gen_content("banners", $template_file, $cnt);
		break;
	}
	
	debug ("content: ".$content);
    debug("=== end: banners_hook ===");
    return $content;
}


function banners_view_by_category()
{
    debug("*** banners_view_by_category ***");
    global $user;
	global $page_title;
    global $config;

    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        'admin_link' => '',
        'edit_link' => '',
        'descr' => '',
		'if_show_admin_link' => ''
    );	

	$i = 0;

	if (isset($_GET['category']))
		$category = $_GET['category'];
	else if (isset($_GET['element']))
		$category = $_GET['element'];
	else if (isset($_POST['category']))
		$category = $_POST['category'];
	else
		$category = 0;
		
	$result = exec_query ("SELECT * FROM ksh_banners_categories WHERE id='".mysql_real_escape_string($category)."'");
	$cat = mysql_fetch_array($result);

    $content['category_id'] = $category;
    $content['category'] = stripslashes($cat['title']);


	
    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have banners to delete");
            exec_query("DELETE FROM ksh_banners WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Баннер успешно удалён";
        }
        else
        {
            debug ("don't have banners to delete");
        }

        $content['if_show_admin_link'] = "yes";
	}

	// FIXME: Check if there are categories; else user has a warning
    debug ("category name: ".$content['category']);
    $result = exec_query("SELECT * FROM ksh_banners WHERE category='".mysql_real_escape_string($category)."' ORDER BY `id` DESC");

	$content['banners'] = array();

    while ($row = mysql_fetch_array($result))
    {
        debug("show banners ".$row['id']);
		if ("" != $row['image'])
			$content['banners'][$i]['image'] = $row['descr_image'];
		else
			$content['banners'][$i]['image'] = "";

        $content['banners'][$i]['descr'] = stripslashes($row['descr']);
        $content['banners'][$i]['id'] = $row['id'];
		$content['banners'][$i]['name'] = stripslashes($row['name']);
		$content['banners'][$i]['title'] = stripslashes($row['title']);

        if (1 == $user['id'])
        {
            $content['banners'][$i]['edit_link'] = "ID: ".$row['id'].". <a href=\"/index.php?module=banners&amp;action=edit&amp;banners=".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/index.php?module=banners&amp;action=del&amp;banners=".$row['id']."\">Удалить</a>";
        }
        else
        {
        	$content['banners'][$i]['edit_link'] = "";
        }
        $i++;
    }
    mysql_free_result($result);

	$page_title .= " | ".$content['category'];

    return $content;
    debug("*** end: banners_view_by_category ***");
}


function banners_add()
{
    debug ("*** banners_add ***");
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

	if (isset($_GET['category']))
		$category_id = $_GET['category'];
	else if (isset($_GET['element']))
		$category_id = $_GET['element'];
	else if (isset($_POST['category']))
		$category_id = $_POST['category'];
	else
		$category_id = 0;
	
	$content['category'] = $category_id;
    $content['date'] = date("Y-m-d");		

    $i = 0;
    $result = exec_query("SELECT * FROM ksh_banners_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = $category['id'];
        $content['categories_select'][$i]['name'] = $category['name'];
        $content['categories_select'][$i]['title'] = $category['title'];
        if ($category['id'] == $category_id)
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
                    if (file_exists($doc_root.$upl_pics_dir."banners/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."banners/",$if_file_exists);
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
                debug ("banners name isn't empty");
                exec_query("INSERT INTO ksh_banners (
					name,
					title,
					category,
					descr,
					file,
					params,
					alt,
					width,
					height,
					type,
					class,
					`link`
					) VALUES (
					'".mysql_real_escape_string($_POST['name'])."',
					'".mysql_real_escape_string($_POST['title'])."',
					'".mysql_real_escape_string($_POST['category'])."',
					'".mysql_real_escape_string($_POST['descr'])."',
					'".mysql_real_escape_string($file_path)."',
					'".mysql_real_escape_string($_POST['params'])."',
					'".mysql_real_escape_string($_POST['alt'])."',
					'".mysql_real_escape_string($_POST['width'])."',
					'".mysql_real_escape_string($_POST['height'])."',
					'".mysql_real_escape_string($_POST['type'])."',
					'".mysql_real_escape_string($_POST['class'])."',
					'".mysql_real_escape_string($_POST['link'])."'
				)");
                $content['result'] .= "Баннер добавлен";
            }
            else
            {
                debug ("banners name is empty");
                $content['result'] .= "Пожалуйста, задайте название баннера";
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

    debug ("*** end: banners_add ***");
    return $content;
}

function banners_add_batch()
{
    debug ("*** banners_add_batch ***");
    global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;


    $content = array (
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'date' => ''
    );

	if (isset($_GET['category']))
		$category_id = $_GET['category'];
	else if (isset($_GET['element']))
		$category_id = $_GET['element'];
	else if (isset($_POST['category']))
		$category_id = $_POST['category'];
	else
		$category_id = 0;
	
	$content['category'] = $category_id;
    $content['date'] = date("Y-m-d");		

    $i = 0;
    $result = exec_query("SELECT * FROM ksh_banners_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = $category['id'];
        $content['categories_select'][$i]['name'] = $category['name'];
        $content['categories_select'][$i]['title'] = $category['title'];
        if ($category['id'] == $category_id)
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

		if (isset($_POST['do_add']))
		{
            debug ("have data to add");
			for ($i = 0; $i <= 9; $i++)
			{

	    		if (isset($_FILES['image_'.$i]))
					$image = $_FILES['image_'.$i];
	    		$if_file_exists = 0;
	    		$file_path = "";

	            if ((isset($image)) && ("" != $image['name']))
   	            {
       	            debug ("there is an image to upload");
           	        if (file_exists($doc_root.$upl_pics_dir."banners/".$image['name'])) $if_file_exists = 1;
               	    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."banners/",$if_file_exists);
                   	debug ("size: ".filesize($home.$file_path));

                    if (filesize($home.$file_path) > $max_file_size)
   	                {
       	                debug ("file size > max file size!");
           	            $content .= "<p>Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт</p>";
               	        if (unlink ($home.$file_path)) debug ("file deleted");
                   	    else debug ("can't delete file!");
                        $file_path = "";
   	                }

					if ("" != $file_path)
					{
						$name = str_replace($upl_pics_dir."banners/", "", $file_path);
	      	        	$sql_query = "INSERT INTO `ksh_banners` (
							`name`,
							`title`,
							`category`,
							`file`,
							`type`
							) VALUES (
							'".mysql_real_escape_string($name)."',
							'".mysql_real_escape_string($name)."',
							'".mysql_real_escape_string($_POST['category'])."',
							'".mysql_real_escape_string($file_path)."',
							'".mysql_real_escape_string($_POST['type'])."'
						)";
						exec_query($sql_query);
					}
           	    }
               	else
   	                debug ("no image to upload");
			}
		}
		else
			debug ("no data to add");
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: banners_add_batch ***");
    return $content;
}


function banners_edit()
{
    debug ("*** banners_edit ***");
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
        'image' => '',
		'params' => '',
		'alt' => '',
		'width' => '',
		'height' => '',
		'type' => '',
		'class' => '',
		'link' => ''
    );

    if (isset($_FILES['image']))
    {
        debug ("have an image!");
        $image = $_FILES['image'];
    }
    else debug ("don't have an image!");
    $if_file_exists = 0;
    $file_path = "";

    if (isset($_GET['banners']))
		$banners_id = $_GET['banners'];
	else if (isset($_GET['element']))
		$banners_id = $_GET['element'];
    else if (isset($_POST['id']))
		$banners_id =$_POST['id'];
    else $banners_id =0;
    debug ("banners id: ".$banners_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");
        if (isset($_POST['do_update']))
        {

                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."banners/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."banners/",$if_file_exists);
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
                debug ("banners name isn't empty");
                exec_query("UPDATE ksh_banners set 
					`name`='".mysql_real_escape_string($_POST['name'])."',
					`title`='".mysql_real_escape_string($_POST['title'])."',
					`category`='".mysql_real_escape_string($_POST['category'])."',
					`descr`='".mysql_real_escape_string($_POST['descr'])."',
					`file`='".mysql_real_escape_string($file_path)."',
					`params`='".mysql_real_escape_string($_POST['params'])."',
					`alt`='".mysql_real_escape_string($_POST['alt'])."',
					`width`='".mysql_real_escape_string($_POST['width'])."',
					`height`='".mysql_real_escape_string($_POST['height'])."',
					`type`='".mysql_real_escape_string($_POST['type'])."',
					`class`='".mysql_real_escape_string($_POST['class'])."',
					`link`='".mysql_real_escape_string($_POST['link'])."'
					WHERE id='".mysql_real_escape_string($banners_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("banners name is empty");
                $content['result'] .= "Пожалуйста, задайте название баннера";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_banners WHERE id='".mysql_real_escape_string($banners_id)."'");
            $banners = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['name'] = stripslashes($banners['name']);
            $content['title'] = stripslashes($banners['title']);
            $content['category'] = stripslashes($banners['category']);
            $content['descr'] = stripslashes($banners['descr']);
            $content['image'] = stripslashes($banners['file']);
            $content['params'] = stripslashes($banners['params']);
            $content['id'] = stripslashes($banners['id']);
            $content['alt'] = stripslashes($banners['alt']);
            $content['width'] = stripslashes($banners['width']);
            $content['height'] = stripslashes($banners['height']);
            $content['type'] = stripslashes($banners['type']);
            $content['link'] = stripslashes($banners['link']);
            $content['class'] = stripslashes($banners['class']);

            $result = exec_query("SELECT * FROM ksh_banners_categories");

            $i = 0;
            while ($category = mysql_fetch_array($result))
            {
		        debug ("show category ".$category['id']);
		        $content['categories_select'][$i]['id'] = $category['id'];
		        $content['categories_select'][$i]['name'] = $category['name'];
		        $content['categories_select'][$i]['title'] = $category['title'];
		        if ($category['id'] == $banners['category'])
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

    debug ("*** end: banners_edit ***");
    return $content;
}

function banners_del()
{
    debug ("*** banners_del ***");
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
        $result = exec_query("SELECT * FROM ksh_banners WHERE id='".mysql_real_escape_string($_GET['banners'])."'");
        $banners = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($banners['id']);
        $content['name'] = stripslashes($banners['name']);
        $content['category_id'] = stripslashes($banners['category']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: banners_del ***");
    return $content;
}

function banners_view()
{
    debug ("*** banners_view ***");
	global $config;
	global $page_title;

    $content = array(
    	'name' => '',
        'date' => '',
        'descr' => '',
        'full_text' => '',
        'category' => '',
        'category_id' => '',
		'show_previous_banners_link' => '',
		'show_next_banners_link' => ''
    );

    $result = exec_query("SELECT * FROM ksh_banners WHERE id='".mysql_real_escape_string($_GET['banners'])."'");
    $banners = mysql_fetch_array($result);
    mysql_free_result($result);

    $content['banners_qty'] = mysql_result(exec_query("SELECT COUNT(*) FROM ksh_banners WHERE category='".$banners['category']."'"), 0, 0);

    $content['id'] = stripslashes($banners['id']);
    $content['name'] = stripslashes($banners['name']);
    $content['image'] = stripslashes($banners['image']);
    $content['params'] = stripslashes($banners['params']);
    $content['descr'] = stripslashes($banners['descr']);
    $content['category'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_banners_categories WHERE id='".mysql_real_escape_string($banners['category'])."'"), 0, 0));
    $content['category_id'] = stripslashes($banners['category']);
	
	$previous_banners_qty = stripslashes(mysql_result(exec_query("SELECT count(*) FROM ksh_banners WHERE category='".$banners['category']."' and id < '".$banners['id']."'"), 0, 0));
	if ($previous_banners_qty > 0)
	{
		$content['show_previous_banners_link'] = "yes";
		$content['previous_banners_id'] = stripslashes(mysql_result(exec_query("SELECT id FROM ksh_banners WHERE category='".$banners['category']."' and id < '".$banners['id']."' ORDER BY id DESC LIMIT 1"), 0, 0));
		
	}
		
	$next_banners_qty = stripslashes(mysql_result(exec_query("SELECT count(*) FROM ksh_banners WHERE category='".$banners['category']."' and id > '".$banners['id']."'"), 0, 0));
	if ($next_banners_qty > 0)
	{
		$content['show_next_banners_link'] = "yes";
		$content['next_banners_id'] = stripslashes(mysql_result(exec_query("SELECT id FROM ksh_banners WHERE category='".$banners['category']."' and id > '".$banners['id']."' ORDER BY id ASC LIMIT 1"), 0, 0));
	}

    $config['modules']['current_id'] = $banners['id'];
    $config['modules']['current_category'] = $banners['category'];
    $config['modules']['current_title'] = $content['name'];
	
	

	$config['pages']['page_title'] = $content['name'];
	
    debug ("*** end: banners_view ***");
    return $content;
}

?>
