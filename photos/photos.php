<?php

// Photos administration functions of the "photos" module

include_once ($config['modules']['location']."files/index.php"); // to upload pictures

function photos_view_by_category()
{
    debug("*** photos_view_by_category ***");
    global $user;
    global $config;

    $content = array(
    	'content' => '',
        'result' => '',
        'category' => '',
        'admin_link' => '',
        'edit_link' => '',
        'descr' => '',
		'if_show_admin_link' => ''
    );	

	$i = 0;

	if (isset($_GET['category']))
		$category = $_GET['category'];
	else if (isset($_GET['element']))
		$category = $_GET['element'];
	else if (isset($_POST['category']))
		$category = $_POST['category'];
	else
		$category = 0;
		
	$result = exec_query ("SELECT * FROM `ksh_photos_categories` WHERE `id` = '".mysql_real_escape_string($category)."'");
	$cat = mysql_fetch_array($result);
	mysql_free_result($result);

    $content['category'] = $category;
    $content['category_title'] = stripslashes($cat['title']);
	
    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        if (isset($_POST['do_del']))
        {
            debug ("have photos to delete");
            exec_query("DELETE FROM `ksh_photos` WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            $content['result'] .= "Баннер успешно удалён";
        }
        else
        {
            debug ("don't have banners to delete");
        }

        $content['show_admin_link'] = "yes";
	}

	// FIXME: Check if there are categories; else user has a warning
    debug ("category name: ".$content['category']);
    $result = exec_query("SELECT * FROM `ksh_photos` WHERE `category` = '".mysql_real_escape_string($category)."' ORDER BY `date` DESC, `id` DESC");

	$content['photos'] = array();

    while ($row = mysql_fetch_array($result))
    {
        debug("show photo ".$row['id']);

		foreach($row as $k => $v)
			$content['photos'][$i][$k] = stripslashes($v);

		$content['photos'][$i]['date'] = format_date($content['photos'][$i]['date'], "ru");

        if (1 == $user['id'])
            $content['photos'][$i]['show_admin_link'] = "yes";

        $i++;
    }
    mysql_free_result($result);

    return $content;
    debug("*** end: photos_view_by_category ***");
}

function photos_add()
{
    debug ("*** photos_add ***");
    global $config;
    global $user;

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];
    $if_file_exists = 0;
    $file_path = "";

    $content = array (
    	'content' => '',
        'result' => '',
        'categories_select' => '',
        'date' => ''
    );

	if (isset($_GET['category']))
		$category_id = $_GET['category'];
	else if (isset($_GET['element']))
		$category_id = $_GET['element'];
	else if (isset($_POST['category']))
		$category_id = $_POST['category'];
	else
		$category_id = 0;
	
	$content['category'] = $category_id;
    $content['date'] = date("Y-m-d");		

    $i = 0;
    $result = exec_query("SELECT * FROM ksh_photos_categories");
    while ($category = mysql_fetch_array($result))
    {
        debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = $category['id'];
        $content['categories_select'][$i]['name'] = $category['name'];
        $content['categories_select'][$i]['title'] = $category['title'];
        if ($category['id'] == $category_id)
			$content['categories_select'][$i]['selected'] = " selected";
        else
			$content['categories_select'][$i]['selected'] = "";
        $i++;
    }
    mysql_free_result($result);

                if ((isset($image)) && ("" != $image['name']))
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."photos/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."photos/",$if_file_exists);
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
		debug ("have data to add");
		exec_query("INSERT INTO `ksh_photos` (
			`category`,
			`date`,
			`title`,
			`image`,
			`descr`
			) VALUES (
			'".mysql_real_escape_string($_POST['category'])."',
			'".mysql_real_escape_string($_POST['date'])."',
			'".mysql_real_escape_string($_POST['title'])."',
			'".mysql_real_escape_string($file_path)."',
			'".mysql_real_escape_string($_POST['descr'])."'
		)");
		$content['result'] .= "Фото добавлено";
	}
        else
            debug ("no data to add");

    debug ("*** end: banners_add ***");
    return $content;
}


function photos_edit()
{
    debug ("*** photos_edit ***");
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
        'date' => '',
        'descr' => '',
        'image' => ''
    );

    if (isset($_FILES['image']))
    {
        debug ("have an image!");
        $image = $_FILES['image'];
    }
    else debug ("don't have an image!");
    $if_file_exists = 0;
    $file_path = "";

    if (isset($_GET['photo']))
		$id = $_GET['photo'];
	else if (isset($_GET['element']))
		$id = $_GET['element'];
    else if (isset($_POST['id']))
		$id =$_POST['id'];
    else $id =0;
    debug ("id: ".$id);
	$content['id'] = $id;

	if (isset($_POST['do_update']))
	{
        debug ("have data to update");

                if ("" != $image['name'])
                {
                    debug ("there is an image to upload");
                    if (file_exists($doc_root.$upl_pics_dir."photos/".$image['name'])) $if_file_exists = 1;
                    $file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."photos/",$if_file_exists);
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



		exec_query("UPDATE `ksh_photos` set 
			`title`='".mysql_real_escape_string($_POST['title'])."',
			`date`='".mysql_real_escape_string($_POST['date'])."',
			`category`='".mysql_real_escape_string($_POST['category'])."',
			`descr`='".mysql_real_escape_string($_POST['descr'])."',
			`image`='".mysql_real_escape_string($file_path)."'
			WHERE id='".mysql_real_escape_string($id)."'");
		$content['result'] .= "Изменения записаны";

	}
	else
		debug ("no data to update");


	$result = exec_query("SELECT * FROM `ksh_photos` WHERE id='".mysql_real_escape_string($id)."'");
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	foreach($row as $k => $v)
		$content[$k] = stripslashes($v);

	$result = exec_query("SELECT * FROM `ksh_photos_categories`");

	$i = 0;
	while ($category = mysql_fetch_array($result))
	{
		debug ("show category ".$category['id']);
        $content['categories_select'][$i]['id'] = $category['id'];
        $content['categories_select'][$i]['name'] = $category['name'];
        $content['categories_select'][$i]['title'] = $category['title'];
        if ($category['id'] == $content['category'])
			$content['categories_select'][$i]['selected'] = " selected";
        else
			$content['categories_select'][$i]['selected'] = "";
        $i++;
	}
	mysql_free_result($result);


    debug ("*** end: photos_edit ***");
    return $content;
}

function photos_del()
{
    global $config;
    global $user;

    debug ("*** photos_del ***");

    $content = array(
    	'content' => '',
        'id' => '',
        'title' => ''
    );

	$result = exec_query("SELECT * FROM `ksh_photos` WHERE id='".mysql_real_escape_string($_GET['element'])."'");
	$row = mysql_fetch_array($result);
	mysql_free_result($result);

	foreach($row as $k => $v)
		$content[$k] = stripslashes($v);

    debug ("*** end: photos_del ***");
    return $content;
}

?>
