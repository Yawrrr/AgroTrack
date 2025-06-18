<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Crop;

class CropController {
    private $cropModel;

    public function __construct($db) {
        $this->cropModel = new Crop($db);
    }

    public function index(Request $request, Response $response) {
        $crops = $this->cropModel->getAll();
        $response->getBody()->write(json_encode($crops));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        // Log incoming data for debugging
        error_log("Creating crop with data: " . json_encode($data));
        
        if (!$this->validateCropData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid crop data. Required fields: name, variety, planting_date, expected_harvest_date, status']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $cropId = $this->cropModel->create($data);
        
        if ($cropId) {
            $newCrop = $this->cropModel->getById($cropId);
            $response->getBody()->write(json_encode([
                'message' => 'Crop created successfully',
                'crop' => $newCrop
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to create crop']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        // Log incoming data for debugging
        error_log("Updating crop {$id} with data: " . json_encode($data));
        
        // Check if crop exists
        $existingCrop = $this->cropModel->getById($id);
        if (!$existingCrop) {
            $response->getBody()->write(json_encode(['error' => 'Crop not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        if (!$this->validateCropData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid crop data. Required fields: name, variety, planting_date, expected_harvest_date, status']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->cropModel->update($id, $data);
        
        if ($success) {
            $updatedCrop = $this->cropModel->getById($id);
            $response->getBody()->write(json_encode([
                'message' => 'Crop updated successfully',
                'crop' => $updatedCrop
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to update crop']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $id = $args['id'];
        
        // Check if crop exists
        $existingCrop = $this->cropModel->getById($id);
        if (!$existingCrop) {
            $response->getBody()->write(json_encode(['error' => 'Crop not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $success = $this->cropModel->delete($id);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Crop deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to delete crop']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $crop = $this->cropModel->getById($id);
        if ($crop) {
            $response->getBody()->write(json_encode($crop));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Crop not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }

    private function validateCropData($data) {
        $required = ['name', 'variety', 'planting_date', 'expected_harvest_date', 'status'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
