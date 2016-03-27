<?php

// Models functions of auto_models module


function auto_models_add()
{
    debug ("*** auto_models_add ***");
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
		'if_show_add_form' => '',
		'if_show_admin_link' => ''
    );

	if (1 == $user['id'])
	{
		debug ("user has administrator rights");
		$content['if_show_add_form'] = "yes";
		$content['if_show_admin_link'] = "yes";

	    if (isset($_POST['do_add']))
	    {
	        debug ("have data to insert into DB");
	        unset ($_POST['do_add']);

                if ((isset($image)) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."auto_models/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/",$if_file_exists);
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

			$sql_query = "INSERT INTO ksh_auto_models (
				name,
				category,
				title,
				template,
				full_text,
				`image`,
				`link`
			) values (
				'".mysql_real_escape_string($_POST['name'])."',
				'".mysql_real_escape_string($_POST['category'])."',
				'".mysql_real_escape_string($_POST['title'])."',
				'".mysql_real_escape_string($_POST['template'])."',
				'".mysql_real_escape_string($_POST['full_text'])."',
				'".mysql_real_escape_string($file_path)."',
				'".mysql_real_escape_string($_POST['link'])."'
			)";
			exec_query ($sql_query);
			$content['result'] = "Модель добавлена";
	    }
	    else
	        debug ("don't have data to insert into DB");

		$cat = new Category();
		$content['categories_select'] = $cat -> get_select("ksh_auto_models_categories");

	}
	else
	{
		debug ("user doesn't have administrator rights");
		$content['content'] = "Пожалуйста, войдите на сайт как администратор";
	}

    return $content;
    debug ("*** end: auto_models_add ***");
}

function auto_models_del()
{
    debug ("*** auto_models_del ***");
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
        $result = exec_query("SELECT * FROM ksh_auto_models WHERE id='".mysql_real_escape_string($_GET['model'])."'");
        $model = mysql_fetch_array($result);
        mysql_free_result($result);
		//stripslashes($model);

        $content['id'] = stripslashes($model['id']);
		$content['title'] = stripslashes($model['title']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: auto_models_del ***");
    return $content;
}


function auto_models_edit()
{
    debug ("*** auto_models_edit ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'id' => '',
        'name' => '',
        'title' => '',
        'full_text' => '',
        'template' => '',
        'image' => '',
		'link' => ''
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

    if (1 == $user['id'])
    {

        if (isset($_POST['id'])) $model_id = $_POST['id'];
        else if (isset($_GET['model'])) $model_id = $_GET['model'];
        else $page_id = 0;

        if (isset($_POST['do_update']))
        {
            debug ("have data to insert into DB");
            unset ($_POST['do_update']);


            if ("" != $image['name'])
            {
                debug ("there is an image to upload");
                if (file_exists($doc_root.$upl_pics_dir."auto_models/".$image['name'])) $if_file_exists = 1;
                $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/",$if_file_exists);
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

            $sql_query = "UPDATE ksh_auto_models set
				category = '".mysql_real_escape_string($_POST['category'])."',
				name = '".mysql_real_escape_string($_POST['name'])."',
				title = '".mysql_real_escape_string($_POST['title'])."',
				full_text = '".mysql_real_escape_string($_POST['full_text'])."',
				template = '".mysql_real_escape_string($_POST['template'])."',
				image='".mysql_real_escape_string($file_path)."',
				`link` = '".mysql_real_escape_string($_POST['link'])."'
			WHERE id='".$model_id."'";
			exec_query ($sql_query);
        }
        else
        {
            debug ("don't have data to insert into DB");
        }

        $result = exec_query("SELECT * FROM ksh_auto_models WHERE id='".mysql_real_escape_string($model_id)."'");
        $model = mysql_fetch_array($result);
        mysql_free_result($result);
		//stripslashes($model);
        $content['id'] .= stripslashes($model['id']);
		$content['category'] .= stripslashes($model['category']);
        $content['name'] .= stripslashes($model['name']);
        $content['title'] .= stripslashes($model['title']);
		$content['template'] .= stripslashes($model['template']);
        $content['full_text'] .= htmlspecialchars(stripslashes($model['full_text']));
		$content['image'] = stripslashes($model['image']);
		$content['link'] = stripslashes($model['link']);

		$cat = new Category();
		$content['categories_select'] = $cat -> get_select("ksh_auto_models_categories", $content['category']);

	}
    else
    {
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    return $content;
    debug ("*** end: auto_models_edit ***");
}

function auto_models_list()
{
	debug ("*** auto_models_list ***");
    global $config;
    $models = array();

    $result = exec_query("SELECT * FROM ksh_auto_models ORDER BY ".mysql_real_escape_string($config['auto_models']['sort_list_by'])." ASC");
    while ($row = mysql_fetch_array($result))
        $models[] = $row;
    mysql_free_result($result);
    debug ("*** end: auto_models_list ***");
    return $models;
}


function auto_models_view($page)
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
			'if_show_edit_link' => ''
        );

        $result = exec_query ("SELECT * FROM ksh_auto_models WHERE name='".mysql_real_escape_string($_GET['model'])."'");

		if (mysql_num_rows($result))
		{
			$model = mysql_fetch_array($result);
	        mysql_free_result($result);

			//stripslashes($model);

	        $config['modules']['current_id'] = stripslashes($model['id']);

			$content['id'] = stripslashes($model['id']);
	        $content['title'] = stripslashes($model['title']);
			$content['name'] = stripslashes($model['name']);
			$content['template'] = stripslashes($model['template']);
	        $content['full_text'] = stripslashes($model['full_text']);
	        $content['link'] = stripslashes($model['link']);

			$config['themes']['page_title']['element'] = $content['title'];

			if (1 == $user['id'])
				$content['if_show_edit_link'] = "yes";
/*
			$config['pages']['page_title'] = $content['title'];
			$config['pages']['page_name'] = $content['name'];
			if ("" != $model['template'])
				$config['themes']['page_tpl'] = $page['template'];
*/
		}
		else
		{
			$content['content'] = "Извините, такой модели у нас нет";
		}
        debug ("*** end: auto_models_view ***");

        return $content;
}

function auto_models_list_view()
{
        debug ("*** auto_models_list_view ***");
        global $config;
        global $user;
        $content = array(
        	'content' => '',
            'pages' => ''
        );
        $i = 0;

		if (1 == $user['id'])
		{
			debug ("user has admin rights");
			if (isset($_POST['do_del']))
            {
            	exec_query ("DELETE FROM ksh_auto_models WHERE id='".mysql_real_escape_string($_POST['id'])."'");
				$content['content'] .= "Модель успешно удалена";
            }
		}

        $models = auto_models_list();

		if (0 == count($models))
			$content['content'] .= "Моделей нет";
		else
		{
        	foreach ($models as $k => $v)
        	{
            	$content['models'][$i]['id'] = stripslashes($v['id']);
                $content['models'][$i]['name'] = stripslashes($v['name']);
                $content['models'][$i]['title'] = stripslashes($v['title']);
                if (1 == $user['id'])
                {
					$content['models'][$i]['show_id'] = "yes";
					$content['models'][$i]['show_name'] = "yes";
                	$content['models'][$i]['if_show_edit_link'] = "yes";
					$content['models'][$i]['if_show_del_link'] = "yes";
                }
                else
                {
                }
				$i++;
        	}
		}

        debug ("*** end: auto_models_list_view");

        return $content;
}


?>
