<?php

namespace Model;

class FakeModelForTesting extends BaseModel {
    protected $table = 'fake_table';

    public function __construct($db) {
        parent::__construct($db);
    }

    public function callCreate(array $data) {
        return $this->create($data);
    }

    public function callUpdate(array $data, $where) {
        return $this->update($data, $where);
    }

    public function callFind($id) {
        return $this->find($id);
    }
}