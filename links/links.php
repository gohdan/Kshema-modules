<?php

// links administration functions of the links module

include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function links($category)
{
    debug("*** links ***");
    global $user;
    $content = "";
    $links = 2;

    debug ("category name: ".$category);
    $category_id = mysql_result(exec_query("SELECT id FROM ksh_links_categories WHERE name='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category id: ".$category_id);
    $result = exec_query("SELECT * FROM ksh_links WHERE category='".mysql_real_escape_string($category_id)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($links)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show links ".$row['id']);
        $content .= "<tr><td>
                    <a href=\"/index.php?module=links&action=view&links=".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/index.php?module=links&action=view&links=".$row['id']."\">".$row['name']."</a><br>
                    ".substr(stripslashes($row['descr']), 0, 100)."...<br>
                    <span class=\"more\"><a href=\"/index.php?module=links&action=view&links=".$row['id']."\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/index.php?module=links&action=admin\">Администрирование</a></p>";

    return $content;
    debug("*** end: links ***");
}

function links_view_by_category()
{
    debug("*** links ***");
    global $user;
	global $page_title;

    $content['content'] = "";
    $content['category'] = "";
    $content['links'] = "";
	$category = $_GET['category'];

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have links to delete");
            exec_query("DELETE FROM ksh_links WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['content'] .= "<p>Новость успешно удалена</p>";
        }
        else
        {
            debug ("don't have links to delete");
        }
		$content['content'] .= "<p><a href=\"/index.php?module=links&action=add_links&category=".$category."\">Добавить ссылку</a></p>";
    }


    $content['category'] = mysql_result(exec_query("SELECT name FROM ksh_links_categories WHERE id='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category name: ".$content['category']);
    $result = exec_query("SELECT * FROM ksh_links WHERE category='".mysql_real_escape_string($category)."' ORDER BY id DESC");

    // $content['links'] .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show links ".$row['id']);
		/*
		$content['links'] .= "<tr><td>
                    <a href=\"/index.php?module=links&action=view&links=".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/index.php?module=links&action=view&links=".$row['id']."\">".$row['name']."</a><br>
                    ".substr(stripslashes($row['descr']), 0, 100)."<br>
                    <span class=\"more\"><a href=\"/index.php?module=links&action=view&links=".$row['id']."\">Подробнее...</a></span>
        ";
        if (1 == $user['id'])
            $content['links'] .= "
                    <br><span class=\"more\"><a href=\"/index.php?module=links&action=edit&links=".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/index.php?module=links&action=del&links=".$row['id']."\">Удалить</a></span>
            ";
        $content['links'] .= "
                </td></tr>
        ";
		*/
		$content['links'] .= "<a href=\"/index.php?module=links&action=view&links=".$row['id']."\"><img src=\"".$row['image']."\"></a><br>
        ";
        if (1 == $user['id'])
            $content['links'] .= "
                    <br><span class=\"more\"><a href=\"/index.php?module=links&action=edit&links=".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/index.php?module=links&action=del&links=".$row['id']."\">Удалить</a></span><br>";
    }
    mysql_free_result($result);
    // $content['links'] .= "</table>";

    if (1 == $user['id']) $content['links'] .= "<p><a href=\"/index.php?module=links&action=admin\">Администрирование</a></p>";

	$page_title .= " | ".$content['category'];

    return $content;
    debug("*** end: links ***");
}


function links_add()
{
    debug ("*** fn: links_add ***");
	global $config;
	global $user;

    global $debug;

	global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;
	
	if (isset($_FILES['image']))
	{
		debug ("FILES['image'] is set");
		$image = $_FILES['image'];
	}
	else
		debug ("FILES['image'] is NOT set");
    $if_file_exists = 0;
    $file_path = "";


    $content['content'] = "";
    $content['categories'] = "";

    $result = exec_query("SELECT * FROM ksh_links_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories'] .= "<option name=\"category\" value=\"".$category['id']."\"";
		if ($category['id'] == $_GET['category']) $content['categories'] .= " selected";
		$content['categories'] .= ">".$category['name']."</option>";
    }
    mysql_free_result($result);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

				if ((isset($image)) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."links/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."links/",$if_file_exists);
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
                debug ("links name isn't empty");
                exec_query("INSERT INTO `ksh_links` (`name`, `category`, `url`, `descr`, `date`, `image`) VALUES (
					'".mysql_real_escape_string($_POST['name'])."',
					'".mysql_real_escape_string($_POST['category'])."',
					'".mysql_real_escape_string($_POST['url'])."',
					'".mysql_real_escape_string($_POST['descr'])."',
					CURDATE(),
					'".mysql_real_escape_string($file_path)."'
					)");
                $content['content'] .= "<p>Ссылка добавлена</p>";
            }
            else
            {
                debug ("links name is empty");
                $content['content'] .= "<p>Пожалуйста, задайте название новости</p>";
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
        $content['content'] = "<p>Пожалуйста, войдите в систему как администратор.</p>";
    }

    debug ("*** end: fn: links_add ***");
    return $content;
}

function links_edit()
{
    debug ("*** fn: links_edit ***");
	global $config;
    global $user;
    global $debug;

	global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;


    $content['content'] = "";
    $content['categories'] = "";
    $content['name'] = "";
    $content['descr'] = "";

	if (isset($_FILES['image']))
    {
        debug ("have an image!");
        $image = $_FILES['image'];
    }
    else debug ("don't have an image!");
    $if_file_exists = 0;
    $file_path = "";


    if (isset($_GET['links'])) $links_id =$_GET['links'];
    else if (isset($_POST['id'])) $links_id =$_POST['id'];
    else $links_id =0;
    debug ("links id: ".$links_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");
        if (isset($_POST['do_update']))
        {
	
			if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."links/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."links/",$if_file_exists);
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
                debug ("links name isn't empty");
                exec_query("UPDATE `ksh_links` set
					`name` = '".mysql_real_escape_string($_POST['name'])."',
					`category` = '".mysql_real_escape_string($_POST['category'])."',
					`descr` = '".mysql_real_escape_string($_POST['descr'])."',
					`image` = '".mysql_real_escape_string($file_path)."'
					WHERE `id` = '".mysql_real_escape_string($links_id)."'");
                $content['content'] .= "<p>Изменения записаны</p>";
            }
            else
            {
                debug ("links name is empty");
                $content['content'] .= "<p>Пожалуйста, задайте название ссылки</p>";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_links WHERE id='".mysql_real_escape_string($links_id)."'");
            $links = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['name'] = $links['name'];
            $content['descr'] = stripslashes($links['descr']);
            $content['id'] = $links['id'];
            $content['old_image'] = $links['image'];
			debug ("old image: ".$content['old_image']);
            $content['image'] = $links['image'];
			debug ("image: ".$content['image']);
            $content['url'] = $links['url'];

            $result = exec_query("SELECT * FROM ksh_links_categories");
            while ($category = mysql_fetch_array($result))
            {
                debug ("show category ".$category['id']);
                $content['categories'] .= "<option name=\"category\" value=\"".$category['id']."\"";
                if ($category['id'] == $links['category'])
                    $content['categories'] .= " selected";
                $content['categories'] .= ">".$category['name']."</option>";
            }
            mysql_free_result($result);

    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "<p>Пожалуйста, войдите в систему как администратор.</p>";
    }

    debug ("*** end: fn: links_edit ***");
    return $content;
}

function links_del()
{
    debug ("*** fn: links_del ***");
    global $user;
    $content['content'] = "";
    $content['id'] = "";
    $content['name'] = "";
    $content['category_id'] = "";

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_links WHERE id='".mysql_real_escape_string($_GET['links'])."'");
        $links = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = $links['id'];
        $content['name'] = $links['name'];
        $content['category_id'] = $links['category'];
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "<p>Пожалуйста, войдите в систему как администратор</p>";
    }

    debug ("*** end: fn: links_del ***");
    return $content;
}

function links_view()
{
    debug ("*** fn: links_view ***");

	global $page_title;

    $content['name'] = "";
    $content['date'] = "";
    $content['descr'] = "";
    $content['category'] = "";
    $content['category_id'] = "";

    $result = exec_query("SELECT * FROM ksh_links WHERE id='".mysql_real_escape_string($_GET['links'])."'");
    $links = mysql_fetch_array($result);
    mysql_free_result($result);

    $content['name'] = $links['name'];
    $content['date'] = $links['date'];
    $content['descr'] = stripslashes($links['descr']);
    $content['url'] = $links['url'];
    $content['image'] = $links['image'];
    $content['category'] = mysql_result(exec_query("SELECT name FROM ksh_links_categories WHERE id='".mysql_real_escape_string($links['category'])."'"), 0, 0);
    $content['category_id'] = $links['category'];

	$page_title .= " | ".$content['name'];


    debug ("*** end: fn: links_view ***");
    return $content;
}

function links_archive()
{
    debug("*** links_archive ***");
    global $user;
	global $page_title;

	$page_title .= " | Архив ссылок";
    $content['content'] = "";

    $result = exec_query("SELECT * FROM ksh_links ORDER BY id DESC");

    $content['content'] .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show links ".$row['id']);
        $content['content'] .= "<tr><td>
                    <a href=\"/index.php?module=links&action=view&links=".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/index.php?module=links&action=view&links=".$row['id']."\">".$row['name']."</a><br>
                    ".substr(stripslashes($row['descr']), 0, 100)."...<br>
                    <span class=\"more\"><a href=\"/index.php?module=links&action=view&links=".$row['id']."\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content['content'] .= "</table>";

    if (1 == $user['id']) $content['content'] .= "<p><a href=\"/index.php?module=links&action=admin\">Администрирование</a></p>";

    return $content;
    debug("*** end: links_archive ***");
}


?>
