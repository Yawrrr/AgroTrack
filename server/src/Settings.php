<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // Set settings
    $container->set('settings', [
        'db' => [
            'host'   => $_ENV['DB_HOST'] ,
            'dbname' => $_ENV['DB_NAME'] ,
            'user'   => $_ENV['DB_USER'] ,
            'pass'   => $_ENV['DB_PASS'] ,
            'charset'=> $_ENV['DB_CHARSET'],
        ],
    ]);
};
