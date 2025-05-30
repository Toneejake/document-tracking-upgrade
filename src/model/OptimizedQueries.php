<?php
namespace Model;

class OptimizedQueries {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get recent notifications for a user with pagination and optimized query
     * Uses the idx_notification_user_status index
     */
    public function getUserNotifications($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT id, content, status, timestamp 
               FROM tbl_notification 
               WHERE user_id = :user_id 
               ORDER BY timestamp DESC 
               LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get document tracking information with optimized query
     * Uses the idx_document_status, idx_document_updated indexes
     */
    public function getDocumentsForTracking($status = null, $limit = 50) {
        $sql = "SELECT id, qr_filename, document_code, document_type, 
                      data_source, sender, cur_office, status, updated_at 
               FROM tbl_uploaded_document 
               WHERE status != 'pulled' AND completed != 'decline'";
        
        if ($status) {
            $sql .= " AND status = :status";
        }
        
        $sql .= " ORDER BY updated_at DESC LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find document by code with optimized query
     * Uses the idx_document_code index
     */
    public function findDocumentByCode($code) {
        $sql = "SELECT id, document_code, document_type, subject, 
                      sender, status, completed, updated_at 
               FROM tbl_uploaded_document 
               WHERE document_code = :code";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Check for idle documents with optimized query
     * Uses the idx_document_status, idx_document_updated indexes
     */
    public function getIdleDocuments($idleDays = 7) {
        $thresholdDate = date('Y-m-d H:i:s', strtotime("-{$idleDays} days"));
        
        $sql = "SELECT d.id, d.document_code, d.subject, d.sender_id, d.cur_office 
               FROM tbl_uploaded_document d 
               WHERE d.status = 'pending' 
               AND d.updated_at < :threshold_date 
               AND d.id NOT IN (
                   SELECT docu_id FROM tbl_notification 
                   WHERE content LIKE CONCAT('%idle for ', :idle_days, ' days%') 
                   AND timestamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
               )";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':threshold_date', $thresholdDate);
        $stmt->bindParam(':idle_days', $idleDays);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}