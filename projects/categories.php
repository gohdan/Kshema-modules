<?php

// Categories administration functions of the "projects" module

function projects_categories_add()
{
    debug ("*** projects_categories_add ***");
	global $config;
    global $user;

	global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
        'statuses' => '',
        'result' => ''
    );

    $i = 0;

	if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";


    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

        $i = 0;
		$result = exec_query("SELECT * FROM ksh_projects_statuses");
        while ($row = mysql_fetch_array($result))
        {
        	$content['statuses'][$i]['id'] = stripslashes($row['id']);
            $content['statuses'][$i]['name'] = stripslashes($row['name']);
            $content['statuses'][$i]['selected'] = "";
            $i++;
        }
		mysql_free_result($result);

        if (isset($_POST['do_add']))
        {
            debug ("have data to add");
            if ("" != $_POST['name'])
            {
                debug ("category name isn't empty");
				$category_dir = $config['base']['doc_root']."/uploads/projects/".$_POST['name'];
				debug ("category dir: ".$category_dir);
				if (file_exists($category_dir))
				{
					debug ("category dir already exists!");
				}
				else
				{
					debug ("category dir doesn't exist");
					mkdir ($category_dir);
				}

				if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($category_dir."/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."projects/".$_POST['name']."/",$if_file_exists);
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


                exec_query("INSERT INTO ksh_projects_categories (name, title, author, status, descr, descr_image, att_project) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['title'])."',
				'".mysql_real_escape_string($_POST['author'])."',
				'".mysql_real_escape_string($_POST['status'])."', '".mysql_real_escape_string($_POST['descr'])."', '".mysql_real_escape_string($file_path)."',
				'".mysql_real_escape_string($_POST['att_project'])."')");
                $content['result'] .= "Категория добавлена";
            }
            else
            {
                debug ("category name is empty");
                $content['result'] .= "Пожалуйста, задайте имя категории";
            }
        }
    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_categories_add ***");
    return $content;
}

function projects_categories_del()
{
    debug ("*** projects_categories_del ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'id' => '',
        'name' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $content['id'] = $_GET['category'];
        $content['name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($_GET['category'])."'"), 0, 0));
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['result'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_categories_del ***");
    return $content;
}

function projects_categories_view()
{
    debug ("*** projects_categories_edit ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'categories' => ''
    );
    $i = 0;

    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_del']))
        {
            debug ("deleting category ".$_POST['id']);
            exec_query ("DELETE FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            exec_query ("DELETE FROM ksh_projects WHERE category='".mysql_real_escape_string($_POST['id'])."'");
        }

        $i = 0;
        $result = exec_query("SELECT * FROM ksh_projects_categories");
        while ($category = mysql_fetch_array($result))
        {
        	$content['categories'][$i]['id'] = stripslashes($category['id']);
            $content['categories'][$i]['name'] = stripslashes($category['name']);
			if (1 == $user['id'])
            {
            	$content['categories'][$i]['edit_link'] = "<a href=\"/index.php?module=projects&action=category_edit&category=".$category['id']."\">Редактировать</a>";
                $content['categories'][$i]['del_link'] = "<a href=\"/index.php?module=projects&action=del_category&category=".$category['id']."\">Удалить</a>";
            }
            $i++;
        }
        mysql_free_result($result);
    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_categories_edit ***");
    return $content;
}

function projects_categories_edit()
{
    debug ("*** projects_categories_edit ***");
	global $config;
    global $user;

	global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
        'result' => '',
        'category_id' => '',
        'name' => '',
        'title' => '',
        'author' => '',
        'descr' => '',
        'image' => '',
        'statuses' => '',
		'att_project' => ''
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

    if (isset($_GET['category'])) $category_id =$_GET['category'];
    else if (isset($_POST['id'])) $category_id =$_POST['id'];
    else $category_id =0;
    debug ("category id: ".$category_id);

	$result = exec_query ("SELECT * FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($category_id)."'");
    $category = mysql_fetch_array($result);
    mysql_free_result($result);
    debug ("category name: ".$category['name']);


    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_update']))
        {
            debug ("have data to update");

			if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."projects/".$category['name']."/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."projects/".$category['name']."/",$if_file_exists);
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

        if (isset($_POST['image'])) debug ("POST image: ".$_POST['image']);
        debug ("file path: ".$file_path);


            if ("" != $_POST['name'])
            {
                debug ("category name isn't empty");
                exec_query("UPDATE ksh_projects_categories set name='".mysql_real_escape_string($_POST['name'])."', title='".mysql_real_escape_string($_POST['title'])."',
				author='".mysql_real_escape_string($_POST['author'])."',
				status='".mysql_real_escape_string($_POST['status'])."', descr='".mysql_real_escape_string($_POST['descr'])."', descr_image='".mysql_real_escape_string($file_path)."', att_project='".mysql_real_escape_string($_POST['att_project'])."' WHERE id='".mysql_real_escape_string($category_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("category name is empty");
                $content['result'] .= "Пожалуйста, задайте название категории";
            }
        }
        else
        {
            debug ("no data to update");
        }

		$result = exec_query("SELECT * FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($category_id)."'");
        $category = mysql_fetch_array($result);
		mysql_free_result($result);

        $i = 0;
		$result = exec_query("SELECT * FROM ksh_projects_statuses");
        while ($row = mysql_fetch_array($result))
        {
        	$content['statuses'][$i]['id'] = stripslashes($row['id']);
            $content['statuses'][$i]['name'] = stripslashes($row['name']);
			if (stripslashes($row['id']) == $category['status']) $content['statuses'][$i]['selected'] = "selected";
            else $content['statuses'][$i]['selected'] = "";
            $i++;
        }

		$content['category_id'] = stripslashes($category['id']);
		$content['name'] = stripslashes($category['name']);
		$content['title'] = stripslashes($category['title']);
		$content['author'] = stripslashes($category['author']);
		$content['descr'] = stripslashes($category['descr']);
		$content['image'] = stripslashes($category['descr_image']);
		$content['att_project'] = stripslashes($category['att_project']);

    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_categories_edit ***");
    return $content;
}

?>