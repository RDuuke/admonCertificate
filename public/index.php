<?php
header('Content-Type: text/html; charset=ISO-8859-1');
date_default_timezone_set("America/Bogota");
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
session_start();
ini_set('max_execution_time', 1800); //3 minutes
error_reporting(E_ALL ^ E_NOTICE);
define("DS", DIRECTORY_SEPARATOR);
define("BASE_DIR",  dirname(__DIR__) . DS);
define("BASE_TEMP", BASE_DIR . "temp");

require_once dirname(__DIR__) . DS  .  "config" . DS . "bootstrap.php";
