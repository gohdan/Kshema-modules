<?php

// portfolio administration functions of the portfolio module

include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function portfolio_view_all()
{
    global $user;
	global $page_title;
    global $config;
	
    debug("*** portfolio_view_all ***");

	debug ("page template: ".$config['themes']['page_tpl']);
	debug ("menu template: ".$config['themes']['menu_tpl']);	

    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        'show_admin_link' => '',
        'edit_link' => '',
        'descr' => '',
		'categories_titles' => '',
		'portfolio' => array()
    );
	
	$priv = new Privileges();

	if (isset($_GET['tag']))
		$content['tag'] = $_GET['tag'];
	if (isset($_GET['year']))
		$content['year'] = $_GET['year'];


	// Tags
	$tags = array();

	$sql_query = "SELECT `tags` FROM `ksh_portfolio`";
	$result = exec_query($sql_query);
	$i = 0;
	while ($row = mysql_fetch_array($result))
	{
		$tar = explode("|", stripslashes($row['tags']));
		foreach($tar as $k => $v)
			if ("" != $v && !in_array($v, $tags))
				$tags[] = $v;
	}
	mysql_free_result($result);

	$i = 0;
	$content['tags'] = array();
	foreach($tags as $k => $v)
	{
		$content['tags'][$i]['tag'] = $v;
		$content['tags'][$i]['have_other'] = "yes";
		if (isset($_GET['tag']) && $v == $_GET['tag'])
			$content['tags'][$i]['current'] = "yes";
		if (isset($_GET['year']))
			$content['tags'][$i]['year'] = $_GET['year'];
		$i++;
	}
	$content['tags'][$i-1]['have_other'] = "";

	// Years
	$years = array();

	$sql_query = "SELECT `year` FROM `ksh_portfolio`";
	$result = exec_query($sql_query);
	$i = 0;
	while ($row = mysql_fetch_array($result))
	{
		$yar = explode("|", stripslashes($row['year']));
		foreach($yar as $k => $v)
			if ("" != $v && !in_array($v, $years))
				$years[] = $v;
	}
	mysql_free_result($result);
	sort($years);

	$i = 0;
	$content['years'] = array();
	foreach($years as $k => $v)
	{
		$content['years'][$i]['year'] = $v;
		$content['years'][$i]['have_other'] = "yes";
		if (isset($_GET['year']) && $v == $_GET['year'])
			$content['years'][$i]['current'] = "yes";
		if (isset($_GET['tag']))
			$content['years'][$i]['tag'] = $_GET['tag'];
		$i++;
	}
	$content['years'][$i-1]['have_other'] = "";


	// Get pages
	if ((isset($_GET['page'])) && ($_GET['page'] > 1))
	{
		debug("dumping GET", 2);
		dump($_GET);
		debug("GET page is set");
		$start_page = $_GET['page'];
		$content['page'] = $_GET['page'];
	}
    else
		$start_page = 1; // Need to determine correct LIMIT
	debug("start page: ".$start_page);
	$elements_on_page = $config['portfolio']['elements_on_page'];

	$sql_query = "SELECT COUNT(*) FROM `ksh_portfolio`";
	if (isset($_GET['tag']))
	{
		$sql_query .= " WHERE `tags` LIKE '%|".mysql_real_escape_string($_GET['tag'])."|%'";
		if (isset($_GET['year']))
			$sql_query .= " AND`year` LIKE '%|".mysql_real_escape_string($_GET['year'])."|%'";
	}
	else if (isset($_GET['year']))
		$sql_query .= " WHERE `year` LIKE '%|".mysql_real_escape_string($_GET['year'])."|%'";

	$elements_qty = mysql_result(exec_query($sql_query), 0, 0);
    debug ("elements qty: ".$elements_qty);
    $pages_qty = ceil($elements_qty / $elements_on_page);
    debug ("pages qty: ".$pages_qty);

	// Pages counting

    if ($pages_qty > 1)
    {
		debug("building pages");
        for ($i = 1; $i <= $pages_qty; $i++)
        {
			$content['pages'][$i]['id'] = $i;
			if (isset($_GET['tag']))
				$content['pages'][$i]['tag'] = $_GET['tag'];
			if (isset($_GET['year']))
				$content['pages'][$i]['year'] = $_GET['year'];

			if ((!isset($_GET['page']) && ($i == 1)) || (isset($_GET['page']) && $i == $_GET['page']))
			{
				$content['pages'][$i]['show_link'] = "";
				if (isset($content['pages'][$i]['tag']))
					$content['pages'][$i]['tag'] = "";
				if (isset($content['pages'][$i]['year']))
					$content['pages'][$i]['year'] = "";
			}
            else
                $content['pages'][$i]['show_link'] = "yes";
        }
		debug("pages:", 2);
		dump($content['pages']);
    }
    // End: Pages counting


	$sql_query = "SELECT * FROM `ksh_portfolio`";
	if (isset($_GET['tag']))
	{
		$sql_query .= " WHERE `tags` LIKE '%|".mysql_real_escape_string($_GET['tag'])."|%'";
		if (isset($_GET['year']))
			$sql_query .= " AND `year` LIKE '%|".mysql_real_escape_string($_GET['year'])."|%'";
	}
	else if (isset($_GET['year']))
		$sql_query .= " WHERE `year` LIKE '%|".mysql_real_escape_string($_GET['year'])."|%'";

	$sql_query .= " ORDER BY `order` DESC, `date` DESC, `id` DESC LIMIT ".mysql_real_escape_string(($start_page - 1) * $elements_on_page).",".$elements_on_page;
	$result = exec_query($sql_query);

	$i = 0;
	while ($row = mysql_fetch_array($result))
	{
		foreach($row as $k => $v)
			$row[$k] = stripslashes($v);

		foreach($row as $k => $v)
			$content['portfolio'][$i][$k] = $v;

		if ($priv -> has("portfolio", "admin", "write"))
			$content['portfolio'][$i]['show_admin_link'] = "yes";

		$i++;
	}
	mysql_free_result($result);



    return $content;
    debug("*** end: portfolio_view_all ***");
}

function portfolio_add()
{
    global $config;
    global $user;

	//$config['base']['debug_level'] = "3";
    debug ("*** portfolio_add ***");

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array (
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'date' => ''
    );

    $content['date'] = date("Y-m-d");
	$content['year'] = date("Y");

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

	if ((isset($image)) && ("" != $image['name']))
	{
		debug ("there is an image to upload");
		if (file_exists($doc_root.$upl_pics_dir."portfolio/".$image['name'])) $if_file_exists = 1;
		$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."portfolio/",$if_file_exists);
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

	if (isset($_POST['year']))
		$_POST['year'] = portfolio_tags_encode($_POST['year']);

	if (isset($_POST['tags']))
		$_POST['tags'] = portfolio_tags_encode($_POST['tags']);

	$dob = new DataObject();
	$dob -> table = "ksh_portfolio";
	$dob -> categories_table = "ksh_portfolio_categories";
	$cnt = $dob -> add();

	$content = array_merge($content, $cnt);

    debug ("*** end: portfolio_add ***");
	//$config['base']['debug_level'] = "3";


    return $content;
}

function portfolio_edit()
{
    debug ("*** portfolio_edit ***");
    global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    $content = array(
    	'content' => '',
        'result' => '',
        'categories' => '',
        'id' => '',
        'name' => '',
        'date' => '',
		'short_descr' => '',
        'descr' => '',
        'full_text' => '',
        'image' => ''
    );

	$priv = new Privileges();

    if (isset($_GET['portfolio'])) $portfolio_id =$_GET['portfolio'];
    else if (isset($_POST['id'])) $portfolio_id =$_POST['id'];
    else if (isset($_GET['element'])) $portfolio_id =$_GET['element'];
    else $portfolio_id =0;
    debug ("portfolio id: ".$portfolio_id);


        if (isset($_POST['do_update']))
        {
            debug ("have data to update");

			/* === descr image block === */
		    if (isset($_FILES['image']))
		    {
		        debug ("have an image!");
		        $image = $_FILES['image'];
		    }
		    else debug ("don't have an image!");
		    $if_file_exists = 0;
		    $file_path = "";

			if ("" != $image['name'])
			{
				debug ("there is an image to upload");
				if (file_exists($doc_root.$upl_pics_dir."portfolio/".$image['name'])) $if_file_exists = 1;
				$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."portfolio/",$if_file_exists);
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
			/* === end: descr image block === */

			/* === add images block === */
			$images = "|";
			if (isset($_POST['add_images']))
				foreach($_POST['add_images'] as $k => $v)
					if (!isset($_POST['add_images_del']) || !in_array($v, $_POST['add_images_del']))
						$images .= $v."|";

			for ($i = 0; $i < 10; $i++)
			{
			    if (isset($_FILES['add_new_image_'.$i]))
			    {
			        debug ("have an add_new_image!");
			        $image = $_FILES['add_new_image_'.$i];
			    }
			    else debug ("don't have an add_new_image!");
			    $if_file_exists = 0;
			    $new_image_path = "";

				if ("" != $image['name'])
				{
					debug ("there is an image to upload");
					if (file_exists($doc_root.$upl_pics_dir."portfolio/".$image['name'])) $if_file_exists = 1;
					$new_image_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."portfolio/",$if_file_exists);
					debug ("size: ".filesize($home.$new_image_path));

					if (filesize($home.$new_image_path) > $max_file_size)
					{
						debug ("file size > max file size!");
						$content['content'] .= "Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт";
						if (unlink ($home.$new_image_path)) debug ("file deleted");
						else debug ("can't delete file!");
					}

					$_POST['add_new_image_'.$i] = $new_image_path;

				}
				else
					debug ("no image to upload");

	    	    debug ("new_image_path: ".$new_image_path);

				if ("" != $new_image_path)
					$images .= $new_image_path."|";

			}
			/* === end: add images block === */

            if ("" != $_POST['name'])
            {
				debug ("portfolio name isn't empty");

				if (isset($_POST['tags']))
					$_POST['tags'] = portfolio_tags_encode($_POST['tags']);

				if (isset($_POST['year']))
					$_POST['year'] = portfolio_tags_encode($_POST['year']);

                $sql_query = "UPDATE `ksh_portfolio` SET
					`name`= '".mysql_real_escape_string($_POST['name'])."',
					`title` = '".mysql_real_escape_string($_POST['title'])."',
					`date` = '".mysql_real_escape_string($_POST['date'])."',
					`order` = '".mysql_real_escape_string($_POST['order'])."',
					`year` = '".mysql_real_escape_string($_POST['year'])."',
					`category` = '".mysql_real_escape_string($_POST['category'])."',
					`tags` = '".mysql_real_escape_string($_POST['tags'])."',
					`image` = '".mysql_real_escape_string($file_path)."',
					`descr` = '".mysql_real_escape_string($_POST['descr'])."',
					`full_text`= '".mysql_real_escape_string($_POST['full_text'])."',
					`images` = '".mysql_real_escape_string($images)."'
					WHERE `id` = '".mysql_real_escape_string($portfolio_id)."'";
				exec_query($sql_query);

                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("portfolio name is empty");
                $content['result'] .= "Пожалуйста, задайте название";
            }
        }
        else
        {
            debug ("no data to update");
        }

	$result = exec_query("SELECT * FROM `ksh_portfolio` WHERE `id` = '".mysql_real_escape_string($portfolio_id)."'");
	$portfolio = mysql_fetch_array($result);
	mysql_free_result($result);
	foreach($portfolio as $k => $v)
		$content[$k] = stripslashes($v);

	if (isset($content['tags']))
		$content['tags'] = portfolio_tags_decode($content['tags']);
	
	if (isset($content['year']))
		$content['year'] = portfolio_tags_decode($content['year']);

	$images = explode("|", $content['images']);
	$content['images'] = array();
	$i = 0;
	foreach($images as $k => $v)
		if ("" != $v)
		{
			$content['images'][$i]['image'] = $v;
			if ($priv -> has("portfolio", "admin", "write"))
				$content['images'][$i]['show_admin_link'] = "yes";
			$i++;
		}


    debug ("*** end: portfolio_edit ***");
    return $content;
}

function portfolio_del()
{
    debug ("*** portfolio_del ***");
    global $config;
    global $user;

    $content = array(
    	'content' => '',
        'id' => '',
        'name' => '',
        'category_id' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $result = exec_query("SELECT * FROM ksh_portfolio WHERE id='".mysql_real_escape_string($_GET['portfolio'])."'");
        $portfolio = mysql_fetch_array($result);
        mysql_free_result($result);

        $content['id'] = stripslashes($portfolio['id']);
        $content['name'] = stripslashes($portfolio['name']);
        $content['category_id'] = stripslashes($portfolio['category']);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: portfolio_del ***");
    return $content;
}

function portfolio_tags_encode($tags_string)
{
	global $config;
	global $user;

	debug("*** portfoliio_tags_encode ***");

	$tagline = str_replace(", ", ",", $tags_string);
	$tags = explode(",", $tagline);
	$tagline = "";
	foreach($tags as $k => $v)
		if ("" != $v)
				$tagline .= "|".$v;

	if ("" != $tagline)
		$tagline .= "|";

	debug("*** end: portfolio_tags_encode ***");
	return $tagline;
}

function portfolio_tags_decode($tags_string)
{
	global $config;
	global $user;

	debug("*** portfoliio_tags_decode ***");

	$tags = explode("|", $tags_string);
	$tagline = "";
	foreach($tags as $k => $v)
		if ("" != $v)
				$tagline .= $v.", ";

	$tagline = rtrim($tagline, ", ");

	debug("*** end: portfolio_tags_decode ***");
	return $tagline;
}

?>
