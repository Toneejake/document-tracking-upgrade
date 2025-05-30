<?php

use PHPUnit\Framework\TestCase;
use Config\Database;

class DatabaseTest extends TestCase
{
    public function testCanGetDatabaseInstance()
    {
        $db = Database::getInstance();
        $this->assertInstanceOf(\PDO::class, $db);
    }
}