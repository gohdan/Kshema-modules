<?php

// Base functions of the "booking" module


include_once ("db.php");
include_once ("booking.php");
include_once ("rooms.php");
include_once ("prices.php");
include_once ("transfer.php");

function booking_admin()
{
	debug ("*** booking_admin ***");
	global $config;
    global $user;
    $content = array(
    	'content' => '',
    	'heading' => ''
    );
    $content['heading'] = "Администрирование страниц сайта";
	debug ("*** end: booking_admin ***");
    return $content;
}

function booking_help()
{
	debug ("*** booking_help ***");
	global $config;
	global $user;
	$content['content'] = "";

	debug ("*** end: booking_help ***");
	return $content;
}

function booking_frontpage()
{
        debug ("*** booking_frontpage ***");
        global $config;
        $content = array(
        	'content' => '',
			'if_show_admin_link' => ''
        );
		$priv = new Privileges();
		if ($priv -> has("booking", "admin", "write"))
			$content['if_show_admin_link'] = "yes";

        debug ("*** end: booking_frontpage ***");
        return $content;
}



function booking_default_action()
{
        global $user;
        global $config;

        $content = "";

		if(isset($_GET['element']) && !isset($_GET['page']))
			$_GET['page'] = rtrim($_GET['element'], "/");

		$module_data = array (
			'module_name' => "booking",
			'module_title' => "Бронирование номеров"
		);
		$config['booking']['page_title'] = $module_data['module_title'];
		$config['themes']['page_title']['module'] = "Бронирование номеров";


        debug("<br>=== mod: booking ===");

        if (isset($_GET['action']))
        {
			if (isset($_POST['do_del_category']))
			{
				debug ("deleting category");
				$cat = new Category();
				$result = $cat -> del("ksh_booking_categories", "ksh_booking", $_POST['category']);
			}
			
                debug ("*** action: ".$_GET['action']);
                switch ($_GET['action'])
                {
                        default:
							$config['themes']['page_title']['action'] = "";
							$content .= gen_content("booking", "frontpage", booking_frontpage());
                        break;

                        case "help":
							$config['themes']['page_title']['action'] = "Справка";
                            $content .= gen_content("booking", "help", booking_help());
                        break;

                        case "create_tables":
							$config['themes']['page_title']['action'] = "Создание таблиц БД";
                            $content .= gen_content("booking", "tables_create", booking_tables_create());
                        break;

                        case "drop_tables":
							$config['themes']['page_title']['action'] = "Удаление таблиц БД";
                            $content .= gen_content("booking", "drop_tables", booking_tables_drop());
                        break;

                        case "update_tables":
							$config['themes']['page_title']['action'] = "Обновление таблиц БД";
					        $content .= gen_content("booking", "tables_update", booking_tables_update());
                        break;

                        case "add":
							$config['themes']['page_title']['action'] = "Добавление брони";

							if (isset($_POST['do_add']))
							{
								$sql_query = "SELECT MAX(id) FROM `ksh_booking`";
								$result = exec_query($sql_query);
								$row = mysql_fetch_array($result);
								mysql_free_result($result);
								$last_id = stripslashes($row['MAX(id)']);
								$log = new Log();
								$log -> add("booking", "add", "Добавлена <a href=\"/booking/view/".$last_id."/\">бронь</a>");
							}
                            $content .= gen_content("booking", "add", booking_add());
                        break;

						case "del":
							$config['themes']['page_title']['action'] = "Удаление брони";
							$config['themes']['page_tpl'] = "orders_calendar";

							if (isset($_POST['do_del']))
							{
								$log = new Log();
								$log -> add("booking", "del", "Удалена бронь ".$_GET['element']);
							}
                            $content .= gen_content("booking", "del", booking_del());
                        break;

                        case "edit":
							$config['themes']['page_tpl'] = "orders_calendar";
							$config['themes']['page_title']['action'] = "Редактирование брони";

							if (isset($_POST['do_update']))
							{
								$log = new Log();
								$log -> add("booking", "edit", "Изменена <a href=\"/booking/view/".$_GET['element']."/\">бронь</a>");	
							}
                            $content .= gen_content("booking", "edit", booking_edit());
                        break;

                        case "admin":
							$config['themes']['page_title']['action'] = "Администрирование";
                            $content .= gen_content("booking", "admin", booking_admin());
                        break;

                        case "view":
							$config['themes']['page_title']['action'] = "Просмотр брони";
							$_GET['module'] = "booking";
							$_GET['action'] = "view";
							$config['themes']['page_tpl'] = "orders_calendar";

							$content .= gen_content("booking", "view", booking_view($_GET['page']));
                        break;

                        case "list_view":
							$config['themes']['page_title']['action'] = "Список броней";
							$config['themes']['page_tpl'] = "orders_calendar";
							
							if (isset($_POST['do_del']))
							{
								$log = new Log();
								$log -> add("booking", "del", "Удалена бронь ".$_POST['id']);
							}
                            $content .= gen_content("booking", "list_view", booking_list_view());
                        break;

                        case "orders_list":
							$config['themes']['page_tpl'] = "orders_list";
							$config['themes']['page_title']['action'] = "Список заказов";
                            $content .= gen_content("booking", "orders_list", booking_orders_list());
                        break;

                        case "orders_calendar":
							$config['themes']['page_tpl'] = "orders_calendar";
							$config['themes']['page_title']['action'] = "Календарь заказов";
                            $content .= gen_content("booking", "orders_calendar", booking_orders_calendar());
                        break;

                        case "rooms_edit":
							$config['themes']['page_title']['action'] = "Редактирование номеров";

							if (isset($_POST['do_update']))
							{
								$log = new Log();
								$log -> add("booking", "rooms_edit", "Отредактированы номера");
							}
                            $content .= gen_content("booking", "rooms_edit", booking_rooms_edit());
                        break;

                        case "prices_edit":
							$config['themes']['page_title']['action'] = "Редактирование цен";

							if (isset($_POST['do_update']))
							{
								$log = new Log();
								$log -> add("booking", "prices_edit", "Отредактированы цены");
							}
                            $content .= gen_content("booking", "prices_edit", booking_prices_edit());
                        break;
						
                        case "prices_get":
							$config['themes']['page_title']['action'] = "Получение цены";
                            $content .= gen_content("booking", "prices_get", booking_prices_get());
                        break;
						
						case "privileges_edit":
							$config['themes']['page_title']['action'] = "Назначение прав";						
							$template['title'] .= " - Назначение прав";
							$priv = new Privileges();
							$cnt = $priv -> edit("booking");

							if (isset($_POST['do_update']))
							{
								$log = new Log();
								$log -> add("booking", "privileges_edit", "Изменены права доступа");
							}
							$content .= gen_content("bbcpanel", "privileges_edit", array_merge($module_data, $cnt));
						break;
						
                        case "transfer_add":
							$config['themes']['page_title']['action'] = "Добавление трансфера";

							if (isset($_POST['do_add']))
							{
								$sql_query = "SELECT MAX(id) FROM `ksh_booking_transfer`";
								$result = exec_query($sql_query);
								$row = mysql_fetch_array($result);
								mysql_free_result($result);
								$last_id = stripslashes($row['MAX(id)']);
								$log = new Log();
								$log -> add("booking", "add", "Добавлена <a href=\"/booking/view/".$last_id."/\">бронь</a>");

								$log = new Log();
								$log -> add("booking", "transfer_add", "Добавлен <a href=\"/booking/transfer_view/".$last_id."/page_template:orders_calendar\">трансфер</a>");
							}
                            $content .= gen_content("booking", "transfer_add", booking_transfer_add());
                        break;
						
                        case "transfer_edit":
							$config['themes']['page_title']['action'] = "Редактирование трансфера";

							if (isset($_POST['do_update']))
							{
								$log = new Log();
								$log -> add("booking", "transfer_edit", "Отредактирован <a href=\"/booking/transfer_view/".$_GET['element']."/page_template:orders_calendar\">трансфер </a>");
							}
                            $content .= gen_content("booking", "transfer_edit", booking_transfer_edit());
                        break;
						
                        case "transfer_del":
							$config['themes']['page_title']['action'] = "Удаление трансфера";
                            $content .= gen_content("booking", "transfer_del", booking_transfer_del());
                        break;
						
                        case "transfer_view":
							$config['themes']['page_title']['action'] = "Просмотр трансфера";
                            $content .= gen_content("booking", "transfer_view", booking_transfer_view());
                        break;
						
                        case "transfer_calendar_view":
							$config['themes']['page_title']['action'] = "Планирование трансфера";

							if (isset($_POST['do_del']))
							{
								$log = new Log();
								$log -> add("booking", "transfer_del", "Удалён трансфер ".$_POST['element']);
							}
                            $content .= gen_content("booking", "transfer_calendar_view", booking_transfer_calendar_view());
                        break;
												
                        case "log_view":
							$config['themes']['page_title']['action'] = "Просмотр уведомлений";

							$log = new Log();
							$cnt = $log -> view("booking");

                            $content .= gen_content("booking", "log_view", $cnt);
                        break;

                }
        }

        else
        {
                debug ("*** action: default");
				$config['themes']['page_title']['action'] = "";
                $content = gen_content("booking", "frontpage", booking_frontpage());
        }

        debug("=== end: mod: booking ===<br>");
        return $content;

}

?>
