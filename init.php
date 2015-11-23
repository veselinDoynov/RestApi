<?php


define('PATH', dirname(__FILE__));
define('PATH_TO_ROOT', dirname($_SERVER['SCRIPT_NAME']));
define('TITLE_PREFIX', 'ResrApi');
require_once PATH.'/classes/Router.php';
require_once PATH.'/functions.php';


//DB::getInstance('apidb');
//exit;

session_start();

//dbRequired('apidb');

?>
