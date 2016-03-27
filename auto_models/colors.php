<?php

// colors functions of the auto_models module


include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function auto_models_colors_view()
{
	debug ("*** auto_models_colors_view ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'if_show_add_link' => '',
		'colors' => ''
    );

	$model_id = $_GET['model'];

	$sql_query = "SELECT title FROM ksh_auto_models WHERE id='".mysql_real_escape_string($model_id)."'";
	$result = exec_query ($sql_query);
	$model = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['model_id'] = $model_id;
	$content['model_title'] = stripslashes($model['title']);

	if (1 == $user['id'])
	{
		$content['if_show_add_link'] = "yes";

		if (isset($_POST['do_del']))
		{
			$sql_query = "DELETE FROM ksh_auto_models_colors WHERE id='".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			$content['result'] = "Цвет удалён";
		}
	}

	$sql_query = "SELECT * FROM ksh_auto_models_colors WHERE model='".mysql_real_escape_string($model_id)."'";
	$result = exec_query($sql_query);
	$i = 0;
	while ($colors = mysql_fetch_array($result))
	{
		$content['colors'][$i]['id'] = stripslashes($colors['id']);
		$content['colors'][$i]['title'] = stripslashes($colors['title']);
		$content['colors'][$i]['image'] = stripslashes($colors['image']);
		$content['colors'][$i]['code'] = stripslashes($colors['code']);
		if (1 == $user['id'])
			$content['colors'][$i]['if_show_admin_link'] = "yes";
		$i++;
	}
	mysql_free_result($result);


	debug ("*** end: auto_models_colors_view ***");
    return $content;
}

function auto_models_colors_add()
{
	debug ("*** auto_models_colors_add ***");
	global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
		'if_show_add_form' => ''
    );

	if (isset($_POST['model']))
		$model_id = $_POST['model'];
	else if (isset($_GET['model']))
		$model_id = $_GET['model'];
	else
		$model_id = 0;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";
		

	$sql_query = "SELECT name,title FROM ksh_auto_models WHERE id='".mysql_real_escape_string($model_id)."'";
	$result = exec_query ($sql_query);
	$model = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['model_id'] = $model_id;
	$content['model'] = $model_id;
	$content['model_title'] = stripslashes($model['title']);
	$content['model_name'] = stripslashes($model['name']);

	if (1 == $user['id'])
	{
		$content['if_show_add_form'] = "yes";

		if ((isset($image)) && ("" != $image['name']))
		{
			debug ("there is an image to upload");
			if (file_exists($doc_root.$upl_pics_dir."auto_models/colors/".$image['name'])) $if_file_exists = 1;
			$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/colors/",$if_file_exists);
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
			$sql_query = "INSERT INTO ksh_auto_models_colors (
				model,
				title,
				image,
				code
				) VALUES (
				'".mysql_real_escape_string($_POST['model'])."',
				'".mysql_real_escape_string($_POST['title'])."',
				'".mysql_real_escape_string($file_path)."',
				'".mysql_real_escape_string($_POST['code'])."'
				)";
			exec_query($sql_query);
			$content['result'] = "Цвет добавлен";

		}
	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_colors_add ***");
    return $content;
}

function auto_models_colors_edit()
{
	debug ("*** auto_models_colors_edit ***");
	global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
		'if_show_edit_form' => '',
		'title' => '',
		'image' => '',
		'code' => ''
    );

	if (isset($_POST['colors']))
		$colors_id = $_POST['colors'];
	else if (isset($_GET['colors']))
		$colors_id = $_GET['colors'];
	else
		$colors_id = 0;

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
		$content['if_show_edit_form'] = "yes";


				if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."auto_models/colors/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/colors/",$if_file_exists);
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
		

		if (isset($_POST['do_save']))
		{
			$sql_query = "UPDATE ksh_auto_models_colors set 
				title =	'".mysql_real_escape_string($_POST['title'])."',
				image = '".mysql_real_escape_string($file_path)."',
				code = '".mysql_real_escape_string($_POST['code'])."'
				WHERE id='".mysql_real_escape_string($colors_id)."'";
			exec_query($sql_query);
			$content['result'] = "Изменения сохранены";

		}

		$sql_query = "SELECT * FROM ksh_auto_models_colors WHERE id='".mysql_real_escape_string($colors_id)."'";
		$result = exec_query ($sql_query);
		$colors = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = $colors_id;
		$content['model'] = stripslashes($colors['model']);
		$content['title'] = stripslashes($colors['title']);
		$content['image'] = stripslashes($colors['image']);
		$content['code'] = stripslashes($colors['code']);



	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_colors_edit ***");
    return $content;
}

function auto_models_colors_del()
{
	debug ("*** auto_models_colors_del ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'model' => '',
		'id' => '',
		'title' => '',
		'if_show_del_form' => ''
    );

	if (isset($_GET['colors']))
		$colors_id = $_GET['colors'];
	else
		$colors_id = 0;

	$sql_query = "SELECT * FROM ksh_auto_models_colors WHERE id='".mysql_real_escape_string($colors_id)."'";
	$result = exec_query($sql_query);
	$colors = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['model'] = stripslashes($colors['model']);
	$content['id'] = $colors_id;
	$content['title'] = stripslashes($colors['title']);


	if (1 == $user['id'])
	{
		debug ("user has admin rights");
		$content['if_show_del_form'] = "yes";
	}
	else
	{
		debug ("user doesn't have admin rights");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end: auto_models_colors_del ***");
    return $content;
}

?>

