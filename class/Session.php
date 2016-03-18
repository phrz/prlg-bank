<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'User.php';
    include_once 'Crypto.php';

    date_default_timezone_set('America/Chicago');

    class Session
    {
        public function __construct()
        {
            if (!isset($_SESSION)) {
                session_start();
            }
        }

        public function authenticate($username, $password)
        {
            // Check parameters
            if (!isset($username) || !isset($password)) {
                return false;
            }

            // Check user existance
            $u = null;
            try {
                $u = User::find($username);
            } catch (Exception $e) {
                return false; // user does not exist
            }

            // Test password
            $given = new Crypto($password, $u->crypto->getSalt());
            $known = $u->crypto;

            if (!Crypto::compare($given, $known)) {
                // hash mismatch, bad password
                return false;
            }

            // good password
            // log in
            $_SESSION['user'] = $_POST['username'];

            return true;
        }

        public function isAuth()
        {
            if (!headers_sent()) {
                session_regenerate_id();
            }

            return isset($_SESSION['user']);
        }

        public function destroy()
        {
            unset($_SESSION['user']);
            session_destroy();
        }

        public function __get($name)
        {
            if (isset($_SESSION[$name])) {
                return $_SESSION[$name];
            } else {
                throw new Exception('Unset session variable.', 1);
            }
        }

        public function __set($name, $value)
        {
            $_SESSION[$name] = $value;
        }
    }
