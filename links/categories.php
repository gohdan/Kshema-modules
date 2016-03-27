<?php

// Categories administration functions of the links module

function links_categories_add()
{
    debug ("*** fn: links_categories_add ***");
    global $user;
    global $debug;
    $content['content'] = "";

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
                exec_query("INSERT INTO ksh_links_categories (name) VALUES ('".mysql_real_escape_string($_POST['name'])."')");
                $content['content'] .= "<p>Категория добавлена</p>";
            }
            else
            {
                debug ("category name is empty");
                $content['content'] .= "<p>Пожалуйста, задайте имя категории</p>";
            }
        }
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "<p>Пожалуйста, войдите в систему как администратор.</p>";
    }

    debug ("*** end: fn: links_categories_add ***");
    return $content;
}

function links_categories_del()
{
    debug ("*** fn: links_categories_del ***");
    global $user;
    $content['content'] = "";
    $content['id'] = "";
    $content['name'] = "";

    if (1 == $user['id'])
    {
        debug ("user has admin rights");
        $content['id'] = $_GET['category'];
        $content['name'] = mysql_result(exec_query("SELECT name FROM ksh_links_categories WHERE id='".mysql_real_escape_string($_GET['category'])."'"), 0, 0);
    }
    else
    {
        debug ("user doesn't have admin rights!");
        $content['content'] .= "<p>Пожалуйста, войдите в систему как администратор</p>";
    }

    debug ("*** end: fn: links_categories_del ***");
    return $content;
}

function links_categories_view()
{
    debug ("*** fn: links_categories_edit ***");
    global $user;
    global $debug;

	$content['content'] = "";
    $content['categories'] = "";

    if (1 == $user['id'])
    {
        debug ("user is admin");

        if (isset($_POST['do_del']))
        {
            debug ("deleting category ".$_POST['id']);
            exec_query ("DELETE FROM ksh_links_categories WHERE id='".mysql_real_escape_string($_POST['id'])."'");
            exec_query ("DELETE FROM ksh_links WHERE category='".mysql_real_escape_string($_POST['id'])."'");
        }

        $content['categories'] .= "<table>";
        $result = exec_query("SELECT * FROM ksh_links_categories");
        while ($category = mysql_fetch_array($result))
        {
            $content['categories'] .= "<tr>";
            $content['categories'] .= "<td><a href=\"/index.php?module=links&action=view_by_category&category=".$category['id']."\">".$category['name']."</a></td>";
            $content['categories'] .= "<td><a href=\"/index.php?module=links&action=category_edit&category=".$category['id']."\">Редактировать</a></td>";
            $content['categories'] .= "<td><a href=\"/index.php?module=links&action=del_category&category=".$category['id']."\">Удалить</a></td>";
            $content['categories'] .= "</tr>";
        }
        mysql_free_result($result);
        $content['categories'] .= "</table>";
    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "<p>Пожалуйста, войдите в систему как администратор.</p>";
    }

    debug ("*** end: fn: links_categories_edit ***");
    return $content;
}

function links_categories_edit()
{
    debug ("*** fn: links_categories_edit ***");
    global $user;
    global $debug;
    $content['content'] = "";
	$content['category_id'] = "";
	$content['name'] = "";

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
                exec_query("UPDATE ksh_links_categories set name='".mysql_real_escape_string($_POST['name'])."' WHERE id='".mysql_real_escape_string($category_id)."'");
                $content['content'] .= "<p>Изменения записаны</p>";
            }
            else
            {
                debug ("category name is empty");
                $content['content'] .= "<p>Пожалуйста, задайте название категории</p>";
            }
        }
        else
        {
            debug ("no data to update");
        }

            $result = exec_query("SELECT * FROM ksh_links_categories WHERE id='".mysql_real_escape_string($category_id)."'");
            $category = mysql_fetch_array($result);
			$content['category_id'] = $category['id'];
			$content['name'] = $category['name'];
            mysql_free_result($result);


    }
    else
    {
        debug ("user isn't admin");
        $content['content'] = "<p>Пожалуйста, войдите в систему как администратор.</p>";
    }

    debug ("*** end: fn: links_categories_edit ***");
    return $content;
}


?>