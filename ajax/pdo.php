<?php
define('DATABASE_NAME', 'lucky');
define('DATABASE_USER', 'root');
define('DATABASE_PASS', '');
define('DATABASE_HOST', '127.0.0.1');

class DBPDO {

    public $pdo;
    private $error;
    private static $instance;

    private function __construct() {
        $this->connect();
    }
    function prep_query($query){
        return $this->pdo->prepare($query);
    }
    function connect(){
        if(!$this->pdo){
            $dsn      = 'mysql:dbname=' . DATABASE_NAME . ';host=' . DATABASE_HOST.';charset=utf8';
            $user     = DATABASE_USER;
            $password = DATABASE_PASS;
            try {
                $this->pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_PERSISTENT => true));
                $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                return true;
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                die($this->error);
                return false;
            }
        }else{
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return true;
        }
    }
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

    function table_exists($table_name){
        $stmt = $this->prep_query('SHOW TABLES LIKE ?');
        $stmt->execute(array($table_name));
        return $stmt->rowCount() > 0;
    }
    function execute($query, $values = null){
        if($values == null){
            $values = array();
        }else if(!is_array($values)){
            $values = array($values);
        }
        $stmt = $this->prep_query($query);
        $stmt->execute($values);
        return $stmt;
    }
    function beginTransaction(){
        $this->pdo->beginTransaction();
    }
    function commit(){
        $this->pdo->commit();
    }
    function rollBack(){
        $this->pdo->rollBack();
    }

    function exec($query){
        $count=$this->pdo->exec($query);
        return $count;
    }
    function fetch($query, $values = null){
        if($values == null){
            $values = array();
        }else if(!is_array($values)){
            $values = array($values);
        }
        $stmt = $this->execute($query, $values);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function fetchAll($query, $values = null, $key = null){
        if($values == null){
            $values = array();
        }else if(!is_array($values)){
            $values = array($values);
        }
        $stmt = $this->execute($query, $values);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Allows the user to retrieve results using a
        // column from the results as a key for the array
        if($key != null && $results[0][$key]){
            $keyed_results = array();
            foreach($results as $result){
                $keyed_results[$result[$key]] = $result;
            }
            $results = $keyed_results;
        }
        //$stmt = null;
        return $results;
    }
    function lastInsertId(){
        return $this->pdo->lastInsertId();
    }
}