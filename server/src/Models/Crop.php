<?php

namespace App\Models;

class Crop {
    private $db;
    private $table = 'crops';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
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

    public function create($data) {
        $query = "INSERT INTO {$this->table} (name, variety, planting_date, expected_harvest_date, status) 
                 VALUES (:name, :variety, :planting_date, :expected_harvest_date, :status)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':variety', $data['variety']);
        $stmt->bindParam(':planting_date', $data['planting_date']);
        $stmt->bindParam(':expected_harvest_date', $data['expected_harvest_date']);
        $stmt->bindParam(':status', $data['status']);

        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET name = :name, 
                     variety = :variety, 
                     planting_date = :planting_date, 
                     expected_harvest_date = :expected_harvest_date, 
                     status = :status 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':variety', $data['variety']);
        $stmt->bindParam(':planting_date', $data['planting_date']);
        $stmt->bindParam(':expected_harvest_date', $data['expected_harvest_date']);
        $stmt->bindParam(':status', $data['status']);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
