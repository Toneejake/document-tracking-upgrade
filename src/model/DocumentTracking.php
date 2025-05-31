<?php

namespace Model;

class DocumentTracking {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insert($docu_id, $action, $person, $office) {
        $sql = "INSERT INTO tbl_document_tracking (docu_id, action_taken, person, office) VALUES (:docu_id, :action, :person, :office)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':docu_id', $docu_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':person', $person);
        $stmt->bindParam(':office', $office);

        return $stmt->execute();
    }

    public function insertCompleted($docu_id, $action, $person, $currentTimestamp) {
        $sql = "INSERT INTO tbl_document_tracking (docu_id, action_taken, person, timestamp) VALUES (:docu_id, :action, :person, :timestamp)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':docu_id', $docu_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':person', $person);
        $stmt->bindParam(':timestamp', $currentTimestamp);

        return $stmt->execute();
    }
}