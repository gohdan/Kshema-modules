<?php

// Equipment functions of the auto_models module


include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function auto_models_equipment_view()
{
	debug ("*** auto_models_equipment_view ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'if_show_add_link' => '',
		'equipment' => ''
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
			$sql_query = "DELETE FROM ksh_auto_models_equipment WHERE id='".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			$content['result'] = "Дополнительное оборудование удалено";
		}
	}

	$sql_query = "SELECT * FROM ksh_auto_models_equipment WHERE model='".mysql_real_escape_string($model_id)."'";
	$result = exec_query($sql_query);
	$i = 0;
	while ($equipment = mysql_fetch_array($result))
	{
		$content['equipment'][$i]['id'] = stripslashes($equipment['id']);
		$content['equipment'][$i]['title'] = stripslashes($equipment['title']);
		$content['equipment'][$i]['image'] = stripslashes($equipment['image']);
		$content['equipment'][$i]['full_text'] = stripslashes($equipment['full_text']);
		if (1 == $user['id'])
			$content['equipment'][$i]['if_show_admin_link'] = "yes";
		$i++;
	}
	mysql_free_result($result);


	debug ("*** end: auto_models_equipment_view ***");
    return $content;
}

function auto_models_equipment_add()
{
	debug ("*** auto_models_equipment_add ***");
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
			if (file_exists($doc_root.$upl_pics_dir."auto_models/equipment/".$image['name'])) $if_file_exists = 1;
			$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/equipment/",$if_file_exists);
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
			$sql_query = "INSERT INTO ksh_auto_models_equipment (
				model,
				title,
				image,
				full_text
				) VALUES (
				'".mysql_real_escape_string($_POST['model'])."',
				'".mysql_real_escape_string($_POST['title'])."',
				'".mysql_real_escape_string($file_path)."',
				'".mysql_real_escape_string($_POST['full_text'])."'
				)";
			exec_query($sql_query);
			$content['result'] = "Дополнительное оборудование добавлено";

		}
	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_equipment_add ***");
    return $content;
}

function auto_models_equipment_edit()
{
	debug ("*** auto_models_equipment_edit ***");
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
		'full_text' => ''
    );

	if (isset($_POST['equipment']))
		$equipment_id = $_POST['equipment'];
	else if (isset($_GET['equipment']))
		$equipment_id = $_GET['equipment'];
	else
		$equipment_id = 0;

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
                    if (file_exists($doc_root.$upl_pics_dir."auto_models/equipment/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."auto_models/equipment/",$if_file_exists);
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
			$sql_query = "UPDATE ksh_auto_models_equipment set 
				title =	'".mysql_real_escape_string($_POST['title'])."',
				image = '".mysql_real_escape_string($file_path)."',
				full_text = '".mysql_real_escape_string($_POST['full_text'])."'
				WHERE id='".mysql_real_escape_string($equipment_id)."'";
			exec_query($sql_query);
			$content['result'] = "Изменения сохранены";

		}

		$sql_query = "SELECT * FROM ksh_auto_models_equipment WHERE id='".mysql_real_escape_string($equipment_id)."'";
		$result = exec_query ($sql_query);
		$equipment = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = $equipment_id;
		$content['model'] = stripslashes($equipment['model']);
		$content['title'] = stripslashes($equipment['title']);
		$content['image'] = stripslashes($equipment['image']);
		$content['full_text'] = stripslashes($equipment['full_text']);



	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_equipment_edit ***");
    return $content;
}

function auto_models_equipment_del()
{
	debug ("*** auto_models_equipment_del ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'model' => '',
		'id' => '',
		'title' => '',
		'if_show_del_form' => ''
    );

	if (isset($_GET['equipment']))
		$equipment_id = $_GET['equipment'];
	else
		$equipment_id = 0;

	$sql_query = "SELECT * FROM ksh_auto_models_equipment WHERE id='".mysql_real_escape_string($equipment_id)."'";
	$result = exec_query($sql_query);
	$equipment = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['model'] = stripslashes($equipment['model']);
	$content['id'] = $equipment_id;
	$content['title'] = stripslashes($equipment['title']);


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

	debug ("*** end: auto_models_equipment_del ***");
    return $content;
}

?>

