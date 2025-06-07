<?php

namespace App\Models;

class Reading {
    private $db;
    private $table = 'readings';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY timestamp DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getBySensorId($sensorId) {
        $query = "SELECT * FROM {$this->table} WHERE sensor_id = :sensor_id ORDER BY timestamp DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sensor_id', $sensorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (sensor_id, value, unit, timestamp) 
                 VALUES (:sensor_id, :value, :unit, :timestamp)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sensor_id', $data['sensor_id']);
        $stmt->bindParam(':value', $data['value']);
        $stmt->bindParam(':unit', $data['unit']);
        $stmt->bindParam(':timestamp', $data['timestamp']);

        return $stmt->execute();
    }

    public function getLatestReadings($limit = 10) {
        $query = "SELECT * FROM {$this->table} ORDER BY timestamp DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
