<?php

    include_once 'Model.php';
    include_once 'User.php';

    class Account extends Model
    {
        // Model Schema
        //
        // user : User
        // number : string
        // balance : double

        // Table Schema
        //
        // username : varchar
        // accountnum : varchar
        // balance : double

        protected $_settable = array('balance');
        protected $_fillable = array('username', 'accountnum', 'balance');

        const _tableName = 'accounts';
        const _pKey = 'accountnum';

        public function __get($name)
        {
            // simple property return
            if ($name == 'number') {
                return $this->_modelData['accountnum'];
            } elseif ($name == 'balance') {
                return floatval($this->_modelData['balance']);
            }

            // relational property: user is corresponding User
            // (one-to-one)
            elseif ($name == 'user') {
                return User::find($this->_modelData['username']);
            } else {
                throw new Exception("Model property $name not found.", 1);
            }
        } // end __get

        public static function create(array $properties)
        {
            throw new Exception('Not implemented.', 1);
        }
    }
