<?php

// Articles administration functions of the "articles" module

include_once ($config['modules']['location']."/files/index.php"); // to upload pictures

function articles($category)
{
    debug("*** articles ***");
    global $user;
    $content = "";
    $articles = 2;

    debug ("category name: ".$category);
    $category_id = mysql_result(exec_query("SELECT id FROM ksh_articles_categories WHERE name='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category id: ".$category_id);
    $result = exec_query("SELECT * FROM ksh_articles WHERE category='".mysql_real_escape_string($category_id)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($articles)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show articles ".$row['id']);
        $content .= "<tr><td>";

        if ("" != $row['descr_image']) $content .= "<img src=\"".stripslashes($row['descr_image'])."\" style=\"clear: right; float: left; margin-right: 5px\">";

        $content['articles'] .= "
                    <a href=\"/index.php?module=articles&action=view&articles=".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/index.php?module=articles&action=view&articles=".$row['id']."\">".stripslashes($row['name'])."</a><br>
        ";
        if ("" != $row['descr']) $content .= $row['descr'];
        else $content .= substr(stripslashes($row['full_text']), 0, 100)."...";
        $content .= "<br>
                    <span class=\"more\"><a href=\"/index.php?module=articles&action=view&articles=".$row['id']."\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/index.php?module=articles&action=admin\">Администрирование</a></p>";

    return $content;
    debug("*** end: articles ***");
}

function articles_view_by_category()
{
    debug("*** articles_view_by_category ***");
    global $user;
    global $config;
	global $page_title;
    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
		'categories' => '',
        'articles' => '',
		'add_category_link' => '',
        'add_article_link' => '',
        'admin_link' => ''
    );
    $i = 0;

    $category = $_GET['category'];

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have articles to delete");
            exec_query("DELETE FROM ksh_articles WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Статья успешно удалена";
        }
        else
        {
            debug ("don't have articles to delete");
        }
		$content['add_category_link'] .= "<a href=\"/index.php?module=articles&action=add_category&category=".$category."\">Добавить подкатегорию</a>";
        $content['add_article_link'] .= "<a href=\"/index.php?module=articles&action=add_articles&category=".$category."\">Добавить статью</a>";
        $content['admin_link'] .= "<a href=\"/index.php?module=articles&action=admin\">Администрирование статей</a>";
    }

	$i = 0;
	$result = exec_query("SELECT * FROM ksh_articles_categories WHERE parent='".mysql_real_escape_string($category)."'");
	while ($row = mysql_fetch_array($result))
	{
		$content['categories'][$i]['id'] = stripslashes($row['id']);
		$content['categories'][$i]['name'] = stripslashes($row['name']);
		if (1 == $user['id'])
            {
				$content['categories'][$i]['edit_link'] = "<a href=\"/index.php?module=articles&action=category_edit&category=".stripslashes($row['id'])."\">Редактировать</a>";
	            $content['categories'][$i]['del_link'] = "<a href=\"/index.php?module=articles&action=del_category&category=".stripslashes($row['id'])."\">Удалить</a>";
			}
		$i++;

	}
	mysql_free_result($result);


    $sql_query = "SELECT * FROM ksh_articles_categories WHERE id='".mysql_real_escape_string($category)."'";
	$result = exec_query($sql_query);
	$cat = mysql_fetch_array($result);
	mysql_free_result($result);

    $content['category'] = stripslashes($cat['name']);
    debug ("category name: ".$content['category']);

	if ("" != $cat['menu_template'])
		$config['themes']['menu_tpl'] = stripslashes($cat['menu_template']);

    $result = exec_query("SELECT * FROM ksh_articles WHERE category='".mysql_real_escape_string($category)."' ORDER BY id DESC");

    while ($row = mysql_fetch_array($result))
    {
        debug("show article ".$row['id']);
        $content['articles'][$i]['id'] = stripslashes($row['id']);
        $content['articles'][$i]['date'] = stripslashes($row['date']);
        $content['articles'][$i]['name'] = stripslashes($row['name']);
        $content['articles'][$i]['doc'] = stripslashes($row['doc']);

        if ("" != $row['descr_image']) $content['articles'][$i]['descr_image'] = "<img src=\"".stripslashes($row['descr_image'])."\" style=\"clear: right; float: left; margin-right: 5px\">";
        else $content['articles'][$i]['descr_image'] = "";

		if ("" != $row['descr']) $content['articles'][$i]['descr'] = stripslashes($row['descr']);
        else $content['articles'][$i]['descr'] = substr(stripslashes($row['full_text'], 0, 100))."...";

        if (1 == $user['id'])
			$content['articles'][$i]['show_admin_link'] = "yes";

		$i++;

    }
    mysql_free_result($result);

	$page_title .= " | ".$content['category'];
    debug("*** end: articles ***");
    return $content;
}


function articles_add()
{
    debug ("*** articles_add ***");
    global $user;
    global $config;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

    if (isset($_FILES['doc'])) $doc = $_FILES['doc'];
    $if_doc_exists = 0;
    $doc_path = "";

$content = array(
    	'content' => '',
        'result' => '',
        'categories_select' => ''
    );

    $i = 0;


    if (isset($_GET['category'])) $category_id = $_GET['category'];
    else if (isset($_POST['category'])) $category_id = $_POST['category'];
    else $category_id = 0;
    debug ("category id: ".$category_id);

    $result = exec_query ("SELECT * FROM ksh_articles_categories WHERE id='".mysql_real_escape_string($category_id)."'");
    $category = mysql_fetch_array($result);
    mysql_free_result ($result);
    debug ("category name: ".$category['name']);

    $result = exec_query("SELECT * FROM ksh_articles_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = stripslashes($category['id']);
        $content['categories_select'][$i]['name'] = stripslashes($category['name']);
        if ($category['id'] == $category_id) $content['categories_select'][$i]['selected'] = "selected";
        $i++;
    }
    mysql_free_result($result);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_add']))
        {
                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."articles/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."articles/",$if_file_exists);
                    debug ("size: ".filesize($home.$file_path));

                    if (filesize($home.$file_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content['result'] .= "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
                        if (unlink ($home.$file_path)) debug ("file deleted");
                        else debug ("can't delete file!");
                        $file_path = "";
                    }

                    $_POST['image'] = $file_path;

                }
                else
                {
                    debug ("no image to upload");
                    $file_path = $_POST['image'];
                }

				if ("" != $doc['name'])
                {
                    debug ("there is a doc to upload");
                    if (file_exists($doc_root.$upl_pics_dir."articles/".$doc['name'])) $if_doc_exists = 1;
                    $doc_path = upload_file($doc['name'],$doc['tmp_name'],$home,$upl_pics_dir."articles/",$if_doc_exists);
                    debug ("size: ".filesize($home.$doc_path));

                    if (filesize($home.$doc_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content['result'] .= "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
                        if (unlink ($home.$doc_path)) debug ("file deleted");
                        else debug ("can't delete file!");
                        $doc_path = "";
                    }

                    $_POST['doc'] = $doc_path;

                }
                else
                {
                    debug ("no doc to upload");
                    $doc_path = $_POST['doc'];
                }

            debug ("have data to add");
            if ("" != $_POST['name'])
            {
                debug ("articles name isn't empty");
                exec_query("INSERT INTO ksh_articles (name, category, descr_image, doc, descr, full_text, date) VALUES (
					'".mysql_real_escape_string($_POST['name'])."',
					'".mysql_real_escape_string($_POST['category'])."',
	                '".mysql_real_escape_string($file_path)."',
	                '".mysql_real_escape_string($doc_path)."',
					'".mysql_real_escape_string($_POST['descr'])."',
					'".mysql_real_escape_string($_POST['full_text'])."',
					CURDATE()
					)");
                $content['result'] .= "Добавлено";
            }
            else
            {
                debug ("articles name is empty");
                $content['result'] .= "Пожалуйста, задайте название";
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
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_add ***");
    return $content;
}

function articles_edit()
{
    debug ("*** articles_edit ***");
    global $user;
    global $config;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'name' => '',
        'descr' => '',
        'full_text' => '',
        'image' => ''
    );

    $i = 0;

    if (isset($_FILES['image']))
    {
        debug ("have an image!");
        $image = $_FILES['image'];
    }
    else debug ("don't have an image!");
    $if_file_exists = 0;
    $file_path = "";
	
	if (isset($_FILES['doc']))
    {
        debug ("have a doc!");
        $doc = $_FILES['doc'];
    }
    else debug ("don't have a doc!");
    $if_doc_exists = 0;
    $doc_path = "";

    if (isset($_GET['articles'])) $articles_id =$_GET['articles'];
    else if (isset($_POST['id'])) $articles_id =$_POST['id'];
    else $articles_id =0;
    debug ("articles id: ".$articles_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_update']))
        {
                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."articles/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."articles/",$if_file_exists);
                    debug ("size: ".filesize($home.$file_path));

                    if (filesize($home.$file_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content['result'] .= "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
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
				
				if ("" != $doc['name'])
                {
                    debug ("there is a doc to upload");
                    if (file_exists($doc_root.$upl_pics_dir."articles/".$doc['name'])) $if_doc_exists = 1;
                    $doc_path = upload_file($doc['name'],$doc['tmp_name'],$home,$upl_pics_dir."articles/",$if_doc_exists);
                    debug ("size: ".filesize($home.$doc_path));

                    if (filesize($home.$doc_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content['result'] .= "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
                        if (unlink ($home.$doc_path)) debug ("file deleted");
                        else debug ("can't delete file!");
                        $doc_path = $_POST['old_doc'];
                    }

                    $_POST['doc'] = $doc_path;

                }
                else
                {
                    debug ("no doc to upload");
                    $doc_path = $_POST['old_doc'];
                }

        if (isset($_POST['image'])) debug ("POST image: ".$_POST['image']);
        debug ("file path: ".$file_path);

        if (isset($_POST['doc'])) debug ("POST doc: ".$_POST['doc']);
        debug ("doc path: ".$doc_path);

            debug ("have data to update");
            if ("" != $_POST['name'])
            {
                debug ("articles name isn't empty");
                exec_query("UPDATE ksh_articles set 
					name='".mysql_real_escape_string($_POST['name'])."', 
					category='".mysql_real_escape_string($_POST['category'])."', 
					descr_image='".mysql_real_escape_string($file_path)."',  
					doc='".mysql_real_escape_string($doc_path)."',  
					descr='".mysql_real_escape_string($_POST['descr'])."',  
					full_text='".mysql_real_escape_string($_POST['full_text'])."' 
					WHERE id='".mysql_real_escape_string($articles_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("articles name is empty");
                $content['result'] .= "Пожалуйста, задайте название статьи";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_articles WHERE id='".mysql_real_escape_string($articles_id)."'");
            $articles = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['name'] = stripslashes($articles['name']);
            $content['image'] = stripslashes($articles['descr_image']);
            $content['doc'] = stripslashes($articles['doc']);
            $content['descr'] = stripslashes($articles['descr']);
            $content['full_text'] = stripslashes($articles['full_text']);
            $content['id'] = stripslashes($articles['id']);

            $result = exec_query("SELECT * FROM ksh_articles_categories");
            while ($category = mysql_fetch_array($result))
            {
		        debug ("show category ".$category['id']);
		        $content['categories_select'][$i]['id'] = stripslashes($category['id']);
		        $content['categories_select'][$i]['name'] = stripslashes($category['name']);
		        if ($category['id'] == $articles['id']) $content['categories_select'][$i]['selected'] = "selected";
		        $i++;
            }
            mysql_free_result($result);

    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_edit ***");
    return $content;
}

function articles_del()
{
    debug ("*** articles_del ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'id' => '',
        'name' => '',
        'category_id' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_articles WHERE id='".mysql_real_escape_string($_GET['articles'])."'");
        $articles = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($articles['id']);
        $content['name'] = stripslashes($articles['name']);
        $content['category_id'] = stripslashes($articles['category']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_del ***");
    return $content;
}

function articles_view()
{
    debug ("*** articles_view ***");
    global $config;
	global $page_title;

    $content = array(
    	'name' => '',
        'date' => '',
        'descr' => '',
        'full_text' => '',
        'category' => '',
        'category_id' => ''
    );

    $result = exec_query("SELECT * FROM ksh_articles WHERE id='".mysql_real_escape_string($_GET['articles'])."'");
    $articles = mysql_fetch_array($result);
    mysql_free_result($result);

    $content['name'] = stripslashes($articles['name']);
    $content['date'] = stripslashes($articles['date']);
    $content['descr'] = stripslashes($articles['descr']);
    $content['full_text'] = stripslashes($articles['full_text']);
    $content['category'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_articles_categories WHERE id='".mysql_real_escape_string($articles['category'])."'"), 0, 0));
    $content['category_id'] = stripslashes($articles['category']);

	$page_title .= " | ".$content['name'];

    debug ("*** end: articles_view ***");
    return $content;
}

function articles_archive()
{
    debug("*** articles_archive ***");
    global $user;
    global $config;
	global $page_title;

	$page_title .= " | Архив статей".

    $content = array(
    	'content' => '',
        'articles' => '',
        'admin_link' => ''
    );
    $i = 0;

    $result = exec_query("SELECT * FROM ksh_articles ORDER BY id DESC");

    while ($row = mysql_fetch_array($result))
    {
        debug("show article ".$row['id']);
        $content['articles'][$i]['id'] = stripslashes($row['id']);
        $content['articles'][$i]['date'] = stripslashes($row['date']);
        $content['articles'][$i]['name'] = stripslashes($row['name']);
        $content['articles'][$i]['descr_image'] = stripslashes($row['descr_image']);
      	$content['articles'][$i]['edit_link'] = "";
        $content['articles'][$i]['del_link'] = "";
        if ("" != $row['descr']) $content['articles'][$i]['descr'] = stripslashes($row['descr']);
        else $content['articles'][$i]['descr'] = substr(stripslashes($row['full_text']), 0, 100)."...";
        $i++;
    }
    mysql_free_result($result);

    if (1 == $user['id']) $content['admin_link'] .= "<a href=\"/index.php?module=articles&action=admin\">Администрирование</a>";

    return $content;
    debug("*** end: articles_archive ***");
}


?>
