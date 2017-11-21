<?php

// Categories administration functions of the "houses" module

function houses_categories_add()
{
    debug ("*** houses_categories_add ***");
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
                exec_query("INSERT INTO ksh_houses_categories (name, title) VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['title'])."')");
                $content['result'] .= "Категория добавлена";
                
				$category_dir = $config['base']['doc_root'].$config['base']['domain_dir']."/uploads/houses/".$_POST['name'];
				debug ("category dir: ".$category_dir);
				if (file_exists($category_dir))
				{
					debug ("category dir already exists!");
				}
				else
				{
					debug ("category dir doesn't exist");
					mkdir ($category_dir);
				}
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

    debug ("*** end: houses_categories_add ***");
    return $content;
}

function houses_categories_del()
{
    debug ("*** houses_categories_del ***");
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
        $content['name'] = mysql_result(exec_query("SELECT name FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($_GET['category'])."'"), 0, 0);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: houses_categories_del ***");
    return $content;
}

function houses_categories_view()
{
    debug ("*** houses_categories_view ***");
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
            exec_query ("DELETE FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            exec_query ("DELETE FROM ksh_houses WHERE category='".mysql_real_escape_string($_POST['id'])."'");
        }

        $result = exec_query("SELECT * FROM ksh_houses_categories");
        while ($category = mysql_fetch_array($result))
        {
        	$content['categories'][$i]['id'] = $category['id'];
            $content['categories'][$i]['name'] = $category['name'];
            $content['categories'][$i]['title'] = $category['title'];
			$i++;            
        }
        mysql_free_result($result);
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: houses_categories_view ***");
    return $content;
}

function houses_categories_edit()
{
    debug ("*** houses_categories_edit ***");
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
                exec_query("UPDATE ksh_houses_categories set name='".mysql_real_escape_string($_POST['name'])."', title='".mysql_real_escape_string($_POST['title'])."' WHERE id='".mysql_real_escape_string($category_id)."'");
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

            $result = exec_query("SELECT * FROM ksh_houses_categories WHERE id='".mysql_real_escape_string($category_id)."'");
            $category = mysql_fetch_array($result);
            mysql_free_result($result);
            $content['category_id'] = stripslashes($category['id']);
            $content['name'] = stripslashes($category['name']);
            $content['title'] = stripslashes($category['title']);
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "Пожалуйста, войдите в систему как администратор";
    }

    debug ("*** end: houses_categories_edit ***");
    return $content;
}

?>