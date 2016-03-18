<?php

    include_once 'Model.php';
    include_once 'User.php';
    include_once 'Config.php';

    class Nonce extends Model
    {
        // Model Schema
        //
        // nonce : string
        // timestamp : DateTime
        // user : User

        protected $_settable = array('nonce', 'username');

        // Table Schema
        //
        // nonce : varchar(128)
        // timestamp : timestamp
        // username : varchar

        protected $_fillable = array('nonce', 'username');

        const _tableName = 'nonces';
        const _pKey = 'nonce';

        public function __get($name)
        {
            if ($name == 'nonce') {
                // simple property
                return $this->_modelData['nonce'];
            } elseif ($name == 'timestamp') {
                // mapped property sql timestamp -> php DateTime
                // Set timezone to server time (because CURRENT_TIMESTAMP is timezone-dependent)
                $d = new DateTimeImmutable($this->_modelData['timestamp'],
                                            new DateTimeZone(Config::$database['timezone']));

                return $d;
            } elseif ($name == 'user') {
                // relational property, get corr. User model
                return User::find($this->_modelData['username']);
            } else {
                throw new Exception("Model property $name not found.", 1);
            }
        } // end __get

        public static function create(array $properties)
        {
            $n = new self();
            // nonce:string -> nonce:string
            $n->nonce = $properties['nonce']; // required

            // user:User -> username:string
            if (isset($properties['user'])) {
                $n->username = $properties['user']->username;
            }

            return $n;
        }
    } // end Nonce
;
