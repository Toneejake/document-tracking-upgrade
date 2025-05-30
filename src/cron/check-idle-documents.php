<?php
    require '../connection.php';
    require '../model/tbl-notification-model.php';
    
    $notificationModel = new NotificationModel($pdo);
    
    // Check for documents idle for 7 days
    $idle_count = $notificationModel->checkIdleDocuments(7);
    
    echo "Found and notified {$idle_count} idle documents.";
?>