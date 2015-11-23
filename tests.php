<?php

define('PATH', dirname(__FILE__));

require_once PATH.'/classes/Router.php';
require_once PATH.'/functions.php';


dbRequired('apidb_test');

$tests =  new ApiTests();
$tests->run();

