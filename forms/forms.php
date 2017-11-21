<?php

// Forms function of the forms module

function forms_submit()
{
    debug ("*** forms_submit ***");
    global $config;
    global $user;

    $content = array(
    	'content' => '',
		'result' => '',
		'fields' => '',
		'values' => '',
		'formname' => '',
		'fio' => '',
		'email' => ''
    );

	$content['fio'] = $_POST['fio'];
	$content['email'] = $_POST['email'];

	unset ($_POST['do_submit']);
	unset ($_POST['do_send']);
    foreach ($_POST as $k => $v)
    {
    	debug ($k.":".$v);
        $fields .= str_replace("|", "_", $k)."|";
        $values .= str_replace("|", "_", $v)."|";
    }

    $fields = substr_replace ($fields, '', -1, 1);
    $values = substr_replace ($values, '', -1, 1);

	$content['fields'] = $fields;
	$content['values'] = $values;

	debug ("fields: ".$fields);
    debug ("values: ".$values);

	$fields = explode ("|", $fields);
	$values = explode ("|", $values);

	$content['formname'] = $_GET['formname'];

	$result = exec_query ("SELECT flds_names, flds_descrs FROM ksh_forms WHERE name='".$_GET['formname']."'");
	$flds = mysql_fetch_array($result);
	mysql_free_result($result);
	$flds['flds_names'] = explode("|", $flds['flds_names']);
	array_pop($flds['flds_names']);
	array_pop($flds['flds_descrs']);
	$flds['flds_descrs'] = explode("|", $flds['flds_descrs']);
	foreach ($flds['flds_names'] as $k => $v)
	{
		$keys[$k]['name'] = stripslashes($v);
		$keys[$k]['value'] = stripslashes($flds['flds_descrs'][$k]);
	}

	debug ("fields keys: ");
	foreach ($keys as $k => $v)
		debug ($v['name'].":".$v['value']);

	foreach ($fields as $k => $v)
	{
		debug ("processing field ".$k.", value: ".$v);
		$fld = stripslashes($v);
		foreach ($keys as $s => $x)
		{
			if ($v == $x['name'])
				$fld = $x['value'];
		}
		debug ("fld: ".$fld);
		debug ("value: ".$values[$k]);
		if ("" != $values[$k])
			$content['form'] .= $fld." : ".stripslashes($values[$k])."<br>";
	}

	$content['form'] = str_replace("yes", "да", $content['form']);
	$content['form'] = str_replace("no", "нет", $content['form']);


    debug ("*** end: forms_submit ***");
    return $content;
}

function forms_send()
{
    debug ("*** forms_send ***");
    global $config;
    global $user;

    $content = array(
    	'content' => '',
		'result' => ''
    );

	$fields = $_POST['fields'];
    $values = $_POST['values'];

    if (isset($_POST['formname']))
    {
    	$formname = $_POST['formname'];
        unset ($_POST['formname']);
    }
    else if (isset($_GET['formname']))
    {
    	$formname = $_GET['formname'];
    }
    else $formname = "";

	$formtype = stripslashes(mysql_result(exec_query("SELECT id FROM ksh_forms WHERE name='".mysql_real_escape_string($formname)."'"), 0, 0));
	debug ("form type: ".$formtype);

	$config['modules']['current_module'] = "forms";
	debug ("GET form id: ".$_GET['form_id']);
	if (isset($_GET['form_id']))
		$config['modules']['current_id'] = $_GET['form_id'];
	else if (isset($_POST['form_id']))
		$config['modules']['current_id'] = $_POST['form_id'];
	else
		$config['modules']['current_id'] = 0;

	debug ("modules: current id: ".$config['modules']['current_id']);

    unset ($_POST['do_submit']);
	unset ($_POST['do_send']);
	unset ($_POST['send']);

	/*
    foreach ($_POST as $k => $v)
    {
    	debug ($k.":".$v);
        $fields .= str_replace("|", "_", $k)."|";
        $values .= str_replace("|", "_", $v)."|";
    }

    $fields = substr_replace ($fields, '', -1, 1);
    $values = substr_replace ($values, '', -1, 1);
	*/

    debug ("fields: ".$fields);
    debug ("values: ".$values);

    $date = date("Y-m-d");
    debug ("date: ".$date);
    $time = date("H:i:s");
    debug ("time: ".$time);

    exec_query ("INSERT INTO ksh_forms_submitted (name, type, flds, vls, date, time) values ('".mysql_real_escape_string($formname)."', '".mysql_real_escape_string($formtype)."', '".mysql_real_escape_string($fields)."', '".mysql_real_escape_string($values)."', '".mysql_real_escape_string($date)."', '".mysql_real_escape_string($time)."')");

	if ("yes" == $config['base']['send_emails'])
	{
		debug ("sending the email");
		if ("yes" == $config['base']['mail']['use_phpmailer'])
		{
			$values = explode("|",$values);
			include_once ($config['libs']['location']."phpmailer/class.phpmailer.php");

			$mail = new PHPMailer();

			$mail->IsSMTP();                                      // set mailer to use SMTP
			$mail->Host = $config['base']['mail']['host'];  // specify main and backup server
			$mail->SMTPAuth = true;     // turn on SMTP authentication
			$mail->Username = $config['base']['mail']['username'];  // SMTP username
			$mail->Password = $config['base']['mail']['password']; // SMTP password

			$mail->From = "gohdan@gohdan.ru";
			$mail->FromName = "TAMAK";
			//$mail->AddAddress($config['base']['admin_email'], "GohDan");
			$mail->AddAddress("yasens@inbox.ru", "GohDan");
			$mail->AddAddress("marketing@tamak.ru");                  // name is optional
			$mail->AddAddress("e_marketing@tamak.ru");                  // name is optional

			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(false);                                  // set email format to HTML

			$mail->Subject = "ТАМАК - анкета (".$_POST['fio'].", ".$_POST['email'].")";

			$_GET['forms'] = mysql_insert_id();
			debug ("sending form ".$_GET['forms']);
			$form_content = str_replace("<br>", "\n", gen_content("forms", "mail_submitted", forms_view_submitted()));
			debug ("sending data: ".$form_content);
			$mail->Body = "На сайте ТАМАК заполнена новая анкета\n".$form_content;


			if($mail->Send())
			{
				$content['result'] = "Ваша анкета отправлена";
			}
			else
			{
				$content['result'] .= "Невозможно отправить анкету. <p>";
				$content['result'] .=  "Ошибка почты: " . $mail->ErrorInfo;

			}
		}
		else
		{
			debug ("PEAR root:".$config['base']['PEAR_root']);
			include_once ($config['base']['PEAR_root']."/Mail/Mail.php");
			$mail_object = & Mail::factory ($config['base']['mail']['backend'], $config['base']['mail']);
			$to = $config['base']['admin_email'];
			$subject = "Заполненная форма с ".$config['base']['site_name'];
			$body = "На сайте ".$config['base']['site_name']." заполнена новая анкета.";
			//	$headers = "Content-type: text/plain; charset=utf-8 \r\n";

			$headers['From']    = 'inquiry@tamak.ru';
			$headers['To']      = 'gohdan@mail.ru';
			$headers['Subject'] = 'Form submitted';
			// $sending_result = $mail_object->send($to, $headers, $body);

			/*	if (mail ($to, $subject, $body, $headers))
				$content['result'] = "Ваша анкета отправлена";
			else
				$content['result'] = "При отправлении анкеты произошла ошибка, напишите об этом по адресу ".$config['base']['admin_email'];
			*/
			debug ("sending result: ".$sending_result);
			if ("TRUE" == $sending_result)
				$content['result'] = "Ваша анкета отправлена";
			else
			{
				$content['result'] = "При отправлении анкеты произошла ошибка, напишите об этом по адресу ".$config['base']['admin_email'];
				print_r(get_object_vars($sending_result));
			}
		}

	}
	else
	{
		debug ("not sending the email");
	}

    debug ("*** end: forms_send ***");
    return $content;
}

function forms_view_submitted_forms()
{
        debug ("*** forms_view_submitted_forms ***");
        global $config;
        global $user;
        $content = array(
        	'content' => '',
            'submitted_forms' => ''
        );

		if (isset($_GET['type'])) $type = $_GET['type'];
		else $type = 0;

		if (isset($_POST['do_del']))
		{
			if (1 == $user['id'])
			{
				exec_query("DELETE FROM ksh_forms_submitted WHERE id='".mysql_real_escape_string($_POST['id'])."'");
				$content['result'] = "Анкета удалена";
			}
			else
			{
				$content['result'] = "Анкета не удалена";
				$content['content'] = "Пожалуйста, войдите как администратор";
			}
		}

        if (1 == $user['id'])
        {
        	debug ("user is admin");
			$i = 0;
			$result = exec_query("SELECT * FROM ksh_forms_submitted WHERE type='".mysql_real_escape_string($type)."' ORDER BY id");
            while ($form = mysql_fetch_array($result))
            {
            	$content['submitted_forms'][$i]['id'] = stripslashes($form['id']); $content['submitted_forms'][$i]['date'] = stripslashes($form['date']); $content['submitted_forms'][$i]['time'] = stripslashes($form['time']);
				$i++;
            }
			mysql_free_result($result);
        }
        else
        {
        	debug ("user isn't admin");
            $content['content'] = "Пожалуйста, войдите в систему как администратор";

        }

        debug ("*** end: forms_view_submitted_forms ***");
        return $content;
}


function forms_add()
{
    debug ("*** forms_add ***");
    global $config;
    global $user;

    $content = array (
    	'content' => '',
        'result' => ''
    );


    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");


        if (isset($_POST['do_add']))
        {
            debug ("have data to add");
            if ("" != $_POST['name'])
            {
                debug ("forms name isn't empty");
                exec_query("INSERT INTO ksh_forms (name, template) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['template'])."')");
                $content['result'] .= "Анкета добавлена";
            }
            else
            {
                debug ("forms name is empty");
                $content['result'] .= "Пожалуйста, задайте название формы";
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

    debug ("*** end: forms_add ***");
    return $content;
}

function forms_edit()
{
    debug ("*** forms_edit ***");
    global $config;
    global $user;


    $content = array(
    	'content' => '',
        'result' => '',
        'id' => '',
        'name' => '',
		'fields' => ''
    );


    if (isset($_GET['forms'])) $forms_id =$_GET['forms'];
    else if (isset($_POST['id'])) $forms_id =$_POST['id'];
    else $forms_id =0;
    debug ("forms id: ".$forms_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");
        if (isset($_POST['do_update']))
        {
			unset ($_POST['do_update']);
            debug ("have data to update");
            if ("" != $_POST['name'])
            {
                debug ("forms name isn't empty");

				$name = mysql_real_escape_string($_POST['name']);
				unset ($_POST['name']);
				$title = mysql_real_escape_string($_POST['title']);
				unset ($_POST['title']);
				$template = mysql_real_escape_string($_POST['template']);
				unset ($_POST['template']);

				$flds_names = "";
				$flds_descrs = "";

				if ("" == $_POST['new_field_name'] && "" == $_POST['new_field_value'])
				{
					unset ($_POST['new_field_name']);
					unset ($_POST['new_field_value']);
				}

				$i = 0;
				foreach ($_POST as $k => $v)
				{
					debug ("POST: ".$k.":".$v);
					if (0 == $i)
					{
						$flds_names = $flds_names . $v . "|";
						$i = 1;
					}
					else if (1 == $i)
					{
						$flds_descrs = $flds_descrs . $v . "|";
						$i = 0;
					}

				}

				$flds_names = mysql_real_escape_string($flds_names);
				$flds_descrs = mysql_real_escape_string($flds_descrs);
				debug ("fields names: ".$flds_names);
				debug ("fields descrs: ".$flds_descrs);


                exec_query("UPDATE ksh_forms set name='".$name."', title='".$title."', template='".$template."', flds_names='".$flds_names."', flds_descrs='".$flds_descrs."' WHERE id='".mysql_real_escape_string($forms_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("forms name is empty");
                $content['result'] .= "Пожалуйста, задайте название анкеты";
            }
        }
        else
        {
            debug ("no data to update");
        }

		$result = exec_query("SELECT * FROM ksh_forms WHERE id='".mysql_real_escape_string($forms_id)."'");
		$forms = mysql_fetch_array($result);
		mysql_free_result($result);
		$content['name'] = stripslashes($forms['name']);
		$content['title'] = stripslashes($forms['title']);
		$content['template'] = htmlspecialchars(stripslashes($forms['template']));
		$content['id'] = stripslashes($forms['id']);

		$fields['names'] = explode("|", stripslashes($forms['flds_names']));
		array_pop($fields['names']);
		$fields['values'] = explode("|", stripslashes($forms['flds_descrs']));
		array_pop($fields['values']);
		foreach ($fields['names'] as $k => $v)
			$content['fields'][$k]['name'] = $v;
		foreach ($fields['values'] as $k => $v)
			$content['fields'][$k]['descr'] = $v;


    }
    else
    {
        debug ("user isn't admin");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: forms_edit ***");
    return $content;
}


function forms_list()
{
	debug ("*** forms_list ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'forms' => ''
    );

    $i = 0;

    if ($user['id'] == 1)
    {
    	debug ("user is admin");
        $result = exec_query ("SELECT id,name,title FROM ksh_forms");
        while ($forms = mysql_fetch_array($result))
        {
        	$content['forms'][$i]['id'] = stripslashes($forms['id']);
            $content['forms'][$i]['name'] = stripslashes($forms['name']);
			$content['forms'][$i]['title'] = stripslashes($forms['title']);
			$content['forms'][$i]['qty'] = stripslashes(mysql_result(exec_query("SELECT count(*) FROM ksh_forms_submitted WHERE type='".$forms['id']."'"), 0, 0));
	        $i++;
        }
    }
    else
    {
    	debug ("user isn't admin");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: forms_list ***");
    return $content;
}

function forms_hook()
{
    debug("*** forms_hook ***");
    global $user;
    global $config;
    $content = "";

    $result = exec_query("SELECT * FROM ksh_hooks WHERE hook_module='forms' AND to_module='".mysql_real_escape_string($config['modules']['current_module'])."' AND (to_id='".mysql_real_escape_string($config['modules']['current_id'])."' OR (to_type='category' AND to_id='".mysql_real_escape_string($config['modules']['current_category'])."'))");
	while ($hook = mysql_fetch_array($result))
	{
		if ("forms" == stripslashes($hook['hook_type']))
		{
		    $id = stripslashes($hook['hook_id']);

	    	$categories = exec_query("SELECT * FROM ksh_forms WHERE id='".mysql_real_escape_string($id)."'");

	    	while ($row = mysql_fetch_array($categories))
	    	{
	        	debug("show forms ".$row['id']);
                $tpl = str_replace ("@title@", $config['modules']['current_title'], stripslashes($row['template']));
				$content .= $tpl;
	    	}
	    	mysql_free_result($categories);
		}

	}
    mysql_free_result($result);

    if (1 == $user['id']) $content .= "<p><a href=\"/index.php?module=forms&action=admin\">Администрирование анкет</a></p>";

    debug("*** end: forms_hook ***");
    return $content;
}

function forms_view_submitted()
{
	debug ("*** forms_view_submitted ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'form' => '',
		'id' => '',
		'date' => '',
		'time' => '',
		'type' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
	}

		if (isset($_GET['forms']))
			$form_id = $_GET['forms'];
		else
			$form_id = 0;

		$result = exec_query ("SELECT * FROM ksh_forms_submitted WHERE id='".$form_id."'");
		$form = mysql_fetch_array($result);
		mysql_free_result($result);

		$content['id'] = stripslashes($form['id']);
		$content['date'] = stripslashes($form['date']);
		$content['time'] = stripslashes($form['time']);
		$content['type'] = stripslashes(mysql_result(exec_query("SELECT title FROM ksh_forms WHERE id='".$form['type']."'"), 0, 0));

		$fields = explode ("|", $form['flds']);
		$values = explode ("|", $form['vls']);

		$result = exec_query ("SELECT flds_names, flds_descrs FROM ksh_forms WHERE id='".$form['type']."'");
		$flds = mysql_fetch_array($result);
		mysql_free_result($result);
		$flds['flds_names'] = explode("|", $flds['flds_names']);
		array_pop($flds['flds_names']);
		array_pop($flds['flds_descrs']);
		$flds['flds_descrs'] = explode("|", $flds['flds_descrs']);
		foreach ($flds['flds_names'] as $k => $v)
		{
			$keys[$k]['name'] = stripslashes($v);
			$keys[$k]['value'] = stripslashes($flds['flds_descrs'][$k]);
		}

		debug ("fields keys: ");
		foreach ($keys as $k => $v)
			debug ($v['name'].":".$v['value']);

		foreach ($fields as $k => $v)
		{
			$fld = stripslashes($v);
			foreach ($keys as $s => $x)
				if ($v == $x['name'])
					$fld = $x['value'];
			if ("" != $values[$k])
				$content['form'] .= $fld." : ".stripslashes($values[$k])."<br>";
		}

		$content['form'] = str_replace("yes", "да", $content['form']);
		$content['form'] = str_replace("no", "нет", $content['form']);


	debug ("*** end:forms_view_submitted ***");
	return $content;
}

function forms_del_submitted()
{
	debug ("*** forms_del_submitted ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'type' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
	{
		debug ("user isn't admin");
		$content['content'] = "Пожалуйста, войдите как администратор";
	}

	$result = exec_query("SELECT id, type FROM ksh_forms_submitted WHERE id='".mysql_real_escape_string($_GET['forms'])."'");
	$form = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($form['id']);
	$content['type'] = stripslashes($form['type']);

	debug ("*** end:forms_del_submitted ***");
	return $content;
}

?>
