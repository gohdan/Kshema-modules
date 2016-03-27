<?php

function create_tables($tables)
{
	$queries[] = "create table ".$tables['tasks']." (
		id int auto_increment primary key,
		user_from int,
		user_to int,
		status tinyint,
		deadline datetime,
		prior tinyint,
		name tinytext,
		descr text,
		attach tinytext
	)";

	$queries_qty = count($queries);
	$content .= "<p>Number of queries to DB: ".$queries_qty."</p>\n<hr>\n";

	if ($queries_qty > 0)
	{
		foreach ($queries as $idx => $sql_query) exec_query ($sql_query);
		$content .= "<p>Запросы выполнены</p>";
	}

	return $content;
}


function drop_tables($tables)
{
	debug ("*** drop_tables");

	foreach ($tables as $k => $v) exec_query ("DROP TABLE ".$v);
	$content = "<p>Таблицы БД успешно удалены</p>";

	debug ("*** end: drop_tables");
	return $content;
}

function incoming($user, $tables)
{
	debug ("*** incoming");
	$content = "";

	$result = exec_query ("SELECT id,prior,name,deadline FROM ".$tables['tasks']." where user_to='".$user['id']."'");

	$content .= "<table style=\"width: 100%\"><tr><td style=\"background-color: #eeeeee\"></td><td style=\"background-color: #eeeeee; text-align: center\">Поручение</td><td style=\"background-color: #ff9900; color: white; font-weight: bold; text-align: center\">Срок исполнения <img src=\"/themes/default/images/sort_ascend.gif\"></td></tr>";
	while ($row = mysql_fetch_array ($result))
	{
		$content .= "<tr>";
		$content .= "<td>";
		switch ($row['prior'])
		{
			default: $content .= "<img src=\"/themes/default/images/flag_green.gif\">"; break;
			case "1": $content .= "<img src=\"/themes/default/images/flag_red.gif\">"; break;
			case "2": $content .= "<img src=\"/themes/default/images/flag_blue.gif\">"; break;
		}
		$content .= "</td>";
		$content .= "<td><a href=\"/index.php?module=tasks&action=view&id=".$row['id']."\">".$row['name']."</a></td>";
		$content .= "<td style=\"width: 150px; text-align: center\">".$row['deadline']."</td>";
		$content .= "</tr>";
	}
	$content .="</table>";
	mysql_free_result ($result);

	debug ("*** end: incoming");
	return $content;
}

function outgoing($user, $tables)
{
	debug ("*** outgoing");
	$content = "";

	$result = exec_query ("SELECT id,prior,name,deadline FROM ".$tables['tasks']." where user_from='".$user['id']."'");

	$content .= "<table style=\"width: 100%\"><tr><td style=\"background-color: #eeeeee\"></td><td style=\"background-color: #eeeeee; text-align: center\">Поручение</td><td style=\"background-color: #ff9900; color: white; font-weight: bold; text-align: center\">Срок исполнения <img src=\"/themes/default/images/sort_ascend.gif\"></td></tr>";
	while ($row = mysql_fetch_array ($result))
	while ($row = mysql_fetch_array ($result))
	{
		$content .= "<tr>";
		$content .= "<td>";
		switch ($row['prior'])
		{
			default: $content .= "<img src=\"/themes/default/images/flag_green.gif\">"; break;
			case "1": $content .= "<img src=\"/themes/default/images/flag_red.gif\">"; break;
			case "2": $content .= "<img src=\"/themes/default/images/flag_blue.gif\">"; break;
		}
		$content .= "</td>";
		$content .= "<td><a href=\"/index.php?module=tasks&action=view&id=".$row['id']."\">".$row['name']."</a></td>";
		$content .= "<td style=\"width: 150px; text-align: center\">".$row['deadline']."</td>";
		$content .= "</tr>";

	}
	$content .="</table>";
	mysql_free_result ($result);

	debug ("*** end: outgoing");
	return $content;
}

function own($user, $tables)
{
	debug ("*** own");
	$content = "";

	debug ("table: ".$tables['tasks']);
	$result = exec_query ("SELECT id,prior,name,deadline FROM ".$tables['tasks']." where user_to='".$user['id']."' and user_from='".$user['id']."'");

	$content .= "<table style=\"width: 100%\"><tr><td style=\"background-color: #eeeeee\"></td><td style=\"background-color: #eeeeee; text-align: center\">Поручение</td><td style=\"background-color: #ff9900; color: white; font-weight: bold; text-align: center\">Срок исполнения <img src=\"/themes/default/images/sort_ascend.gif\"></td></tr>";
	while ($row = mysql_fetch_array ($result))
	{
		$content .= "<tr>";
		$content .= "<td>";
		switch ($row['prior'])
		{
			default: $content .= "<img src=\"/themes/default/images/flag_green.gif\">"; break;
			case "1": $content .= "<img src=\"/themes/default/images/flag_red.gif\">"; break;
			case "2": $content .= "<img src=\"/themes/default/images/flag_blue.gif\">"; break;
		}
		$content .= "</td>";
		$content .= "<td><a href=\"/index.php?module=tasks&action=view&id=".$row['id']."\">".$row['name']."</a></td>";
		$content .= "<td style=\"width: 150px; text-align: center\">".$row['deadline']."</td>";
		$content .= "</tr>";
	}
	$content .="</table>";
	mysql_free_result ($result);

	debug ("*** end: own");
	return $content;
}


function view($user, $table, $id)
{
	debug ("*** view");
	$content = "";
	$result = exec_query ("SELECT * FROM ".$table." WHERE id='".$id."'");

	$row = mysql_fetch_array ($result);
	mysql_free_result ($result);

	$content .= "<div align=right><img src=\"/themes/default/images/accept.gif\" alt=\"Подтвердить\" title=\"Подтвердить\"><img src=\"/themes/default/images/reject.gif\" alt=\"Отказаться\" title=\"Отказаться\"></div>";
	$content .= "<p>";
	if ($user['id'] != $row['user_from']) $content .= "<b>Получено от:</b> ".$row['user_from']."<br>";
	if ($user['id'] != $row['user_to']) $content .= "<b>Направлено:</b> ".$row['user_to']."<br>";
	$content .= "<b>Название:</b> <font color=\"blue\">".$row['name']."</font><br>";
	$content .= "<b>Срок исполнения:</b> <font color=\"blue\">".$row['deadline']."</font><br>";
	$content .= "<b>Приоритет:</b> <b>".$row['prior']."</b><br>";
	$content .= "<div style=\"display: inline; clear: right; text-align: left\"><b>Описание:</b></div> ".$row['descr']."";

	$content .= "</p>";
	$content .= "<div align=right><img src=\"/themes/default/images/discuss.gif\" alt=\"Обсудить\" title=\"Обсудить\"> <img src=\"/themes/default/images/send_report.gif\" alt=\"Отправить отчёт\" title=\"Отправить отчёт\"></div>";

	debug ("*** end: view");
	return $content;
}


function create($user, $tables)
{
	debug ("*** create");
	$content = "";
	$default_status = 1;

	$content .= "
		<h1>Создание задания</h1>
		<form action=\"/index.php?module=tasks\" method=\"post\">
			<input type=\"hidden\" name=\"table\" value=\"".$tables['tasks']."\">
			<input type=\"hidden\" name=\"user_from\" value=\"".$user['id']."\">
			<input type=\"hidden\" name=\"status\" value=\"".$default_status."\">

			<table>
			<tr>
				<td>Название:</td>
				<td><input type=\"text\" name=\"name\"></td>
			</tr>
			<tr>
				<td>Исполнитель:</td>
				<td><input type=\"text\" name=\"user_to\">
					<img src=\"/themes/default/images/userlist.gif\" alt=\"Адресная книга\" title=\"Адресная книга\">
				</td>
			</tr>
			<tr>
				<td>Время выполнения:</td>
				<td>
					<select><option>Июнь</option></select>
					<select><option>23</option></select>
					<select><option>2006</option></select>
					с <input type=\"text\" size=\"4\">
					по <input type=\"text\" size=\"4\">
				</td>
			</tr>
			<tr>
				<td>Приоритет:</td>
				<td><input type=\"text\" name=\"prior\"></td>
			</tr>
			<tr>
				<td>Вложение:</td>
				<td><input type=\"text\" name=\"attach\">
					<img src=\"/themes/default/images/attach.gif\" alt=\"Вложение\" title=\"Вложение\">
				</td>
			</tr>
			<tr>
				<td>Описание:</td>
				<td><textarea rows=\"10\" cols=\"30\" name=\"descr\"></textarea></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type=\"radio\" checked>Без отчёта
					<input type=\"radio\">Предоставить отчёт
				</td>
			</tr>
			</table>
			<div style=\"text-align: right\">
			<input type=\"submit\" name=\"do_insert\" value=\"Отправить\">
			</div>
		</form>
	";

	debug ("*** end: create");
	return $content;
}

function planner_day($user, $tables)
{
	$content = incoming ($user, $tables);
	return $content;
}

function planner_week($user, $tables)
{
	$content = "";

	$content = "
<table class=\"planner_week\">
<tr>
	<td rowspan=\"2\" style=\"background-color: #cccccc\"></td>
	<td class=\"header\" style=\"color: #00669a\">понедельник</td>
	<td class=\"header\" style=\"color: #00669a\">вторник</td>
	<td class=\"header\" style=\"color: #00669a\">среда</td>
	<td class=\"header\" style=\"color: #00669a\">четверг</td>
	<td class=\"header\" style=\"color: #00669a\">пятница</td>
	<td class=\"header\" style=\"color: #00669a\">суббота</td>
	<td class=\"header\" style=\"color: #00669a\">воскресенье</td>
</tr>
<tr>
	<td class=\"header\" style=\"background-color: #f0f0f0\">28</td>
	<td class=\"header\" style=\"background-color: #f0f0f0\">29</td>
	<td class=\"header\" style=\"background-color: #f0f0f0\">30</td>
	<td class=\"header\" style=\"background-color: #f0f0f0\">1</td>
	<td class=\"header\" style=\"background-color: #ff9900\">2</td>
	<td class=\"header\" style=\"background-color: #f0f0f0\">3</td>
	<td class=\"header\" style=\"background-color: #f0f0f0\">4</td>
</tr>
<tr>
	<td>8:00 - 9:00</td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>9:00 - 10:00</td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>10:00 - 11:00</td>
	<td></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>11:00 - 12:00</td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
</tr>
<tr>
	<td>12:00 - 13:00</td>
	<td></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>13:00 - 14:00</td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>14:00 - 15:00</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>15:00 - 16:00</td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
</tr>
<tr>
	<td>16:00 - 17:00</td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>17:00 - 18:00</td>
	<td><img src=\"/themes/default/images/task_small.gif\"><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
	<td></td>
	<td><img src=\"/themes/default/images/task_small.gif\"></td>
	<td></td>
	<td></td>
</tr>
</table>


	";

	return $content;
}

function planner_month($user, $tables)
{
	$content = "";

	$content = "
<table class=\"planner_month\">
<tr>
	<td class=\"header\" style=\"color: #00669a\">понедельник</td>
	<td class=\"header\" style=\"color: #00669a\">вторник</td>
	<td class=\"header\" style=\"color: #00669a\">среда</td>
	<td class=\"header\" style=\"color: #00669a\">четверг</td>
	<td class=\"header\" style=\"color: #00669a\">пятница</td>
	<td class=\"header\" style=\"color: #00669a\">суббота</td>
	<td class=\"header\" style=\"color: #00669a\">воскресенье</td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td>1</td>
	<td class=\"task\">2</td>
	<td>3</td>
	<td>4</td>
</tr>
<tr>
	<td class=\"task\">5</td>
	<td>6</td>
	<td>7</td>
	<td class=\"task\">8</td>
	<td>9</td>
	<td>10</td>
	<td class=\"task\">11</td>
</tr>
<tr>
	<td>12</td>
	<td>13</td>
	<td>14</td>
	<td class=\"task\">15</td>
	<td>16</td>
	<td>17</td>
	<td>18</td>
</tr>
<tr>
	<td>19</td>
	<td>20</td>
	<td class=\"task\">21</td>
	<td class=\"task\">22</td>
	<td>23</td>
	<td>24</td>
	<td>25</td>
</tr>
<tr>
	<td class=\"task\">26</td>
	<td class=\"task\">27</td>
	<td>28</td>
	<td>29</td>
	<td class=\"task\">30</td>
	<td></td>
	<td></td>
</tr>

</table>


	";

	return $content;}


function tasks_default_action()
{
	global $user;
	$tables['tasks'] = "tasks";
	$content = "";
	$nav_string = "
		<p>
			<a href=\"/index.php?module=tasks&action=incoming\">Входящие задания</a>
			<a href=\"/index.php?module=tasks&action=outgoing\">Исходящие задания</a>
			<a href=\"/index.php?module=tasks&action=create\">Создать задание</a>
			<a href=\"/index.php?module=tasks&action=create_tables\">Создать таблицы</a>
			<a href=\"/index.php?module=tasks&action=drop_tables\">Удалить таблицы</a>
		</p>
	";

	$content .= $nav_string;

	debug("<br>=== mod: tasks ===");

	if (isset($_POST['do_insert']))
	{
		debug ("inserting to DB");
		debug ("POST vars:");
		foreach ($_POST as $k => $v) debug ($k.":".$v);
		$table_name = $_POST['table'];
		unset ($_POST['do_insert']);
		unset ($_POST['table']);

		$keys = "";
		$values = "";
		foreach ($_POST as $name => $value)
		{
			$keys .= mysql_real_escape_string($name).",";
			$values .= "'".mysql_real_escape_string($value)."',";
		}
		$keys = ereg_replace(",$", "", $keys);
		$values = ereg_replace(",$", "", $values);
		$sql_query = "INSERT INTO ".mysql_real_escape_string($table_name)." (".$keys.") values (".$values.")";
		exec_query ($sql_query);
		$content .= "<p>Задание создано</p>";
	}

	if (isset($_GET['action']))
	{
		debug ("action: ".$_GET['action']);

		switch ($_GET['action'])
		{
			default: break;
			case "create_tables": $content .= create_tables($tables); break;
			case "drop_tables": $content .= drop_tables($tables); break;
			case "incoming": $content .= incoming($user, $tables); break;
			case "outgoing": $content .= outgoing($user, $tables); break;
			case "create": $content .= create($user, $tables); break;
			case "view": $content .= view($user, $tables['tasks'], $_GET['id']); break;
			case "own": $content .= own($user, $tables); break;
			case "planner_day": $content .= planner_day($user, $tables); break;
			case "planner_week": $content .= planner_week($user, $tables); break;
			case "planner_month": $content .= planner_month($user, $tables); break;
		}
	}

	else
	{
		debug ("*** default");
	}

	debug("=== end: mod: tasks ===<br>");
	return $content;
}

?>
