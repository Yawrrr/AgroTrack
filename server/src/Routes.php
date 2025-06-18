<?php

use Slim\App;

return function (App $app) {
  // PUBLIC ROUTES (no authentication required)
  $app->post('/api/auth/register', \App\Controllers\AuthController::class . ':register');
  $app->post('/api/auth/login', \App\Controllers\AuthController::class . ':login');

  // PROTECTED ROUTES (authentication required)
  $app->group('/api', function ($group) {
    // AUTH ROUTES
    $group->get('/auth/me', \App\Controllers\AuthController::class . ':me');
    $group->post('/auth/logout', \App\Controllers\AuthController::class . ':logout');

    // CROPS
    $group->get('/crops', \App\Controllers\CropController::class . ':index');
    $group->get('/crops/{id}', \App\Controllers\CropController::class . ':show');
    $group->post('/crops', \App\Controllers\CropController::class . ':create');
    $group->put('/crops/{id}', \App\Controllers\CropController::class . ':update');
    $group->delete('/crops/{id}', \App\Controllers\CropController::class . ':delete');

    // READINGS
    $group->get('/readings', \App\Controllers\ReadingController::class . ':index');

    // SENSORS
    $group->get('/sensors', \App\Controllers\SensorController::class . ':index');
    $group->post('/sensors', \App\Controllers\SensorController::class . ':create');
    $group->put('/sensors/{id}', \App\Controllers\SensorController::class . ':update');
    $group->delete('/sensors/{id}', \App\Controllers\SensorController::class . ':delete');

    // ACTUATORS
    $group->get('/actuators', \App\Controllers\ActuatorController::class . ':index');
    $group->post('/actuators', \App\Controllers\ActuatorController::class . ':create');
    $group->put('/actuators/{id}', \App\Controllers\ActuatorController::class . ':update');
    $group->delete('/actuators/{id}', \App\Controllers\ActuatorController::class . ':delete');
    $group->post('/actuators/{id}/command', \App\Controllers\ActuatorController::class . ':sendCommand');
  })->add(\App\Middleware\AuthMiddleware::class);
};
