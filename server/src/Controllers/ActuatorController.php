<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Actuator;

class ActuatorController {
    private $actuatorModel;

    public function __construct($db) {
        $this->actuatorModel = new Actuator($db);
    }

    public function index(Request $request, Response $response) {
        $actuators = $this->actuatorModel->getAll();
        $response->getBody()->write(json_encode($actuators));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        if (!$this->validateActuatorData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid actuator data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->actuatorModel->create($data);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Actuator created successfully']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to create actuator']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        if (!$this->validateActuatorData($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid actuator data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->actuatorModel->update($id, $data);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Actuator updated successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to update actuator']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function sendCommand(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        if (!isset($data['command']) || !in_array($data['command'], ['on', 'off'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid command. Must be "on" or "off"']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $success = $this->actuatorModel->updateStatus($id, $data['command']);
        
        if ($success) {
            $response->getBody()->write(json_encode([
                'message' => 'Command sent successfully',
                'status' => $data['command']
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to send command']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $success = $this->actuatorModel->delete($id);
        
        if ($success) {
            $response->getBody()->write(json_encode(['message' => 'Actuator deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Failed to delete actuator']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    private function validateActuatorData($data) {
        $required = ['name', 'type', 'status', 'location'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
