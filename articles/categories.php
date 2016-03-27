<?php

// Categories administration functions of the "articles" module

function articles_categories_add()
{
    debug ("*** articles_categories_add ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
		'categories_select' => ''
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
                debug ("category name isn't empty");
                exec_query("INSERT INTO ksh_articles_categories (name, parent, menu_template) VALUES (
					'".mysql_real_escape_string($_POST['name'])."',
					'".mysql_real_escape_string($_POST['parent'])."',
					'".mysql_real_escape_string($_POST['menu_template'])."'
					)");
				$content['result'] .= "Категория добавлена";
            }
            else
            {
                debug ("category name is empty");
                $content['result'] .= "Пожалуйста, задайте имя категории";
            }
        }

		$result = exec_query("SELECT * FROM ksh_articles_categories");
		while ($category = mysql_fetch_array($result))
		{
			$id = stripslashes($category['id']);
			$categories[$id]['name'] = stripslashes($category['name']);
			$categories[$id]['parent'] = stripslashes($category['parent']);
		}
		mysql_free_result($result);

		foreach ($categories as $k => $v)
		{
			$content['categories_select'][$k]['id'] = $k;
			$content['categories_select'][$k]['parent'] = $v['parent'];
			$content['categories_select'][$k]['name'] = "";
			if ($k == $_GET['category'])
				$content['categories_select'][$k]['selected'] = " selected";

			$current_id = $k;
			$current_parent = $v['parent'];
			$current_name = $v['name'];
			while ("0" != $current_parent)
			{
				$content['categories_select'][$k]['name'] = $current_name." / ".$content['categories_select'][$k]['name'];
				$current_id = $current_parent;
				$current_parent = $categories[$current_id]['parent'];
				$current_name = $categories[$current_id]['name'];
			}
			$content['categories_select'][$k]['name'] = $current_name." / ".$content['categories_select'][$k]['name'];
		}
    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_categories_add ***");
    return $content;
}

function articles_categories_del()
{
    debug ("*** articles_categories_del ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'id' => '',
        'name' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $content['id'] = $_GET['category'];
        $content['name'] = stripslashes(mysql_result(exec_query("SELECT name FROM ksh_articles_categories WHERE id='".mysql_real_escape_string($_GET['category'])."'"), 0, 0));
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_categories_del ***");
    return $content;
}

function articles_categories_view()
{
    debug ("*** articles_categories_edit ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'categories' => ''
    );
    $i = 0;

    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_del']))
        {
            debug ("deleting category ".$_POST['id']);
            exec_query ("DELETE FROM ksh_articles_categories WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            exec_query ("DELETE FROM ksh_articles WHERE category='".mysql_real_escape_string($_POST['id'])."'");
        }

        $result = exec_query("SELECT * FROM ksh_articles_categories WHERE parent='0'");
        while ($category = mysql_fetch_array($result))
        {
        	$content['categories'][$i]['id'] = stripslashes($category['id']);
        	$content['categories'][$i]['name'] = stripslashes($category['name']);
            if (1 == $user['id'])
            {
				$content['categories'][$i]['edit_link'] = "<a href=\"/index.php?module=articles&action=category_edit&category=".stripslashes($category['id'])."\">Редактировать</a>";
	            $content['categories'][$i]['del_link'] = "<a href=\"/index.php?module=articles&action=del_category&category=".stripslashes($category['id'])."\">Удалить</a>";
			}
            $i++;
        }
        mysql_free_result($result);
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_categories_edit ***");
    return $content;
}

function articles_categories_edit()
{
    debug ("*** articles_categories_edit ***");
    global $user;
    global $config;
    $content = array(
    	'content' => '',
        'result' => '',
        'category_id' => '',
        'name' => '',
		'categories_select' => ''
    );

    if (isset($_GET['category'])) $category_id =$_GET['category'];
    else if (isset($_POST['id'])) $category_id =$_POST['id'];
    else $category_id =0;
    debug ("category id: ".$category_id);

    debug ("user id: ".$user['id']);
    if (1 == $user['id'])
    {
        debug ("user is admin");
        if (isset($_POST['do_update']))
        {
            debug ("have data to update");
            if ("" != $_POST['name'])
            {
                debug ("category name isn't empty");
                exec_query("UPDATE ksh_articles_categories set
					name='".mysql_real_escape_string($_POST['name'])."',
					parent='".mysql_real_escape_string($_POST['parent'])."',
					menu_template = '".mysql_real_escape_string($_POST['menu_template'])."'
					WHERE id='".mysql_real_escape_string($category_id)."'");
                $content['result'] .= "Изменения записаны";
            }
            else
            {
                debug ("category name is empty");
                $content['result'] .= "Пожалуйста, задайте название категории";
            }
        }
        else
        {
            debug ("no data to update");
        }

		$result = exec_query("SELECT * FROM ksh_articles_categories WHERE id='".mysql_real_escape_string($category_id)."'");
		$category = mysql_fetch_array($result);
		$content['category_id'] = stripslashes($category['id']);
		$content['name'] = stripslashes($category['name']);
		$content['parent'] = stripslashes($category['parent']);
		$content['menu_template'] = stripslashes($category['menu_template']);
		mysql_free_result($result);

		$result = exec_query("SELECT * FROM ksh_articles_categories");
		while ($category = mysql_fetch_array($result))
		{
			$id = stripslashes($category['id']);
			$categories[$id]['name'] = stripslashes($category['name']);
			$categories[$id]['parent'] = stripslashes($category['parent']);
		}
		mysql_free_result($result);

		foreach ($categories as $k => $v)
		{
			if ($k != $category_id)
			{
				$content['categories_select'][$k]['id'] = $k;
				$content['categories_select'][$k]['parent'] = $v['parent'];
				$content['categories_select'][$k]['name'] = "";
				if ($k == $content['parent'])
					$content['categories_select'][$k]['selected'] = " selected";

				$current_id = $k;
				$current_parent = $v['parent'];
				$current_name = $v['name'];
				while ("0" != $current_parent)
				{
					$content['categories_select'][$k]['name'] = $current_name." / ".$content['categories_select'][$k]['name'];
					$current_id = $current_parent;
					$current_parent = $categories[$current_id]['parent'];
					$current_name = $categories[$current_id]['name'];
				}
				$content['categories_select'][$k]['name'] = $current_name." / ".$content['categories_select'][$k]['name'];
			}
		}

    }
    else
    {
        debug ("user isn't admin");
        $content['result'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: articles_categories_edit ***");
    return $content;
}

?>
