<?php

use PHPUnit\Framework\TestCase;
use Config\Config;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        // Simulate $_SERVER values
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'localhost';
    }

    public function testGetReturnsCorrectEmail()
    {
        $this->assertEquals('danbsit4b@gmail.com', Config::get('email.address'));
    }

    public function testGetReturnsCorrectTimezone()
    {
        $this->assertEquals('Asia/Manila', Config::get('app.timezone'));
    }

    public function testGetReturnsNullForInvalidKey()
    {
        $this->assertNull(Config::get('invalid.key'));
    }

    public function testInitSetsTimezone()
    {
        Config::init();
        $this->assertEquals('Asia/Manila', date_default_timezone_get());
    }

    public function testInitSetsBasePathCorrectly()
    {
        Config::init();
        
        $reflection = new \ReflectionClass('Config\Config');
        $configProperty = $reflection->getProperty('config');
        $configProperty->setAccessible(true);

        $config = $configProperty->getValue();
        $this->assertEquals('https://localhost/document-tracking/',  $config['app']['base_path']);
    }
}