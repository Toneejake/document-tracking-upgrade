-- Add indexes to tbl_notification for faster notification queries
ALTER TABLE tbl_notification ADD INDEX idx_notification_user_status (user_id, status);
ALTER TABLE tbl_notification ADD INDEX idx_notification_timestamp (timestamp);

-- Add indexes to tbl_uploaded_document for faster document queries
ALTER TABLE tbl_uploaded_document ADD INDEX idx_document_status (status);
ALTER TABLE tbl_uploaded_document ADD INDEX idx_document_completed (completed);
ALTER TABLE tbl_uploaded_document ADD INDEX idx_document_updated (updated_at);
ALTER TABLE tbl_uploaded_document ADD INDEX idx_document_code (document_code);

-- Add indexes to tbl_document_tracking for faster tracking queries
ALTER TABLE tbl_document_tracking ADD INDEX idx_tracking_docu_id (docu_id);