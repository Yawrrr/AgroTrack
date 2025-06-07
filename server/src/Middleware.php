<?php

use Slim\App;

return function (App $app) {
  $app->addBodyParsingMiddleware();
  $app->add(function ($req, $handler) {
      $response = $handler->handle($req);
      return $response
          ->withHeader('Access-Control-Allow-Origin', '*')
          ->withHeader('Content-Type', 'application/json');
  });
};
