<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Sensor;

class SensorController {
    private $sensorModel;

    public function __construct($db) {
        $this->sensorModel = new Sensor($db);
    }

    public function index(Request $request, Response $response) {
        $sensors = $this->sensorModel->getAll();
        $response->getBody()->write(json_encode($sensors));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        if (!$this->validateSensorData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid sensor data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->sensorModel->create($data);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Sensor created successfully']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to create sensor']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        if (!$this->validateSensorData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid sensor data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->sensorModel->update($id, $data);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Sensor updated successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to update sensor']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $success = $this->sensorModel->delete($id);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Sensor deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to delete sensor']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    private function validateSensorData($data) {
        $required = ['name', 'type', 'location', 'status'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
