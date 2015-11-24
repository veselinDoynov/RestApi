<?php

require_once 'init.php';

dbRequired('apidb_test');

$tests =  new ApiTests();
$tests->run();

