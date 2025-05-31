<?php

namespace Model;

class ConversationModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function checkForConversationId($conversation_id) {
        $sql = "SELECT conversation_id FROM tbl_conversation WHERE conversation_id = :conversation_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function insertToConversation($conversation_id, $user_id) {
        $sql = "INSERT INTO tbl_conversation (conversation_id, user_id) VALUES (:conversation_id, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    public function insertMessage($conversation_id, $user_id, $message) {
        $sql = "INSERT INTO tbl_messages (conversation_id, user_id, message) VALUES (:conversation_id, :user_id, :message)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':message', $message);

        return $stmt->execute();
    }
}