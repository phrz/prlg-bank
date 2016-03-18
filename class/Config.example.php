<?php
    class Config
    {
        public static $database = [
            'host' => '',
            'username' => '',
            'password' => '',
            'database' => '',
            'charset' => 'utfmb4', // table charset
            'prefix' => '', // table prefix
            'timezone' => 'America/Chicago', // Central Time with DST
        ];

        public static $nonce = [
                   // Nonce expiry interval:
            'expiry' => 'PT5M', // DateInterval spec (5 Minutes)
        ];
    }
