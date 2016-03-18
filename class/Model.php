<?php

    include_once 'Database.php';
    include_once 'Config.php';

    // Abstract class for Model objects
    // representing a table and the transactional
    // queries that can be sent to it. Returns
    // a model or an array of models from queries.

    // Heartily inspired by the architecture of
    // Laravel's Eloquent ORM.

    abstract class Model {

        // const _tableName = ''; // : string
        // const _pKey = ''; // : string
        protected $_modelData; // : map

        protected $_fillable = array();
        protected $_settable = array();

        function __construct($md = null) {

            // if this model is created as the result of a query,
            // allow model data to be set.
            if($md) {
                $this->_modelData = $md;
            }
        }

        // all() -> array(Model)
        public static function all() {
            $db = new Database();

            $c = get_called_class();
            $db->setTable($c::_tableName);

            $db->connect();

            $collection = array();

            // build models
            foreach($db->all() as $row) {
                array_push($collection, new static($row));
            }

            $db->disconnect();
            return $collection;
        }

        // find(value: *) -> Model
        public static function find($value) {
            $db = new Database();

            // $c = get_called_class();
            $db->setTable(static::_tableName);

            $pKey = static::_pKey;

            $db->connect();

            // query
            $rows = $db->where($pKey,$value);

            // build model
            if(isset($rows[0])) {
                $model = new static($rows[0]);
            } else {
                throw new Exception("Could not find()!", 1);
            }

            $db->disconnect();
            return $model;
        }

        // findAndDelete()
        public static function findAndDelete($value) {
            $db = new Database();

            // $c = get_called_class();
            $db->setTable(static::_tableName);

            $pKey = static::_pKey;

            $db->connect();

            // query
            $rows = $db->whereAndDelete($pKey,$value);

            // build model
            if(isset($rows[0])) {
                $model = new static($rows[0]);
            } else {
                throw new Exception("Could not findAndDelete()!", 1);
            }

            $db->disconnect();
            return $model;
        }

        // delete()
        public function delete() {
            // delete the model with the same pKey.
            $db = new Database();
            $db->connect();
            $db->setTable(static::_tableName);

            $pKey = static::_pKey;

            $db->deleteWhere($pKey, $this->_modelData[$pKey]);

            $db->disconnect();
        }

        // where(attr,value) -> array(Model)
        public static function where($attr,$value) {
            $db = new Database();

            // $c = get_called_class();
            $db->setTable(static::_tableName);

            $db->connect();

            $collection = array();

            // build models

            foreach($db->where($attr,$value) as $row) {
                array_push($collection, new static($row));
            }

            $db->disconnect();
            return $collection;
        }

        // save()
        public function save() {

            $db = new Database();

            $sql = "REPLACE INTO `" . Config::$database['prefix'] . static::_tableName . "` SET";
            $params = array();

            foreach ($this->_modelData as $key => $value) {
                if(in_array($key,$this->_fillable)) {
                    $params[$key] = $value;
                }
            }

            if(empty($params)) {
                return false;
            }

            foreach($params as $key => $value) {
                $sql .= " $key = :$key,";
            }

            $sql = substr_replace($sql, ";", -1);

            $db->connect();
            $statement = $db->pdo->prepare($sql);

            foreach($params as $key => $value) {
                $statement->bindValue(":$key",$value);
            }

            $statement->execute();

            $rc = $statement->rowCount();

            if($rc == 0) {
                return false;
            } else if($rc == 1) {
                // CREATE
                return true;
            } else if($rc == 2) {
                // UPDATE
                return true;
            } else {
                return false;
            }
        }

        // get($name) -> *
        // get data from the model
        public function __get($name) {
            return $this->_modelData[$name];
        }

        // set($name,$val)
        public function __set($name,$val) {
            if( in_array($name,$this->_settable) ) {
                $this->_modelData[$name] = $val;
            } else {
                throw new Exception("Property is not settable.",1);
            }
        } // __set

        // create($properties: array)
        // creates a model by building the proper _modelData (mirrors Table schema)
        // from Model schema.
        public abstract static function create(array $properties);

    } // Model

?>
