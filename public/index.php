<?php

require __DIR__ . '/../vendor/autoload.php';

$secretkey = '698ee85b52b7b65dde71e42705f3aa3aa276b173';
$db_user = 'root';
$db_password = '';
$db_host = 'localhost';
$db_name = 'webservice';

putenv("DB_USER=$db_user");
putenv("DB_PASSWORD=$db_password");
putenv("DB_HOST=$db_host");
putenv("DB_NAME=$db_name");
putenv("SECRETKEY=$secretkey");

// Register settings
$settings = require __DIR__ . '/../src/settings.php';

// Instantiate app
$app = new \Slim\App($settings);

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
