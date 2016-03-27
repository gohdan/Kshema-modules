<?php

// Authors functions of the "shop" module

function shop_authors_view()
{
	debug ("*** shop_authors_view ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'authors' => '',
		'show_admin_link' => '',
		'show_add_link' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (isset($_POST['do_del']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, deleting from DB");
			exec_query ("delete from ksh_shop_authors where id='".mysql_real_escape_string($_POST['id'])."'");
			$content['result'] = "Автор удалён";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Автор не удалён";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$content['authors'] = shop_authors_list();

	foreach ($content['authors'] as $k => $v)
	{
		if (1 == $user['id'])
		{
			$content['authors'][$k]['show_edit_link'] = "yes";
			$content['authors'][$k]['show_del_link'] = "yes";
		}
		else
			if ("1" == $content['authors'][$k]['if_hide'])
				unset($content['authors'][$k]);
	}


	debug ("*** end:shop_authors_view ***");
	return $content;
}

function shop_authors_view_by_category()
{
	debug ("*** shop_authors_view_by_category ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'authors' => ''

	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
		$content['show_admin_link'] = "yes";
		$content['show_add_link'] = "yes";
	}
	else
		debug ("user isn't admin");

	if (isset($_GET['category']))
		$id = $_GET['category'];
	else if (isset($_POST['category']))
		$id = $_POST['category'];
	else if (isset($_GET['element']))
		$id = $_GET['element'];
	else
		$id = 0;

	$content['category_id'] = $id;
	$content['category_name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_shop_categories WHERE id='".$id."'"),0,0));


    $i = 0;
    $result = exec_query ("SELECT `id`, `name`, `if_hide`, `image` FROM `ksh_shop_authors` WHERE `category` = '".mysql_real_escape_string($id)."' ORDER BY `name`");
    while ($author = mysql_fetch_array($result))
    {
        $authors[$i]['id'] = stripslashes($author['id']);
        $authors[$i]['name'] = stripslashes($author['name']);
		$authors[$i]['if_hide'] = stripslashes($author['if_hide']);
		$authors[$i]['image'] = stripslashes($author['image']);

		if ("1" == $user['id'])
		{
			$authors[$i]['show_edit_link'] = "yes";
			$authors[$i]['show_del_link'] = "yes";
		}

		$author_goods = shop_view_by_authors($authors[$i]['id']);
		debug("author goods:", 2);
		dump($author_goods);

		$tpl = new Templater();
		$goods_by_author = $tpl -> colonize($author_goods['goods_by_author'], $config['shop']['lastitems']);

		foreach($goods_by_author as $good_id => $good_data)
		{
			$good_data['author_id'] = $authors[$i]['id'];
			$authors[$i]['authors_goods'] .= gen_content("shop", "authors_goods", $good_data );
		}

		debug("authors:", 2);
		dump($authors);

        $i++;
    }
    mysql_free_result($result);

	$content['authors_by_category'] = $authors;

	debug ("*** end:shop_authors_view_by_category ***");
	return $content;
}


function shop_authors_add()
{
	debug ("*** shop_authors_add ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'categories' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	/* Image uploading funcs */

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];

    $if_file_exists = 0;
    $file_path = "";

	if ("" != $image['name'])
	{
		debug ("there is an image to upload");
		if (file_exists($doc_root.$upl_pics_dir."shop/".$image['name'])) $if_file_exists = 1;
		$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."shop/",$if_file_exists);
		debug ("size: ".filesize($home.$file_path));

		if (filesize($home.$file_path) > $max_file_size)
		{
			debug ("file size > max file size!");
			$content['content'] .= "<p>Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт</p>";
			if (unlink ($home.$file_path)) debug ("file deleted");
			else debug ("can't delete file!");
			$file_path = "";
		}

		$_POST['image'] = $file_path;

	}
	else
	{
		debug ("no image to upload");
		$file_path = $_POST['image'];
	}


	if (isset($_POST['do_add']))
	{
		if (1 == $user['id'])
		{
			debug ("user is admin, inserting into DB");
			exec_query ("INSERT INTO `ksh_shop_authors`
				(`name`, `category`, `if_hide`, `descr`, `image`)
				values (
				'".mysql_real_escape_string($_POST['name'])."',
				'".mysql_real_escape_string($_POST['category'])."',
				'".mysql_real_escape_string($_POST['if_hide'])."',
				'".mysql_real_escape_string($_POST['descr'])."',
				'".mysql_real_escape_string($_POST['image'])."'
				)");
			$content['result'] = "Автор добавлен";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Автор не добавлен";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$categories = shop_categories_list();
	foreach ($categories as $k => $v)
	{
		$content['categories'] .= "<option value=\"".$v['id']."\"";
		if ($_POST['category'] == $v['id'])
			$content['categories'] .= " selected";
		$content['categories'] .= ">".$v['name']."</option>";
	}



	debug ("*** end:shop_authors_add ***");
	return $content;
}

function shop_authors_edit()
{
	debug ("*** shop_authors_edit ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => ''
	);

	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	if (isset($_POST['id']))
		$author_id = $_POST['id'];
	else if (isset($_GET['authors']))
		$author_id = $_GET['authors'];
	else if (isset($_GET['element']))
		$author_id = $_GET['element'];
	else
		$author_id = 0;

	/* Image uploading funcs */

    global $upl_pics_dir;
    global $doc_root;
    global $max_file_size;
    global $home;

    if (isset($_FILES['image'])) $image = $_FILES['image'];

    $if_file_exists = 0;
    $file_path = "";

	if ("" != $image['name'])
	{
		debug ("there is an image to upload");
		if (file_exists($doc_root.$upl_pics_dir."shop/".$image['name'])) $if_file_exists = 1;
		$file_path = upload_file($image['name'],$image['tmp_name'],$home,$upl_pics_dir."shop/",$if_file_exists);
		debug ("size: ".filesize($home.$file_path));

		if (filesize($home.$file_path) > $max_file_size)
		{
			debug ("file size > max file size!");
			$content['content'] .= "<p>Простите, но нельзя закачать файл размером больше ".($max_file_size / 1024)." килобайт</p>";
			if (unlink ($home.$file_path)) debug ("file deleted");
			else debug ("can't delete file!");
			$file_path = "";
		}

		$_POST['image'] = $file_path;

	}
	else
	{
		debug ("no image to upload");
		$file_path = $_POST['image'];
	}

	/* End: Image uploading funcs */



	if (isset($_POST['do_update']))
	{
		if (1 == $user['id'])
		{
			exec_query ("UPDATE `ksh_shop_authors` set
				`name` = '".mysql_real_escape_string($_POST['name'])."',
				`category` = '".mysql_real_escape_string($_POST['category'])."',
				`image` = '".mysql_real_escape_string($_POST['image'])."',
				`descr` = '".mysql_real_escape_string($_POST['descr'])."',
				`if_hide` = '".mysql_real_escape_string($_POST['if_hide'])."'
				where id='".mysql_real_escape_string($author_id)."'");
			$content['result'] = "Изменения записаны";
		}
		else
		{
			debug ("user isn't admin, doing nothing");
			$content['result'] = "Изменения не записаны";
			$content['content'] = "Пожалуйста, войдите в систему как администратор";
		}
	}

	$result = exec_query("SELECT * FROM `ksh_shop_authors` WHERE id='".mysql_real_escape_string($author_id)."'");
	$author = mysql_fetch_array($result);
	mysql_free_result($result);

	$content['id'] = stripslashes($author['id']);
	$content['name'] = stripslashes($author['name']);
	$content['image'] = stripslashes($author['image']);
	$content['descr'] = stripslashes($author['descr']);
	$content['if_hide'] = stripslashes($author['if_hide']);

	$categories = shop_categories_list();

	foreach ($categories as $k => $v)
	{
		$content['categories'] .= "<option value=\"".$v['id']."\"";
		if ($author['category'] == $v['id']) $content['categories'] .= " selected";
		$content['categories'] .= ">".$v['name']."</option>";
	}

	debug ("*** end:shop_authors_edit ***");
	return $content;
}

function shop_authors_del()
{
	debug ("*** shop_authors_del ***");
	global $user;
	global $config;
	$content = array(
		'result' => '',
		'content' => '',
		'id' => '',
		'name' => ''
	);
	if (1 == $user['id'])
	{
		debug ("user is admin");
	}
	else
		debug ("user isn't admin");

	$result = exec_query("select name from ksh_shop_authors where id='".mysql_real_escape_string($_GET['authors'])."'");
	$content['id'] = $_GET['authors'];
	$content['name'] = stripslashes(mysql_result($result, 0, 0));
	mysql_free_result ($result);

	debug ("*** end:shop_authors_del ***");
	return $content;
}


function shop_authors_list()
{
	debug ("*** shop_authors_list ***");
	global $user;
	global $config;

	$authors = array();

    $i = 0;
    $result = exec_query ("SELECT `id`, `name`, `category`, `if_hide`, `image` FROM `ksh_shop_authors` ORDER BY `name`");
    while ($author = mysql_fetch_array($result))
    {
        $authors[$i]['id'] = stripslashes($author['id']);
        $authors[$i]['name'] = stripslashes($author['name']);
		$authors[$i]['category'] = stripslashes($author['category']);
		$authors[$i]['if_hide'] = stripslashes($author['if_hide']);
		$authors[$i]['image'] = stripslashes($author['image']);
        $i++;
    }
    mysql_free_result($result);
	debug ("*** end: shop_authors_list ***");
    return $authors;
}

function shop_authors_get_name($id)
{
	debug ("*** shop_authors_get_name ***");
	global $user;
	global $config;

	$name = "";

	if ($id)
	{
		$sql_query = "SELECT `name` FROM `ksh_shop_authors` WHERE `id` = '".mysql_real_escape_string($id)."'";
		$result = exec_query($sql_query);
		if ($result && mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);
			$name = stripslashes($row['name']);
		}
	}

	debug ("*** end: shop_authors_get_name ***");
    return $name;
}

?>
