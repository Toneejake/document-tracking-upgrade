<?php

use PHPUnit\Framework\TestCase;
use Model\ConversationModel;
use PDO;

class ConversationModelTest extends TestCase
{
    private $pdo;
    private $conversation;

    protected function setUp(): void
    {
        // Use in-memory SQLite DB
        $this->pdo = new PDO('sqlite::memory:');
        
        // Create tables
        $this->pdo->exec("CREATE TABLE tbl_conversation (
            id INTEGER PRIMARY KEY,
            conversation_id TEXT NOT NULL,
            user_id INTEGER NOT NULL
        )");

        $this->pdo->exec("CREATE TABLE tbl_messages (
            id INTEGER PRIMARY KEY,
            conversation_id TEXT NOT NULL,
            user_id INTEGER NOT NULL,
            message TEXT NOT NULL
        )");

        // Initialize class under test
        $this->conversation = new ConversationModel($this->pdo);
    }

    public function testCheckForConversationIdReturnsFalseWhenNotFound()
    {
        $result = $this->conversation->checkForConversationId('conv_123');
        $this->assertFalse($result);
    }

    public function testInsertToConversationReturnsTrueOnSuccess()
    {
        $result = $this->conversation->insertToConversation('conv_123', 1);
        $this->assertTrue($result);

        $stmt = $this->pdo->prepare("SELECT * FROM tbl_conversation WHERE conversation_id = 'conv_123'");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertEquals('conv_123', $rows[0]['conversation_id']);
        $this->assertEquals(1, $rows[0]['user_id']);
    }

    public function testInsertMessageReturnsTrueOnSuccess()
    {
        // First insert conversation
        $this->conversation->insertToConversation('conv_123', 1);

        // Then insert message
        $result = $this->conversation->insertMessage('conv_123', 1, 'Hello World');

        $this->assertTrue($result);

        $stmt = $this->pdo->prepare("SELECT * FROM tbl_messages WHERE conversation_id = 'conv_123'");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertEquals('Hello World', $rows[0]['message']);
    }

    public function testCheckForConversationIdReturnsTrueAfterInsert()
    {
        // Insert conversation
        $this->conversation->insertToConversation('conv_123', 1);

        // Now check if exists
        $result = $this->conversation->checkForConversationId('conv_123');
        $this->assertTrue($result);
    }
}