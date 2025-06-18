<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;

class AuthMiddleware {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function __invoke(Request $request, Handler $handler): Response {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Authorization header missing'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Extract token from "Bearer <token>"
        $token = str_replace('Bearer ', '', $authHeader);
        
        $result = $this->userModel->verifyJWT($token);
        
        if (!$result['success']) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Invalid or expired token'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Add user info to request
        $request = $request->withAttribute('user', [
            'id' => $result['data']->user_id,
            'email' => $result['data']->email,
            'name' => $result['data']->name
        ]);

        return $handler->handle($request);
    }
} 