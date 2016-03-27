<?php

// Base functions of the "performance" module

debug ("performance module included");

include_once ($config['modules']['location']."performance/config.php");

$config_file = $config['base']['doc_root']."/config/performance.php";
if (file_exists($config_file))
	include($config_file);

// ### BY ANDREW ###########################################
function startTimer()
{
  global $starttime;
  $mtime = microtime ();
  $mtime = explode (' ', $mtime);
  $mtime = $mtime[1] + $mtime[0];
  $starttime = $mtime;
}
function endTimer()
{
  global $starttime;
  $mtime = microtime ();
  $mtime = explode (' ', $mtime);
  $mtime = $mtime[1] + $mtime[0];
  $endtime = $mtime;
  $totaltime = round (($endtime - $starttime), 5);
  return $totaltime;
}

// #########################################################


?>
