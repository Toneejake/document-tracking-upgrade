<?php

use PHPUnit\Framework\TestCase;
use Model\FakeModelForTesting;
use PDO;

class BaseModelTest extends TestCase
{
    private $pdo;
    private $model;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec("CREATE TABLE fake_table (
            id INTEGER PRIMARY KEY,
            name TEXT,
            email TEXT
        )");

        $this->model = new FakeModelForTesting($this->pdo);
    }

    public function testCreateInsertsRecord()
    {
        $result = $this->model->callCreate([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT * FROM fake_table");
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('John Doe', $record['name']);
        $this->assertEquals('john@example.com', $record['email']);
    }

    public function testUpdateModifiesExistingRecord()
    {
        $this->pdo->exec("INSERT INTO fake_table (id, name, email) VALUES (1, 'Jane Doe', 'jane@example.com')");

        $result = $this->model->callUpdate([
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ], "id = 1");

        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT * FROM fake_table WHERE id = 1");
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Updated Name', $record['name']);
        $this->assertEquals('updated@example.com', $record['email']);
    }

    public function testFindReturnsCorrectRecord()
    {
        $this->pdo->exec("INSERT INTO fake_table (id, name, email) VALUES (1, 'Alice', 'alice@example.com')");

        $record = $this->model->callFind(1);

        $this->assertIsArray($record);
        $this->assertEquals('Alice', $record['name']);
        $this->assertEquals('alice@example.com', $record['email']);
    }
}