<?php

use PHPUnit\Framework\TestCase;
use Model\DocumentTracking;
use PDO;

class DocumentTrackingTest extends TestCase
{
    private $pdo;
    private $tracking;

    protected function setUp(): void
    {
        // Use in-memory SQLite DB
        $this->pdo = new PDO('sqlite::memory:');
        
        // Create table schema
        $this->pdo->exec("CREATE TABLE tbl_document_tracking (
            id INTEGER PRIMARY KEY,
            docu_id INTEGER NOT NULL,
            action_taken TEXT NOT NULL,
            person TEXT NOT NULL,
            office TEXT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Initialize class under test
        $this->tracking = new DocumentTracking($this->pdo);
    }

    public function testInsertAddsNewRecord()
    {
        $result = $this->tracking->insert(1, 'Received', 'John Doe', 'Admin Office');
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT * FROM tbl_document_tracking");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]['docu_id']);
        $this->assertEquals('Received', $rows[0]['action_taken']);
        $this->assertEquals('John Doe', $rows[0]['person']);
        $this->assertEquals('Admin Office', $rows[0]['office']);
    }

    public function testInsertCompletedAddsRecordWithCustomTimestamp()
    {
        $customTime = '2025-06-01 10:00:00';

        $result = $this->tracking->insertCompleted(2, 'Completed', 'Jane Smith', $customTime);
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT * FROM tbl_document_tracking");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertEquals(2, $rows[0]['docu_id']);
        $this->assertEquals('Completed', $rows[0]['action_taken']);
        $this->assertEquals('Jane Smith', $rows[0]['person']);
        $this->assertEquals($customTime, $rows[0]['timestamp']);
    }

    public function testInsertReturnsFalseOnInvalidInput()
    {
        $this->assertFalse($this->tracking->insert('', '', '', ''));     // Empty strings
        $this->assertFalse($this->tracking->insert(null, null, null, null)); // Nulls
        $this->assertFalse($this->tracking->insert(0, '', '', ''));      // Zero + empty
    }
}