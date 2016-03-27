<?php

// images functions of the auto_models module


include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function auto_models_images_view()
{
	debug ("*** auto_models_images_view ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'if_show_add_link' => '',
		'images' => ''
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
			$sql_query = "DELETE FROM ksh_auto_models_images WHERE id='".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			$content['result'] = "Фотография удалена";
		}
	}

	$sql_query = "SELECT * FROM ksh_auto_models_images WHERE model='".mysql_real_escape_string($model_id)."'";
	$result = exec_query($sql_query);
	$i = 0;
	while ($images = mysql_fetch_array($result))
	{
		$content['images'][$i]['id'] = stripslashes($images['id']);
		$content['images'][$i]['title'] = stripslashes($images['title']);
		$content['images'][$i]['image'] = stripslashes($images['image']);
		$content['images'][$i]['descr'] = stripslashes($images['descr']);
		if (1 == $user['id'])
			$content['images'][$i]['if_show_admin_link'] = "yes";
		$i++;
	}
	mysql_free_result($result);


	debug ("*** end: auto_models_images_view ***");
    return $content;
}

function auto_models_images_add()
{
	debug ("*** auto_models_images_add ***");
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
			if (file_exists($doc_root.$upl_pics_dir."auto_models/images/".$image['name'])) $if_file_exists = 1;
			$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/images/",$if_file_exists);
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
			$sql_query = "INSERT INTO ksh_auto_models_images (
				model,
				title,
				image,
				descr
				) VALUES (
				'".mysql_real_escape_string($_POST['model'])."',
				'".mysql_real_escape_string($_POST['title'])."',
				'".mysql_real_escape_string($file_path)."',
				'".mysql_real_escape_string($_POST['descr'])."'
				)";
			exec_query($sql_query);
			$content['result'] = "Фотография добавлена";

		}
	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_images_add ***");
    return $content;
}

function auto_models_images_edit()
{
	debug ("*** auto_models_images_edit ***");
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
		'descr' => ''
    );

	if (isset($_POST['images']))
		$images_id = $_POST['images'];
	else if (isset($_GET['images']))
		$images_id = $_GET['images'];
	else
		$images_id = 0;

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
                    if (file_exists($doc_root.$upl_pics_dir."auto_models/images/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/images/",$if_file_exists);
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
			$sql_query = "UPDATE ksh_auto_models_images set 
				title =	'".mysql_real_escape_string($_POST['title'])."',
				image = '".mysql_real_escape_string($file_path)."',
				descr = '".mysql_real_escape_string($_POST['descr'])."'
				WHERE id='".mysql_real_escape_string($images_id)."'";
			exec_query($sql_query);
			$content['result'] = "Изменения сохранены";

		}

		$sql_query = "SELECT * FROM ksh_auto_models_images WHERE id='".mysql_real_escape_string($images_id)."'";
		$result = exec_query ($sql_query);
		$images = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = $images_id;
		$content['model'] = stripslashes($images['model']);
		$content['title'] = stripslashes($images['title']);
		$content['image'] = stripslashes($images['image']);
		$content['descr'] = stripslashes($images['descr']);



	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_images_edit ***");
    return $content;
}

function auto_models_images_del()
{
	debug ("*** auto_models_images_del ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'model' => '',
		'id' => '',
		'title' => '',
		'if_show_del_form' => ''
    );

	if (isset($_GET['images']))
		$images_id = $_GET['images'];
	else
		$images_id = 0;

	$sql_query = "SELECT * FROM ksh_auto_models_images WHERE id='".mysql_real_escape_string($images_id)."'";
	$result = exec_query($sql_query);
	$images = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['model'] = stripslashes($images['model']);
	$content['id'] = $images_id;
	$content['title'] = stripslashes($images['title']);


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

	debug ("*** end: auto_models_images_del ***");
    return $content;
}

?>

