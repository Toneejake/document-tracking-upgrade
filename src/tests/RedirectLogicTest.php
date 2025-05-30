<?php

use PHPUnit\Framework\TestCase;

class RedirectLogicTest extends TestCase
{
    protected function setUp(): void
    {
        // Start session for each test
        session_start();
    }

    protected function tearDown(): void
    {
        // Clear session after each test
        $_SESSION = [];
        session_destroy();
    }

    public function testGuestRedirect()
    {
        // Simulate session data
        $_SESSION['userid'] = 1;
        $_SESSION['role'] = 'guest';

        // Capture output to prevent header errors
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        // We can't catch header() directly, so simulate expected behavior
        $this->expectOutputRegex('/views\/guest\/dashboard\.php/');
    }

    public function testAdminRedirect()
    {
        // Simulate session data
        $_SESSION['userid'] = 1;
        $_SESSION['office'] = "Administrative Section Records";

        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        $this->expectOutputRegex('/views\/record_office\/dashboard\.php/');
    }

    public function testUnauthenticatedUserSeesLoginForm()
    {
        // No session set
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        // Check if login form appears in output
        $this->assertStringContainsString('id="form-login"', $output);
    }
}