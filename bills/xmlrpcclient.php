<?php

function bills_sections_update()
{
	global $user;
	global $config;
	debug ("*** bills_sections_update ***");

	$sections = array();
	$sections_ids = array();

	$client =new xmlrpc_client("/modules/bills/xmlrpcserver.php", $config['bbcpanel']['bbcpanel_domain']);
	$message =new xmlrpcmsg('getcategories', array(
		new xmlrpcval($config['bbcpanel']['bb_id'], "int"),
		new xmlrpcval($config['bbcpanel']['password'], "string")
		));
	$response =$client->send($message);

	if (!$response ->faultCode(  ))
	{
		// First clean up tables
		$sql_query = "DELETE FROM `ksh_bills_categories`";
		exec_query($sql_query);

		// Get categories from response
		$v=$response->value();

		write_file_log("bills_sections_update: server answered\n");
		write_file_log("bills_sections_update server response:\n".$response->serialize(), 2);
		
		for($a=0; $a<$v->arraysize(  ); $a++)
		{
			$z=$v->arraymem($a);

			$struct_id = $z -> structmem("id");
			$struct_parent = $z -> structmem("parent");
			$struct_name = $z -> structmem("name");
			$struct_title = $z -> structmem("title");

			$section['id'] = $struct_id->scalarval();
			$section['parent'] = $struct_parent->scalarval();
			$section['name'] = $struct_name->scalarval();
			$section['title'] = $struct_title->scalarval();

			$sql_query = "INSERT INTO `ksh_bills_categories` (`id`, `parent`, `name`, `title`) VALUES (
				'".mysql_real_escape_string($section['id'])."',
				'".mysql_real_escape_string($section['parent'])."',
				'".mysql_real_escape_string($section['name'])."',
				'".mysql_real_escape_string($section['title'])."'
				)";
			$result = exec_query($sql_query);
		}
	}
	else
	    write_file_log("bills_sections_update: Problem code: " . $response->faultCode(  ) . " Reason: '" .$response->faultString(  )."'");

	// Checking categories for orphans
	debug("checking for orphans");
	$i = 0;
	$sql_query = "SELECT `id`, `parent` FROM `ksh_bills_categories`";
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
			$sql_query = "UPDATE `ksh_bills_categories` SET `parent` = '0' WHERE `id` = '".mysql_real_escape_string($v['id'])."'";
			exec_query($sql_query);
		}

	debug ("*** end: bills_sections_update ***");
	return 1;
}

function bills_bill_send($bb_id, $bill)
{
	global $user;
	global $config;
	debug ("*** bills_bill_send ***");

	debug("bb_id: ".$bb_id);

	$sql_query = "SELECT `url` FROM `ksh_bbcpanel_bbs` WHERE `id` = '".mysql_real_escape_string($bb_id)."'";
	$result = exec_query($sql_query);
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	$bb_url = stripslashes($row['url']);
	if ("http://" == substr($bb_url, 0, 7))
		$bb_url = substr($bb_url, 7);
	$bb_url = rtrim($bb_url, "/");
	debug("bb_url: ".$bb_url);

	debug("bill:");
	dump($bill);

	$client =new xmlrpc_client("/modules/bills/xmlrpcserver.php", $bb_url);
	$message =new xmlrpcmsg('receive_bill', array(
		new xmlrpcval($bill['category'], "int"),
		new xmlrpcval($bill['name'], "string"),
		new xmlrpcval($bill['title'], "string"),
		new xmlrpcval(base64_encode($bill['text']), "string"),
		new xmlrpcval($config['bbcpanel']['password'], "string")
		));
	$response =$client->send($message);

	if (!$response ->faultCode(  ))
	{
		$v=$response->value();

		debug("bills_bill_send: Server answered");
		debug("bills_bill_send server response:\n".$response->serialize(), 2);
	}
	else
	    debug("bills_bill_send: Problem code: " . $response->faultCode(  ) . " Reason: '" .$response->faultString(  )."'");

	debug ("*** end: bills_bill_send ***");
	return 1;
}


?>
