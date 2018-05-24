<?php

require __DIR__ . '/../vendor/autoload.php';

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
