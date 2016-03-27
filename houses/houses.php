<?php

// houses administration functions of the houses module

include_once ($config['modules']['location']."/files/index.php"); // to upload pictures

function houses($category)
{
    debug("*** houses_houses ***");
    global $user;
    $content = "";
    $houses = 2;

    debug ("category name: ".$category);
    $category_id = mysql_result(exec_query("SELECT id FROM ksh_houses_categories WHERE name='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category id: ".$category_id);
    $result = exec_query("SELECT * FROM ksh_houses WHERE category='".mysql_real_escape_string($category_id)."' ORDER BY id DESC LIMIT ".mysql_real_escape_string($houses)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show houses ".$row['id']);
        $content .= "<tr><td>
        ";

        if ("" != $row['descr_image']) $content .= "<img src=\"".$row['descr_image']."\" style=\"clear: right; float: left; margin-right: 5px\">";

        $content .= "
                    <a href=\"/houses/view/".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/houses/view/".$row['id']."\">".$row['name']."</a><br>
        ";

        if ("" != $row['descr']) $content .= stripslashes($row['descr']);
        else $content .= substr(stripslashes($row['full_text'], 0, 200))."...";

        $content .= "<br>
                    <span class=\"more\"><a href=\"/houses/view/".$row['id']."\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/houses/admin/\">Администрирование</a></p>";

    return $content;
    debug("*** end: houses_houses ***");
}

function lasthouses($category)
{
    debug("*** lasthouses ***");
    global $user;
    $content = "";
    $houses = 3;

    debug ("category name: ".$category);
    $result = exec_query("SELECT * FROM ksh_houses WHERE `if_show` = 'yes' ORDER BY id DESC LIMIT ".mysql_real_escape_string($houses)."");

    $content .= "<table>";
    while ($row = mysql_fetch_array($result))
    {
        debug("show houses ".$row['id']);
        $content .= "<tr><td>
        ";

        if ("" != $row['descr_image']) $content .= "<img src=\"".$row['descr_image']."\" style=\"clear: right; float: left; margin-right: 5px\">";

        $content .= "
                    <a href=\"/houses/view/".$row['id']."\">".$row['date']."</a><br>
                    <a href=\"/houses/view/".$row['id']."\">".$row['name']."</a><br>
        ";

        if ("" != $row['descr']) $content .= stripslashes($row['descr']);
        else $content .= substr(stripslashes($row['full_text']), 0, 200)."...";

        $content .= "<br>
                    <span class=\"more\"><a href=\"/houses/view/".$row['id']."\">Подробнее...</a></span>
                </td></tr>
        ";
    }
    mysql_free_result($result);
    $content .= "</table>";

    if (1 == $user['id']) $content .= "<p><a href=\"/houses/admin/\">Администрирование</a></p>";

    return $content;
    debug("*** end: lasthouses ***");
}

function houses_hook()
{
    debug("*** houses_hook ***");
    global $user;
    global $config;
    $content = "";
    $houses = 3;

    $result = exec_query("SELECT * FROM ksh_hooks
		WHERE hook_module='houses'
		AND to_module='".mysql_real_escape_string($config['modules']['current_module'])."'
		AND to_id='".mysql_real_escape_string($config['modules']['current_id'])."'
		");
	while ($hook = mysql_fetch_array($result))
	{
		if ("category" == stripslashes($hook['hook_type']))
		{
		    $category = stripslashes($hook['hook_id']);

	    	$categories = exec_query("SELECT * FROM ksh_houses WHERE category='".mysql_real_escape_string($category)."' AND `if_show` = 'yes' ORDER BY id DESC LIMIT ".mysql_real_escape_string($houses)."");

	    	while ($row = mysql_fetch_array($categories))
	    	{
	        	debug("show houses ".$row['id']);
        		$content .= "<div style=\"text-align: center; padding: 10px 0px 0px 0px\">";
				$content .= "<a href=\"/houses/view/".stripslashes($row['id'])."\" target=\"_new\"><img src=\"".stripslashes($row['image'])."\" style=\"border: none\"></a>";
				$content .= "</div>";
	    	}
	    	mysql_free_result($categories);
		}
		else if ("houses" == stripslashes($hook['hook_type']))
		{
		    $id = stripslashes($hook['hook_id']);

	    	$categories = exec_query("SELECT * FROM ksh_houses WHERE id='".mysql_real_escape_string($id)."' AND `if_show` = 'yes' ORDER BY id DESC LIMIT ".mysql_real_escape_string($houses)."");

	    	while ($row = mysql_fetch_array($categories))
	    	{
	        	debug("show houses ".$row['id']);
        		$content .= "<div style=\"text-align: center; padding: 10px 0px 0px 0px\">";
				$content .= "<a href=\"/houses/view/".stripslashes($row['id'])."\" target=\"_new\"><img src=\"".stripslashes($row['image'])."\" style=\"border: none\"></a>";
				$content .= "</div>";
	    	}
	    	mysql_free_result($categories);
		}

	}
    mysql_free_result($result);

    if (1 == $user['id']) $content .= "<p><a href=\"/houses/admin/\">Администрирование</a></p>";

    debug("*** end: houses_hook ***");
    return $content;
}


function houses_view_by_category()
{
    debug("*** houses_view_by_category ***");
    global $user;
	global $page_title;
    global $config;
	global $upl_pics_dir;
    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        'houses' => '',
        'admin_link' => '',
        'edit_link' => '',
    );

	$i = 0;

    $category = $_GET['category'];

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have houses to delete");
            exec_query("DELETE FROM ksh_houses WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Проект успешно удален";
        }
        else
        {
            debug ("don't have houses to delete");
        }

        $content['admin_link'] .= "<a href=\"/houses/admin/\">Администрирование</a><br><a href=\"/houses/add_houses/".$category."\">Добавить проект дома</a>";
		$if_show_hidden = "yes";
    }


    // FIXME: Check if there are categories; else user has a warning
    $content['category'] = mysql_result(exec_query("SELECT title FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($category)."'"), 0, 0);
    debug ("category title: ".$content['category']);
	$category_name = mysql_result(exec_query("SELECT name FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($category)."'"), 0, 0);
	if ("yes" == $if_show_hidden)
		$sql_query = "SELECT * FROM ksh_houses WHERE category='".mysql_real_escape_string($category)."' ORDER BY id DESC";
	else
		$sql_query = "SELECT * FROM ksh_houses WHERE category='".mysql_real_escape_string($category)."' AND `if_show` = 'yes' ORDER BY id DESC";
    $result = exec_query($sql_query);

    while ($row = mysql_fetch_array($result))
    {
        debug("show houses ".$row['id']);
		$project_dir = $upl_pics_dir."houses/".$category_name."/".stripslashes($row['name'])."/";
		if ("" != $row['image'])
			$content['houses'][$i]['image'] = "<img src=\"".stripslashes($row['image'])."\">";
		else
			$content['houses'][$i]['image'] = "<img src=\"".$project_dir."3Ds.jpg\">";

        $content['houses'][$i]['id'] = stripslashes($row['id']);
        if (1 == $user['id'])
            $content['houses'][$i]['edit_link'] = stripslashes($row['id']).". ".stripslashes($row['name'])."<br><a href=\"/houses/edit/".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/houses/del/".$row['id']."\">Удалить</a>";
		$i++;
    }
    mysql_free_result($result);

	$page_title .= " | ".$content['category'];

    return $content;
    debug("*** end: houses_view_by_category ***");
}


function houses_add()
{
    debug ("*** houses_add ***");
    global $config;
    global $user;

    $content = array (
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'date' => ''
    );

    $content['date'] = date("Y-m-d");

    $i = 0;
    $result = exec_query("SELECT * FROM ksh_houses_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = $category['id'];
        $content['categories_select'][$i]['name'] = $category['name'];
        $content['categories_select'][$i]['title'] = $category['title'];
        if (((isset($_GET['category'])) && ($category['id'] == $_GET['category'])) && ((isset($_POST['category'])) && ($category['id'] == $_POST['category']))) $content['categories_select'][$i]['selected'] = " selected";
        else $content['categories_select'][$i]['selected'] = "";
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
            if ("" != $_POST['name'])
            {
                debug ("houses name isn't empty");

                $category_name = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($_POST['category'])."'"), 0, 0));
				$project_dir = $config['base']['doc_root'].$config['base']['domain_dir']."/uploads/houses/".$category_name."/".$_POST['name'];
				debug ("project dir: ".$project_dir);
				if (file_exists($project_dir))
				{
					debug ("project dir already exists!");
				}
				else
				{
					debug ("project dir doesn't exist");
					mkdir ($project_dir);
				}


                exec_query("INSERT INTO ksh_houses (
					name,
					category,
					price,
					sq_common,
					sq_balcones,
					sq_living,
					composition,
					image,
					3d,
					fasad,
					1floor_t,
					1floor,
					2floor_t,
					2floor,
					pdf,
					if_show
					) VALUES (
					'".mysql_real_escape_string($_POST['name'])."',
					'".mysql_real_escape_string($_POST['category'])."',
					'".mysql_real_escape_string($_POST['price'])."',
					'".mysql_real_escape_string($_POST['sq_common'])."',
					'".mysql_real_escape_string($_POST['sq_balcones'])."',
					'".mysql_real_escape_string($_POST['sq_living'])."',
					'".mysql_real_escape_string($_POST['composition'])."',
					'".mysql_real_escape_string(files_upload ("image", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("3d", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("fasad", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("1floor_t", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("1floor", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("2floor_t", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("2floor", $project_dir))."',
					'".mysql_real_escape_string(files_upload ("pdf", $project_dir))."',
					'".mysql_real_escape_string($_POST['if_show'])."'					
					)");
                $content['result'] .= "Проект добавлен";
            }
            else
            {
                debug ("houses name is empty");
                $content['result'] .= "Пожалуйста, задайте название проекта дома";
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

    debug ("*** end: houses_add ***");
    return $content;
}

function houses_edit()
{
    debug ("*** houses_edit ***");
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
        'descr' => '',
        'full_text' => '',
        'image' => '',
		'composition' => '',
		'if_show' => ''
    );

    if (isset($_GET['houses'])) $houses_id =$_GET['houses'];
    else if (isset($_POST['id'])) $houses_id =$_POST['id'];
    else $houses_id =0;
    debug ("houses id: ".$houses_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");
        if (isset($_POST['do_update']))
        {
            debug ("have data to update");

			$category_name = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($_POST['category'])."'"), 0, 0));
			$project_dir = $config['base']['doc_root'].$config['base']['domain_dir']."/uploads/houses/".$category_name."/".$_POST['name'];
			debug ("project dir: ".$project_dir);
			if (file_exists($project_dir))
			{
				debug ("project dir already exists!");
			}
			else
			{
				debug ("project dir doesn't exist");
				mkdir ($project_dir);
			}

            if ("" != $_POST['name'])
            {
                debug ("houses name isn't empty");
                exec_query("UPDATE ksh_houses set 
					name='".mysql_real_escape_string($_POST['name'])."',
					category='".mysql_real_escape_string($_POST['category'])."',
					price='".mysql_real_escape_string($_POST['price'])."',
					sq_common='".mysql_real_escape_string($_POST['sq_common'])."',
					sq_balcones='".mysql_real_escape_string($_POST['sq_balcones'])."',
					sq_living='".mysql_real_escape_string($_POST['sq_living'])."',
					image='".mysql_real_escape_string(files_upload ("image", $project_dir))."',
					3d='".mysql_real_escape_string(files_upload ("3d", $project_dir))."',
					fasad='".mysql_real_escape_string(files_upload ("fasad", $project_dir))."',
					1floor_t='".mysql_real_escape_string(files_upload ("1floor_t", $project_dir))."',
					1floor='".mysql_real_escape_string(files_upload ("1floor", $project_dir))."',
					2floor_t='".mysql_real_escape_string(files_upload ("2floor_t", $project_dir))."',
					2floor='".mysql_real_escape_string(files_upload ("2floor", $project_dir))."',
					pdf='".mysql_real_escape_string(files_upload ("pdf", $project_dir))."',
					composition='".mysql_real_escape_string($_POST['composition'])."',
					`if_show` = '".mysql_real_escape_string($_POST['if_show'])."'
					WHERE id='".mysql_real_escape_string($houses_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("houses name is empty");
                $content['result'] .= "Пожалуйста, задайте название проекта дома";
            }
        }
        else
        {
            debug ("no data to update");
        }

        $result = exec_query("SELECT * FROM ksh_houses WHERE id='".mysql_real_escape_string($houses_id)."'");
        $houses = mysql_fetch_array($result);
        mysql_free_result($result);

		$content['id'] = stripslashes($houses['id']);
		$content['name'] = stripslashes($houses['name']);
    	$content['image'] = stripslashes($houses['image']);
    	$content['category'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($houses['category'])."'"), 0, 0));
    	$content['category_id'] = stripslashes($houses['category']);
        $content['price'] = stripslashes($houses['price']);
    	$content['3d'] = stripslashes($houses['3d']);
    	$content['fasad'] = stripslashes($houses['fasad']);
    	$content['1floor_t'] = stripslashes($houses['1floor_t']);
    	$content['1floor'] = stripslashes($houses['1floor']);
    	$content['2floor_t'] = stripslashes($houses['2floor_t']);
    	$content['2floor'] = stripslashes($houses['2floor']);
    	$content['pdf'] = stripslashes($houses['pdf']);
    	$content['sq_common'] = stripslashes($houses['sq_common']);
    	$content['sq_balcones'] = stripslashes($houses['sq_balcones']);
    	$content['sq_living'] = stripslashes($houses['sq_living']);
		$content['composition'] = stripslashes($houses['composition']);
		$content['if_show'] = stripslashes($houses['if_show']);

        $result = exec_query("SELECT * FROM ksh_houses_categories");

        $i = 0;
        while ($category = mysql_fetch_array($result))
        {
		    debug ("show category ".$category['id']);
		    $content['categories_select'][$i]['id'] = $category['id'];
		    $content['categories_select'][$i]['name'] = $category['name'];
		    $content['categories_select'][$i]['title'] = $category['title'];
		    if ($category['id'] == $houses['category'])
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

    debug ("*** end: houses_edit ***");
    return $content;
}

function houses_del()
{
    debug ("*** houses_del ***");
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
        $result = exec_query("SELECT * FROM ksh_houses WHERE id='".mysql_real_escape_string($_GET['houses'])."'");
        $houses = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($houses['id']);
        $content['name'] = stripslashes($houses['name']);
        $content['category_id'] = stripslashes($houses['category']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: houses_del ***");
    return $content;
}

function houses_view()
{
    debug ("*** houses_view ***");
	global $config;
	global $user;
	global $page_title;
	global $upl_pics_dir;

    $content = array(
    	'name' => '',
        'price' => '',
        'date' => '',
        'descr' => '',
        'full_text' => '',
        'category' => '',
        'category_id' => '',
		'edit_link' => '',
		'del_link' => '',
		'composition' => ''
    );

    $_GET['page_template'] = "project"; // dirty hack but let it be

    $result = exec_query("SELECT * FROM ksh_houses WHERE id='".mysql_real_escape_string($_GET['houses'])."' OR name='".mysql_real_escape_string($_GET['houses'])."'");
    $houses = mysql_fetch_array($result);
    mysql_free_result($result);

	$content['category'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_houses_categories WHERE id='".$houses['category']."'"), 0, 0));
    $content['category_id'] = stripslashes($houses['category']);

	$category_name = mysql_result(exec_query("SELECT name FROM ksh_houses_categories WHERE id='".$houses['category']."'"), 0, 0);

	$content['name'] = stripslashes($houses['name']);
    $content['price'] = number_format(stripslashes($houses['price']), 0, ',', ' ');
	$content['composition'] = stripslashes($houses['composition']);

	$project_dir = $upl_pics_dir."houses/".$category_name."/".stripslashes($houses['name'])."/";

	if ("" != $houses['image'])
		$content['image'] = stripslashes($houses['image']);
	else
		$content['image'] = $project_dir."3Ds.jpg";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['image'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['image'] = "";
    }


	if ("" != $houses['3d'])
		$content['3d'] = stripslashes($houses['3d']);
	else
		$content['3d'] = $project_dir."3d.jpg";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['3d'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['3d'] = "";
    }


	if ("" != $houses['fasad'])
		$content['fasad'] = stripslashes($houses['fasad']);
	else
		$content['fasad'] = $project_dir."Fasad.jpg";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['fasad'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['fasad'] = "";
    }


	if ("" != $houses['1floor_t'])
		$content['1floor_t'] = stripslashes($houses['1floor_t']);
	else
		$content['1floor_t'] = $project_dir."Plan1.gif";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['1floor_t'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['1floor_t'] = "";
    }


	if ("" != $houses['1floor'])
		$content['1floor'] = stripslashes($houses['1floor']);
	else
		$content['1floor'] = $project_dir."Plan1_B.gif";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['1floor'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['1floor'] = "";
    }


	if ("" != $houses['2floor_t'])
		$content['2floor_t'] = stripslashes($houses['2floor_t']);
	else
		$content['2floor_t'] = $project_dir."Plan2.gif";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['2floor_t'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['2floor_t'] = "";
    }


	if ("" != $houses['2floor'])
		$content['2floor'] = stripslashes($houses['2floor']);
	else
		$content['2floor'] = $project_dir."Plan2_B.gif";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['2floor'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['2floor'] = "";
    }

	if ("" != $houses['pdf'])
		$content['pdf'] = stripslashes($houses['pdf']);
	else
		$content['pdf'] = $project_dir.$content['name'].".pdf";
    $file_path = $config['base']['doc_root'].$config['base']['domain_dir'].$content['pdf'];
    debug ("cheking existence of ".$file_path);
    if (file_exists($file_path))
    	debug ("file exists");
    else
   	{
    	debug ("file doesn't exist");
    	$content['pdf'] = "";
    }


    $content['sq_common'] = stripslashes($houses['sq_common']);
    $content['sq_balcones'] = stripslashes($houses['sq_balcones']);
    $content['sq_living'] = stripslashes($houses['sq_living']);

	if (1 == $user['id'])
	{
		$content['edit_link'] = "<a href=\"/houses/edit/".stripslashes($houses['id'])."\">Редактировать</a>";
		$content['del_link'] = "<a href=\"/houses/del/".stripslashes($houses['id'])."\">Удалить</a>";
	}
	else
	{
		$content['edit_link'] = "";
		$content['del_link'] = "";
	}

	$page_title .= " | ".$content['name'];

    debug ("*** end: houses_view ***");
    return $content;
}

function houses_view_short()
{
    debug ("*** houses_view_short ***");
	global $config;
	global $user;

    $content = array(
    	'name' => '',
        'price' => '',
        'date' => '',
        'descr' => '',
        'full_text' => '',
		'composition' => ''
    );

    $result = exec_query("SELECT * FROM ksh_houses WHERE id='".mysql_real_escape_string($_GET['houses'])."' OR name='".mysql_real_escape_string($_GET['houses'])."'");
    $houses = mysql_fetch_array($result);
    mysql_free_result($result);


	$content['name'] = stripslashes($houses['name']);
    $content['price'] = number_format(stripslashes($houses['price']), 0, ',', ' ');
	$content['composition'] = stripslashes($houses['composition']);
    $content['sq_common'] = stripslashes($houses['sq_common']);
    $content['sq_balcones'] = stripslashes($houses['sq_balcones']);
    $content['sq_living'] = stripslashes($houses['sq_living']);

    debug ("*** end: houses_view_short ***");
    return $content;
}


function houses_archive()
{
    debug("*** houses_archive ***");
    global $config;
    global $user;
	global $page_title;

	$page_title .= " | Архив проектов домов";
    $content = array(
    	'content' => '',
        'houses' => '',
        'admin_link' => ''
    );

    $result = exec_query("SELECT * FROM ksh_houses ORDER BY id DESC");

    $i = 0;
    while ($row = mysql_fetch_array($result))
    {
        debug("show houses ".$row['id']);
        $content['houses'][$i]['id'] = $row['id'];
        $content['houses'][$i]['date'] = $row['date'];
        $content['houses'][$i]['name'] = $row['name'];

        if ("" != $row['descr']) $content['houses'][$i]['descr'] = stripslashes($row['descr']);
        else $content['houses'][$i]['descr'] = substr($row['full_text'], 0, 100)."...";

        $content['houses'][$i]['full_text'] = stripslashes($row['full_text']);

        $content['houses'][$i]['more'] = "<span class=\"more\"><a href=\"/houses/view/".$row['id']."\">Подробнее...</a></span>";
        $content['houses'][$i]['edit_link'] = "";
        $content['houses'][$i]['descr_image'] = "";
        $i++;
    }
    mysql_free_result($result);

    if (1 == $user['id']) $content['admin_link'] .= "<a href=\"/houses/admin/\">Администрирование</a>";

    return $content;
    debug("*** end: houses_archive ***");
}

function houses_search()
{
    debug ("*** houses_search ***");
    global $config;
    global $upl_pics_dir;
    global $user;
	global $page_title;
    $content = array(
    	'result' => '',
    	'content' => '',
        'type' => '',
        'houses' => '',
        'sq_from' => '',
        'sq_to' => '',
		'page_template' => '',
		'id' => ''
    );

	$config['modules']['current_module'] = "houses";
	if (isset($_GET['id']))
	{
		$config['modules']['current_id'] = $_GET['id'];
		$content['id'] = $_GET['id'];
	}
	else
		$config['modules']['current_id'] = 0;

	if (isset($_GET['page_template'])) $content['page_template'] = $_GET['page_template'];

	$config['pages']['page_title'] = "Поиск в каталоге";
	$config['pages']['page_name'] = "search";


    if (isset($_GET['type'])) $content['type'] = $_GET['type'];
    if (isset($_POST['sq_from'])) $content['sq_from'] = $_POST['sq_from'];
    if (isset($_POST['sq_to'])) $content['sq_to'] = $_POST['sq_to'];

    if (isset($_POST['do_search']))
    {
    	debug ("doing search");
	    $result = exec_query("SELECT * FROM ksh_houses 
			WHERE sq_common >= '".mysql_real_escape_string($_POST['sq_from'])."'
			AND sq_common <= '".mysql_real_escape_string($_POST['sq_to'])."'
			AND name LIKE '".mysql_real_escape_string($content['type'])."%'
			AND `if_show` = 'yes'
			ORDER BY sq_common DESC");

   		$i = 0;
	    while ($row = mysql_fetch_array($result))
	    {
	        debug("show houses ".$row['id']);

            $category_name = mysql_result(exec_query("SELECT name FROM ksh_houses_categories WHERE id='".$row['category']."'"), 0, 0);
			$project_dir = $upl_pics_dir."houses/".$category_name."/".stripslashes($row['name'])."/";

			if ("" != $row['image'])
				$content['houses'][$i]['image'] = "<img src=\"".stripslashes($row['image'])."\">";
			else
            	$content['houses'][$i]['image'] = "<img src=\"".$project_dir."3Ds.jpg\">";

            $content['houses'][$i]['id'] = stripslashes($row['id']);
			$content['houses'][$i]['name'] = stripslashes($row['name']);
		    $content['houses'][$i]['price'] = number_format(stripslashes($row['price']), 0, ',', ' ');
            $content['houses'][$i]['sq_common'] = stripslashes($row['sq_common']);
            $content['houses'][$i]['sq_balcones'] = stripslashes($row['sq_balcones']);
            $content['houses'][$i]['sq_living'] = stripslashes($row['sq_living']);

	        if (1 == $user['id'])
	            $content['houses'][$i]['edit_link'] = "<a href=\"/houses/edit/".$row['id']."\">Редактировать</a>&nbsp;<a href=\"/houses/del/".$row['id']."\">Удалить</a>";
	        else
            	$content['houses'][$i]['edit_link'] = "";
			$i++;
	    }
	    mysql_free_result($result);
    }
    else
    	debug ("not doing search");

	debug("content:");
	dump($content);

    debug ("*** end: houses_search");
    return $content;
}

function houses_read_csv()
{
	debug ("*** houses_read_csv ***");
    global $config;
    global $user;
    $content = array(
    	'content' => '',
        'result' => ''
    );

	$file_path = $config['base']['doc_root'].$config['base']['domain_dir']."/modules/houses/db.csv";
	debug ("CSV file path: ".$file_path);
	$handle = fopen ($file_path,"r");
	while ($data = fgetcsv ($handle, 1000, ";"))
    {
        $content['id'] = $data[0];
        $content['num'] = $data[1];
        $content['choice'] = $data[2];
        $content['flache'] = $data[3];
        $content['floors'] = $data[4];
        $content['lflache'] = $data[5];
        $content['hightroom'] = $data[6];
        $content['bauflache'] = $data[7];
        $content['flat'] = $data[8];
        $content['ter_balk'] = $data[9];
        if ("P" == substr($content['choice'], 0, 1)) $content['category'] = 4;
        else if ("B" == substr($content['choice'], 0, 1)) $content['category'] = 5;
        else $content['category'] = 6;
        exec_query("INSERT INTO ksh_houses (category, name, sq_common, sq_living, sq_balcones, floors) values ('".mysql_real_escape_string($content['category'])."', '".mysql_real_escape_string($content['choice'])."', '".mysql_real_escape_string($content['flache'])."', '".mysql_real_escape_string($content['lflache'])."', '".mysql_real_escape_string($content['ter_balk'])."', '".mysql_real_escape_string($content['floors'])."')");
	}
	fclose ($handle);

    debug ("*** end: houses_read_csv ***");
    return $content;
}

?>
