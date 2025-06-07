<?php

use Slim\App;

return function (App $app) {
  // CROPS
  $app->get('/api/crops', \App\Controllers\CropController::class . ':index');
  $app->post('/api/crops', \App\Controllers\CropController::class . ':create');
  $app->put('/api/crops/{id}', \App\Controllers\CropController::class . ':update');
  $app->delete('/api/crops/{id}', \App\Controllers\CropController::class . ':delete');

  // READINGS
  $app->get('/api/readings', \App\Controllers\ReadingController::class . ':index');
  // … other CRUD as needed

  // SENSORS
  $app->get('/api/sensors', \App\Controllers\SensorController::class . ':index');
  $app->post('/api/sensors', \App\Controllers\SensorController::class . ':create');
  $app->put('/api/sensors/{id}', \App\Controllers\SensorController::class . ':update');
  $app->delete('/api/sensors/{id}', \App\Controllers\SensorController::class . ':delete');

  // ACTUATORS
  $app->get('/api/actuators', \App\Controllers\ActuatorController::class . ':index');
  $app->post('/api/actuators', \App\Controllers\ActuatorController::class . ':create');
  $app->put('/api/actuators/{id}', \App\Controllers\ActuatorController::class . ':update');
  $app->delete('/api/actuators/{id}', \App\Controllers\ActuatorController::class . ':delete');
  $app->post('/api/actuators/{id}/command', \App\Controllers\ActuatorController::class . ':sendCommand');
};
