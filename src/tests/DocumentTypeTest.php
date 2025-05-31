<?php

use PHPUnit\Framework\TestCase;
use Model\DocumentType;
use PDO;

class DocumentTypeTest extends TestCase
{
    private $pdo;
    private $docType;

    protected function setUp(): void
    {
        // Use in-memory SQLite DB
        $this->pdo = new PDO('sqlite::memory:');
        
        // Create table schema
        $this->pdo->exec("CREATE TABLE tbl_document_type (
            id INTEGER PRIMARY KEY,
            document_type TEXT NOT NULL UNIQUE
        )");

        // Initialize class under test
        $this->docType = new DocumentType($this->pdo);
    }

    public function testInsertAddsNewDocumentType()
    {
        $result = $this->docType->insert('Annual Report');
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT * FROM tbl_document_type");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertEquals('Annual Report', $rows[0]['document_type']);
    }

    public function testCheckDocumentFindsExistingTypeCaseInsensitive()
    {
        // Insert document type manually first
        $this->pdo->exec("INSERT INTO tbl_document_type (document_type) VALUES ('Annual Report')");

        // Now check if exists using lowercase
        $found = $this->docType->checkDocument('annual report');
        $this->assertTrue($found);
    }

    public function testCheckDocumentReturnsFalseWhenNotFound()
    {
        $found = $this->docType->checkDocument('Nonexistent Type');
        $this->assertFalse($found);
    }

    public function testUpdateChangesExistingDocumentType()
    {
        // Insert initial value
        $this->pdo->exec("INSERT INTO tbl_document_type (document_type) VALUES ('Old Type')");
        $row = $this->pdo->query("SELECT id FROM tbl_document_type LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];

        // Now update
        $updated = $this->docType->update($id, 'Updated Type');
        $this->assertTrue($updated);

        // Check if updated
        $stmt = $this->pdo->query("SELECT * FROM tbl_document_type WHERE id = $id");
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Updated Type', $record['document_type']);
    }

    public function testInsertReturnsFalseOnFailure()
    {
        // First insert
        $this->assertTrue($this->docType->insert('Duplicate Type'));

        // Second insert should fail due to UNIQUE constraint
        $duplicateResult = $this->docType->insert('Duplicate Type');
        $this->assertFalse($duplicateResult);
    }
}