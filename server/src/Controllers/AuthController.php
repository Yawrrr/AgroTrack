<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function register(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        // Validate input
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Name, email, and password are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validate email format
        if (!$this->userModel->validateEmail($data['email'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Invalid email format'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validate password strength
        if (!$this->userModel->validatePassword($data['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Password must be at least 6 characters long'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $result = $this->userModel->register($data);
        
        if ($result['success']) {
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Email and password are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $result = $this->userModel->login($data['email'], $data['password']);
        
        if ($result['success']) {
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }

    public function me(Request $request, Response $response) {
        // Get the user from the request (set by auth middleware)
        $user = $request->getAttribute('user');
        
        if ($user) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'user' => $user
            ]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'User not found'
        ]));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response) {
        // Since JWT is stateless, we just return a success message
        // In a real application, you might want to blacklist the token
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
} 