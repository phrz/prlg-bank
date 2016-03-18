<?php

    include_once 'Config.php';

    class Database
    {
        private $settings; // : map(host,username,password,database,charset[,prefix])
        private $connected = false;

        public $pdo; // : PDO
        private $tableName; // : string

        // Use Config::$database settings by default
        public function __construct()
        {
            $this->settings = Config::$database;
            if (!isset($this->settings['prefix'])) {
                $this->settings['prefix'] = '';
            }
        }

        public function __destruct()
        {
            if ($this->connected) {
                $this->disconnect();
            }
        }

        // connect()
        public function connect()
        {
            if ($this->connected) {
                throw new Exception('Already Connected', 1);
            } else {
                $dsn = 'mysql:host='.
                        $this->settings['host'].
                        ';dbname='.
                        $this->settings['database'].
                        ';charset='.
                        $this->settings['charset'];

                try {
                    $this->pdo = new PDO(
                        $dsn,
                        $this->settings['username'],
                        $this->settings['password']);
                } catch (PDOException $pe) {
                    die('Unable to connect to database');
                }

                $this->connected = true;
            }
        }

        // all() -> array(map())
        public function all()
        {
            if (!$this->connected) {
                throw new Exception('Call to all() when not connected!', 1);
            }
            $s = $this->pdo->query('SELECT * FROM `'.$this->tableName.'`');

            return $s->fetchAll(PDO::FETCH_ASSOC);
        }

        // where($attr,$val) -> map()
        public function where(string $attr, $val)
        {
            if (!$this->connected) {
                throw new Exception('Call to where() when not connected!', 1);
            }

            $t = $this->tableName;
            $sql = "SELECT * FROM `$t` WHERE `$attr` = ?";

            $s = $this->pdo->prepare($sql);
            $s->execute(array($val));

            return $s->fetchAll(PDO::FETCH_ASSOC);
        }

        public function deleteWhere(string $attr, $val): bool
        {
            if (!$this->connected) {
                throw new Exception('Call to deleteWhere() when not connected!', 1);
            }

            $t = $this->tableName;
            $sql = "DELETE FROM `$t` WHERE `$attr` = ?;";

            $s = $this->pdo->prepare($sql);
            $s->execute(array($val));

            return $s->rowCount() > 0;
        }

        public function whereAndDelete(string $attr, $val)
        {
            if (!$this->connected) {
                throw new Exception('Call to whereAndDelete() when not connected!', 1);
            }

            // BEGIN
            $this->pdo->beginTransaction();
            $t = $this->tableName;

            // SELECT
            $sql = "SELECT * FROM `$t` WHERE `$attr` = ?";
            $s = $this->pdo->prepare($sql);
            $s->execute(array($val));
            error_log($sql);

            $results = $s->fetchAll(PDO::FETCH_ASSOC);

            // DELETE
            $deleteSQL = "DELETE FROM `$t` WHERE `$attr` = ?;";

            $delete = $this->pdo->prepare($deleteSQL);
            $delete->execute(array($val));

            // END
            $this->pdo->commit();

            // return match
            return $results;
        }

        // disconnect()
        public function disconnect()
        {
            $this->pdo = null; // destructs PDO and self-disconnects
            $this->connected = false;
        }

        // isConnected() -> bool
        public function isConnected()
        {
            return $this->connected;
        }

        // setTable(t: string)
        public function setTable($t)
        {
            $this->tableName = $this->settings['prefix'].$t;
        }
    }
