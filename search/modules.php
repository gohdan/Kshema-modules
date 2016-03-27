<?php

// Modules search functions of search module

function search_bills($search_string)
{
	global $user;
	global $config;

	debug("*** search_bills ***");

	$results = array();
	$i = 0;

	debug("searching in bills categories");
	$sql_query = "SELECT * FROM `ksh_bills_categories`
		WHERE 
		`title` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Разделы объявлений";
				$if_show_heading = 0;
			}

			$results[$i]['element_type'] = "category";
			$results[$i]['ext'] = "/";
			if ("bills" != $config['modules']['default_module'])
				$results[$i]['module'] = "/bills";
			if ("view_by_category" != $config['bills']['default_action'])
				$results[$i]['action'] = "/view_by_category";
			if ("" != $row['name'] && NULL != $row['name'])
				$results[$i]['id'] = stripslashes($row['name']);
			else
				$results[$i]['id'] = stripslashes($row['id']);
				
			$results[$i]['title'] = stripslashes($row['title']);
			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];
					
			$i++;
		}
		mysql_free_result($result);
	}


	debug("searching in bills");
	$sql_query = "SELECT * FROM `ksh_bills`
		WHERE 
		`title` LIKE '%".mysql_real_escape_string($search_string)."%'
		OR
		`full_text` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Объявления";
				$if_show_heading = 0;
			}
			if ("bills" != $config['modules']['default_module'])
				$results[$i]['module'] = "/bills";

			$results[$i]['action'] = "";
			$results[$i]['element_type'] = "bill";
			$results[$i]['id'] = stripslashes($row['id']);
			if ("" != $row['name'])
				$results[$i]['name'] = stripslashes($row['name']);
			else
				$results[$i]['name'] = stripslashes($row['id']);
			$results[$i]['title'] = stripslashes($row['title']);
			$results[$i]['text'] = stripslashes($row['full_text']);
			$results[$i]['ext'] = ".html";
			if ("yes" == $config['base']['ext_links_redirect'])
			{
				include_once($config['modules']['location']."redirect/index.php");
				$results[$i]['text'] = redirect_links_replace(stripslashes($row['full_text']));
			}
			else
				$results[$i]['text'] = stripslashes($row['full_text']);

			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];

			$i++;
		}
		mysql_free_result($result);
	}
	debug("*** end: search_bills ***");

	return $results;
}

function search_articles($search_string)
{
	global $user;
	global $config;

	debug("*** search_articles ***");

	$results = array();
	$i = 0;

	debug("searching in articles categories");
	$sql_query = "SELECT * FROM `ksh_articles_categories`
		WHERE 
		`title` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Разделы статей";
				$if_show_heading = 0;
			}
			$results[$i]['element_type'] = "category";
			$results[$i]['ext'] = "/";
			if ("articles" != $config['modules']['default_module'])
				$results[$i]['module'] = "/articles";
			if ("view_by_category" != $config['articles']['default_action'])
				$results[$i]['action'] = "/view_by_category";
			if ("" != $row['name'] && NULL != $row['name'])
				$results[$i]['id'] = stripslashes($row['name']);
			else
				$results[$i]['id'] = stripslashes($row['id']);
			
			$results[$i]['title'] = stripslashes($row['title']);
			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];

			$i++;
		}
		mysql_free_result($result);
	}

	debug("searching in articles");
	$sql_query = "SELECT * FROM `ksh_articles`
		WHERE 
		`title` LIKE '%".mysql_real_escape_string($search_string)."%'
		OR
		`full_text` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Статьи";
				$if_show_heading = 0;
			}
			if ("articles" != $config['modules']['default_module'])
				$results[$i]['module'] = "articles";

			$results[$i]['action'] = "";
			$results[$i]['element_type'] = "article";
			$results[$i]['id'] = stripslashes($row['id']);
			if ("" != $row['name'])
				$results[$i]['name'] = stripslashes($row['name']);
			else
				$results[$i]['name'] = stripslashes($row['id']);
			$results[$i]['title'] = stripslashes($row['title']);
			$results[$i]['text'] = stripslashes($row['full_text']);
			$results[$i]['ext'] = ".html";
			if ("yes" == $config['base']['ext_links_redirect'])
			{
				include_once($config['modules']['location']."redirect/index.php");
				$results[$i]['text'] = redirect_links_replace(stripslashes($row['full_text']));
			}
			else
				$results[$i]['text'] = stripslashes($row['full_text']);

			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];
			debug("result ".$i.":", 2);
			dump($results[$i]);
			$i++;
		}
		mysql_free_result($result);
	}

	debug("*** end: search_articles ***");
	return $results;
}

function search_pages($search_string)
{
	global $user;
	global $config;

	debug("*** search_pages ***");

	$results = array();
	$i = 0;

	debug("searching in pages categories");
	$sql_query = "SELECT * FROM `ksh_pages_categories`
		WHERE 
		`title` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Разделы страниц";
				$if_show_heading = 0;
			}

			$results[$i]['name'] = "/pages/view_by_category/".stripslashes($row['id']);
				
			$results[$i]['title'] = stripslashes($row['title']);
			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];

			$i++;
		}
		mysql_free_result($result);
	}

	debug("searching in pages");

	$title = "title_".$config['base']['lang']['current'];
	$full_text = "full_text_".$config['base']['lang']['current'];

	$sql_query = "SELECT * FROM `ksh_pages`
		WHERE 
		`".mysql_real_escape_string($title)."` LIKE '%".mysql_real_escape_string($search_string)."%'
		OR
		`".mysql_real_escape_string($full_text)."` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Страницы";
				$if_show_heading = 0;
			}
			if ("pages" != $config['modules']['default_module'])
				$results[$i]['module'] = "pages";

			$results[$i]['action'] = "";
			$results[$i]['element_type'] = "page";
			$results[$i]['id'] = stripslashes($row['id']);
			if ("" != $row['name'])
				$results[$i]['name'] = stripslashes($row['name']);
			else
				$results[$i]['name'] = stripslashes($row['id']);
			$results[$i]['title'] = stripslashes($row[$title]);

			$results[$i]['ext'] = ".html";

			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];
			debug("result ".$i.":", 2);
			dump($results[$i]);
			$i++;
		}
		mysql_free_result($result);
	}


	debug("*** end: search_pages ***");

	return $results;
}
function search_news($search_string)
{
	global $user;
	global $config;

	debug("*** search_news ***");

	$results = array();
	$i = 0;

	debug("searching in news categories");
	$sql_query = "SELECT * FROM `ksh_news_categories`
		WHERE 
		`title` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Категории новостей";
				$if_show_heading = 0;
			}

			$results[$i]['name'] = "/news/view_by_category/".stripslashes($row['id']);
				
			$results[$i]['title'] = stripslashes($row['title']);
			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];

			$i++;
		}
		mysql_free_result($result);
	}

	debug("searching in news");

	$sql_query = "SELECT * FROM `ksh_news`
		WHERE 
		`name` LIKE '%".mysql_real_escape_string($search_string)."%'
		OR
		`short_descr` LIKE '%".mysql_real_escape_string($search_string)."%'
		OR
		`descr` LIKE '%".mysql_real_escape_string($search_string)."%'
		OR
		`full_text` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Новости";
				$if_show_heading = 0;
			}
			if ("news" != $config['modules']['default_module'])
				$results[$i]['module'] = "news";

			$results[$i]['action'] = "";
			$results[$i]['element_type'] = "news";
			$results[$i]['id'] = stripslashes($row['id']);
			$results[$i]['name'] = stripslashes($row['id']);
			$results[$i]['title'] = stripslashes($row['name']);

			$results[$i]['ext'] = ".html";

			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];
			debug("result ".$i.":", 2);
			dump($results[$i]);
			$i++;
		}
		mysql_free_result($result);
	}

	debug("*** end: search_news ***");
	return $results;
}

function search_shop($search_string)
{
	global $user;
	global $config;

	debug("*** search_shop ***");

	$results = array();
	$i = 0;

	debug("searching in shop authors");

	$sql_query = "SELECT * FROM `ksh_shop_authors`
		WHERE 
		`name` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Авторы";
				$if_show_heading = 0;
			}
	
			$results[$i]['module'] = "shop";
			$results[$i]['action'] = "view_by_authors";
			$results[$i]['id'] = stripslashes($row['id']);
			$results[$i]['name'] = "authors:".stripslashes($row['id']);
			$results[$i]['ext'] = "";
			$results[$i]['title'] = stripslashes($row['name']);


			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];
			debug("result ".$i.":", 2);
			dump($results[$i]);
			$i++;
		}
		mysql_free_result($result);
	}

	debug("searching in shop goods");

	$sql_query = "SELECT * FROM `ksh_shop_goods`
		WHERE 
		`name` LIKE '%".mysql_real_escape_string($search_string)."%' OR
		`commentary` LIKE '%".mysql_real_escape_string($search_string)."%'
		";
	$result = exec_query($sql_query);

	if ($result && mysql_num_rows($result))
	{
		$if_show_heading = 1;
		while ($row = mysql_fetch_array($result))
		{
			if ($if_show_heading)
			{
				$results[$i]['heading'] = "Товары";
				$if_show_heading = 0;
			}
	
			$results[$i]['module'] = "shop";
			$results[$i]['action'] = "view_good";
			$results[$i]['id'] = stripslashes($row['id']);
			$results[$i]['name'] = "good:".stripslashes($row['id']);
			$results[$i]['ext'] = "";
			$results[$i]['title'] = stripslashes($row['name']);

			$results[$i]['inst_root'] = $config['base']['inst_root'];
			$results[$i]['site_url'] = $config['base']['site_url'];
			debug("result ".$i.":", 2);
			dump($results[$i]);
			$i++;
		}
		mysql_free_result($result);
	}

	debug("*** end: search_shop ***");
	return $results;
}


?>
