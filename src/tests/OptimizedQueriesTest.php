<?php

use PHPUnit\Framework\TestCase;
use Model\OptimizedQueries;
use PDO;

class OptimizedQueriesTest extends TestCase
{
    private $pdo;
    private $queries;

    protected function setUp(): void
    {
        // Use in-memory SQLite DB
        $this->pdo = new PDO('sqlite::memory:');
        
        // Create tables with full schema matching your app
        $this->pdo->exec("CREATE TABLE tbl_notification (
            id INTEGER PRIMARY KEY,
            user_id INTEGER,
            content TEXT,
            status TEXT,
            timestamp DATETIME
        )");

        $this->pdo->exec("CREATE TABLE tbl_uploaded_document (
            id INTEGER PRIMARY KEY,
            qr_filename TEXT,
            document_code TEXT,
            document_type TEXT,
            subject TEXT,
            sender TEXT,
            cur_office TEXT,
            status TEXT,
            completed TEXT,
            updated_at DATETIME,
            data_source TEXT
        )");

        // Initialize class under test
        $this->queries = new OptimizedQueries($this->pdo);
    }

    public function testGetUserNotificationsReturnsEmptyWhenNoData()
    {
        $results = $this->queries->getUserNotifications(1);
        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }

    public function testFindDocumentByCodeReturnsNullWhenNotFound()
    {
        $result = $this->queries->findDocumentByCode('DOC123');
        $this->assertFalse($result);
    }

    public function testGetDocumentsForTrackingReturnsEmptyWhenNoData()
    {
        $results = $this->queries->getDocumentsForTracking();
        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }

    public function testGetIdleDocumentsReturnsEmptyWithNoData()
    {
        $results = $this->queries->getIdleDocuments(7);
        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }
}