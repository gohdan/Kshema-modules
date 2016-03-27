<?php

// Projects administration functions of the "projects" module

include_once ($mods_dir."/files/index.php"); // to upload pictures

function projects($category)
{
    debug("*** projects ***");
    global $user;
    $content = "";
    $projects = 2;

    debug ("category name: ".$category);
    $category_id = mysql_result(exec_query("SELECT id FROM ksh_projects_categories WHERE name='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category id: ".$category_id);
    $result = exec_query("SELECT * FROM ksh_projects WHERE category='".mysql_real_escape_string($category_id)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($projects)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show projects ".$row['id']);
        $content .= "<tr><td>";

        if ("" != $row['descr_image']) $content .= "<img src=\"".stripslashes($row['descr_image'])."\" style=\"clear: right; float: left; margin-right: 5px\">";

        $content['projects'] .= "
                    <a href=\"/index.php?module=projects&action=view&projects=".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/index.php?module=projects&action=view&projects=".$row['id']."\">".stripslashes($row['name'])."</a><br>
        ";
        if ("" != $row['descr']) $content .= $row['descr'];
        else $content .= substr(stripslashes($row['full_text']), 0, 100)."...";
        $content .= "<br>
                    <span class=\"more\"><a href=\"/index.php?module=projects&action=view&projects=".$row['id']."\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/index.php?module=projects&action=admin\">Администрирование</a></p>";

    return $content;
    debug("*** end: projects ***");
}

function projects_view_by_category()
{
    debug("*** projects_view_by_category ***");
	global $config;
    global $user;
	global $page_title;
    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        'projects' => '',
        'add_project_link' => '',
        'admin_link' => '',
        'edit_link' => '',
        'del_link' => ''
    );

    $i = 0;

    $category = $_GET['category'];

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have projects to delete");
            exec_query("DELETE FROM ksh_projects WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Проект успешно удалён";
        }
        else
        {
            debug ("don't have projects to delete");
        }
        $content['add_project_link'] .= "<a href=\"/index.php?module=projects&action=add_projects&category=".$category."\">Добавить проект</a>";
    }


    $content['category'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($category)."'"), 0, 0));
    debug ("category name: ".$content['category']);
    $content['category_descr'] = stripslashes(mysql_result(exec_query("SELECT descr FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($category)."'"), 0, 0));
    debug ("category description: ".$content['category_descr']);

  	$i = 0;
    $result = exec_query("SELECT * FROM ksh_projects WHERE category='".mysql_real_escape_string($category)."' ORDER BY id ASC");
    while ($row = mysql_fetch_array($result))
    {
        debug("show project ".$row['id']);

        if ("" != $row['descr_image']) $content['projects'][$i]['descr_image'] = "<img src=\"".stripslashes($row['descr_image'])."\" style=\"clear: right; float: left; margin-right: 5px\">";
        else $content['projects'][$i]['descr_image'] = "";

		$numbers = exec_query ("SELECT DISTINCT number FROM ksh_projects_files WHERE project='".mysql_real_escape_string($row['id'])."' ORDER BY number ASC");
		$content['projects'][$i]['files'] = "";
		while ($number = mysql_fetch_array($numbers))
		{
			debug ("show files number ".$number['number']);
			$files = exec_query("SELECT name, number, part, file_path FROM ksh_projects_files WHERE project='".mysql_real_escape_string($row['id'])."' AND number='".$number['number']."' ORDER BY part ASC");
			$pervonah = 1;
			while ($file = mysql_fetch_array($files))
			{
				if ($pervonah)
				{
					$content['projects'][$i]['files'] .= stripslashes($file['number']).". ".stripslashes($file['name'])." ";
					$pervonah = 0;
				}
				if ("" != $file['file_path'])
				{
					$content['projects'][$i]['files'] .= "<a href=\"".stripslashes($file['file_path'])."\">";
					debug ("trying to detect file size by local path");
					$file_realpath = $config['base']['doc_root']."/".$file['file_path'];
					debug ("file real path: ".$file_realpath);

					if (file_exists($file_realpath))
					{
						$filesize = filesize($file_realpath);
						debug ("file size: ".$filesize);
						$content['projects'][$i]['files'] .= " (".round(($filesize/1024/1024), 2)." Мб)";
					}
					else
					{
						debug ("didn't work, trying to detect file size by URL");
						$url = parse_url($file['file_path']);
						$file_realpath = $config['projects']['another_doc_root'].urldecode($url['path']);
						debug ("file real path: ".$file_realpath);

						if (file_exists($file_realpath))
						{
							$filesize = filesize($file_realpath);
							debug ("file size: ".$filesize."<br>");
							$content['projects'][$i]['files'] .= " (".round(($filesize/1024/1024), 2)." Мб)";
						}
						else
							debug ("didn't work; can't determine file size!");
					}
					$content['projects'][$i]['files'] .= "</a>";
				}
				else $content['projects'][$i]['files'] .= stripslashes($file['name']);
			}
			$content['projects'][$i]['files'] .= "<br>";
		}
		mysql_free_result($numbers);

		$content['projects'][$i]['attached_categories'] = "";
		$attached_categories = exec_query("SELECT * FROM ksh_projects_categories WHERE att_project='".mysql_real_escape_string($row['id'])."'");
		while ($att_category = mysql_fetch_array($attached_categories))
		{
			$content['projects'][$i]['attached_categories'] .= "<a href=\"/index.php?module=projects&action=view_by_category&category=".stripslashes($att_category['id'])."\">".stripslashes($att_category['title'])."</a> - отдельная история<br>";
		}
		mysql_free_result($attached_categories);

        if (1 == $user['id'])
        {
            $content['projects'][$i]['edit_link'] = "<a href=\"/index.php?module=projects&action=edit&projects=".$row['id']."\">Редактировать</a>";
            $content['projects'][$i]['del_link'] = "<a href=\"/index.php?module=projects&action=files_view_by_project&project=".$row['id']."\">Файлы</a>&nbsp;<a href=\"/index.php?module=projects&action=del&projects=".$row['id']."\">Удалить</a>";
        }
        $i++;
    }
    mysql_free_result($result);



    if (1 == $user['id']) $content['admin_link'] .= "<a href=\"/index.php?module=projects&action=admin\">Администрирование</a>";

	$page_title .= " | ".$content['category'];

    return $content;
    debug("*** end: projects_view_by_category ***");
}


function projects_add()
{
    debug ("*** projects_add ***");
	global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

    $content = array(
    	'content' => '',
        'result' => '',
        'categories_select' => ''
    );

    $i = 0;


	if (isset($_GET['category'])) $category_id = $_GET['category'];
	else if (isset($_POST['category'])) $category_id = $_POST['category'];
	else $category_id = 1;
	debug ("category id: ".$category_id);

    $i = 0;
    $result = exec_query("SELECT * FROM ksh_projects_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = stripslashes($category['id']);
        $content['categories_select'][$i]['name'] = stripslashes($category['name']);

        if ($category['id'] == $category_id)
		{
			$content['categories'][$i]['selected'] = "selected";
			$category_name = $category['name'];
			debug ("category name: ".$category['name']);
		}
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
			$project_dir = $config['base']['doc_root']."/uploads/projects/".$category_name."/".$_POST['name'];
			debug ("project dir: ".$project_dir);
			if (file_exists($project_dir))
			{
				debug ("project dir already exists!");
			}
			else
			{
				debug ("project dir doesn't exist");
				mkdir ($project_dir, 0777);
			}


				if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($project_dir."/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."projects/".$category_name."/".$_POST['name']."/",$if_file_exists);
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


            debug ("have data to add");
            if ("" != $_POST['name'])
            {
                debug ("projects name isn't empty");
                exec_query("INSERT INTO ksh_projects (name, title, category, descr_image, descr, date) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['title'])."', '".mysql_real_escape_string($_POST['category'])."',
                '".mysql_real_escape_string($file_path)."', '".mysql_real_escape_string($_POST['descr'])."', CURDATE())");
                $content['result'] .= "Проект добавлен";
            }
            else
            {
                debug ("projects name is empty");
                $content['result'] .= "Пожалуйста, задайте название проекта";
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

    debug ("*** end: projects_add ***");
    return $content;
}

function projects_edit()
{
    debug ("*** projects_edit ***");
	global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'name' => '',
        'title' => '',
        'descr' => '',
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

    if (isset($_GET['projects'])) $projects_id =$_GET['projects'];
    else if (isset($_POST['id'])) $projects_id =$_POST['id'];
    else $projects_id =0;
    debug ("projects id: ".$projects_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_update']))
        {
                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."projects/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."projects/",$if_file_exists);
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


            debug ("have data to update");
            if ("" != $_POST['name'])
            {
                debug ("projects name isn't empty");
                exec_query("UPDATE ksh_projects set name='".mysql_real_escape_string($_POST['name'])."',
				title='".mysql_real_escape_string($_POST['title'])."', category='".mysql_real_escape_string($_POST['category'])."', descr_image='".mysql_real_escape_string($file_path)."',  descr='".mysql_real_escape_string($_POST['descr'])."' WHERE id='".mysql_real_escape_string($projects_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("projects name is empty");
                $content['result'] .= "Пожалуйста, задайте название проекта";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_projects WHERE id='".mysql_real_escape_string($projects_id)."'");
            $projects = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['name'] = stripslashes($projects['name']);
			$content['title'] = stripslashes($projects['title']);
            $content['image'] = stripslashes($projects['descr_image']);
            $content['descr'] = stripslashes($projects['descr']);
            $content['id'] = stripslashes($projects['id']);

            $i = 0;
            $result = exec_query("SELECT * FROM ksh_projects_categories");
            while ($category = mysql_fetch_array($result))
            {
                debug ("show category ".$category['id']);
                $content['categories_select'][$i]['id'] = stripslashes($category['id']);
                $content['categories_select'][$i]['name'] = stripslashes($category['name']);

                if ($category['id'] == $projects['category'])
                    $content['categories'][$i]['selected'] = "selected";
                else
                	$content['categories'][$i]['selected'] = "";
            }
            mysql_free_result($result);

    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_edit ***");
    return $content;
}

function projects_del()
{
    debug ("*** fn: projects_del ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'id' => '',
        'name' => '',
        'category_id' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_projects WHERE id='".mysql_real_escape_string($_GET['projects'])."'");
        $projects = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($projects['id']);
        $content['name'] = stripslashes($projects['name']);
        $content['category_id'] = stripslashes($projects['category']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['result'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_del ***");
    return $content;
}

function projects_view()
{
    debug ("*** projects_view ***");
	global $config;
	global $page_title;

    $content = array(
    	'content' => '',
        'name' => '',
        'title' => '',
        'date' => '',
        'descr' => '',
        'category' => '',
        'category_id' => ''
    );

    $result = exec_query("SELECT * FROM ksh_projects WHERE id='".mysql_real_escape_string($_GET['projects'])."'");
    $projects = mysql_fetch_array($result);
    mysql_free_result($result);

    $content['name'] = stripslashes($projects['name']);
	$content['title'] = stripslashes($projects['title']);
    $content['date'] = stripslashes($projects['date']);
    $content['descr'] = stripslashes($projects['descr']);
    $content['category'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($projects['category'])."'"), 0, 0));
    $content['category_id'] = $projects['category'];

	$page_title .= " | ".$content['name'];

    debug ("*** end: projects_view ***");
    return $content;
}

function projects_archive()
{
    debug("*** projects_archive ***");
    global $config;
    global $user;
	global $page_title;

	$page_title .= " | Архив проектов".

    $content = array(
    	'content' => '',
        'projects' => '',
        'admin_link' => ''
    );

    $i = 0;

    $result = exec_query("SELECT * FROM ksh_projects ORDER BY id DESC");

    while ($row = mysql_fetch_array($result))
    {
        debug("show project ".$row['id']);
        $content['projects'][$i]['descr_image'] = stripslashes($row['descr_image']);
        $content['projects'][$i]['files'] = "";
        if (1 == $user['id'])
        {
        	$content['projects'][$i]['edit_link'] = "";
            $content['projects'][$i]['del_link'] = "";
        }
        $i++;
    }
    mysql_free_result($result);

    if (1 == $user['id']) $content['admin_link'] .= "<a href=\"/index.php?module=projects&action=admin\">Администрирование</a>";

    return $content;
    debug("*** end: projects_archive ***");
}


?>