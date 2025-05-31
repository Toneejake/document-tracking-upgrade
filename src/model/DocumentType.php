<?php

namespace Model;

class DocumentType {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insert($document_type) {
        try {
            $sql = "INSERT INTO tbl_document_type (document_type) VALUES (:document_type)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':document_type', $document_type);
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function checkDocument($document_type) {
        $sql = "SELECT * FROM tbl_document_type WHERE TRIM(LOWER(document_type)) = TRIM(LOWER(:document_type))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':document_type', $document_type);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    public function update($id, $document_type) {
        $sql = "UPDATE tbl_document_type SET document_type = :document_type WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':document_type', $document_type);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}