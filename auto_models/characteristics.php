<?php

// Characteristics functions of the "auto_models" module


function auto_models_characteristics_view()
{
	debug ("*** auto_models_characteristics_view ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'if_show_admin_link' => '',
		'full_text' => '',
		'model' => '',
		'model_title' => ''
    );

	if (1 == $user['id'])
	{
		debug ("user has admin rights");
		$content['if_show_admin_link'] = "yes";
	}
	else
	{
		debug ("user doesn't have admin rights");
	}

	if (isset($_GET['model']))
		$model_id = $_GET['model'];
	else
		$model_id = 0;

	$content['model'] = $model_id;

	$sql_query = "SELECT title FROM ksh_auto_models WHERE id='".mysql_real_escape_string($model_id)."'";
	$content['model_title'] = stripslashes(mysql_result(exec_query($sql_query), 0, 0));

	$sql_query = "SELECT full_text FROM ksh_auto_models_characteristics WHERE model='".mysql_real_escape_string($model_id)."'";
	$result = exec_query($sql_query);
	if ($result)
	{
		$model = mysql_fetch_array($result);
		//stripslashes($model);
		$content['full_text'] = stripslashes($model['full_text']);
	}
	mysql_free_result($result);

	debug ("*** end: auto_models_characteristics_view ***");
    return $content;
}

function auto_models_characteristics_edit()
{
	debug ("*** auto_models_characteristics_edit ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
		'result' => '',
		'if_show_edit_form' => '',
		'model' => '',
		'model_title' => '',
		'full_text' => ''
    );

	if (1 == $user['id'])
	{
		debug ("user has admin rights");
		$content['if_show_edit_form'] = "yes";

		if (isset($_GET['model']))
			$model_id = $_GET['model'];
		else if (isset($_POST['model']))
			$model_id = $_POST['model'];
		else
			$model_id = 0;

		$content['model'] = $model_id;

		$sql_query = "SELECT title FROM ksh_auto_models WHERE id='".mysql_real_escape_string($model_id)."'";
		$content['model_title'] = stripslashes(mysql_result(exec_query($sql_query), 0, 0));

		if (isset($_POST['do_save']))
		{
			$sql_query = "SELECT count(*) FROM ksh_auto_models_characteristics WHERE model='".mysql_real_escape_string($model_id)."'";
			$result = exec_query($sql_query);
			$if_record_exist = mysql_result($result, 0, 0);
			mysql_free_result($result);

			if ($if_record_exist > 0)
				$sql_query = "UPDATE ksh_auto_models_characteristics SET full_text='".mysql_real_escape_string($_POST['full_text'])."' WHERE model='".mysql_real_escape_string($model_id)."'";
			else
				$sql_query = "INSERT INTO ksh_auto_models_characteristics (model, full_text) VALUES ('".mysql_real_escape_string($model_id)."', '".mysql_real_escape_string($_POST['full_text'])."')";
			exec_query($sql_query);
			$content['result'] = "Запись сохранена";
		}

		$sql_query = "SELECT full_text FROM ksh_auto_models_characteristics WHERE model='".mysql_real_escape_string($model_id)."'";
		$result = exec_query($sql_query);
		if ($result)
		{
			$model = mysql_fetch_array($result);
			//stripslashes($model);
			$content['full_text'] = stripslashes($model['full_text']);
		}
		mysql_free_result($result);

	}
	else
	{
		debug ("user doesn't have admin rights");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	debug ("*** end: auto_models_characteristics_edit ***");
    return $content;
}

?>
