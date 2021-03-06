<?php

include_once("../../config.php");

ini_set('error_reporting', $config['base']['error_reporting']);
error_reporting($config['base']['error_reporting']);
if ($config['base']['error_reporting'])
	ini_set('display_errors', 1);
else
	ini_set('display_errors', 0);

include_once($config['base']['doc_root']."/modules/base/index.php");
include_once($config['base']['doc_root']."/modules/db/index.php");
include_once($config['base']['doc_root']."/modules/templater/index.php");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpc.inc");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpc_wrappers.inc");
include_once($config['base']['doc_root']."/libs/xmlrpc/xmlrpcs.inc");
connect_2db ($config['db']['db_user'], $config['db']['db_password'], $config['db']['db_host'], $config['db']['db_name']);

function articles_get_ctitle($bb_id, $category_id)
{
	global $user;
	global $config;
	debug ("*** articles_get_ctitle ***");

	debug("articles_get_ctitle: bb ".$bb_id.", category ".$category_id);

	$sql_query = "SELECT `title` FROM `ksh_articles_categories_titles` WHERE
		`satellite` = '".mysql_real_escape_string($bb_id)."' AND
		`category` = '".mysql_real_escape_string($category_id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$ctitle = stripslashes($row['title']);
	mysql_free_result($result);

	debug ("*** end: articles_get_ctitle ***");
	return $ctitle;
}

function articles_get_cname($bb_id, $category_id)
{
	global $user;
	global $config;
	debug ("*** articles_get_cname ***");

	debug("articles_get_cname: bb ".$bb_id.", category ".$category_id);

	$sql_query = "SELECT `name` FROM `ksh_articles_categories_titles` WHERE
		`satellite` = '".mysql_real_escape_string($bb_id)."' AND
		`category` = '".mysql_real_escape_string($category_id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$cname = stripslashes($row['name']);
	mysql_free_result($result);

	debug ("*** end: articles_get_cname ***");
	return $cname;
}




	/**
	* Used to test usage of object methods in dispatch maps and in wrapper code
	*/
	class xmlrpc_server_methods_container
	{
		/**
		* Method used to test logging of php warnings generated by user functions.
		*/
		function phpwarninggenerator($m)
		{
			$a = $b; // this triggers a warning in E_ALL mode, since $b is undefined
			return new xmlrpcresp(new xmlrpcval(1, 'boolean'));
		}

	    /**
	     * Method used to testcatching of exceptions in the server.
	     */
	    function exceptiongenerator($m)
	    {
	        throw new Exception("it's just a test", 1);
	    }
	}


	/* === sections_update === */
	$sections_update_sig = array(array($xmlrpcArray, $xmlrpcString));
	$sections_update_doc = "Forces sections update";
	function sections_update($m)
	{
		global $config;
		$v = $m -> getParam(0);
		$password = base64_decode($v -> scalarval());
		if ($password == $config['bbcpanel']['password'])
		{
			debug("sections_update: password matches");
			
			$sections = array();
			$sections_ids = array();
			debug("calling ".$config['bbcpanel']['bbcpanel_domain']);

			$client =new xmlrpc_client("/modules/articles/xmlrpcserver.php", $config['bbcpanel']['bbcpanel_domain']);
			$message =new xmlrpcmsg('getcategories', array(
				new xmlrpcval($config['bbcpanel']['bb_id'], "int"),
				new xmlrpcval($config['bbcpanel']['password'], "string")
				));
			$response =$client->send($message);
		
			if (!$response ->faultCode(  ))
			{
				// First clean up tables
				$sql_query = "DELETE FROM `ksh_articles_categories`";
				exec_query($sql_query);

				// Get categories from response
				$v=$response->value();

				debug("articles_sections_update: server answered\n");
				debug("articles_sections_update: server response:\n".$response->serialize(), 2);
		
				for($a=0; $a<$v->arraysize(  ); $a++)
				{
					$z=$v->arraymem($a);

					$struct_id = $z -> structmem("id");
					$struct_parent = $z -> structmem("parent");
					$struct_name = $z -> structmem("name");
					$struct_title = $z -> structmem("title");

					$section['id'] = base64_decode($struct_id->scalarval());
					$section['parent'] = base64_decode($struct_parent->scalarval());
					$section['name'] = base64_decode($struct_name->scalarval());
					$section['title'] = base64_decode($struct_title->scalarval());

					$sql_query = "INSERT INTO `ksh_articles_categories` (`id`, `parent`, `name`, `title`) VALUES (
						'".mysql_real_escape_string($section['id'])."',
						'".mysql_real_escape_string($section['parent'])."',
						'".mysql_real_escape_string($section['name'])."',
						'".mysql_real_escape_string($section['title'])."'
						)";
					debug($sql_query);
					exec_query($sql_query);
				}
			}
			else
			{
			    debug("articles_sections_update: Problem code: " . $response->faultCode(  ) . " Reason: '" .$response->faultString(  )."'");
				debug("articles_sections_update: server response:\n".$response->serialize(), 2);
			}

			// Checking categories for orphans
			debug("checking for orphans");
			$i = 0;
			$sql_query = "SELECT `id`, `parent` FROM `ksh_articles_categories`";
			$result = exec_query($sql_query);
			while ($row = mysql_fetch_array($result))
			{
				$sections[$i]['id'] = stripslashes($row['id']);
				$sections[$i]['parent'] = stripslashes($row['parent']);
				$sections_ids[$i] = stripslashes($row['id']);
				$i++;
			}
			mysql_free_result($result);

			foreach ($sections as $k => $v)
				if (!in_array($v['parent'], $sections_ids))
				{
					$sql_query = "UPDATE `ksh_articles_categories` SET `parent` = '0' WHERE `id` = '".mysql_real_escape_string($v['id'])."'";
					exec_query($sql_query);
				}

			$result="ok";

		}
		else
		{
			debug("sections_update: password doesn't match");
			$result = "Password doesn't match";
		}
		return new xmlrpcresp(new xmlrpcval($result, "string"));
	}
	/* === end: sections_update === */

	/* === getcategories === */

	$getcategories_sig=array(array($xmlrpcArray, $xmlrpcInt, $xmlrpcString));
	$getcategories_doc='Returns categories';
	function getcategories($m)
	{
		global $config;
		global $xmlrpcArray;

		$v=$m->getParam(0);
		$bb_id = $v->scalarval();

		$v = $m -> getParam(1);
		$password = $v -> scalarval();

		if ($password == $config['bbcpanel']['password'])
		{
			debug("getcategories: password matches");
			$categories = new xmlrpcval();

			$cat = new Category;

			$sat = new Satellite;
			$sat -> id = $bb_id;
			$sat -> url = $sat -> get_url();

			$cnf = $sat -> get_config("ksh_articles_config");

			$sections = array();
			foreach($cnf as $k => $v)
				if ("sections" == $v['name'])
					$sections = explode("|", $v['value']);

			foreach ($sections as $k => $v)
			{
				if ("subcats" == substr($v, 0, 7))
				{
					debug("get subcats");
					$id = substr($v, 8);
					debug ("id: ".$id);

					$sql_query = "SELECT `id`, `name`, `title`, `parent` FROM `ksh_articles_categories` WHERE `id` = '".mysql_real_escape_string($id)."'";
					$result = exec_query($sql_query);
					$row = mysql_fetch_array($result);
					mysql_free_result($result);

					$name = stripslashes($row['name']);
					$title = stripslashes($row['title']);
					debug("title: ".$title.", name: ".$name);

					if (in_array("bbcpanel", $config['modules']['installed']))
					{
						debug("getcategories: bbcpanel module installed");
						debug("getcategories: checking title for category ".stripslashes($row['id']));

						$custom_title = articles_get_ctitle($bb_id, $row['id']);
						$custom_name = articles_get_cname($bb_id, $row['id']);

						debug("custom title: ".$custom_title);

						if ("" != $custom_title && NULL != $custom_title)
						{
							debug("switching title to custom");
							$title = $custom_title;

							if ("" == $custom_name || NULL == $custom_name)
								$name = transliterate($custom_title, "ru", "en");
							else
								$name = $custom_name;
						}
					}
					debug("title: ".$title.", name: ".$name);

					$t = array();
					$t[] = new xmlrpcval(
						array(
							"id" => new xmlrpcval(base64_encode(stripslashes($row['id'])), "string"),
							"parent" => new xmlrpcval(base64_encode(stripslashes($row['parent'])), "string"),
							"name" => new xmlrpcval(base64_encode($name), "string"),
							"title" => new xmlrpcval(base64_encode($title), "string")
						), "struct");
					$categories->addArray($t);

					$subsections = $cat -> get_categories_list("ksh_articles_categories", stripslashes($row['id']));
					foreach($subsections as $subs_k => $subs_v)
					{
						$sql_query = "SELECT `id`, `name`, `title`, `parent` FROM `ksh_articles_categories` WHERE `id` = '".mysql_real_escape_string($subs_v)."'";
						$result = exec_query($sql_query);
						$row = mysql_fetch_array($result);
						mysql_free_result($result);

						$name = stripslashes($row['name']);
						$title = stripslashes($row['title']);
						debug("title: ".$title.", name: ".$name);


						if (in_array("bbcpanel", $config['modules']['installed']))
						{
							debug("getcategories: bbcpanel module installed");
							debug("getcategories: checking title for category ".stripslashes($row['id']));

               				$custom_title = articles_get_ctitle($bb_id, $row['id']);
							$custom_name = articles_get_cname($bb_id, $row['id']);
							debug("custom title: ".$custom_title);

							if ("" != $custom_title && NULL != $custom_title)
							{
								debug("switching title to custom");
								$title = $custom_title;

								if ("" == $custom_name || NULL == $custom_name)
									$name = transliterate($custom_title, "ru", "en");
								else
									$name = $custom_name;
							}

						}
						debug("title: ".$title);

						$t = array();
						$t[] = new xmlrpcval(
							array(
								"id" => new xmlrpcval(base64_encode(stripslashes($row['id'])), "string"),
								"parent" => new xmlrpcval(base64_encode(stripslashes($row['parent'])), "string"),
								"name" => new xmlrpcval(base64_encode($name), "string"),
								"title" => new xmlrpcval(base64_encode($title), "string")
							), "struct");
						$categories->addArray($t);
					}

				}
				else if(is_numeric($v))
				{
					debug("get ordinary category, no subcats");
					$sql_query = "SELECT `id`, `name`, `title`, `parent` FROM `ksh_articles_categories` WHERE `id` = '".mysql_real_escape_string($v)."'";
					$result = exec_query($sql_query);
					$row = mysql_fetch_array($result);
					mysql_free_result($result);

					$name = stripslashes($row['name']);
					$title = stripslashes($row['title']);
					debug("title: ".$title.", name: ".$name);

					if (in_array("bbcpanel", $config['modules']['installed']))
					{
						debug("getcategories: bbcpanel module installed");
						debug("getcategories: checking title for category ".stripslashes($row['id']));

           				$custom_title = articles_get_ctitle($bb_id, $row['id']);
						$custom_name = articles_get_cname($bb_id, $row['id']);
						debug("custom title: ".$custom_title.", custom name: ".$custom_name);

						if ("" != $custom_title && NULL != $custom_title)
						{
							debug("switching title to custom");
							$title = $custom_title;

							if ("" == $custom_name || NULL == $custom_name)
								$name = transliterate($custom_title, "ru", "en");
							else
								$name = $custom_name;
						}
					}
					debug("title: ".$title);

					$t = array();
					$t[] = new xmlrpcval(
						array(
							"id" => new xmlrpcval(base64_encode(stripslashes($row['id'])), "string"),
							"parent" => new xmlrpcval(base64_encode(stripslashes($row['parent'])), "string"),
							"name" => new xmlrpcval(base64_encode($name), "string"),
							"title" => new xmlrpcval(base64_encode($title), "string")
						), "struct");
					$categories->addArray($t);
				}
			}
		}
		else
		{
			debug("password doesn't match");
			$categories = array();
		}

		
		$r = new xmlrpcresp($categories);
		return $r;
	}

	/* === end: getcategories === */
	
	/* === inform_moderators === */
	$inform_moderators_sig = array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString));
	$inform_moderators_doc = "Informs moderators about update";
	function inform_moderators($m)
	{
		global $config;
		$v = $m -> getParam(0);
		$password = base64_decode($v -> scalarval());
		$v = $m -> getParam(1);
		$par = base64_decode($v -> scalarval());
		if ($password == $config['bbcpanel']['password'])
		{
			debug("inform_moderators: password matches");

			$params = explode("_", $par);
			$sat_id = $params[0];
			$el_id = $params[1];

			debug("inform_moderators: sat_id: ".$sat_id);
			debug("inform_moderators: el_id: ".$el_id);

			$dbo = new DataObject;
			$dbo -> table = $config['articles']['table']."_".$sat_id;
			debug("articles table: ".$dbo -> table);
			$article = $dbo -> get($el_id);
			$dbo -> table = "ksh_bbcpanel_bbs";
			$satellite = $dbo -> get($sat_id);

			$cat = new Category();

			if ("http://" != substr($satellite['url'], 0, 7))
				$satellite['url'] = "http://".$satellite['url'];

			$cnt = array(
				'site_name' => $satellite['title'],
				'site_url' => $satellite['url'],
				'site_id' => $sat_id,
				'panel_url' => $config['base']['site_url'],
				'bill_id' => $el_id,
				'title' => $article['title'],
				'text' => $article['full_text'],
				'section' => $cat -> get_title($config['articles']['categories_table'], $article['category'])
			);

			$groups = array();
			$addresses = array();

			$sql_query = "SELECT `id` FROM `ksh_articles_privileges` WHERE 
				(`action` = 'moderate_edit' OR `action` = 'moderate_del') AND
				(`write` = '1') AND
				(`type` = 'group')
				";
			$result = exec_query($sql_query);
			while ($row = mysql_fetch_array($result))
				$groups[] = stripslashes($row['id']);
			mysql_free_result($result);

			$groups = array_unique($groups);

			foreach($groups as $k => $v)
			{
				$sql_query = "SELECT `email` FROM `ksh_users` WHERE `group` = '".$v."'";
				$result = exec_query($sql_query);
				while ($row = mysql_fetch_array($result))
					$addresses[] = stripslashes($row['email']);
				mysql_free_result($result);
			}

			$addresses = array_unique($addresses);
		
			if (count($addresses) > 0)
			{
				debug("inform_moderators: have addresses, sending mail");
				$subj = "Новая статья";
				$headers = "Content-type: text/plain; charset=utf-8 \r\n";
	
				$message = gen_content("articles", "email_moderators", $cnt);
		
				include_once ($config['libs']['location']."phpmailer/class.phpmailer.php");

				$mail = new PHPMailer();

				$mail->IsSMTP();                                      // set mailer to use SMTP
		
				$mail->Host = $config['base']['mail']['host'];  // specify main and backup server
				$mail->SMTPAuth = true;     // turn on SMTP authentication
				$mail->Username = $config['base']['mail']['username'];  // SMTP username
				$mail->Password = $config['base']['mail']['password']; // SMTP password
		
				$mail->From = $config['base']['mail']['from_address'];
				$mail->FromName = $config['base']['mail']['from_address'];
				$mail->AddAddress($config['base']['admin_email'], "Admin");
				foreach ($addresses as $k => $v)
				{
					$mail->AddAddress($v);
					debug("inform_moderators: address ".$v);
				}

				$mail->WordWrap = 50;                                 // set word wrap to 50 characters
				$mail->IsHTML(false);                                  // set email format to HTML

				$mail->Subject = $subj;

				$mail->Body = $message;

				if($mail->Send())
					debug("inform_moderators: mail sent");
				else
					debug("inform_moderators: can't send mail, error " . $mail->ErrorInfo);
			}

		}
		else
		{
			debug("inform_moderators: password doesn't match");
			$result = "Password doesn't match";
		}
		return new xmlrpcresp(new xmlrpcval($result, "string"));
}
	/* === end: inform_moderators === */

	/* === update_tables === */

	$update_tables_sig=array(array($xmlrpcArray, $xmlrpcString));
	$update_tables_doc='Updates DB tables';
	function update_tables($m)
	{
		global $config;
		global $xmlrpcArray;

		$v = $m->getParam(0);
		$password = base64_decode($v->scalarval());

		if($password == $config['bbcpanel']['password'])
		{
			debug("update_tables: password matches");
			articles_tables_update();
		}
		else
			debug("update_tables: password doesn't match");

		$resp = new xmlrpcval(1, "int");
		$r = new xmlrpcresp($resp);
		return $r;
	}

	/* === end: update_tables === */


	$o=new xmlrpc_server_methods_container;
	$a=array(
		"sections_update" => array(
			"function" => "sections_update",
			"signature" => $sections_update_sig,
			"docstring" => $sections_update_doc
		),
		"getcategories" => array(
			"function" => "getcategories",
			"signature" => $getcategories_sig,
			"docstring" => $getcategories_doc
		),
		"inform_moderators" => array(
			"function" => "inform_moderators",
			"signature" => $inform_moderators_sig,
			"docstring" => $inform_moderators_doc
		),
		"update_tables" => array(
			"function" => "update_tables",
			"signature" => $update_tables_sig,
			"docstring" => $update_tables_doc
		)
	);

	$s=new xmlrpc_server($a, false);
	$s->setdebug(3);
	$s->compress_response = true;


	$s->service();

?>
