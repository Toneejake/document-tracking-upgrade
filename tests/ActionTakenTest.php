<?php

use PHPUnit\Framework\TestCase;
use Model\ActionTaken;
use PDO;

class ActionTakenTest extends TestCase
{
    private $pdo;
    private $actionTaken;

    protected function setUp(): void
    {
        // Use in-memory SQLite DB for fast testing
        $this->pdo = new PDO('sqlite::memory:');
        
        // Create table schema
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS tbl_action_taken (
            id INTEGER PRIMARY KEY,
            docu_id INTEGER NOT NULL,
            action_taken TEXT NOT NULL
        )");

        // Initialize class under test
        $this->actionTaken = new ActionTaken($this->pdo);
    }

    public function testInsertReturnsTrueOnValidInput()
    {
        // Test valid data insertion
        $result = $this->actionTaken->insert(1, 'Document received');
        $this->assertTrue($result);

        // Verify that the data was inserted correctly
        $stmt = $this->pdo->query("SELECT * FROM tbl_action_taken");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]['docu_id']);
        $this->assertEquals('Document received', $rows[0]['action_taken']);
    }

    public function testInsertReturnsFalseOnInvalidInput()
    {
        // Test empty values
        $this->assertFalse($this->actionTaken->insert('', ''));

        // Test null values
        $this->assertFalse($this->actionTaken->insert(null, null));

        // Test zero and empty string
        $this->assertFalse($this->actionTaken->insert(0, ''));

        // Test non-scalar values
        $this->assertFalse($this->actionTaken->insert([], 'test'));
        $this->assertFalse($this->actionTaken->insert(new stdClass(), 'test'));
        $this->assertFalse($this->actionTaken->insert(1, 2)); // action must be a string
    }
}