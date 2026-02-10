<?php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'g3_database_actual');
define('DB_USER', 'test_user');
define('DB_PASS', 'strong_password');

define('ROOT_PATH', dirname(__DIR__));

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $basePath);
define('ASSETS_URL', $basePath . '/assets');


// $host = '127.0.0.1';
// $dbname = "g3_database_actual";
// $dbuser = "test_user";
// $dbpass = "strong_password";
// $users_table = "users";
// $subject_table = "subject";