<?php
require_once '../connection.php';

// Set error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration
$archiveOlderThan = 365; // Archive records older than 1 year
$logFile = __DIR__ . '/cleanup_log.txt';

// Log function
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    echo "$message\n";
}

try {
    logMessage("Starting database cleanup process...");
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // 1. Archive old notifications
    $archiveDate = date('Y-m-d H:i:s', strtotime("-$archiveOlderThan days"));
    
    // Create archive table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS tbl_notification_archive LIKE tbl_notification");
    
    // Move old notifications to archive
    $stmt = $pdo->prepare("INSERT INTO tbl_notification_archive 
                          SELECT * FROM tbl_notification 
                          WHERE timestamp < :archive_date");
    $stmt->bindParam(':archive_date', $archiveDate);
    $stmt->execute();
    $archivedNotifications = $stmt->rowCount();
    
    // Delete archived notifications from main table
    $stmt = $pdo->prepare("DELETE FROM tbl_notification 
                          WHERE timestamp < :archive_date");
    $stmt->bindParam(':archive_date', $archiveDate);
    $stmt->execute();
    
    // 2. Archive completed documents
    $pdo->exec("CREATE TABLE IF NOT EXISTS tbl_uploaded_document_archive LIKE tbl_uploaded_document");
    
    $stmt = $pdo->prepare("INSERT INTO tbl_uploaded_document_archive 
                          SELECT * FROM tbl_uploaded_document 
                          WHERE (completed = 'yes' OR status = 'pulled') 
                          AND updated_at < :archive_date");
    $stmt->bindParam(':archive_date', $archiveDate);
    $stmt->execute();
    $archivedDocuments = $stmt->rowCount();
    
    $stmt = $pdo->prepare("DELETE FROM tbl_uploaded_document 
                          WHERE (completed = 'yes' OR status = 'pulled') 
                          AND updated_at < :archive_date");
    $stmt->bindParam(':archive_date', $archiveDate);
    $stmt->execute();
    
    // 3. Optimize tables
    $tables = [
        'tbl_notification',
        'tbl_uploaded_document',
        'tbl_document_tracking',
        'tbl_action_taken'
    ];
    
    foreach ($tables as $table) {
        $pdo->exec("OPTIMIZE TABLE $table");
        logMessage("Optimized table: $table");
    }
    
    // Commit transaction
    $pdo->commit();
    
    logMessage("Cleanup completed successfully!");
    logMessage("Archived $archivedNotifications notifications");
    logMessage("Archived $archivedDocuments documents");
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    logMessage("Error: " . $e->getMessage());
}