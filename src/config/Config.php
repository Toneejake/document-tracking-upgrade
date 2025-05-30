<?php

namespace Config;

class Config {
    private static $config = [
        'email' => [
            'address' => 'danbsit4b@gmail.com',
            'password' => 'dgtwxvvucrxjlknv',
            'host' => 'smtp.gmail.com',
            'security' => 'tls',
            'port' => 587,
            'from_name' => 'NIA Document Tracking System'
        ],
        'app' => [
            'timezone' => 'Asia/Manila',
            'base_path' => null
        ]
    ];

    public static function init() {
        date_default_timezone_set(self::$config['app']['timezone']);
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        self::$config['app']['base_path'] = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/document-tracking/';
    }

    public static function get($key) {
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) return null;
            $value = $value[$k];
        }
        
        return $value;
    }
}