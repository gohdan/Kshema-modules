<?php

// videos functions of the auto_models module


function auto_models_videos_view()
{
	debug ("*** auto_models_videos_view ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'if_show_add_link' => '',
		'videos' => ''
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
			$sql_query = "DELETE FROM ksh_auto_models_videos WHERE id='".mysql_real_escape_string($_POST['id'])."'";
			exec_query($sql_query);
			$content['result'] = "Ролик удален";
		}
	}

	$sql_query = "SELECT * FROM ksh_auto_models_videos WHERE model='".mysql_real_escape_string($model_id)."'";
	$result = exec_query($sql_query);
	$i = 0;
	while ($videos = mysql_fetch_array($result))
	{
		$content['videos'][$i]['id'] = stripslashes($videos['id']);
		$content['videos'][$i]['title'] = stripslashes($videos['title']);
		$content['videos'][$i]['video'] = stripslashes($videos['video']);
		$content['videos'][$i]['descr'] = stripslashes($videos['descr']);
		if (1 == $user['id'])
			$content['videos'][$i]['if_show_admin_link'] = "yes";
		$i++;
	}
	mysql_free_result($result);


	debug ("*** end: auto_models_videos_view ***");
    return $content;
}

function auto_models_videos_add()
{
	debug ("*** auto_models_videos_add ***");
	global $config;
    global $user;

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

		if (isset($_POST['do_add']))
		{
			$sql_query = "INSERT INTO ksh_auto_models_videos (
				model,
				title,
				video,
				descr
				) VALUES (
				'".mysql_real_escape_string($_POST['model'])."',
				'".mysql_real_escape_string($_POST['title'])."',
				'".mysql_real_escape_string($_POST['video'])."',
				'".mysql_real_escape_string($_POST['descr'])."'
				)";
			exec_query($sql_query);
			$content['result'] = "Ролик добавлен";

		}
	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_videos_add ***");
    return $content;
}

function auto_models_videos_edit()
{
	debug ("*** auto_models_videos_edit ***");
	global $config;
    global $user;

    $content = array(
    	'content' => '',
		'if_show_edit_form' => '',
		'title' => '',
		'video' => '',
		'descr' => ''
    );

	if (isset($_POST['videos']))
		$videos_id = $_POST['videos'];
	else if (isset($_GET['videos']))
		$videos_id = $_GET['videos'];
	else
		$videos_id = 0;

    
	if (1 == $user['id'])
	{
		$content['if_show_edit_form'] = "yes";

		if (isset($_POST['do_save']))
		{
			$sql_query = "UPDATE ksh_auto_models_videos set 
				title =	'".mysql_real_escape_string($_POST['title'])."',
				video = '".mysql_real_escape_string($_POST['video'])."',
				descr = '".mysql_real_escape_string($_POST['descr'])."'
				WHERE id='".mysql_real_escape_string($videos_id)."'";
			exec_query($sql_query);
			$content['result'] = "Изменения сохранены";

		}

		$sql_query = "SELECT * FROM ksh_auto_models_videos WHERE id='".mysql_real_escape_string($videos_id)."'";
		$result = exec_query ($sql_query);
		$videos = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = $videos_id;
		$content['model'] = stripslashes($videos['model']);
		$content['title'] = stripslashes($videos['title']);
		$content['video'] = stripslashes($videos['video']);
		$content['descr'] = stripslashes($videos['descr']);



	}
	else
	{
		$content['content'] = "Пожалуйста, войдите как администратор";
	}


	debug ("*** end: auto_models_videos_edit ***");
    return $content;
}

function auto_models_videos_del()
{
	debug ("*** auto_models_videos_del ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'model' => '',
		'id' => '',
		'title' => '',
		'if_show_del_form' => ''
    );

	if (isset($_GET['videos']))
		$videos_id = $_GET['videos'];
	else
		$videos_id = 0;

	$sql_query = "SELECT * FROM ksh_auto_models_videos WHERE id='".mysql_real_escape_string($videos_id)."'";
	$result = exec_query($sql_query);
	$videos = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['model'] = stripslashes($videos['model']);
	$content['id'] = $videos_id;
	$content['title'] = stripslashes($videos['title']);


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

	debug ("*** end: auto_models_videos_del ***");
    return $content;
}

?>

