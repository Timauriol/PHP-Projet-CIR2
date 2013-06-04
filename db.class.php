<?php
include_once("config.php");

// resprésente une base de données
class DB{
    static $instance = null;
    private $pdo = null;

    function __construct(){
        try
        {
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
            $this->pdo = new PDO(SQL_DSN, SQL_USER, SQL_PASS, $options);
        }
        catch (PDOException $e) {
            echo("Echec : " . $e->getMessage());
        }
    }

    static function getInstance(){
        if(!self::$instance)
            self::$instance = new self;
        return self::$instance;
    }

    function getPdo(){ return $this->pdo; }

}
?>
