<?php
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create Container
$container = new Container();

// Create App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Load config, DI, middleware, routes
(require __DIR__ . '/../src/Settings.php')($app);
(require __DIR__ . '/../src/Dependencies.php')($app);
(require __DIR__ . '/../src/Middleware.php')($app);
(require __DIR__ . '/../src/Routes.php')($app);

$app->run();