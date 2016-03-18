<?php

    include_once 'Model.php';
    include_once 'Account.php';

    class User extends Model {

        // Model Schema
        //
        // username : String
        // crypto : Crypto

        // Table Schema
        //
        // username : varchar
        // password : varchar
        // salt : varchar

        const _tableName = 'users';
        const _pKey = 'username';

        public function __get($name) {
            // simple property return
            if($name == 'username') {
                return $this->_modelData['username'];
            }
            // mapped property: db(password,salt) mapped
            // to a Crypto object
            elseif ($name == 'crypto') {
                $hash = $this->_modelData['password'];
                $salt = $this->_modelData['salt'];
                $crypto = Crypto::withHash($hash,$salt);
                return $crypto;
            }
            // relational property: accounts are corresponding account records
            // (one-to-many)
            elseif ($name == 'accounts') {
                return Account::where('username',$this->_modelData['username']);
            }

            else {
                throw new Exception("Model property not found.", 1);
            }
        } // end __get

        public static function create(array $properties) {
            throw new Exception("Not implemented.",1);
        }

    }

?>
