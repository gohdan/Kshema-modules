<?php

// Files administration functions of the "projects" module

include_once ($mods_dir."/files/index.php"); // to upload pictures

function projects_files_view_by_project()
{
    debug("*** projects_files_view_by_project ***");
    global $user;
    global $config;
	global $page_title;
    $content = array(
    	'content' => '',
        'result' => '',
        'project' => '',
        'files' => '',
        'add_file_link' => '',
        'admin_link' => ''
    );

    $i = 0;

    $project = $_GET['project'];

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have files to delete");
            exec_query("DELETE FROM ksh_projects_files WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Файл успешно удалён";
        }
        else
        {
            debug ("don't have files to delete");
        }
        $content['add_file_link'] .= "<a href=\"/index.php?module=projects&action=files_add&project=".$project."\">Добавить файл</a>";
    }


    $content['project'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_projects WHERE id='".mysql_real_escape_string($project)."'"), 0, 0));
    debug ("project title: ".$content['project']);

    $i = 0;
		$filez = exec_query("SELECT * FROM ksh_projects_files WHERE project='".mysql_real_escape_string($project)."'");
		while ($file = mysql_fetch_array($filez))
		{
	        $content['files'][$i]['file_path'] .= "<a href=\"".stripslashes($file['file_path'])."\">".stripslashes($file['name'])."</a> ";
        	$content['files'][$i]['name'] = stripslashes($file['name']);
        	$content['files'][$i]['descr'] = stripslashes($file['descr']);

        	if (1 == $user['id'])
        	{
	        	$content['files'][$i]['edit_link'] = "<a href=\"/index.php?module=projects&action=files_edit&file=".$file['id']."\">Редактировать</a>";
            	$content['files'][$i]['del_link'] = "<a href=\"/index.php?module=projects&action=files_del&file=".$file['id']."\">Удалить</a>";
        	}
			else
			{
				$content['files'][$i]['edit_link'] = "";
				$content['files'][$i]['del_link'] = "";

			}
			$i++;

		}
		mysql_free_result($filez);



    if (1 == $user['id']) $content['admin_link'] .= "<a href=\"/index.php?module=projects&action=admin\">Администрирование</a>";

	$page_title .= " | ".$content['project'];
    debug("*** end: projects_files_view_by_project ***");
    return $content;
}

function projects_files_view_by_date()
{
    debug("*** projects_files_view_by_date ***");
    global $user;
    global $config;
	global $page_title;
	$content = array(
    	'content' => '',
        'files' => ''
    );

    $years = array();
    $months = array();
    $days = array();

	$result = exec_query("SELECT date FROM ksh_projects_files ORDER BY date DESC");
	while ($row = mysql_fetch_array($result))
	{
		$year = substr($row['date'],0,4);
		if (!in_array($year, $years)) $years[] = $year;
	}
	mysql_free_result($result);

	$content['files'] .= "<ul class=\"releases\">";
	foreach ($years as $year_idx => $year)
	{
		$content['files'] .= "<li><a href=\"/index.php?module=projects&action=files_view_by_date&year=".$year."\">".$year."</a></li>";
		if ((isset($_GET['year'])) && ($year == $_GET['year']))
		{
			$result = exec_query("SELECT date FROM ksh_projects_files WHERE date LIKE '".$year."-%' ORDER BY date DESC");
			while ($row = mysql_fetch_array($result))
			{
				$month = substr($row['date'],5,2);
				if (!in_array($month, $months)) $months[] = $month;
			}
			mysql_free_result($result);

			foreach ($months as $month_idx => $month)
			{
				$content['files'] .= "<li><a href=\"/index.php?module=projects&action=files_view_by_date&year=".$year."&month=".$month."\">".base_get_month_name($month)."</a></li>";

				if ((isset($_GET['month'])) && ($month == $_GET['month']))
				{
					$result = exec_query("SELECT date FROM ksh_projects_files WHERE date LIKE '".$year."-".$month."-%' ORDER BY date DESC");
					while ($row = mysql_fetch_array($result))
					{
						$day = substr($row['date'],8,2);
						if (!in_array($day, $days)) $days[] = $day;
					}
					mysql_free_result($result);
					$content['files'] .= "<ul class=\"releases\">";
					foreach ($days as $day_idx => $day)
					{
							$result = exec_query("SELECT name,file_path,project FROM ksh_projects_files WHERE date LIKE '".$year."-".$month."-".$day."' ORDER BY date DESC");

							while ($row = mysql_fetch_array($result))
							{
								$project_title = mysql_result(exec_query("SELECT title FROM ksh_projects WHERE id='".mysql_real_escape_string($row['project'])."'"), 0, 0);

								$category_id = mysql_result(exec_query("SELECT category FROM ksh_projects WHERE id='".mysql_real_escape_string($row['project'])."'"), 0, 0);

								$category_title = mysql_result(exec_query("SELECT title FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($category_id)."'"), 0, 0);

								$content['files'] .= "<li><a href=\"".stripslashes($row['file_path'])."\">".$day." - ".stripslashes($row['name'])." ".stripslashes($category_title)."</a></li>";
							}


					$content['files'] .= "</ul>";
					}
				}
			}
		}
	}
	$content['files'] .= "</ul>";
    debug("*** end: projects_files_view_by_date ***");
    return $content;
}


function projects_files_add()
{
    debug ("*** projects_files_add ***");
	global $config;
    global $user;

    $content = array(
    	'content' => '',
        'result' => '',
        'project' => '',
        'project_title' => ''
    );

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";


	if (isset($_GET['project'])) $project_id = $_GET['project'];
	else if (isset($_POST['project'])) $project_id = $_POST['project'];
	else $project_id = 1;
	$content['project'] = $project_id;
	debug ("project id: ".$project_id);

	$result = exec_query ("SELECT name, title, category FROM ksh_projects WHERE id='".mysql_real_escape_string($project_id)."'");
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$project_name = $row['name'];
	debug ("project name: ".$project_name);
	$content['project_title'] = $row['title'];
	debug ("project title: ".$content['project_title']);
	$category_name = mysql_result(exec_query("SELECT name FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($row['category'])."'"), 0, 0);
	debug ("category name: ".$category_name);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

			$project_dir = $config['base']['doc_root']."/uploads/projects/".$category_name."/".$project_name;
			debug ("project dir: ".$project_dir);

			if ((isset($_POST['file_path'])) && ("" != $_POST['file_path']))
			{
				debug ("using user defined file path");
				$file_path = $_POST['file_path'];

				debug ("trying to detect file mtime by local path");
				$file_realpath = $config['base']['doc_root']."/".$file_path;
				debug ("file real path: ".$file_realpath);

				if (file_exists($file_realpath))
				{
					$file_mtime = date("Y-m-d", filemtime($file_realpath));
					debug ("file mtime: ".$file_mtime."<br>");
				}
				else
				{
					debug ("didn't work, trying to detect file mtime by URL");
					$url = parse_url($file_path);
					$file_realpath = $config['projects']['another_doc_root'].urldecode($url['path']);
					debug ("file real path: ".$file_realpath);

					if (file_exists($file_realpath))
					{
						$file_mtime = date("Y-m-d", filemtime($file_realpath));
						debug ("file mtime: ".$file_mtime."<br>");
					}
					else
                    {
						debug ("didn't work; can't determine file mitme!");
                        $file_mtime = "CURDATE()";
                    }
				}

			}
			else
			{
				debug ("setting own file path");
                $file_mtime = "CURDATE()";
				if ((isset($image['name'])) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($project_dir."/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."projects/".$category_name."/".$project_name."/",$if_file_exists);
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
                    if (isset($_POST['image'])) $file_path = $_POST['image'];
                }
			}

        if (isset($_POST['do_add']))
        {
            debug ("have data to add");
            if ("" != $_POST['name'])
            {
                debug ("file name isn't empty");
                exec_query("INSERT INTO ksh_projects_files (name, number, part, project, descr, file_path, date) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['number'])."', '".mysql_real_escape_string($_POST['part'])."', '".mysql_real_escape_string($project_id)."',
				'".mysql_real_escape_string($_POST['descr'])."',
                '".mysql_real_escape_string($file_path)."', ".mysql_real_escape_string($file_mtime).")");
                $content['result'] .= "Файл добавлен";
            }
            else
            {
                debug ("file name is empty");
                $content['result'] .= "Пожалуйста, задайте название файла";
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

    debug ("*** end: projects_files_add ***");
    return $content;
}

function projects_files_edit()
{
    debug ("*** projects_files_edit ***");
	global $config;
    global $user;

    $content = array(
    	'content' => '',
        'result' => '',
        'project' => '',
        'name' => '',
        'number' => '',
		'part' => '',
        'file_path' => '',
        'date' => '',
        'id' => '',
        'descr' => ''
    );
	global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

    if (isset($_GET['file'])) $file_id =$_GET['file'];
    else if (isset($_POST['id'])) $file_id =$_POST['id'];
    else $file_id =0;
    debug ("file id: ".$file_id);


	$project_id = mysql_result(exec_query("SELECT project FROM ksh_projects_files WHERE id='".$file_id."'"), 0, 0);
	$content['project'] = $project_id;
	debug ("project id: ".$project_id);

	$result = exec_query ("SELECT name, title, category FROM ksh_projects WHERE id='".mysql_real_escape_string($project_id)."'");
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$project_name = $row['name'];
	debug ("project name: ".$project_name);
	$content['project_title'] = $row['title'];
	debug ("project title: ".$content['project_title']);
	$category_name = mysql_result(exec_query("SELECT name FROM ksh_projects_categories WHERE id='".mysql_real_escape_string($row['category'])."'"), 0, 0);
	debug ("category name: ".$category_name);


    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");

			$project_dir = $config['base']['doc_root']."/uploads/projects/".$category_name."/".$project_name;
			debug ("project dir: ".$project_dir);

			if ((isset($_POST['file_path'])) && ("" != $_POST['file_path']))
			{
				debug ("using user defined file path");
				$file_path = $_POST['file_path'];
			}
			else
			{
				debug ("setting own file path");
				if ((isset($image)) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($project_dir."/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."projects/".$category_name."/".$project_name."/",$if_file_exists);
                    debug ("size: ".filesize($home.$file_path));

                    if (filesize($home.$file_path) > $max_file_size)
                    {
                        debug ("file size > max file size!");
                        $content['result'] = "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
                        if (unlink ($home.$file_path)) debug ("file deleted");
                        else debug ("can't delete file!");
                        $file_path = "";
                    }

                    $_POST['image'] = $file_path;

                }
                else
                {
                    debug ("no image to upload");
                    if (isset($_POST['old_filepath'])) $file_path = $_POST['old_filepath'];
                }
			}

        if (isset($_POST['do_update']))
        {
            debug ("have data to update");
            if ("" != $_POST['name'])
            {
                debug ("file name isn't empty");
                exec_query("UPDATE ksh_projects_files set name='".mysql_real_escape_string($_POST['name'])."', number='".mysql_real_escape_string($_POST['number'])."', part='".mysql_real_escape_string($_POST['part'])."',
				descr='".mysql_real_escape_string($_POST['descr'])."', date='".mysql_real_escape_string($_POST['date'])."', file_path='".mysql_real_escape_string($file_path)."' WHERE id='".mysql_real_escape_string($file_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("file name is empty");
                $content['result'] .= "Пожалуйста, задайте название файла";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_projects_files WHERE id='".mysql_real_escape_string($file_id)."'");
            $file = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['name'] = stripslashes($file['name']);
			$content['date'] = stripslashes($file['date']);
            $content['file_path'] = stripslashes($file['file_path']);
            $content['descr'] = stripslashes($file['descr']);
            $content['id'] = stripslashes($file['id']);
            $content['number'] = stripslashes($file['number']);
			$content['part'] = stripslashes($file['part']);

    }
    else
    {
        debug ("user isn't admin");
        $content['result'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_files_edit ***");
    return $content;
}

function projects_files_del()
{
    debug ("*** projects_files_del ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'id' => '',
        'name' => '',
        'project' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_projects_files WHERE id='".mysql_real_escape_string($_GET['file'])."'");
        $file = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($file['id']);
        $content['name'] = stripslashes($file['name']);
        $content['project'] = stripslashes($file['project']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['result'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: projects_files_del ***");
    return $content;
}

?>