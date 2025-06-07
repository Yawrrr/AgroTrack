<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // PDO
    $container->set(\PDO::class, function() use ($container) {
        $c = $container->get('settings')['db'];
        $dsn = "mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}";
        return new \PDO($dsn, $c['user'], $c['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);
    });

    // Controllers
    $container->set(\App\Controllers\CropController::class, function($c) {
        return new \App\Controllers\CropController($c->get(\PDO::class));
    });

    $container->set(\App\Controllers\ReadingController::class, function($c) {
        return new \App\Controllers\ReadingController($c->get(\PDO::class));
    });

    $container->set(\App\Controllers\ActuatorController::class, function($c) {
        return new \App\Controllers\ActuatorController($c->get(\PDO::class));
    });

    $container->set(\App\Controllers\SensorController::class, function($c) {
        return new \App\Controllers\SensorController($c->get(\PDO::class));
    });
};
