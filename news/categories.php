<?php

// Categories administration functions of the "news" module

function news_categories_add()
{
    debug ("*** news_categories_add ***");
    global $config;
    global $user;
	$content = array(
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
                debug ("category name isn't empty");
                exec_query("INSERT INTO ksh_news_categories (name, title, page_template, template, list_template, news_template, menu_template) VALUES (
					'".mysql_real_escape_string($_POST['name'])."',
					'".mysql_real_escape_string($_POST['title'])."',
					'".mysql_real_escape_string($_POST['page_template'])."',
					'".mysql_real_escape_string($_POST['template'])."',
					'".mysql_real_escape_string($_POST['list_template'])."',
					'".mysql_real_escape_string($_POST['news_template'])."',
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
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_categories_add ***");
    return $content;
}

function news_categories_del()
{
    debug ("*** news_categories_del ***");
    global $config;
    global $user;
    $content = array(
    	'content' => '',
        'id' => '',
        'name' => ''
    );

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $content['id'] = $_GET['category'];
        $content['name'] = mysql_result(exec_query("SELECT name FROM ksh_news_categories WHERE id='".mysql_real_escape_string($_GET['category'])."'"), 0, 0);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_categories_del ***");
    return $content;
}

function news_categories_view()
{
    debug ("*** news_categories_view ***");
    global $config;
    global $user;
	$content = array (
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
            exec_query ("DELETE FROM ksh_news_categories WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            exec_query ("DELETE FROM ksh_news WHERE category='".mysql_real_escape_string($_POST['id'])."'");
        }

        $result = exec_query("SELECT * FROM ksh_news_categories");
      	while ($category = mysql_fetch_array($result))
		{
	       	$content['categories'][$i]['id'] = $category['id'];
            $content['categories'][$i]['name'] = $category['name'];
            $content['categories'][$i]['title'] = $category['title'];
			$content['categories'][$i]['page_template'] = $category['page_template'];
			$content['categories'][$i]['template'] = $category['template'];
			$content['categories'][$i]['list_template'] = $category['list_template'];
			$content['categories'][$i]['news_template'] = $category['news_template'];
			$i++;
        }
        mysql_free_result($result);
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_categories_view ***");
    return $content;
}

function news_categories_edit()
{
    debug ("*** news_categories_edit ***");
    global $config;
    global $user;
    $content = array(
    	'content' => '',
        'result' => '',
        'category_id' => '',
        'name' => '',
        'title' => ''
    );

    if (isset($_GET['category'])) $category_id = $_GET['category'];
	else if (isset($_GET['element'])) $category_id = $_GET['element'];
	else if (isset($_POST['id'])) $category_id = $_POST['id'];
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
                exec_query("UPDATE ksh_news_categories set 
					name='".mysql_real_escape_string($_POST['name'])."',
					title='".mysql_real_escape_string($_POST['title'])."',
					page_template='".mysql_real_escape_string($_POST['page_template'])."', 
					template='".mysql_real_escape_string($_POST['template'])."', 
					list_template='".mysql_real_escape_string($_POST['list_template'])."', 
					news_template='".mysql_real_escape_string($_POST['news_template'])."', 
					menu_template='".mysql_real_escape_string($_POST['menu_template'])."' 
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

            $result = exec_query("SELECT * FROM ksh_news_categories WHERE id='".mysql_real_escape_string($category_id)."'");
            $category = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['category_id'] = stripslashes($category['id']);
            $content['name'] = stripslashes($category['name']);
            $content['title'] = stripslashes($category['title']);
			$content['page_template'] = stripslashes($category['page_template']);
			$content['template'] = stripslashes($category['template']);
			$content['list_template'] = stripslashes($category['list_template']);
			$content['news_template'] = stripslashes($category['news_template']);
			$content['menu_template'] = stripslashes($category['menu_template']);
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: news_categories_edit ***");
    return $content;
}

?>
