<?php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'REDACTED');
define('DB_USER', 'REDACTED');
define('DB_PASS', 'REDACTED');

define('ROOT_PATH', dirname(__DIR__));

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $basePath);
define('ASSETS_URL', $basePath . '/assets');

