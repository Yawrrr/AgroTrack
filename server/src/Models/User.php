<?php

namespace App\Models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User {
    private $db;
    private $table = 'users';
    private $secretKey = 'your_jwt_secret_key_here_change_in_production';

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($data) {
        // Check if user already exists
        if ($this->getUserByEmail($data['email'])) {
            return ['success' => false, 'message' => 'User already exists'];
        }

        $query = "INSERT INTO {$this->table} (name, email, password, created_at) 
                 VALUES (:name, :email, :password, NOW())";
        
        $stmt = $this->db->prepare($query);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            $userId = $this->db->lastInsertId();
            $user = $this->getUserById($userId);
            $token = $this->generateJWT($user);
            
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ],
                'token' => $token
            ];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }

    public function login($email, $password) {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        $token = $this->generateJWT($user);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ],
            'token' => $token
        ];
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function generateJWT($user) {
        $payload = [
            'iss' => 'agrotrack',
            'aud' => 'agrotrack-users',
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60), // 24 hours
            'user_id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function verifyJWT($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return ['success' => true, 'data' => $decoded];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid token'];
        }
    }

    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function validatePassword($password) {
        return strlen($password) >= 6;
    }
} 