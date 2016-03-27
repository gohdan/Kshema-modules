<?php

// Guestbook class

class Guestbook
{

function add()
{
	global $user;
	global $config;
	debug ("*** guestbook: add ***");
	$content = array(
		'content' => '',
		'show_admin_link' => '',
		'name' => '',
		'contact' => '',
		'text' => '',
		'success' => '',
		'error' => '',
		'captcha_error' => '',
		'captcha_wrong' => '',
		'if_recaptcha' => '',
		'recaptcha_sitekey' => '',
		'no_text' => ''
	);
	$result = 1;

	if ("yes" == $config['recaptcha']['use'])
	{
		$content['recaptcha'] = "yes";
		$content['recaptcha_sitekey'] = $config['recaptcha']['sitekey'];
	}

	$priv = new Privileges();

	if ($priv -> has("guestbook", "admin", "write"))
		$content['show_admin_link'] = "yes";

	if (isset($_POST['do_add']))
	{
		if ("" == $_POST['text'])
		{
			$result = 0;
			$content['no_text'] = "yes";
		}

		if ("yes" == $config['recaptcha']['use'])
		{
			$post_data = array(
			    "secret" => $config['recaptcha']['secret'],
			    "response" => $_POST['g-recaptcha-response']
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $config['recaptcha']['url']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			$output = curl_exec($ch);  
		    curl_close($ch);

			if ($output === FALSE)
			{
				$result = 0;
				$content['captcha_error'] = curl_error($ch);
			}
			else
			{
				$response = json_decode($output);
				$success = $response->success;
				if (!$success)
				{
					$result = 0;
					$content['captcha_wrong'] = "yes";
				}
			}
		}

		if ($result)
		{
			$sql_query = "INSERT INTO `ksh_guestbook` (
			`date`,
			`time`,
			`name`,
			`contact`,
			`text`
			) VALUES (
			'".mysql_real_escape_string(date("Y-m-d"))."',
			'".mysql_real_escape_string(date("H:i:s"))."',
			'".mysql_real_escape_string($_POST['name'])."',
			'".mysql_real_escape_string($_POST['contact'])."',
			'".mysql_real_escape_string($_POST['text'])."'
			)";
			exec_query($sql_query);
			if (0 == mysql_errno())
				$content['success'] = "yes";
			else
				$content['error'] = "yes";
		}
		else
		{
			$content['name'] = $_POST['name'];
			$content['contact'] = $_POST['contact'];
			$content['text'] = $_POST['text'];
		}
	}

	debug ("*** end: guestbook: add ***");
	return $content;	
}


function del()
{
	global $user;
	global $config;
	debug ("*** guestbook: del ***");
	$content = array(
		'content' => '',
		'result' => '',
		'id' => '',
		'title' => '',
		'category' => '',
		'show_admin_link' => ''
	);

	$priv = new Privileges();
	if ($priv -> has ("guestbook", "admin", "write"))
		$content['show_admin_link'] = "yes";

	$content['id'] = $_GET['element'];

	debug ("*** end: guestbook: del ***");
	return $content;	
}


function view()
{
	global $user;
	global $config;
	global $template;
	debug ("*** guestbook: view ***");
	$content = array(
		'content' => '',
		'heading' => '',
		'result' => '',
		'show_admin_link' => '',
		'messages' => '',
		'pages' => ''
	);

	$priv = new Privileges();
	if ($priv -> has("guestbook", "admin", "write"))
		$content['show_admin_link'] = "yes";

	$content['heading'] = "Просмотр сообщений";

	// Get pages
	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
	{
		$start_page = $_GET['page'];
		$content['page'] = $_GET['page'];
	}
    else
		$start_page = 1; // Need to determine correct LIMIT
	$goods_on_page = $config['guestbook']['messages_on_page'];

	$messages_qty = mysql_result(exec_query("SELECT COUNT(*) FROM `ksh_guestbook` WHERE `approved` = '1'"), 0, 0);
    debug ("messages qty: ".$messages_qty);
	if ($messages_qty)
	    $pages_qty = ceil($messages_qty / $goods_on_page);
	else
		$pages_qty = 1;
    debug ("pages qty: ".$pages_qty);

	// Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
			$content['pages'][$i]['id'] = $i;

			if ((!isset($_GET['page']) && ($i == 1)) || (isset($_GET['page'])) && ($i == $_GET['page']))
				$content['pages'][$i]['show_link'] = "";
            else
                $content['pages'][$i]['show_link'] = "yes";
        }
    }
    // End: Pages counting

	// Get messages
	$sql_query = "SELECT * from `ksh_guestbook`
		WHERE `approved` = '1'
		ORDER BY `date` DESC, `time` DESC , `id` DESC
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page;
	$i = 0;
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		foreach($row as $k => $v)
			$content['messages'][$i][$k] = stripslashes($v);

		$content['messages'][$i]['date'] = format_date($content['messages'][$i]['date'], "ru");

		if ("yes" == $config['base']['ext_links_redirect'])
		{
			include_once($config['modules']['location']."redirect/index.php");
			$content['messages'][$i]['text'] = redirect_links_replace(stripslashes($row['text']));
		}
		else
			$content['messages'][$i]['text'] = stripslashes($row['text']);

		if (1 == $user['id'])
			$content['messages'][$i]['show_del_link'] = "yes";
		$i++;
	}
	mysql_free_result($result);


	debug ("*** end: guestbook: view ***");
	return $content;	
}



function moderate()
{
	global $user;
	global $config;
	global $template;
	debug ("*** guestbook: moderate ***");
	$content = array(
		'content' => '',
		'heading' => '',
		'result' => '',
		'messages' => '',
		'pages' => ''
	);

	$content['heading'] = "Модерирование сообщений";

	if (isset($_POST['do_approve']))
	{
		$sql_query = "UPDATE `ksh_guestbook` SET `approved` = '1' WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
		exec_query($sql_query);
	}

	if (isset($_POST['do_del']))
	{
		$sql_query = "DELETE FROM `ksh_guestbook` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."'";
		exec_query($sql_query);
	}

	// Get pages
	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
	{
		$start_page = $_GET['page'];
		$content['page'] = $_GET['page'];
	}
    else
		$start_page = 1; // Need to determine correct LIMIT
	$goods_on_page = $config['guestbook']['messages_on_page'];

	$messages_qty = mysql_result(exec_query("SELECT COUNT(*) FROM `ksh_guestbook` WHERE `approved` = '0'"), 0, 0);
    debug ("messages qty: ".$messages_qty);
	if ($messages_qty)
	    $pages_qty = ceil($messages_qty / $goods_on_page);
	else
		$pages_qty = 1;
    debug ("pages qty: ".$pages_qty);

	// Pages counting

    if ($pages_qty > 1)
    {
        for ($i = 1; $i <= $pages_qty; $i++)
        {
			$content['pages'][$i]['id'] = $i;

			if ((!isset($_GET['page']) && ($i == 1)) || (isset($_GET['page'])) && ($i == $_GET['page']))
				$content['pages'][$i]['show_link'] = "";
            else
                $content['pages'][$i]['show_link'] = "yes";
        }
    }
    // End: Pages counting

	// Get messages
	$sql_query = "SELECT * from `ksh_guestbook`
		WHERE `approved` = '0'
		ORDER BY `date` DESC, `time` DESC , `id` DESC
		LIMIT ".mysql_real_escape_string(($start_page - 1) * $goods_on_page).",".$goods_on_page;
	$i = 0;
	$result = exec_query($sql_query);
	while ($row = mysql_fetch_array($result))
	{
		foreach($row as $k => $v)
			$content['messages'][$i][$k] = stripslashes($v);

		$content['messages'][$i]['date'] = format_date($content['messages'][$i]['date'], "ru");
		$content['messages'][$i]['show_approve_button'] = "yes";
		$content['messages'][$i]['show_del_button'] = "yes";

		$i++;
	}
	mysql_free_result($result);


	debug ("*** end: guestbook: view ***");
	return $content;	
}


}

?>
