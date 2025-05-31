<?php

use PHPUnit\Framework\TestCase;

class RedirectLogicTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function testUnauthenticatedUserSeesLoginForm()
    {
        ob_start();
        include 'index.php'; // Make sure this path is correct!
        $output = ob_get_clean();

        $this->assertStringContainsString('id="form-login"', $output);
    }
}