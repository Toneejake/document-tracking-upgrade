<?php 
    class NotificationModel{
        
        private $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function insert($user_id, $content){
            
            $sql = "INSERT INTO tbl_notification (user_id, content) VALUES (:user_id, :content)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':content', $content);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }

        public function markAsReadbyUser($user_id){
            
            $sql = "UPDATE tbl_notification set status = 'read' where user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }
        
        // New method to check for idle documents
        public function checkIdleDocuments($idle_days = 7) {
            // Get the date threshold for idle documents
            $threshold_date = date('Y-m-d H:i:s', strtotime("-{$idle_days} days"));
            
            // Find documents that haven't been updated in the specified number of days
            $sql = "SELECT d.id, d.docu_code, d.subject, d.sender_id, d.current_office 
                   FROM tbl_uploaded_document d 
                   WHERE d.status = 'pending' 
                   AND d.last_update < :threshold_date 
                   AND d.id NOT IN (
                       SELECT docu_id FROM tbl_notification 
                       WHERE content LIKE CONCAT('%idle for ', :idle_days, ' days%') 
                       AND timestamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                   )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':threshold_date', $threshold_date);
            $stmt->bindParam(':idle_days', $idle_days);
            $stmt->execute();
            
            $idle_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Send notifications for each idle document
            foreach ($idle_documents as $doc) {
                // Notify document sender
                $sender_message = "Your document '{$doc['subject']}' (Code: {$doc['docu_code']}) has been idle for {$idle_days} days in {$doc['current_office']}.";
                $this->insert($doc['sender_id'], $sender_message);
                
                // If there's a current office, notify them too
                if (!empty($doc['current_office'])) {
                    // Get office admin/handler IDs
                    $office_sql = "SELECT u.id 
                                  FROM tbl_userinformation u 
                                  JOIN tbl_login_account a ON u.id = a.id 
                                  WHERE u.office_code = :office_code 
                                  AND (a.role = 'admin' OR a.role = 'handler')";
                    $office_stmt = $this->pdo->prepare($office_sql);
                    $office_stmt->bindParam(':office_code', $doc['current_office']);
                    $office_stmt->execute();
                    $office_users = $office_stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($office_users as $user) {
                        $office_message = "Document '{$doc['subject']}' (Code: {$doc['docu_code']}) has been idle in your office for {$idle_days} days.";
                        $this->insert($user['id'], $office_message);
                    }
                }
            }
            
            return count($idle_documents);
        }
    }
?>