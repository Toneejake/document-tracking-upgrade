<?php

namespace Model;

abstract class BaseModel {
    protected $db;
    protected $table;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function create(array $data) {
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }

    protected function update(array $data, $where) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }

    protected function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
}