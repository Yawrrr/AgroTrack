<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Reading;

class ReadingController {
    private $readingModel;
    private $db;

    public function __construct($db) {
        $this->readingModel = new Reading($db);
        $this->db = $db;
    }

    public function index(Request $request, Response $response) {
        $params = $request->getQueryParams();
        
        if (isset($params['sensor_id'])) {
            $readings = $this->readingModel->getBySensorId($params['sensor_id']);
        } else {
            $readings = $this->readingModel->getAll();
        }

        // Add sensor names to readings
        $readings = $this->addSensorNames($readings);

        $response->getBody()->write(json_encode($readings));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        if (!$this->validateReadingData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid reading data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Set timestamp if not provided
        if (!isset($data['timestamp'])) {
            $data['timestamp'] = date('Y-m-d H:i:s');
        }

        $success = $this->readingModel->create($data);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Reading recorded successfully']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to record reading']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function getLatest(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
        
        $readings = $this->readingModel->getLatestReadings($limit);
        
        // Add sensor names to readings
        $readings = $this->addSensorNames($readings);

        $response->getBody()->write(json_encode($readings));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validateReadingData($data) {
        $required = ['sensor_id', 'value', 'unit'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    private function addSensorNames($readings) {
        // Get all unique sensor IDs
        $sensorIds = array_unique(array_column($readings, 'sensor_id'));
        
        // Fetch sensor names
        $sensorNames = [];
        if (!empty($sensorIds)) {
            $placeholders = str_repeat('?,', count($sensorIds) - 1) . '?';
            $query = "SELECT id, name FROM sensors WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($query);
            $stmt->execute($sensorIds);
            while ($row = $stmt->fetch()) {
                $sensorNames[$row['id']] = $row['name'];
            }
        }

        // Add sensor names to readings
        foreach ($readings as &$reading) {
            $reading['sensor_name'] = $sensorNames[$reading['sensor_id']] ?? 'Unknown Sensor';
        }

        return $readings;
    }
}
