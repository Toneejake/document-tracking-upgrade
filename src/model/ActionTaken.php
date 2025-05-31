<?php

namespace Model;

class ActionTaken {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Inserts an action taken record into the database.
     *
     * @param mixed $docu_id The document ID
     * @param string $action The action to be recorded
     * @return bool True if insert succeeded, false otherwise
     */
    public function insert($docu_id, $action) {
        // Input validation: fail early if inputs are invalid
        if (empty($docu_id) || !is_scalar($docu_id)) {
            return false;
        }

        if (empty($action) || !is_string($action)) {
            return false;
        }

        try {
            $sql = "INSERT INTO tbl_action_taken (docu_id, action_taken) VALUES (:docu_id, :action)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':docu_id', $docu_id);
            $stmt->bindParam(':action', $action);

            return $stmt->execute();
        } catch (\PDOException $e) {
            // Optionally log or handle the exception
            return false;
        }
    }
}