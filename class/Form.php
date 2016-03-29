<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'Account.php';
    include_once 'Session.php';
    include_once 'NonceManager.php';

    class Form
    {
        protected $_fields = [];
        protected $_formData = [];

        // __construct($type: string)
        public function __construct(string $type)
        {
            switch ($type) {
                case 'GET':
                    $this->_formData = &$_GET;
                    break;
                case 'POST':
                    $this->_formData = &$_POST;
                    break;
                case 'JSON':
                    $this->_formData = json_decode(file_get_contents('php://input'), true);
            }
        }

        public function addField(string $name, callable $test = null)
        {
            $this->_fields[$name] = [
                'test' => $test,
            ];
        }

        public static function implicitTrue($value)
        {
            return true;
        }

        public static function isMoney(string $value)
        {
            $asFloat = floatval($value);
            if ($asFloat < 0) {
                return false;
            }

            return $asFloat == round($asFloat, 2);
        }

        public static function validateAccount(string $accountNumber)
        {
            // First, attempt to get the Account model from the DB.
            try {
                $account = Account::find($accountNumber);
            } catch (Exception $e) {
                return false; // account doesn't exist
            }

            // Now check ownership
            // (require authentication)
            $s = new Session();
            if (!$s->isAuth()) {
                return false;
            }

            // Get the account's user relation Model
            $accountOwner = $account->user;
            if ($accountOwner->username != $s->user) {
                return false;
            }

            return true;
        }

        public static function validateNonce(string $nonce)
        {
            // Get the current user, if any
            $ownerName = null;
            $s = new Session();

            if ($s->isAuth()) {
                $user = User::find($s->user);
                $ownerName = $user->username;

                error_log("SESSION $ownerName");
            } else {
                error_log('NO SESSION');
            }

            // Attempt to consume the Nonce
            $nonceValid = NonceManager::consume($nonce, $ownerName);
            $log = "Nonce $nonce given, ".($ownerName ?? 'no session');
            $log .= ', is '.($nonceValid ? 'valid' : 'invalid');
            error_log($log);

            return $nonceValid;
        }

        // callbacks are `success` and `failure`
        public function submit(array $callbacks)
        {
            if ($this->processSubmission() == true) {
                call_user_func($callbacks['success'], $this->_formData);
            } else {
                call_user_func($callbacks['failure'], $this->_formData);
            }
        }

        private function processSubmission()
        {
            foreach ($this->_fields as $fieldName => $parameters) {

                // Get the corresponding submitted value
                if(isset($this->_formData[$fieldName])) {
                    $value = $this->_formData[$fieldName];
                } else {
                    throw new Exception("failed to provide field $fieldName",1);
                    return false;
                }

                // Check for unsubmitted or empty fields
                if (!isset($value) || empty($value)) {
                    error_log('Form failed on field '.$fieldName.' (not set or empty)');

                    return false;
                }

                // Run the validation function for the field
                $testResult = call_user_func($parameters['test'], $value);
                if (!$testResult) {
                    error_log('Form failed on field '.$fieldName.' with test '.$parameters['test']);

                    return false;
                }
            }

            return true;
        }

        public function getValue($name)
        {
            return $this->_formData[$name];
        }
    } // end Form
;
